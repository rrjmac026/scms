<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\Student;
use App\Models\CounselingCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\GoogleCalendarService;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Log;
use Google_Service_Calendar;


class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->latest()
            ->paginate(10);

        $appointments->getCollection()->transform(function ($appointment) {
            if ($appointment->status === 'pending') {
                $appointment->availableCounselors = $this->getAvailableCounselors(
                    $appointment->preferred_date
                );
            } else {
                $appointment->availableCounselors = collect();
            }
            return $appointment;
        });

        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $categories = CounselingCategory::where('status', 'active')->get();
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->get();

        return view('admin.appointments.create', compact('categories', 'students', 'counselors'));
    }


    public function store(Request $request, GoogleCalendarService $gcal)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'nullable|exists:counselors,id',
            'preferred_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $day = \Carbon\Carbon::parse($value)->dayOfWeek;
                    if ($day === 0 || $day === 6) {
                        $fail('Appointments cannot be scheduled on weekends.');
                    }
                }
            ],
            'preferred_time' => 'required|date_format:H:i',
            'concern' => 'required|string|max:500',
            'counseling_category_id' => 'required|exists:counseling_categories,id',
        ]);

        // Determine counselor (manual selection or auto-assign)
        $counselorId = $validated['counselor_id'] ?? null;
        $googleMsg = '';

        // If no counselor selected, auto-assign
        if (!$counselorId) {
            $availableCounselors = $this->getAvailableCounselors($validated['preferred_date']);
            
            if ($availableCounselors->isEmpty()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No available counselors found for the selected date.');
            }

            // Prioritize counselors with Google Calendar
            $counselorsWithCalendar = $availableCounselors->filter(fn($c) => $c->user && $c->user->google_token);
            $selectedCounselor = $counselorsWithCalendar->first() ?: $availableCounselors->first();
            $counselorId = $selectedCounselor->id;
        }

        // Create appointment
        $appointment = Appointment::create([
            'student_id' => $validated['student_id'],
            'counselor_id' => $counselorId,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'concern' => $validated['concern'],
            'counseling_category_id' => $validated['counseling_category_id'],
            'status' => 'pending',
        ]);

        // Load relationships
        $appointment->load('counselor.user', 'student.user');

        // Attempt Google Calendar sync if counselor has token
        if ($appointment->counselor && $appointment->counselor->user && $appointment->counselor->user->google_token) {
            try {
                $this->createGoogleCalendarEvent($appointment, $gcal);
                $googleMsg = ' Google Calendar event created.';
            } catch (\Exception $e) {
                Log::error("Google Calendar event creation failed for appointment {$appointment->id}: " . $e->getMessage());
                $googleMsg = ' Failed to create Google Calendar event.';
            }
        } else {
            $googleMsg = ' Counselor does not have Google Calendar connected.';
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment created successfully.' . $googleMsg);
    }


    /**
     * Display the specified appointment and list available counselors.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'counselingSession', 'category']);

        $availableCounselors = $this->getAvailableCounselors(
            $appointment->preferred_date
        );

        return view('admin.appointments.show', compact('appointment', 'availableCounselors'));
    }

    /**
     * Assign a counselor to an appointment.
     */
    public function assignCounselor(Request $request, Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            // Only pending appointments can be assigned
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'Only pending appointments can be assigned.');
            }

            if ($appointment->counselor_id) {
                return back()->with('error', 'Appointment already has an assigned counselor.');
            }

            $availableCounselors = $this->getAvailableCounselors($appointment->preferred_date);

            // Auto-assign if no specific counselor selected
            if (empty($request->counselor_id)) {
                $counselorsWithCalendar = $availableCounselors->filter(fn($c) => $c->user && $c->user->google_token);
                $counselor = $counselorsWithCalendar->first() ?: $availableCounselors->first();

                if (!$counselor) {
                    return back()->with('error', 'No available counselors found for this date.');
                }

                $counselorId = $counselor->id;
            } else {
                $counselorId = $request->counselor_id;

                if (!$availableCounselors->contains('id', $counselorId)) {
                    return back()->with('error', 'Selected counselor is not available.');
                }
            }

            // Assign the counselor
            $appointment->update(['counselor_id' => $counselorId]);
            $appointment->load('counselor.user', 'student.user');

            // Attempt Google Calendar sync if counselor has token
            if ($appointment->counselor->user && $appointment->counselor->user->google_token) {
                try {
                    $this->createGoogleCalendarEvent($appointment, $gcal);
                    $googleMsg = 'Google Calendar event created.';
                } catch (\Exception $e) {
                    Log::error("Google Calendar event creation failed for appointment {$appointment->id}: " . $e->getMessage());
                    $googleMsg = 'Failed to create Google Calendar event.';
                }
            } else {
                $googleMsg = 'Counselor does not have Google Calendar connected.';
            }

            return back()->with('success', 'Counselor assigned successfully. ' . $googleMsg);

        } catch (\Exception $e) {
            Log::error('Error assigning counselor: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while assigning the counselor. Please try again.');
        }
    }



    /**
     * Create Google Calendar event for the appointment
     */
    protected function createGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            Log::info("Starting Google Calendar event creation for appointment {$appointment->id}");
            Log::info("App Timezone: " . config('app.timezone'));
            Log::info("Server Timezone: " . date_default_timezone_get());
            
            // Check if counselor exists
            if (!$appointment->counselor) {
                Log::error("No counselor assigned to appointment {$appointment->id}");
                return;
            }

            // Check if counselor has a user
            if (!$appointment->counselor->user) {
                Log::error("Counselor {$appointment->counselor->id} has no associated user");
                return;
            }

            Log::info("Counselor user ID: {$appointment->counselor->user->id}");
            Log::info("Counselor has Google token: " . ($appointment->counselor->user->google_token ? 'Yes' : 'No'));

            // Get Google Calendar service for the counselor
            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);

            if (!$service) {
                Log::warning("Counselor {$appointment->counselor->user->name} doesn't have Google Calendar connected or token is invalid.");
                return;
            }

            Log::info("Google Calendar service obtained successfully");

            // FIXED: Get the actual date without timezone conversion
            // Use getOriginal to bypass any Laravel casting/conversion
            $rawDate = $appointment->getOriginal('preferred_date');
            $rawTime = $appointment->getOriginal('preferred_time');
            
            // Extract just the date part (YYYY-MM-DD) from whatever format it's in
            $dateString = substr($rawDate, 0, 10); // Gets "2025-10-15" from "2025-10-15T16:00:00.000000Z"
            
            // Get time in HH:MM format
            $timeString = substr($rawTime, 0, 5); // Gets "16:00" from "16:00:00"
            
            Log::info("Processing appointment {$appointment->id}");
            Log::info("Raw date from DB: {$rawDate}");
            Log::info("Raw time from DB: {$rawTime}");
            Log::info("Extracted date: {$dateString}");
            Log::info("Extracted time: {$timeString}");
            
            // Create datetime in Manila timezone from the raw values
            $startDateTime = Carbon::parse("{$dateString} {$timeString}", 'Asia/Manila');
            $endDateTime = $startDateTime->copy()->addHour();
            
            Log::info("Start DateTime (Manila): " . $startDateTime->format('Y-m-d H:i:s T'));
            Log::info("Start DateTime (RFC3339): " . $startDateTime->toRfc3339String());
            Log::info("End DateTime (Manila): " . $endDateTime->format('Y-m-d H:i:s T'));
            
            Log::info("Start DateTime (Manila): {$startDateTime->toDateTimeString()}");
            Log::info("Start DateTime (RFC3339): {$startDateTime->toRfc3339String()}");
            Log::info("End DateTime (Manila): {$endDateTime->toDateTimeString()}");

            // Create event as all-day event (no time displayed)
            // Format time as 12-hour format
            $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $timeString)->format('g:i A');
            
            $event = new Google_Service_Calendar_Event([
                'summary' => 'Counseling Session - ' . $appointment->student->user->name . ' at ' . $formattedTime,
                'description' => $this->formatEventDescription($appointment),
                'start' => [
                    'date' => $dateString, // Use 'date' instead of 'dateTime' for all-day events
                ],
                'end' => [
                    'date' => Carbon::parse($dateString)->addDay()->format('Y-m-d'), // Next day for all-day event
                ],
                'attendees' => [
                    ['email' => $appointment->student->user->email, 'displayName' => $appointment->student->user->name],
                    ['email' => $appointment->counselor->user->email, 'displayName' => $appointment->counselor->user->name]
                ],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60], // 1 day before
                        ['method' => 'popup', 'minutes' => 60], // 1 hour before
                    ],
                ],
                'location' => 'Counseling Office',
                'colorId' => '2', // Green color for counseling appointments
            ]);

            Log::info("Event object created, attempting to insert into Google Calendar");

            // Insert event into Google Calendar
            $created = $service->events->insert('primary', $event, [
                'sendUpdates' => 'all' // Send email notifications to all attendees
            ]);

            // Save the Google event ID for future reference
            $appointment->google_event_id = $created->getId();
            $appointment->save();

            Log::info("Google Calendar event created successfully for appointment {$appointment->id} with event ID: {$created->getId()}");

        } catch (\Google\Service\Exception $e) {
            Log::error('Google Calendar API error: ' . $e->getMessage());
            Log::error('Google error details: ' . json_encode($e->getErrors()));
        } catch (\Exception $e) {
            Log::error('Google Calendar event creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            // Don't throw the exception - we don't want calendar failures to break the assignment
        }
    }

    /**
     * Format the event description with appointment details
     */
    protected function formatEventDescription(Appointment $appointment)
    {
        $description = "Counseling Session Details:\n\n";
        
        // Add the appointment time prominently since it's an all-day event
        $timeString = substr($appointment->getOriginal('preferred_time'), 0, 5);
        $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $timeString)->format('h:i A');
        $description .= "⏰ Scheduled Time: " . $formattedTime . "\n\n";
        
        $description .= "Student: " . $appointment->student->user->name . "\n";
        $description .= "Email: " . $appointment->student->user->email . "\n";
        
        if ($appointment->category) {
            $description .= "Category: " . $appointment->category->name . "\n";
        }
        
        if ($appointment->concern) {
            $description .= "Concern: " . $appointment->concern . "\n";
        }
        
        $description .= "\nAppointment ID: " . $appointment->id;
        
        return $description;
    }

    /**
     * Update Google Calendar event (useful for rescheduling or updates)
     */
    public function updateGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            if (!$appointment->google_event_id || !$appointment->counselor) {
                return;
            }

            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);
            
            if (!$service) {
                return;
            }

            // Get existing event
            $event = $service->events->get('primary', $appointment->google_event_id);

            // FIXED: Use raw database values to avoid timezone conversion
            $rawDate = $appointment->getOriginal('preferred_date');
            $rawTime = $appointment->getOriginal('preferred_time');
            
            $dateString = substr($rawDate, 0, 10);
            $timeString = substr($rawTime, 0, 5);
            
            $startDateTime = Carbon::parse("{$dateString} {$timeString}", 'Asia/Manila');
            $endDateTime = $startDateTime->copy()->addHour();

            $event->setSummary('Counseling Session - ' . $appointment->student->user->name);
            $event->setDescription($this->formatEventDescription($appointment));
            $event->getStart()->setDateTime($startDateTime->toRfc3339String());
            $event->getStart()->setTimeZone('Asia/Manila');
            $event->getEnd()->setDateTime($endDateTime->toRfc3339String());
            $event->getEnd()->setTimeZone('Asia/Manila');

            // Update the event
            $service->events->update('primary', $appointment->google_event_id, $event, [
                'sendUpdates' => 'all'
            ]);

            Log::info("Google Calendar event updated successfully for appointment {$appointment->id}");

        } catch (\Exception $e) {
            Log::error('Google Calendar event update failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete Google Calendar event (when appointment is cancelled)
     */
    public function deleteGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            if (!$appointment->google_event_id || !$appointment->counselor) {
                return;
            }

            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);
            
            if (!$service) {
                return;
            }

            $service->events->delete('primary', $appointment->google_event_id, [
                'sendUpdates' => 'all'
            ]);

            $appointment->google_event_id = null;
            $appointment->save();

            Log::info("Google Calendar event deleted successfully for appointment {$appointment->id}");

        } catch (\Exception $e) {
            Log::error('Google Calendar event deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * DEBUG: Test timezone handling
     */
    public function debugTimezone(Appointment $appointment)
    {
        $debug = [
            'appointment_id' => $appointment->id,
            'raw_preferred_date' => $appointment->getOriginal('preferred_date'),
            'raw_preferred_time' => $appointment->getOriginal('preferred_time'),
            'preferred_date_attr' => $appointment->preferred_date,
            'preferred_time_attr' => $appointment->preferred_time,
            'app_timezone' => config('app.timezone'),
            'php_timezone' => date_default_timezone_get(),
            'server_time' => now()->toDateTimeString(),
        ];

        // Test parsing
        if ($appointment->preferred_date instanceof Carbon) {
            $dateString = $appointment->preferred_date->format('Y-m-d');
        } else {
            $dateString = date('Y-m-d', strtotime($appointment->preferred_date));
        }
        
        if ($appointment->preferred_time instanceof Carbon) {
            $timeString = $appointment->preferred_time->format('H:i');
        } else {
            $timeString = substr($appointment->preferred_time, 0, 5);
        }

        $debug['parsed_date'] = $dateString;
        $debug['parsed_time'] = $timeString;
        $debug['combined'] = "{$dateString} {$timeString}";

        // Test Carbon parsing
        $carbonTest = Carbon::parse("{$dateString} {$timeString}", 'Asia/Manila');
        $debug['carbon_datetime'] = $carbonTest->toDateTimeString();
        $debug['carbon_timezone'] = $carbonTest->timezoneName;
        $debug['carbon_offset'] = $carbonTest->format('P');
        $debug['carbon_rfc3339'] = $carbonTest->toRfc3339String();
        $debug['carbon_iso8601'] = $carbonTest->toIso8601String();

        // Test what Google would see
        $debug['google_start'] = [
            'dateTime' => $carbonTest->toRfc3339String(),
            'timeZone' => 'Asia/Manila',
        ];

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Get all available counselors for a given date (ignoring time).
     */
    protected function getAvailableCounselors($date) 
    {
        try {
            $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

            return Counselor::with('user')
                ->whereNotNull('availability_schedule')
                ->get()
                ->filter(function ($counselor) use ($dayOfWeek, $date) {
                    try {
                        $schedule = is_string($counselor->availability_schedule) 
                            ? json_decode($counselor->availability_schedule, true) 
                            : $counselor->availability_schedule;

                        if (!$schedule) {
                            Log::warning("Invalid schedule format for counselor {$counselor->id}");
                            return false;
                        }

                        $availableDays = isset($schedule['days']) 
                            ? array_map('strtolower', $schedule['days'])  
                            : array_map('strtolower', (array)$schedule); 

                        $isAvailable = in_array($dayOfWeek, $availableDays);

                        if (!$isAvailable) {
                            return false;
                        }

                        return !$counselor->appointments()
                            ->whereDate('preferred_date', $date)
                            ->whereIn('status', ['approved', 'completed'])
                            ->exists();

                    } catch (\Exception $e) {
                        Log::error("Error processing counselor {$counselor->id}: " . $e->getMessage());
                        return false;
                    }
                })
                ->map(function($counselor) {
                    // Add Google Calendar connection status
                    $counselor->has_google_calendar = $counselor->user && $counselor->user->google_token ? true : false;
                    return $counselor;
                })
                ->values();

        } catch (\Exception $e) {
            Log::error('Error getting available counselors: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function calendar()
    {
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->get()
            ->map(function ($appointment) {
                $counselorName = $appointment->counselor 
                    ? $appointment->counselor->user->name 
                    : 'Unassigned';
                
                // Combine date and time properly
                $startDateTime = $appointment->preferred_date;
                if ($appointment->preferred_time) {
                    $date = $appointment->preferred_date instanceof \Carbon\Carbon
                        ? $appointment->preferred_date->format('Y-m-d')
                        : $appointment->preferred_date;
                    
                    $time = substr($appointment->preferred_time, 0, 5);
                    $startDateTime = $date . 'T' . $time . ':00';
                }
                
                return [
                    'id'          => $appointment->id,
                    'title'       => $appointment->student->user->name . ' (' . ucfirst($appointment->status) . ')',
                    'start'       => $startDateTime,
                    'color'       => match ($appointment->status) {
                        'pending'   => '#fbbf24',
                        'approved'  => '#3b82f6',
                        'completed' => '#10b981',
                        default     => '#6b7280',
                    },
                    'extendedProps' => [
                        'student'     => $appointment->student->user->name,
                        'counselor'   => $counselorName,
                        'category'    => $appointment->category->name ?? 'General',
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50)
                    ]
                ];
            });

        return view('admin.calendar.index', [
            'appointments' => $appointments,
        ]);
    }

    public function destroy(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            // Delete event from Google Calendar if it exists
            if ($appointment->google_event_id && $appointment->counselor && $appointment->counselor->user) {
                $this->deleteGoogleCalendarEvent($appointment, $gcal);
            }

            // Delete the appointment record
            $appointment->delete();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to delete appointment {$appointment->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the appointment.');
        }
    }
}