<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use Illuminate\Http\Request;

class StudentAppointmentController extends Controller
{
    /**
     * Display a listing of the student's appointments.
     */
    public function index()
    {
        $student = auth()->user()->student;

        // Load appointments with counselor info
        $appointments = Appointment::with('counselor.user')
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
        $counselors = Counselor::with('user')->get();
        return view('students.appointments.create', compact('counselors'));
    }

    /**
     * Store a newly created appointment request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'counselor_id' => 'required|exists:counselors,id',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required',
            'concern' => 'required|string|max:500',
        ]);

        
        $student = auth()->user()->student;

        Appointment::create([
            'student_id' => $student->id,
            'counselor_id' => $validated['counselor_id'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'concern' => $validated['concern'],
            'status' => 'pending',
        ]);

        return redirect()->route('students.appointments.index')
                         ->with('success', 'Appointment request submitted.');
    }

    /**
     * Show a specific appointment details.
     */
    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $appointment->load(['counselor.user', 'student.user', 'session', 'feedback']);
        return view('student.appointments.show', compact('appointment'));
    }

    /**
     * Optionally: cancel an appointment
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        if ($appointment->status === 'pending') {
            $appointment->delete();
            return redirect()->route('students.appointments.index')
                             ->with('success', 'Appointment request cancelled.');
        }

        return redirect()->route('students.appointments.index')
                         ->with('error', 'Only pending appointments can be cancelled.');
    }
}
