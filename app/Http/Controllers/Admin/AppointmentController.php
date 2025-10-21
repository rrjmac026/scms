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
use App\Services\AppointmentCalendarSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLogHelper;

class AppointmentController extends Controller
{
    protected $syncService;

    public function __construct(AppointmentCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

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

        // Audit: admin viewed appointment list / applied filters
        AuditLogHelper::log('appointments_list_viewed', 'Viewed appointment list' . ($request->filled('status') ? " (status={$request->status})" : '') . ($request->filled('search') ? " (search={$request->search})" : ''));

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        try {
            $categories = CounselingCategory::where('status', 'active')->get();
            $students = Student::with('user')->get();
            $counselors = Counselor::with('user')->where('status', 'active')->get();

            // Get all booked slots WITH counselor_id AND student/counselor names for time slot availability
            $bookedSlots = Appointment::with(['student.user', 'counselor.user'])
                ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
                ->get()
                ->map(function ($appointment) {
                    // Handle both Carbon instances and string dates
                    $date = $appointment->preferred_date;
                    if ($date instanceof \Carbon\Carbon) {
                        $date = $date->format('Y-m-d');
                    }
                    
                    // Handle time format - ensure it's HH:MM
                    $time = $appointment->preferred_time;
                    if (strlen($time) > 5) {
                        $time = substr($time, 0, 5); // Remove seconds if present
                    }
                    
                    return [
                        'preferred_date' => $date,
                        'preferred_time' => $time,
                        'counselor_id' => $appointment->counselor_id,
                        'student_id' => $appointment->student_id,
                        // Add the names for display
                        'student_name' => $appointment->student->user->name ?? 'Unknown Student',
                        'counselor_name' => $appointment->counselor->user->name ?? 'Unknown Counselor',
                        'student_number' => $appointment->student->student_number ?? 'N/A',
                    ];
                })
                ->values()
                ->toArray();

            Log::info('Booked slots being passed to view:', ['slots' => $bookedSlots]);

            AuditLogHelper::log('appointment_create_form_opened', 'Opened appointment creation form');

            return view('admin.appointments.create', compact('categories', 'students', 'counselors', 'bookedSlots'));
        } catch (\Exception $e) {
            Log::error('Error loading appointment create form: ' . $e->getMessage());
            return redirect()->route('admin.appointments.index')
                ->with('error', 'An error occurred while loading the form. Please try again.');
        }
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'counselor_id' => 'nullable|exists:counselors,id',
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
                'auto_assign' => 'nullable|boolean',
            ]);

            $counselorId = null;

            // Handle auto-assignment if checkbox is checked
            if ($request->has('auto_assign') && $request->auto_assign) {
                $student = Student::findOrFail($validated['student_id']);
                $gradeLevel = $student->grade_level ?? null;

                if (!$gradeLevel) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['student_id' => 'Student does not have a grade level assigned. Cannot auto-assign counselor.']);
                }

                // Find counselor by assigned grade level
                $counselor = Counselor::where('assigned_grade_level', $gradeLevel)
                    ->where('status', 'active')
                    ->first();

                if (!$counselor) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['auto_assign' => 'No counselor available for grade level ' . $gradeLevel . '. Please select manually.']);
                }

                // Check if THIS SPECIFIC counselor is available on the selected date/time
                $hasConflict = Appointment::where('counselor_id', $counselor->id)
                    ->where('preferred_date', $validated['preferred_date'])
                    ->where('preferred_time', $validated['preferred_time'] . ':00')
                    ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
                    ->exists();

                if ($hasConflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['preferred_time' => 'The assigned counselor for this grade level is not available at the selected time. Please choose another time or select a counselor manually.']);
                }

                $counselorId = $counselor->id;
            } else {
                // Manual selection - validate counselor is provided
                $counselorId = $validated['counselor_id'] ?? null;
                
                if (!$counselorId) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['counselor_id' => 'Please select a counselor or enable auto-assign.']);
                }

                // Check if manually selected counselor is available
                $hasConflict = Appointment::where('counselor_id', $counselorId)
                    ->where('preferred_date', $validated['preferred_date'])
                    ->where('preferred_time', $validated['preferred_time'] . ':00')
                    ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
                    ->exists();

                if ($hasConflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['preferred_time' => 'Selected counselor is not available at this time.']);
                }
            }

            // Use transaction for data consistency
            DB::beginTransaction();

            try {
                // Admin creates appointment as already approved
                $appointment = Appointment::create([
                    'student_id' => $validated['student_id'],
                    'counselor_id' => $counselorId,
                    'preferred_date' => $validated['preferred_date'],
                    'preferred_time' => $validated['preferred_time'] . ':00',
                    'concern' => $validated['concern'],
                    'counseling_category_id' => $validated['counseling_category_id'],
                    'status' => 'approved',
                ]);

                // Sync to Google Calendar
                try {
                    $this->syncService->sync($appointment);
                    $syncMessage = ' Google Calendar synced successfully.';
                } catch (\Exception $e) {
                    Log::error("Sync failed for appointment {$appointment->id}: " . $e->getMessage());
                    $syncMessage = ' Note: Google Calendar sync failed, but the appointment was created.';
                }

                DB::commit();

                $studentName = Student::find($validated['student_id'])->user->name ?? 'Unknown student';
                AuditLogHelper::log('appointment_created', "Created appointment ID {$appointment->id} for {$studentName} on {$appointment->preferred_date} {$appointment->preferred_time}, assigned_counselor_id={$counselorId}");

                $counselor = Counselor::with('user')->find($counselorId);
                $counselorName = $counselor->user->name ?? 'Unknown';
                
                $message = "Appointment created and approved successfully. Assigned to: {$counselorName}.{$syncMessage}";

                return redirect()->route('admin.appointments.index')
                    ->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the appointment. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        try {
            $appointment->load(['student.user', 'counselor.user', 'counselingSession', 'category']);

            $availableCounselors = collect();
            
            if ($appointment->status === 'pending_admin_approval') {
                $availableCounselors = $this->getAvailableCounselors($appointment->preferred_date);
            }

            // Audit: admin viewed a specific appointment
            AuditLogHelper::log('appointment_viewed', "Viewed appointment ID {$appointment->id}");

            return view('admin.appointments.show', compact('appointment', 'availableCounselors'));
        } catch (\Exception $e) {
            Log::error('Error showing appointment: ' . $e->getMessage());
            return redirect()->route('admin.appointments.index')
                ->with('error', 'An error occurred while loading the appointment.');
        }
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        try {
            $categories = CounselingCategory::where('status', 'active')->get();
            $students = Student::with('user')->get();
            $counselors = Counselor::with('user')->where('status', 'active')->get();

            // Get all booked slots WITH counselor_id AND student/counselor names, excluding the current appointment
            $bookedSlots = Appointment::with(['student.user', 'counselor.user'])
                ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
                ->where('id', '!=', $appointment->id)
                ->get()
                ->map(function ($appt) {
                    // Handle both Carbon instances and string dates
                    $date = $appt->preferred_date;
                    if ($date instanceof \Carbon\Carbon) {
                        $date = $date->format('Y-m-d');
                    }
                    
                    // Handle time format - ensure it's HH:MM
                    $time = $appt->preferred_time;
                    if (strlen($time) > 5) {
                        $time = substr($time, 0, 5); // Remove seconds if present
                    }
                    
                    return [
                        'preferred_date' => $date,
                        'preferred_time' => $time,
                        'counselor_id' => $appt->counselor_id,
                        'student_id' => $appt->student_id,
                        // Add the names for display
                        'student_name' => $appt->student->user->name ?? 'Unknown Student',
                        'counselor_name' => $appt->counselor->user->name ?? 'Unknown Counselor',
                        'student_number' => $appt->student->student_number ?? 'N/A',
                    ];
                })
                ->values()
                ->toArray();

            Log::info('Booked slots for edit (excluding current):', ['slots' => $bookedSlots]);

            AuditLogHelper::log('appointment_edit_form_opened', "Opened edit form for appointment ID {$appointment->id}");

            return view('admin.appointments.edit', compact('appointment', 'categories', 'students', 'counselors', 'bookedSlots'));
        } catch (\Exception $e) {
            Log::error('Error loading appointment edit form: ' . $e->getMessage());
            return redirect()->route('admin.appointments.index')
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        try {
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

            // Check if counselor is available (skip if same counselor, date, and time)
            $timeChanged = $appointment->preferred_time !== $validated['preferred_time'] . ':00';
            $counselorChanged = $appointment->counselor_id !== $validated['counselor_id'];
            $dateChanged = $appointment->preferred_date !== $validated['preferred_date'];

            if ($counselorChanged || $dateChanged || $timeChanged) {
                $hasConflict = Appointment::where('counselor_id', $validated['counselor_id'])
                    ->where('preferred_date', $validated['preferred_date'])
                    ->where('preferred_time', $validated['preferred_time'] . ':00')
                    ->where('id', '!=', $appointment->id)
                    ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
                    ->exists();

                if ($hasConflict) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['preferred_time' => 'Selected counselor is not available at this time.']);
                }
            }

            DB::beginTransaction();

            try {
                // Update appointment
                $appointment->update([
                    'student_id' => $validated['student_id'],
                    'counselor_id' => $validated['counselor_id'],
                    'preferred_date' => $validated['preferred_date'],
                    'preferred_time' => $validated['preferred_time'] . ':00',
                    'concern' => $validated['concern'],
                    'counseling_category_id' => $validated['counseling_category_id'],
                ]);

                // Sync to Google Calendar
                try {
                    $this->syncService->sync($appointment);
                    $syncMessage = ' Google Calendar updated successfully.';
                } catch (\Exception $e) {
                    Log::error("Sync failed for appointment {$appointment->id}: " . $e->getMessage());
                    $syncMessage = ' Note: Google Calendar sync failed, but the appointment was updated.';
                }

                DB::commit();

                // Audit: updated appointment
                AuditLogHelper::log('appointment_updated', "Updated appointment ID {$appointment->id} — counselor_id={$validated['counselor_id']}, date={$validated['preferred_date']}, time={$validated['preferred_time']}");

                return redirect()->route('admin.appointments.index')
                    ->with('success', 'Appointment updated successfully.' . $syncMessage);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating appointment: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the appointment. Please try again.');
        }
    }

    /**
     * Approve appointment and assign counselor
     */
    public function approve(Appointment $appointment)
    {
        try {
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
                ->whereIn('status', ['approved', 'accepted', 'completed'])
                ->exists();

            if ($hasConflict) {
                return back()->with('error', 'Selected counselor has a conflict at that time.');
            }

            DB::beginTransaction();

            try {
                // Assign and approve
                $appointment->update([
                    'counselor_id' => $counselor->id,
                    'status' => 'approved',
                ]);

                // Sync to Google Calendar
                try {
                    $this->syncService->sync($appointment);
                } catch (\Exception $e) {
                    Log::error("Sync failed after approval: " . $e->getMessage());
                }

                // Send notification to counselor
                $counselor->user->notify(new \App\Notifications\CounselorAppointmentAssigned($appointment));

                DB::commit();

                // Audit: approved and assigned counselor
                AuditLogHelper::log('appointment_approved', "Approved appointment ID {$appointment->id} and assigned counselor ID {$counselor->id}");

                return back()->with('success', "Appointment approved and counselor {$counselor->user->name} assigned and notified automatically.");

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error approving appointment: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while approving the appointment.');
        }
    }

    /**
     * Reject appointment
     */
    public function reject(Appointment $appointment)
    {
        try {
            if ($appointment->status !== 'accepted') {
                return redirect()->back()->with('error', 'Only accepted appointments can be rejected.');
            }

            $appointment->update(['status' => 'rejected']);
            
            // Add notification
            $appointment->student->user->notify(new \App\Notifications\AppointmentRejected($appointment));

            // Audit: rejected appointment
            AuditLogHelper::log('appointment_rejected', "Rejected appointment ID {$appointment->id}");

            return redirect()->back()->with('success', 'Appointment has been rejected.');
        } catch (\Exception $e) {
            Log::error('Error rejecting appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while rejecting the appointment.');
        }
    }

    /**
     * Decline appointment
     */
    public function decline(Appointment $appointment)
    {
        try {
            if (!in_array($appointment->status, ['pending', 'approved'])) {
                return redirect()->back()->with('error', 'Only pending or approved appointments can be declined.');
            }

            $appointment->update(['status' => 'declined']);
            
            // Add notification
            $appointment->student->user->notify(new \App\Notifications\AppointmentDeclined($appointment));

            // Audit: declined appointment
            AuditLogHelper::log('appointment_declined', "Declined appointment ID {$appointment->id}");

            return redirect()->back()->with('success', 'Appointment has been declined.');
        } catch (\Exception $e) {
            Log::error('Error declining appointment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while declining the appointment.');
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
                            ->whereIn('status', ['approved', 'accepted', 'completed'])
                            ->exists();

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
        try {
            $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
                ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
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
                            'accepted' => '#10b981',
                            'completed' => '#16a34a',
                            'rejected' => '#ef4444',
                        },
                        'extendedProps' => [
                            'student'     => $appointment->student->user->name,
                            'counselor'   => $counselorName,
                            'category'    => $appointment->category->name ?? 'General',
                            'status'      => $appointment->status,
                            'description' => Str::limit($appointment->concern ?? '', 50),
                            'google_event_id' => $appointment->google_event_id,
                        ]
                    ];
                });

            // Audit: admin opened calendar view
            AuditLogHelper::log('appointments_calendar_viewed', 'Viewed appointments calendar');

            return view('admin.calendar.index', [
                'appointments' => $appointments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading calendar: ' . $e->getMessage());
            return redirect()->route('admin.appointments.index')
                ->with('error', 'An error occurred while loading the calendar.');
        }
    }

    /**
     * Delete appointment
     */
    public function destroy(Appointment $appointment)
    {
        try {
            DB::beginTransaction();

            // Change status to cancelled to trigger Google Calendar deletion
            $appointment->update(['status' => 'cancelled']);
            
            // Sync to remove from Google Calendar
            try {
                $this->syncService->sync($appointment);
            } catch (\Exception $e) {
                Log::error("Failed to sync cancelled appointment {$appointment->id}: " . $e->getMessage());
            }

            // Delete the appointment
            $appointmentId = $appointment->id;
            $appointment->delete();

            DB::commit();

            // Audit: deleted appointment
            AuditLogHelper::log('appointment_deleted', "Deleted appointment ID {$appointmentId}");

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete appointment {$appointment->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the appointment.');
        }
    }
}