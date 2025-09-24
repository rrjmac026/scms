<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CounselingHistoryController extends Controller
{
    /**
     * Display the authenticated student's counseling history.
     */
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('home')
                             ->with('error', 'No student record found.');
        }

        $sessions = $student->counselingSessions()
                            ->with(['counselor.user', 'feedback'])
                            ->orderBy('started_at', 'desc')
                            ->paginate(10);

        return view('students.counseling_history.index', compact('sessions'));
    }

    /**
     * Optional: Show details of a single session.
     */
    public function show($id)
    {
        $student = auth()->user()->student;

        $session = $student->counselingSessions()
                           ->with(['counselor.user', 'feedback'])
                           ->findOrFail($id);

        return view('students.counseling_history.show', compact('session'));
    }
}
