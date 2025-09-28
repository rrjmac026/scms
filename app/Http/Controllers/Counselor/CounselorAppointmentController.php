<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CounselorAppointmentController extends Controller
{
    
    public function index()
    {
        $counselor = auth()->user()->counselor;

        $appointments = Appointment::with(['student.user', 'category'])
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
        $appointment->load(['student.user', 'category']); // 👈 add category
        return view('counselors.appointments.show', compact('appointment'));
    }

    public function counselorCalendar()
    {
        $counselor = auth()->user()->counselor;

        $appointments = Appointment::with(['student.user', 'category'])
            ->where('counselor_id', $counselor->id)
            ->whereIn('status', ['pending', 'approved', 'completed'])
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
                        'completed' => '#10b981',
                        default     => '#6b7280',
                    },
                    'extendedProps' => [
                        'student'     => $studentName,
                        'category'    => $appointment->category->name ?? 'General',
                        'status'      => $appointment->status,
                        'description' => Str::limit($appointment->concern ?? '', 50),
                        'google_event_id' => $appointment->google_event_id, // ADD THIS LINE
                    ],
                ];
            });

        return view('counselors.calendar.index', [ // FIX: Changed from 'counselors.calendar.index'
            'appointments' => $appointments,
        ]);
    }

}

