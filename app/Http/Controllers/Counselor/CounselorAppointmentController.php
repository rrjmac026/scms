<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use Illuminate\Http\Request;

class CounselorAppointmentController extends Controller
{
    
    public function index()
    {
        $counselor = auth()->user()->counselor;
        $appointments = Appointment::with('student.user')
                            ->where('counselor_id', $counselor->id)
                            ->orderBy('preferred_date')
                            ->get();

        return view('counselors.appointments.index', compact('appointments'));
    }

    
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $appointment->update([
            'status' => $request->status
        ]);

        
        $studentUser = $appointment->student->user;
        

        return back()->with('success', 'Appointment status updated.');
    }

    
    public function show(Appointment $appointment)
    {
        $appointment->load('student.user');
        return view('counselors.appointments.show', compact('appointment'));
    }
}

