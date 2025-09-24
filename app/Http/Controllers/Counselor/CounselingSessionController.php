<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Student;
use Illuminate\Http\Request;

class CounselingSessionController extends Controller
{
    // Show all counseling sessions in a view
    public function index()
    {
        // Eager load student and counselor relationships
        $counselor = auth()->user()->counselor;

        $sessions = $counselor->counselingSessions()
                            ->with('student.user')
                            ->latest()
                            ->paginate(10);

        return view('counselors.counseling-sessions.index', compact('sessions'));
    }

    // Store a new counseling session
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

        CounselingSession::create($data);

        return redirect()->route('counselors.counseling-sessions.index')
                         ->with('success', 'Counseling session created successfully.');
    }

    // Show a specific counseling session
    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student.user', 'counselor.user']);

        return view('counselors.counseling-sessions.show', compact('counselingSession'));
    }

    // Optionally: edit session
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

        //kung ang status kay on going ug wala pa nag sugod imong e set
        if ($data['status'] === 'ongoing' && !$counselingSession->started_at) {
            $data['started_at'] = now();
        }

        //kung ang session kay humana ug wala pa nag end
        if ($data['status'] === 'completed') {
            $endTime = now();
            $data['ended_at'] = $endTime;

            $startTime = $counselingSession->started_at ?? now();
            $data['duration'] = $endTime->diffInMinutes($startTime);
        }

        $counselingSession->update($data);

        return redirect()
            ->route('counselor.counseling-sessions.index')
            ->with('success', 'Session updated successfully.');
    }



    public function destroy(CounselingSession $counselingSession)
    {
        $counselingSession->delete();

        return redirect()->route('counselors.counseling-sessions.index')
                         ->with('success', 'Session deleted successfully.');
    }
}
