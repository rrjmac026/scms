<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingCategory;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuditLogHelper;

class StudentAppointmentController extends Controller
{
    /**
     * Display a listing of the student's appointments.
     */
    public function index()
    {
        $student = auth()->user()->student;

        $appointments = Appointment::with(['counselor.user', 'category'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(10);

        // Audit: student viewed their appointments list
        AuditLogHelper::log(
            'student_appointments_list_viewed',
            "Student {$student->id} viewed their appointments list. count={$appointments->total()}"
        );

        return view('students.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $categories = CounselingCategory::where('status', 'active')->get();
        
        // Get the current student's grade level
        $student = auth()->user()->student;
        $studentGradeLevel = $student->grade_level;
        
        // Get counselor(s) assigned to this student's grade level
        $assignedCounselors = Counselor::where('status', 'active')
            ->where('assigned_grade_level', $studentGradeLevel)
            ->pluck('id')
            ->toArray();
        
        // If no counselors found for this grade level, log warning
        if (empty($assignedCounselors)) {
            Log::warning("No counselors assigned to grade level {$studentGradeLevel} for student {$student->id}");
        }
        
        // Only get booked slots for counselors assigned to this student's grade level
        $bookedSlots = Appointment::whereIn('status', ['pending', 'approved', 'accepted'])
            ->when(!empty($assignedCounselors), function ($query) use ($assignedCounselors) {
                $query->whereIn('counselor_id', $assignedCounselors);
            })
            ->get(['preferred_date', 'preferred_time', 'counselor_id'])
            ->map(fn($appointment) => [
                'preferred_date' => $appointment->preferred_date instanceof Carbon
                    ? $appointment->preferred_date->format('Y-m-d')
                    : $appointment->preferred_date,
                'preferred_time' => substr($appointment->preferred_time, 0, 5),
                'counselor_id' => $appointment->counselor_id,
            ]);

        $counselors = Counselor::with('user')
            ->where('status', 'active')
            ->where('assigned_grade_level', $studentGradeLevel)
            ->get();

        // Audit: student opened create appointment form
        AuditLogHelper::log('appointment_create_form_opened', "Student {$student->id} (Grade {$studentGradeLevel}) opened appointment creation form");

        return view('students.appointments.create', compact('categories', 'counselors', 'bookedSlots'));
    }

    /**
     * Store a newly created appointment request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'preferred_date' => 'required|date',
            'preferred_time' => 'required',
            'concern' => 'required|string|max:1000',
            'counseling_category_id' => 'required|exists:counseling_categories,id',
        ]);

        $timeWithSeconds = $request->preferred_time . ':00';
        
        // Get student's grade level and assigned counselors
        $student = auth()->user()->student;
        $studentGradeLevel = $student->grade_level;
        
        $assignedCounselors = Counselor::where('status', 'active')
            ->where('assigned_grade_level', $studentGradeLevel)
            ->pluck('id')
            ->toArray();

        // Check if time slot is already booked for THIS STUDENT'S COUNSELOR(S) only
        $exists = Appointment::where('preferred_date', $request->preferred_date)
            ->where('preferred_time', $timeWithSeconds)
            ->whereIn('status', ['pending', 'approved', 'accepted'])
            ->when(!empty($assignedCounselors), function ($query) use ($assignedCounselors) {
                $query->whereIn('counselor_id', $assignedCounselors);
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['preferred_time' => 'This time slot is already booked.'])
                ->withInput();
        }

        $appointment = Appointment::create([
            'student_id' => $student->id,
            'counseling_category_id' => $request->counseling_category_id,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $timeWithSeconds,
            'concern' => $request->concern,
            'status' => 'pending',
        ]);

        // 🔄 Optional Google Calendar Sync
        app(\App\Services\AppointmentCalendarSyncService::class)->sync($appointment);

        // Audit: student created an appointment request
        AuditLogHelper::log(
            'appointment_requested',
            "Student {$student->id} (Grade {$studentGradeLevel}) requested appointment ID {$appointment->id} on {$appointment->preferred_date} at {$appointment->preferred_time}"
        );

        return redirect()
            ->route('student.appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    /**
     * Show specific appointment details.
     */
    public function show(Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->load(['counselor.user', 'student.user', 'counselingSession.feedback', 'category']);

        // Audit: student viewed appointment details
        AuditLogHelper::log('appointment_viewed_by_student', "Student {$student->id} viewed appointment ID {$appointment->id}");

        return view('students.appointments.show', compact('appointment'));
    }

    /**
     * Cancel an appointment (for pending/approved/accepted only).
     */
    public function destroy(Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($appointment->status, ['pending', 'approved', 'accepted'])) {
            return redirect()
                ->route('student.appointments.index')
                ->with('error', 'Only pending, approved, or accepted appointments can be cancelled.');
        }

        $date = $appointment->preferred_date instanceof Carbon
            ? $appointment->preferred_date->format('Y-m-d')
            : $appointment->preferred_date;

        $time = substr($appointment->preferred_time, 0, 5);
        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");

        if ($appointmentDateTime->isBefore(now()->addDay())) {
            return redirect()
                ->route('student.appointments.index')
                ->with('error', 'You can only cancel at least 1 day before the appointment.');
        }

        $appointment->update(['status' => 'cancelled']);

        // 🔄 Google Calendar Sync
        app(\App\Services\AppointmentCalendarSyncService::class)->sync($appointment);

        // Audit: student cancelled appointment (without separate reason endpoint)
        AuditLogHelper::log('appointment_cancelled', "Student {$student->id} cancelled appointment ID {$appointment->id}");

        return redirect()
            ->route('student.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Cancel appointment with reason.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        try {
            $student = auth()->user()->student;

            if ($appointment->student_id !== $student->id) {
                abort(403, 'Unauthorized action.');
            }

            if (in_array($appointment->status, ['completed', 'cancelled', 'rejected', 'declined'])) {
                return back()->with('error', 'This appointment can no longer be cancelled.');
            }

            $request->validate([
                'cancelled_reason' => 'required|string|max:1000',
            ]);

            // ✅ Check if appointment is within 24 hours
            $date = $appointment->preferred_date instanceof Carbon
                ? $appointment->preferred_date->format('Y-m-d')
                : $appointment->preferred_date;

            $time = substr($appointment->preferred_time, 0, 5);
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");

            if ($appointmentDateTime->isBefore(now()->addDay())) {
                return back()->with('error', 'You can only cancel at least 24 hours before the appointment.');
            }

            $appointment->update([
                'status' => 'cancelled',
                'cancelled_reason' => $request->cancelled_reason,
            ]);

            // 🔄 Google Calendar Sync
            app(\App\Services\AppointmentCalendarSyncService::class)->sync($appointment);

            // Audit: student cancelled with reason
            AuditLogHelper::log(
                'appointment_cancelled_with_reason',
                "Student {$student->id} cancelled appointment ID {$appointment->id}. Reason: " . substr($request->cancelled_reason, 0, 250)
            );

            return redirect()
                ->route('student.appointments.index')
                ->with('success', 'Appointment cancelled successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Throwable $e) {
            Log::error('Appointment cancellation failed', [
                'appointment_id' => $appointment->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'An unexpected error occurred while cancelling the appointment. Please try again later.');
        }
    }


    /**
     * Display appointments on student calendar view.
     */
    public function studentCalendar()
    {
        $student = auth()->user()->student;

        $appointments = Appointment::with(['counselor.user', 'category'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved', 'accepted', 'completed'])
            ->get()
            ->map(function ($appointment) {
                $counselorName = $appointment->counselor
                    ? $appointment->counselor->user->name
                    : 'Unassigned';

                $date = $appointment->preferred_date instanceof Carbon
                    ? $appointment->preferred_date->format('Y-m-d')
                    : $appointment->preferred_date;

                $time = substr($appointment->preferred_time, 0, 5);
                $startDateTime = "{$date}T{$time}:00";

                return [
                    'id' => $appointment->id,
                    'title' => "Session with {$counselorName} (" . ucfirst($appointment->status) . ")",
                    'start' => $startDateTime,
                    'color' => match ($appointment->status) {
                        'pending' => '#fbbf24',   // Yellow
                        'approved' => '#3b82f6',  // Blue
                        'accepted' => '#8b5cf6',  // Purple
                        'completed' => '#10b981', // Green
                        default => '#6b7280',     // Gray
                    },
                    'extendedProps' => [
                        'counselor' => $counselorName,
                        'category' => $appointment->category->name ?? 'General',
                        'status' => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50),
                        'google_event_id' => $appointment->google_event_id, // ← ADD THIS LINE!
                    ],
                ];
            });

        // Audit: student opened calendar view
        AuditLogHelper::log('student_calendar_viewed', "Student {$student->id} viewed calendar");

        return view('students.calendar.index', compact('appointments'));
    }
}
