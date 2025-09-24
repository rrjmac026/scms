<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentAppointmentController extends Controller
{
    /**
     * Display a listing of the student's appointments.
     */
    public function index()
    {
        $student = auth()->user()->student;

        $appointments = Appointment::with(['counselor.user'])
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

        return view('students.appointments.create');
    }

    /**
     * Store a newly created appointment request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'concern'        => 'required|string|max:500',
        ]);

        $student = auth()->user()->student;

        Appointment::create([
            'student_id'     => $student->id,
            'counselor_id'   => null, // assigned later by admin
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'concern'        => $validated['concern'],
            'status'         => 'pending',
        ]);

        return redirect()->route('student.appointments.index')
                         ->with('success', 'Appointment request submitted.');
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

        $appointment->load(['counselor.user', 'student.user', 'counselingSession', 'feedback']);

        return view('students.appointments.show', compact('appointment'));
    }

    /**
     * Cancel an appointment (only if 1 day before and still pending).
     */
    public function destroy(Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($appointment->status !== 'pending') {
            return redirect()->route('student.appointments.index')
                            ->with('error', 'Only pending appointments can be cancelled.');
        }

        // ✅ Ensure both are strings in the right format
        $date = $appointment->preferred_date instanceof \Carbon\Carbon
            ? $appointment->preferred_date->format('Y-m-d')
            : $appointment->preferred_date;

        $time = substr($appointment->preferred_time, 0, 5); // get HH:MM only

        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");

        if ($appointmentDateTime->isBefore(now()->addDay())) {
            return redirect()->route('student.appointments.index')
                            ->with('error', 'You can only cancel at least 1 day before the appointment.');
        }

        $appointment->delete();

        return redirect()->route('student.appointments.index')
                        ->with('success', 'Appointment request cancelled.');
    }


}
