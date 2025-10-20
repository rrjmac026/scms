<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use App\Models\CounselingSession;
use App\Services\AppointmentCalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuditLogHelper;

class CounselorAppointmentController extends Controller
{
    protected $syncService;

    // ✅ Inject the sync service
    public function __construct(AppointmentCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

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

        // Audit: counselor viewed appointments list
        AuditLogHelper::log(
            'counselor_appointments_list_viewed',
            "Counselor {$counselor->id} viewed appointments list. own_count={$appointments->total()}, unassigned_count=" . $unassignedAppointments->count()
        );

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

        if (!$appointment->counselor_id) {
            return back()->with('error', 'This appointment has not been assigned to a counselor yet.');
        }

        $appointment->update(['status' => 'accepted']);

        // 🔄 Sync to Google Calendar
        try {
            $this->syncService->sync($appointment);
            Log::info("Calendar synced after counselor accepted appointment {$appointment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to sync calendar after accept: " . $e->getMessage());
        }

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

        // Audit: counselor accepted appointment
        AuditLogHelper::log(
            'appointment_accepted_by_counselor',
            "Counselor {$counselor->id} accepted appointment ID {$appointment->id}"
        );

        return back()->with('success', 'Appointment accepted successfully. Student has been notified via email and calendar updated.');
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

        // 🔄 Sync to Google Calendar (will delete the event)
        try {
            $this->syncService->sync($appointment);
            Log::info("Calendar synced after counselor rejected appointment {$appointment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to sync calendar after reject: " . $e->getMessage());
        }

        // Send rejection notification to student
        $appointment->student->user->notify(new \App\Notifications\AppointmentRejected($appointment));

        // Audit: counselor rejected appointment with reason
        AuditLogHelper::log(
            'appointment_rejected_by_counselor',
            "Counselor {$counselor->id} rejected appointment ID {$appointment->id}. Reason: " . substr($validated['rejection_reason'], 0, 250)
        );

        return back()->with('success', 'Appointment rejected. Student has been notified via email and event removed from calendars.');
    }

    /**
     * Mark appointment as completed
     */
    public function complete(Appointment $appointment)
    {
        $counselor = auth()->user()->counselor;

        // Ensure the logged-in counselor owns the appointment
        if ($appointment->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure only accepted appointments can be marked as completed
        if ($appointment->status !== 'accepted') {
            return back()->with('error', 'Only accepted appointments can be marked as completed.');
        }

        // Update status
        $appointment->update(['status' => 'completed']);

        // 🔄 Sync to Google Calendar (will delete/archive the event)
        try {
            $this->syncService->sync($appointment);
            Log::info("Calendar synced after counselor completed appointment {$appointment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to sync calendar after complete: " . $e->getMessage());
        }

        // Audit: counselor marked appointment completed
        AuditLogHelper::log(
            'appointment_completed_by_counselor',
            "Counselor {$counselor->id} marked appointment ID {$appointment->id} as completed"
        );

        // Redirect back with success message
        return redirect()
            ->route('counselor.appointments.index')
            ->with('success', 'Appointment marked as completed and removed from calendars.');
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
        
        // Audit: counselor viewed appointment details
        AuditLogHelper::log(
            'appointment_viewed_by_counselor',
            "Counselor {$counselor->id} viewed appointment ID {$appointment->id}"
        );

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
                        'google_event_id' => $appointment->google_event_id, // ✅ Include this
                    ],
                ];
            });

        // Audit: counselor opened calendar view
        AuditLogHelper::log(
            'counselor_calendar_viewed',
            "Counselor {$counselor->id} viewed calendar"
        );

        return view('counselors.calendar.index', [
            'appointments' => $appointments,
        ]);
    }
}