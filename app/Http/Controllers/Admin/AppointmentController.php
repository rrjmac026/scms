<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $appointments = Appointment::with(['student.user', 'counselor.user'])
                                   ->latest()
                                   ->paginate(10);

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['student.user', 'counselor.user', 'session', 'feedback']);
        return view('admin.appointments.show', compact('appointment'));
    }
}
