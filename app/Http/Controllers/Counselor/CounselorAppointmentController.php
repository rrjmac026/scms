<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\CounselingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CounselorAppointmentController extends Controller
{
    /**
     * Display counselor's appointments with unassigned ones
     */
    public function index()
    {
        $counselor = auth()->user()->counselor;

        // Counselor's own appointments
        $appointments = Appointment::with(['student.user', 'category'])
            ->where('counselor_id', $counselor->id)
            ->orderBy('preferred_date')
            ->paginate(15);

        // Unassigned appointments that counselors can claim
        $unassignedAppointments = Appointment::with(['student.user', 'category'])
            ->whereNull('counselor_id')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('counselors.appointments.index', compact('appointments', 'unassignedAppointments'));
    }

    /**
     * Accept an appointment (after admin approval)
     */
    public function accept(Appointment $appointment)
    {
        $counselor = auth()->user()->counselor;

        if ($appointment->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($appointment->status !== 'approved') {
            return back()->with('error', 'Only approved appointments can be accepted.');
        }

        $appointment->update(['status' => 'accepted']);

        // Send notification to student
        $appointment->student->user->notify(new \App\Notifications\AppointmentAccepted($appointment));

        // ✅ Automatically create counseling session if not yet created
        if (!$appointment->counselingSession) {
            CounselingSession::create([
                'appointment_id' => $appointment->id,
                'student_id'     => $appointment->student_id,
                'counselor_id'   => $appointment->counselor_id,
                'concern'        => $appointment->concern ?? 'Not specified',
                'status'         => 'pending',
            ]);
        }
        if (!$appointment->counselor_id) {
            return back()->with('error', 'This appointment has not been assigned to a counselor yet.');
        }

        return back()->with('success', 'Appointment accepted successfully. Student has been notified via email.');
    }

    /**
     * Reject an appointment (after admin approval)
     */
    public function reject(Request $request, Appointment $appointment)
    {
        $counselor = auth()->user()->counselor;

        if ($appointment->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($appointment->status !== 'approved') {
            return back()->with('error', 'Only approved appointments can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $appointment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Appointment rejected. Student will be notified.');
    }

        public function complete(Appointment $appointment)
    {
        $counselor = auth()->user()->counselor;

        if ($appointment->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($appointment->status, ['approved', 'accepted'])) {
            return back()->with('error', 'Only approved or accepted appointments can be marked as completed.');
        }

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked as completed.');
    }

    /**
     * Show appointment details
     */
    public function show(Appointment $appointment)
    {
        $counselor = auth()->user()->counselor;

        // Allow viewing unassigned appointments or own appointments
        if ($appointment->counselor_id && $appointment->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->load(['student.user', 'category', 'counselingSession.feedback']);
        
        return view('counselors.appointments.show', compact('appointment'));
    }

    /**
     * Calendar view for counselor
     */
    public function counselorCalendar()
    {
        $counselor = auth()->user()->counselor;

        $appointments = Appointment::with(['student.user', 'category'])
            ->where('counselor_id', $counselor->id)
            ->whereIn('status', ['pending', 'approved', 'accepted', 'completed', 'reschedule_requested_by_student'])
            ->get()
            ->map(function ($appointment) {
                $studentName = $appointment->student
                    ? $appointment->student->user->name
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
                    'id'    => $appointment->id,
                    'title' => 'Session with ' . $studentName . ' (' . ucfirst($appointment->status) . ')',
                    'start' => $startDateTime,
                    'color' => match ($appointment->status) {
                        'pending'   => '#fbbf24',
                        'approved'  => '#3b82f6',
                        'accepted'  => '#8b5cf6',
                        'completed' => '#10b981',
                        'reschedule_requested_by_student' => '#f97316',
                        default     => '#6b7280',
                    },
                    'extendedProps' => [
                        'student'     => $studentName,
                        'category'    => $appointment->category->name ?? 'General',
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50),
                        'google_event_id' => $appointment->google_event_id,
                    ],
                ];
            });

        return view('counselors.calendar.index', [
            'appointments' => $appointments,
        ]);
    }
}