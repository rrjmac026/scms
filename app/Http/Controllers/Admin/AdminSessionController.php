<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\Counselor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    
    public function index()
    {
        $sessions = CounselingSession::with(['student.user', 'counselor.user'])
                        ->latest()
                        ->paginate(10);

        return view('admin.counseling-sessions.index', compact('sessions'));
    }

    // Show form to create a session
    // public function create()
    // {
    //     // Students with approved appointments
    //     $students = Student::whereHas('appointments', function($q) {
    //         $q->where('status', 'approved');
    //     })->with('user')->get();

    //     $dayName = strtolower(now()->format('l'));

    //     // Counselors: show if available today OR have at least one approved appointment
    //     $counselors = Counselor::where(function($q) use ($dayName) {
    //         $q->whereJsonLength("availability_schedule->$dayName", '>', 0) // available today
    //         ->orWhereHas('appointments', function($sub) {
    //             $sub->where('status', 'approved');
    //         });
    //     })->with('user')->get();

    //     return view('admin.counseling-sessions.create', compact('students', 'counselors'));
    // }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'student_id'   => 'required|exists:students,id',
    //         'counselor_id' => 'required|exists:counselors,id',
    //         'concern'      => 'required|string|max:500',
    //         'notes'        => 'nullable|string',
    //     ]);

        
    //     $appointment = Appointment::where('student_id', $request->student_id)
    //         ->where('counselor_id', $request->counselor_id)
    //         ->where('status', 'approved')
    //         ->first();

    //     if (! $appointment) {
    //         return back()->withErrors([
    //             'student_id' => 'This student does not have an approved appointment with this counselor.'
    //         ])->withInput();
    //     }

        
    //     $counselor = Counselor::findOrFail($request->counselor_id);
    //     $dayName = strtolower(now()->format('l')); 

    //     if (! $appointment && ! isset($counselor->availability_schedule[$dayName])) {
    //         return back()->withErrors([
    //             'counselor_id' => 'This counselor is not available today.'
    //         ])->withInput();
    //     }

    //     // ✅ Assign appointment to session
    //     $data['appointment_id'] = $appointment->id;
    //     $data['status'] = 'pending';
    //     $data['started_at'] = null;
    //     $data['ended_at'] = null;
    //     $data['duration'] = null;

    //     CounselingSession::create($data);

    //     return redirect()->route('admin.counseling-sessions.index')
    //         ->with('success', 'Counseling session created successfully.');
    // }

    // Show a specific session
    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student.user', 'counselor.user', 'category']);
        return view('admin.counseling-sessions.show', compact('counselingSession'));
    }

    // Edit session
    // public function edit(CounselingSession $counselingSession)
    // {
    //     $students = Student::with('user')->get();
    //     $counselors = Counselor::with('user')->get();

    //     return view('admin.counseling-sessions.edit', compact('counselingSession', 'students', 'counselors'));
    // }

    // Update session
    public function update(Request $request, CounselingSession $counselingSession)
    {
        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'concern'      => 'nullable|string|max:500',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:pending,ongoing,completed',
        ]);

        // Re-check appointment if counselor or student changed
        if ($counselingSession->student_id != $data['student_id'] ||
            $counselingSession->counselor_id != $data['counselor_id']) {

            $appointment = Appointment::where('student_id', $data['student_id'])
                ->where('counselor_id', $data['counselor_id'])
                ->where('status', 'approved')
                ->first();

            if (! $appointment) {
                return back()->withErrors([
                    'student_id' => 'The student does not have an approved appointment with this counselor.'
                ])->withInput();
            }

            $data['appointment_id'] = $appointment->id;
        }

        // Handle status transitions
        if ($data['status'] === 'ongoing') {
            // Starting a session - set started_at if not already set
            if (!$counselingSession->started_at) {
                $data['started_at'] = now();
            }
            // Clear any previous end data
            $data['ended_at'] = null;
            $data['duration'] = null;
        }
        
        elseif ($data['status'] === 'completed') {
            // Completing a session
            $data['ended_at'] = now();
            
            // Calculate duration from started_at if it exists
            if ($counselingSession->started_at) {
                $data['duration'] = $counselingSession->started_at->diffInSeconds(now()); // store seconds
            } else {
                $data['duration'] = 0;
            }
        }
        
        elseif ($data['status'] === 'pending') {
            // Resetting to pending - clear all timing data
            $data['started_at'] = null;
            $data['ended_at'] = null;
            $data['duration'] = null;
        }

        $counselingSession->update($data);

        return redirect()->route('admin.counseling-sessions.index')
            ->with('success', 'Session updated successfully.');
    }

    // Delete session
    public function destroy(CounselingSession $counselingSession)
    {
        $counselingSession->delete();

        return redirect()->route('admin.counseling-sessions.index')
            ->with('success', 'Session deleted successfully.');
    }
}