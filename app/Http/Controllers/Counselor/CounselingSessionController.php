<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Helpers\AuditLogHelper;

class CounselingSessionController extends Controller
{
    public function index(Request $request)
    {
        $counselor = auth()->user()->counselor;

        $query = $counselor->counselingSessions()
                    ->with('student.user');

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(10);

        // Audit: counselor viewed sessions list
        AuditLogHelper::log(
            'counselor_sessions_list_viewed',
            "Counselor {$counselor->id} viewed counseling sessions list. count={$sessions->total()}"
        );

        return view('counselors.counseling-sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'student_id'     => 'required|exists:students,id',
            'counselor_id'   => 'required|exists:counselors,id',
            'concern'        => 'required|string|max:500',
            'notes'          => 'nullable|string',
        ]);

        $data['status'] = 'pending';
        $data['started_at'] = null;
        $data['ended_at'] = null;
        $data['duration'] = null;

        $session = CounselingSession::create($data);

        // Audit: counseling session created
        AuditLogHelper::log(
            'counseling_session_created',
            "Created counseling session ID {$session->id} by counselor {$session->counselor_id} for student {$session->student_id}"
        );

        return redirect()->route('counselor.counseling-sessions.index')
                        ->with('success', 'Counseling session created successfully.');
    }


    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student.user', 'counselor.user', 'category']);

        // Audit: viewed counseling session
        AuditLogHelper::log(
            'counseling_session_viewed',
            "Counseling session ID {$counselingSession->id} viewed by user " . (auth()->id() ?? 'system')
        );

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

        // Audit: counseling session updated
        AuditLogHelper::log(
            'counseling_session_updated',
            "Updated counseling session ID {$counselingSession->id}: status={$counselingSession->status}, duration=" . ($counselingSession->duration ?? 'N/A')
        );

        return redirect()
            ->route('counselor.counseling-sessions.show', $counselingSession)
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(CounselingSession $counselingSession)
    {
        $id = $counselingSession->id;
        $counselingSession->delete();

        // Audit: counseling session deleted
        AuditLogHelper::log(
            'counseling_session_deleted',
            "Deleted counseling session ID {$id} by user " . (auth()->id() ?? 'system')
        );

        return redirect()->route('counselors.counseling-sessions.index')
                         ->with('success', 'Session deleted successfully.');
    }
}