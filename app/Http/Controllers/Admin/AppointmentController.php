<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Models\Student;
use App\Models\Counselor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $appointments = Appointment::with(['student.user', 'counselor.user'])->latest()->paginate(10);
        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->get();

        return view('admin.appointments.create', compact('students', 'counselors'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required',
            'status' => 'required|string|max:50',
            'concern' => 'nullable|string|max:500',
        ]);

        Appointment::create($validated);

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'session', 'feedback']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->get();

        return view('admin.appointments.edit', compact('appointment', 'students', 'counselors'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required',
            'status' => 'required|string|max:50',
            'concern' => 'nullable|string|max:500',
        ]);

        $appointment->update($validated);

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment deleted successfully.');
    }
}
