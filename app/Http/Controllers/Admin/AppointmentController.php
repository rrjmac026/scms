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

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['student.user', 'counselor.user', 'category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('counselor.user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->latest()->paginate(10)->appends($request->all());

        // Add available counselors for pending_admin_approval appointments
        $appointments->getCollection()->transform(function ($appointment) {
            if ($appointment->status === 'pending_admin_approval') {
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
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $categories = CounselingCategory::where('status', 'active')->get();
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->where('status', 'active')->get();

        return view('admin.appointments.create', compact('categories', 'students', 'counselors'));
    }

    /**
     * Store a newly created appointment (Admin privilege - can directly approve and assign)
     */
    public function store(Request $request, GoogleCalendarService $gcal)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'preferred_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $day = Carbon::parse($value)->dayOfWeek;
                    if ($day === 0 || $day === 6) {
                        $fail('Appointments cannot be scheduled on weekends.');
                    }
                }
            ],
            'preferred_time' => 'required|date_format:H:i',
            'concern' => 'required|string|max:500',
            'counseling_category_id' => 'required|exists:counseling_categories,id',
        ]);

        // Check if counselor is available
        $availableCounselors = $this->getAvailableCounselors($validated['preferred_date']);
        
        if (!$availableCounselors->contains('id', $validated['counselor_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected counselor is not available on the selected date.');
        }

        // Admin creates appointment as already approved
        $appointment = Appointment::create([
            'student_id' => $validated['student_id'],
            'counselor_id' => $validated['counselor_id'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'concern' => $validated['concern'],
            'counseling_category_id' => $validated['counseling_category_id'],
            'status' => 'approved',
        ]);

        $appointment->load('counselor.user', 'student.user');

        // Attempt Google Calendar sync
        $googleMsg = '';
        if ($appointment->counselor->user && $appointment->counselor->user->google_token) {
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
            ->with('success', 'Appointment created and approved successfully.' . $googleMsg);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'counselingSession', 'category']);

        $availableCounselors = collect();
        
        // Only show available counselors for pending admin approval
        if ($appointment->status === 'pending_admin_approval') {
            $availableCounselors = $this->getAvailableCounselors($appointment->preferred_date);
        }

        return view('admin.appointments.show', compact('appointment', 'availableCounselors'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $categories = CounselingCategory::where('status', 'active')->get();
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->where('status', 'active')->get();

        return view('admin.appointments.edit', compact('appointment', 'categories', 'students', 'counselors'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment, GoogleCalendarService $gcal)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'preferred_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $day = Carbon::parse($value)->dayOfWeek;
                    if ($day === 0 || $day === 6) {
                        $fail('Appointments cannot be scheduled on weekends.');
                    }
                }
            ],
            'preferred_time' => 'required|date_format:H:i',
            'concern' => 'required|string|max:500',
            'counseling_category_id' => 'required|exists:counseling_categories,id',
        ]);

        // Check if counselor is available (skip if same counselor and date)
        $counselorChanged = $appointment->counselor_id !== $validated['counselor_id'];
        $dateChanged = $appointment->preferred_date !== $validated['preferred_date'];

        if ($counselorChanged || $dateChanged) {
            $availableCounselors = $this->getAvailableCounselors($validated['preferred_date']);
            
            if (!$availableCounselors->contains('id', $validated['counselor_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected counselor is not available on the selected date.');
            }
        }

        // Update appointment
        $appointment->update($validated);
        $appointment->load('counselor.user', 'student.user');

        // Update Google Calendar event if exists
        $googleMsg = '';
        if ($appointment->google_event_id && $appointment->counselor->user && $appointment->counselor->user->google_token) {
            try {
                $this->updateGoogleCalendarEvent($appointment, $gcal);
                $googleMsg = ' Google Calendar event updated.';
            } catch (\Exception $e) {
                Log::error("Google Calendar event update failed for appointment {$appointment->id}: " . $e->getMessage());
                $googleMsg = ' Failed to update Google Calendar event.';
            }
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment updated successfully.' . $googleMsg);
    }

    /**
     * Approve appointment and assign counselor (for student-submitted requests)
     */
    public function approve(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be approved.');
        }

        $student = $appointment->student;
        $gradeLevel = $student->grade_level ?? null;

        if (!$gradeLevel) {
            return back()->with('error', 'Student does not have a grade level assigned.');
        }

        // Find counselor by assigned grade level
        $counselor = Counselor::where('assigned_grade_level', $gradeLevel)
            ->where('status', 'active')
            ->first();

        if (!$counselor) {
            return back()->with('error', 'No counselor available for this grade level.');
        }

        // Check if counselor is free
        $hasConflict = Appointment::where('counselor_id', $counselor->id)
            ->where('preferred_date', $appointment->preferred_date)
            ->where('preferred_time', $appointment->preferred_time)
            ->whereIn('status', ['approved', 'completed'])
            ->exists();

        if ($hasConflict) {
            return back()->with('error', 'Selected counselor has a conflict at that time.');
        }

        // Assign and approve
        $appointment->update([
            'counselor_id' => $counselor->id,
            'status' => 'approved',
        ]);

        return back()->with('success', "Appointment approved and counselor {$counselor->user->name} assigned automatically.");
    }





    /**
     * Reject appointment (for student-submitted requests)
     */
    public function reject(Request $request, Appointment $appointment)
    {
        try {
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'Only pending appointments can be rejected.');
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            $appointment->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason']
            ]);

            return back()->with('success', 'Appointment rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Error rejecting appointment: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while rejecting the appointment.');
        }
    }

    /**
     * Create Google Calendar event for the appointment
     */
    protected function createGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            Log::info("Starting Google Calendar event creation for appointment {$appointment->id}");
            
            if (!$appointment->counselor || !$appointment->counselor->user) {
                Log::error("No counselor or counselor user for appointment {$appointment->id}");
                return;
            }

            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);

            if (!$service) {
                Log::warning("Counselor doesn't have Google Calendar connected");
                return;
            }

            // Get raw date and time values
            $rawDate = $appointment->getOriginal('preferred_date');
            $rawTime = $appointment->getOriginal('preferred_time');
            
            $dateString = substr($rawDate, 0, 10);
            $timeString = substr($rawTime, 0, 5);
            
            Log::info("Date: {$dateString}, Time: {$timeString}");
            
            // Format time as 12-hour format for display
            $formattedTime = Carbon::createFromFormat('H:i', $timeString)->format('g:i A');
            
            // Create all-day event with time in title and description
            $event = new Google_Service_Calendar_Event([
                'summary' => 'Counseling Session - ' . $appointment->student->user->name . ' at ' . $formattedTime,
                'description' => $this->formatEventDescription($appointment),
                'start' => [
                    'date' => $dateString,
                ],
                'end' => [
                    'date' => Carbon::parse($dateString)->addDay()->format('Y-m-d'),
                ],
                'attendees' => [
                    ['email' => $appointment->student->user->email, 'displayName' => $appointment->student->user->name],
                    ['email' => $appointment->counselor->user->email, 'displayName' => $appointment->counselor->user->name]
                ],
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60],
                        ['method' => 'popup', 'minutes' => 60],
                    ],
                ],
                'location' => 'Counseling Office',
                'colorId' => '2',
            ]);

            $created = $service->events->insert('primary', $event, [
                'sendUpdates' => 'all'
            ]);

            $appointment->google_event_id = $created->getId();
            $appointment->save();

            Log::info("Google Calendar event created successfully");

        } catch (\Exception $e) {
            Log::error('Google Calendar event creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Format the event description
     */
    protected function formatEventDescription(Appointment $appointment)
    {
        $description = "Counseling Session Details:\n\n";
        
        $timeString = substr($appointment->getOriginal('preferred_time'), 0, 5);
        $formattedTime = Carbon::createFromFormat('H:i', $timeString)->format('h:i A');
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
     * Update Google Calendar event
     */
    protected function updateGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            if (!$appointment->google_event_id || !$appointment->counselor) {
                return;
            }

            $service = $gcal->getCalendarServiceForUser($appointment->counselor->user);
            
            if (!$service) {
                return;
            }

            $event = $service->events->get('primary', $appointment->google_event_id);

            $rawDate = $appointment->getOriginal('preferred_date');
            $rawTime = $appointment->getOriginal('preferred_time');
            
            $dateString = substr($rawDate, 0, 10);
            $timeString = substr($rawTime, 0, 5);
            
            $formattedTime = Carbon::createFromFormat('H:i', $timeString)->format('g:i A');

            $event->setSummary('Counseling Session - ' . $appointment->student->user->name . ' at ' . $formattedTime);
            $event->setDescription($this->formatEventDescription($appointment));
            $event->getStart()->setDate($dateString);
            $event->getEnd()->setDate(Carbon::parse($dateString)->addDay()->format('Y-m-d'));

            $service->events->update('primary', $appointment->google_event_id, $event, [
                'sendUpdates' => 'all'
            ]);

            Log::info("Google Calendar event updated successfully");

        } catch (\Exception $e) {
            Log::error('Google Calendar event update failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete Google Calendar event
     */
    protected function deleteGoogleCalendarEvent(Appointment $appointment, GoogleCalendarService $gcal)
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

            Log::info("Google Calendar event deleted successfully");

        } catch (\Exception $e) {
            Log::error('Google Calendar event deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Get available counselors for a given date
     */
    protected function getAvailableCounselors($date) 
    {
        try {
            $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

            return Counselor::with('user')
                ->where('status', 'active')
                ->whereNotNull('availability_schedule')
                ->get()
                ->filter(function ($counselor) use ($dayOfWeek, $date) {
                    try {
                        $schedule = is_string($counselor->availability_schedule) 
                            ? json_decode($counselor->availability_schedule, true) 
                            : $counselor->availability_schedule;

                        if (!$schedule) {
                            return false;
                        }

                        $availableDays = isset($schedule['days']) 
                            ? array_map('strtolower', $schedule['days'])  
                            : array_map('strtolower', (array)$schedule); 

                        $isAvailable = in_array($dayOfWeek, $availableDays);

                        if (!$isAvailable) {
                            return false;
                        }

                        // Check if counselor already has an appointment on this date
                        return !$counselor->appointments()
                            ->whereDate('preferred_date', $date)
                            ->whereIn('status', ['approved', 'completed'])                            ->exists();

                    } catch (\Exception $e) {
                        Log::error("Error processing counselor {$counselor->id}: " . $e->getMessage());
                        return false;
                    }
                })
                ->map(function($counselor) {
                    $counselor->has_google_calendar = $counselor->user && $counselor->user->google_token ? true : false;
                    return $counselor;
                })
                ->values();

        } catch (\Exception $e) {
            Log::error('Error getting available counselors: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Calendar view
     */
    public function calendar()
    {
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->whereIn('status', ['pending', 'approved', 'completed'])            
            ->get()
            ->map(function ($appointment) {
                $counselorName = $appointment->counselor 
                    ? $appointment->counselor->user->name 
                    : 'Unassigned';
                
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
                        'pending' => '#f59e0b',
                        'approved' => '#3b82f6',
                        'completed' => '#16a34a',
                        'rejected' => '#ef4444',
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

    /**
     * Delete appointment
     */
    public function destroy(Appointment $appointment, GoogleCalendarService $gcal)
    {
        try {
            // Delete Google Calendar event if exists
            if ($appointment->google_event_id && $appointment->counselor && $appointment->counselor->user) {
                $this->deleteGoogleCalendarEvent($appointment, $gcal);
            }

            $appointment->delete();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to delete appointment {$appointment->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the appointment.');
        }
    }

    
}