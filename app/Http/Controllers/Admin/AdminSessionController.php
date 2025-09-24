<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\Counselor;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    // Show all sessions
    public function index()
    {
        $sessions = CounselingSession::with(['student.user', 'counselor.user'])
                        ->latest()
                        ->paginate(10);

        return view('admin.counseling-sessions.index', compact('sessions'));
    }

    // Show form to create a session
    public function create()
    {
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->get(); // select which counselor

        return view('admin.counseling-sessions.create', compact('students', 'counselors'));
    }

    // Store a new session
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

        return redirect()->route('admin.counseling-sessions.index')
                         ->with('success', 'Counseling session created and assigned successfully.');
    }

    // Show a specific session
    public function show(CounselingSession $counselingSession)
    {
        $counselingSession->load(['student.user', 'counselor.user']);
        return view('admin.counseling-sessions.show', compact('counselingSession'));
    }

    // Edit session
    public function edit(CounselingSession $counselingSession)
    {
        $students = Student::with('user')->get();
        $counselors = Counselor::with('user')->get();
        return view('admin.counseling-sessions.edit', compact('counselingSession', 'students', 'counselors'));
    }

    // Update session
    public function update(Request $request, CounselingSession $counselingSession)
    {
        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'concern'      => 'nullable|string|max:500',
            'notes'        => 'nullable|string',
            'status'       => 'nullable|in:pending,ongoing,completed',
        ]);

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
