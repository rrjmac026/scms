<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Student;
use Illuminate\Http\Request;

class CounselingSessionController extends Controller
{
    public function index()
    {
        $counselor = auth()->user()->counselor;

        $sessions = $counselor->counselingSessions()
                            ->with('student.user')
                            ->latest()
                            ->paginate(10);

        return view('counselors.counseling-sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'concern'      => 'required|string|max:500',
            'notes'        => 'nullable|string',
        ]);

        $data['status'] = 'pending';
        $data['started_at'] = null;
        $data['ended_at'] = null;
        $data['duration'] = null;

        CounselingSession::create($data);

        return redirect()->route('counselors.counseling-sessions.index')
                         ->with('success', 'Counseling session created successfully.');
    }

    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student.user', 'counselor.user']);

        return view('counselors.counseling-sessions.show', compact('counselingSession'));
    }

    public function edit(CounselingSession $counselingSession)
    {
        return view('counselors.counseling-sessions.edit', compact('counselingSession'));
    }

    public function update(Request $request, CounselingSession $counselingSession)
    {
        $data = $request->validate([
            'notes'   => 'nullable|string',
            'concern' => 'nullable|string|max:500',
            'status'  => 'required|in:pending,ongoing,completed',
        ]);

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
                $data['duration'] = $counselingSession->started_at->diffInMinutes(now());
            } else {
                // Fallback if somehow started_at is missing
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

        return redirect()
            ->route('counselor.counseling-sessions.show', $counselingSession)
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(CounselingSession $counselingSession)
    {
        $counselingSession->delete();

        return redirect()->route('counselors.counseling-sessions.index')
                         ->with('success', 'Session deleted successfully.');
    }
}