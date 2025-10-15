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
use App\Services\AppointmentCalendarSyncService; // ← Use the sync service!
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    protected $syncService;

    // ✅ Inject the sync service
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
    public function store(Request $request)
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
            'preferred_time' => $validated['preferred_time'] . ':00', // Add seconds
            'concern' => $validated['concern'],
            'counseling_category_id' => $validated['counseling_category_id'],
            'status' => 'approved',
        ]);

        // 🔄 Use the sync service instead of direct Google Calendar calls
        try {
            $this->syncService->sync($appointment);
            $message = 'Appointment created and approved successfully. Google Calendar synced.';
        } catch (\Exception $e) {
            Log::error("Sync failed for appointment {$appointment->id}: " . $e->getMessage());
            $message = 'Appointment created but Google Calendar sync failed.';
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', $message);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'counselingSession', 'category']);

        $availableCounselors = collect();
        
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
    public function update(Request $request, Appointment $appointment)
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
        $appointment->update([
            'student_id' => $validated['student_id'],
            'counselor_id' => $validated['counselor_id'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'] . ':00', // Add seconds
            'concern' => $validated['concern'],
            'counseling_category_id' => $validated['counseling_category_id'],
        ]);

        // 🔄 Use the sync service to update Google Calendar
        try {
            $this->syncService->sync($appointment);
            $message = 'Appointment updated successfully. Google Calendar synced.';
        } catch (\Exception $e) {
            Log::error("Sync failed for appointment {$appointment->id}: " . $e->getMessage());
            $message = 'Appointment updated but Google Calendar sync failed.';
        }

        return redirect()->route('admin.appointments.index')
            ->with('success', $message);
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

        // 🔄 Sync to Google Calendar
        try {
            $this->syncService->sync($appointment);
        } catch (\Exception $e) {
            Log::error("Sync failed after approval: " . $e->getMessage());
        }

        // Send notification to counselor
        $counselor->user->notify(new \App\Notifications\CounselorAppointmentAssigned($appointment));

        return back()->with('success', "Appointment approved and counselor {$counselor->user->name} assigned and notified automatically.");
    }

    /**
     * Reject appointment (for student-submitted requests)
     */
    public function reject(Appointment $appointment)
    {
        if ($appointment->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only accepted appointments can be rejected.');
        }

        $appointment->update(['status' => 'rejected']); // or whatever final status you want

        return redirect()->back()->with('success', 'Appointment has been rejected.');
    }


    public function decline(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'Only pending or approved appointments can be declined.');
        }

        $appointment->update(['status' => 'declined']);

        return redirect()->back()->with('success', 'Appointment has been declined.');
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
                            ->whereIn('status', ['approved', 'completed'])
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
        $appointments = Appointment::with(['student.user', 'counselor.user', 'category'])
            ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])  // ← Add 'accepted'
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
                        'google_event_id' => $appointment->google_event_id, // ← IMPORTANT!
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
    public function destroy(Appointment $appointment)
    {
        try {
            // Change status to cancelled to trigger Google Calendar deletion
            $appointment->update(['status' => 'cancelled']);
            
            // 🔄 Sync to remove from Google Calendar
            $this->syncService->sync($appointment);

            // Now delete the appointment
            $appointment->delete();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to delete appointment {$appointment->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the appointment.');
        }
    }
}