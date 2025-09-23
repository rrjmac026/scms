<?php

namespace App\Http\Controllers;

use App\Models\CounselingSession;
use Illuminate\Http\Request;

class CounselingSessionController extends Controller
{
    // List all sessions
    public function index()
    {
        return CounselingSession::with(['student', 'counselor'])->get();
    }

    // Store a new session (initially pending)
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

        return CounselingSession::create($data);
    }

    // Show a single session
    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student', 'counselor']);
        return $counselingSession;
    }

    // Update session details (notes, concern, status)
    public function update(Request $request, CounselingSession $counselingSession)
    {
        $data = $request->validate([
            'notes'   => 'nullable|string',
            'concern' => 'nullable|string|max:500',
            'status'  => 'nullable|in:pending,ongoing,completed',
        ]);

        $counselingSession->update($data);

        return $counselingSession;
    }

    // Start a session (sets started_at and status to 'ongoing')
    public function start(CounselingSession $counselingSession)
    {
        $counselingSession->update([
            'started_at' => now(),
            'status'     => 'ongoing',
        ]);

        return $counselingSession;
    }

    // End a session (sets ended_at, calculates duration, updates status to 'completed')
    public function end(CounselingSession $counselingSession)
    {
        $counselingSession->update([
            'ended_at' => now(),
            'status'   => 'completed',
        ]);

        // Optional: calculate duration in minutes
        $duration = $counselingSession->ended_at->diffInMinutes($counselingSession->started_at);
        $counselingSession->duration = $duration;
        $counselingSession->save();

        return $counselingSession;
    }

    // Delete a session
    public function destroy(CounselingSession $counselingSession)
    {
        $counselingSession->delete();
        return response()->noContent();
    }
}
