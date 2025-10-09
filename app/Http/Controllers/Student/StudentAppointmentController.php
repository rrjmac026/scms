<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingCategory;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

        return view('students.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment request.
     */
    public function create()
    {
        $categories = CounselingCategory::where('status', 'active')->get();
        
        // Get available counselors
        $counselors = Counselor::with('user')
            ->where('status', 'active')
            ->get();
        
        return view('students.appointments.create', compact('categories', 'counselors'));
    }

    /**
     * Store a newly created appointment request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_date'          => 'required|date|after_or_equal:today',
            'preferred_time'          => 'required|date_format:H:i',
            'concern'                 => 'required|string|max:500',
            'counseling_category_id'  => 'required|exists:counseling_categories,id',
        ]);

        $student = auth()->user()->student;

        Appointment::create([
            'student_id'              => $student->id,
            'counselor_id'            => null,
            'preferred_date'          => $validated['preferred_date'],
            'preferred_time'          => $validated['preferred_time'], 
            'concern'                 => $validated['concern'],
            'counseling_category_id'  => $validated['counseling_category_id'],
            'status'                  => 'pending',
        ]);

        return redirect()->route('student.appointments.index')
                        ->with('success', 'Appointment request submitted and pending admin approval.');
    }


    /**
     * Show a specific appointment details.
     */
    public function show(Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->load(['counselor.user', 'student.user', 'counselingSession.feedback', 'category']);

        return view('students.appointments.show', compact('appointment'));
    }

    /**
     * Cancel an appointment (only if 1 day before and still pending/approved).
     */
    public function destroy(Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($appointment->status, ['pending', 'approved'])) {
            return redirect()->route('student.appointments.index')
                            ->with('error', 'Only pending or approved appointments can be cancelled.');
        }

        $date = $appointment->preferred_date instanceof \Carbon\Carbon
            ? $appointment->preferred_date->format('Y-m-d')
            : $appointment->preferred_date;

        $time = substr($appointment->preferred_time, 0, 5);
        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");

        if ($appointmentDateTime->isBefore(now()->addDay())) {
            return redirect()->route('student.appointments.index')
                            ->with('error', 'You can only cancel at least 1 day before the appointment.');
        }

        $appointment->update(['status' => 'cancelled_by_student']);

        // Notify counselor if assigned
        if ($appointment->counselor_id) {
            // Add notification logic here
        }

        return redirect()->route('student.appointments.index')
                        ->with('success', 'Appointment cancelled successfully.');
    }

    public function cancel(Appointment $appointment)
    {
        $student = auth()->user()->student;

        // Ensure only the owner (student) can cancel
        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent cancelling completed, rejected, or already cancelled appointments
        if (in_array($appointment->status, ['completed', 'cancelled', 'rejected'])) {
            return back()->with('error', 'This appointment can no longer be cancelled.');
        }

        // Update status to cancelled
        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('student.appointments.index')
                        ->with('success', 'Appointment cancelled successfully.');
    }



    public function studentCalendar()
    {
        $student = auth()->user()->student;

        $appointments = Appointment::with(['counselor.user', 'category'])
            ->where('student_id', $student->id) 
            ->whereIn('status', ['pending', 'approved', 'completed', 'reschedule_requested_by_counselor'])
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
                    'id'    => $appointment->id,
                    'title' => 'Session with ' . $counselorName . ' (' . ucfirst($appointment->status) . ')',
                    'start' => $startDateTime,
                    'color' => match ($appointment->status) {
                        'pending'   => '#fbbf24',
                        'approved'  => '#3b82f6',
                        'completed' => '#10b981',
                        'reschedule_requested_by_counselor' => '#f97316',
                        default     => '#6b7280',
                    },
                    'extendedProps' => [
                        'counselor'   => $counselorName,
                        'category'    => $appointment->category->name ?? 'General',
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50),
                    ],
                ];
            });

        return view('students.calendar.index', [
            'appointments' => $appointments,
        ]);
    }




}