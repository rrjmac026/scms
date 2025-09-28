<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
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

            // Parse the appointment date and time
            $appointmentDate = Carbon::parse($appointment->preferred_date);
            $appointmentTime = $appointment->preferred_time;
            
            Log::info("Appointment date: {$appointmentDate}");
            Log::info("Appointment time: {$appointmentTime}");
            
            // Combine date and time
            $startDateTime = Carbon::parse($appointmentDate->format('Y-m-d') . ' ' . $appointmentTime);
            $endDateTime = $startDateTime->copy()->addHour(); // 1 hour session

            Log::info("Start DateTime: {$startDateTime->toRfc3339String()}");
            Log::info("End DateTime: {$endDateTime->toRfc3339String()}");

            // Create the event
            $event = new Google_Service_Calendar_Event([
                'summary' => 'Counseling Session - ' . $appointment->student->user->name,
                'description' => $this->formatEventDescription($appointment),
                'start' => [
                    'dateTime' => $startDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'UTC')
                ],
                'end' => [
                    'dateTime' => $endDateTime->toRfc3339String(),
                    'timeZone' => config('app.timezone', 'UTC')
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
                'location' => 'Counseling Office', // You can make this configurable
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
 * Debug method to check Google Calendar event status
 * Add this method to your App\Http\Controllers\Admin\AppointmentController class
 */
    public function debugCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            $debugInfo = [
                'appointment_id' => $appointment->id,
                'google_event_id' => $appointment->google_event_id,
                'counselor_exists' => $appointment->counselor ? true : false,
                'counselor_user_exists' => $appointment->counselor && $appointment->counselor->user ? true : false,
            ];

            if (!$appointment->counselor || !$appointment->counselor->user) {
                return response()->json([
                    'error' => 'No counselor or counselor user found',
                    'debug_info' => $debugInfo
                ]);
            }

            $debugInfo['counselor_email'] = $appointment->counselor->user->email;
            $debugInfo['counselor_has_google_token'] = $appointment->counselor->user->google_token ? true : false;

            if (!$appointment->counselor->user->google_token) {
                return response()->json([
                    'error' => 'Counselor has no Google token',
                    'debug_info' => $debugInfo
                ]);
            }

            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);
            
            if (!$service) {
                return response()->json([
                    'error' => 'Cannot get Google Calendar service',
                    'debug_info' => $debugInfo
                ]);
            }

            $debugInfo['calendar_service_available'] = true;

            if (!$appointment->google_event_id) {
                return response()->json([
                    'error' => 'No Google event ID stored',
                    'debug_info' => $debugInfo
                ]);
            }

            // Try to fetch the event from Google Calendar
            try {
                $event = $service->events->get('primary', $appointment->google_event_id);
                $debugInfo['event_found'] = true;
                $debugInfo['event_summary'] = $event->getSummary();
                $debugInfo['event_start'] = $event->getStart()->getDateTime();
                $debugInfo['event_status'] = $event->getStatus();
            } catch (\Exception $e) {
                $debugInfo['event_found'] = false;
                $debugInfo['event_error'] = $e->getMessage();
            }

            // List all calendars for this user
            try {
                $calendarList = $service->calendarList->listCalendarList();
                $debugInfo['available_calendars'] = collect($calendarList->getItems())->map(function($calendar) {
                    return [
                        'id' => $calendar->getId(),
                        'summary' => $calendar->getSummary(),
                        'primary' => $calendar->getPrimary(),
                        'selected' => $calendar->getSelected(),
                    ];
                })->toArray();
            } catch (\Exception $e) {
                $debugInfo['calendar_list_error'] = $e->getMessage();
            }

            // Test creating a simple event
            try {
                $testEvent = new \Google_Service_Calendar_Event([
                    'summary' => 'Test Event - ' . now()->format('H:i:s'),
                    'start' => [
                        'dateTime' => now()->addMinutes(5)->toRfc3339String(),
                        'timeZone' => config('app.timezone', 'UTC')
                    ],
                    'end' => [
                        'dateTime' => now()->addMinutes(65)->toRfc3339String(),
                        'timeZone' => config('app.timezone', 'UTC')
                    ],
                ]);

                $createdTest = $service->events->insert('primary', $testEvent);
                $debugInfo['test_event_created'] = true;
                $debugInfo['test_event_id'] = $createdTest->getId();

                // Delete the test event immediately
                $service->events->delete('primary', $createdTest->getId());
                $debugInfo['test_event_deleted'] = true;

            } catch (\Exception $e) {
                $debugInfo['test_event_error'] = $e->getMessage();
            }

            return response()->json([
                'success' => true,
                'debug_info' => $debugInfo
            ], 200, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Format the event description with appointment details
     */
    protected function formatEventDescription(Appointment $appointment)
    {
        $description = "Counseling Session Details:\n\n";
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

            // Update event details
            $appointmentDate = Carbon::parse($appointment->preferred_date);
            $appointmentTime = $appointment->preferred_time;
            $startDateTime = Carbon::parse($appointmentDate->format('Y-m-d') . ' ' . $appointmentTime);
            $endDateTime = $startDateTime->copy()->addHour();

            $event->setSummary('Counseling Session - ' . $appointment->student->user->name);
            $event->setDescription($this->formatEventDescription($appointment));
            $event->getStart()->setDateTime($startDateTime->toRfc3339String());
            $event->getEnd()->setDateTime($endDateTime->toRfc3339String());

            // Update the event
            $service->events->update('primary', $appointment->google_event_id, $event, [
                'sendUpdates' => 'all'
            ]);

            \Log::info("Google Calendar event updated successfully for appointment {$appointment->id}");

        } catch (\Exception $e) {
            \Log::error('Google Calendar event update failed: ' . $e->getMessage());
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

            \Log::info("Google Calendar event deleted successfully for appointment {$appointment->id}");

        } catch (\Exception $e) {
            \Log::error('Google Calendar event deletion failed: ' . $e->getMessage());
        }
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
                            \Log::warning("Invalid schedule format for counselor {$counselor->id}");
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
                        \Log::error("Error processing counselor {$counselor->id}: " . $e->getMessage());
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
            \Log::error('Error getting available counselors: ' . $e->getMessage());
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
                
                // ✅ Fix: Combine date and time properly
                $startDateTime = $appointment->preferred_date;
                if ($appointment->preferred_time) {
                    // Ensure we have both date and time
                    $date = $appointment->preferred_date instanceof \Carbon\Carbon
                        ? $appointment->preferred_date->format('Y-m-d')
                        : $appointment->preferred_date;
                    
                    $time = substr($appointment->preferred_time, 0, 5); // Get HH:MM only
                    $startDateTime = $date . 'T' . $time . ':00'; // ISO format for calendar
                }
                
                return [
                    'id'          => $appointment->id,
                    'title'       => $appointment->student->user->name . ' (' . ucfirst($appointment->status) . ')',
                    'start'       => $startDateTime, // ✅ Now includes both date and time
                    'color'       => match ($appointment->status) {
                        'pending'   => '#fbbf24', // yellow
                        'approved'  => '#3b82f6', // blue
                        'completed' => '#10b981', // green
                        default     => '#6b7280', // gray
                    },
                    'extendedProps' => [
                        'student'     => $appointment->student->user->name,
                        'counselor'   => $counselorName,
                        'category'    => $appointment->category->name ?? 'General', // ✅ Safe fallback
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50) // ✅ Use 'concern' field
                    ]
                ];
            });

        return view('admin.calendar.index', [
            'appointments' => $appointments,
        ]);
    }
}