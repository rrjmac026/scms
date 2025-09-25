<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\CounselingSession;
use Illuminate\Http\Request;

class StudentFeedbackController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $feedbacks = Feedback::with('counselor', 'counselingSession')
                             ->where('student_id', $student->id)
                             ->latest()
                             ->get();

        return view('students.feedback.index', compact('feedbacks'));
    }

    /**
     * Show feedback form for a specific counseling session.
     */
    public function create(CounselingSession $session)
    {
        $student = auth()->user()->student;

        // Make sure the session belongs to the logged-in student
        if ($session->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow feedback for completed sessions
        if ($session->status !== 'completed') {
            abort(403, 'You can only provide feedback for completed sessions.');
        }

        return view('students.feedback.create', compact('session'));
    }

    /**
     * Store the submitted feedback for the session.
     */
    public function store(Request $request, CounselingSession $session)
    {
        $student = auth()->user()->student;

        if ($session->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }


        $validated = $request->validate(array_merge(
            [
                'rating'   => 'nullable|integer|min:1|max:5', 
                'comments' => 'nullable|string|max:1000',
                'likes'    => 'nullable|string|max:1000'  
            ],
            array_combine(
                array_map(fn($i) => "q$i", range(1, 12)),
                array_fill(0, 10, 'required|integer|min:1|max:5')
            )
        ));

        $validated['student_id'] = $student->id;
        $validated['counselor_id'] = $session->counselor_id;
        $validated['counseling_session_id'] = $session->id;

        Feedback::create($validated);

        return redirect()->route('student.counseling-history.index')
                         ->with('success', 'Feedback submitted successfully.');
    }

    public function createFeedbackForSession(CounselingSession $session)
    {
        $student = auth()->user()->student;

        // Make sure this session belongs to the logged-in student
        if ($session->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if feedback already exists
        if ($session->feedback) {
            return redirect()->route('student.counseling-history.index')
                            ->with('info', 'Feedback already submitted for this session.');
        }

        return view('students.feedback.create', compact('session'));
    }

    public function storeFeedbackForSession(Request $request, CounselingSession $session)
    {
        $student = auth()->user()->student;

        if ($session->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate(array_merge(
            ['rating' => 'nullable|integer|min:1|max:5', 'comments' => 'nullable|string|max:1000'],
            array_combine(
                array_map(fn($i) => "q$i", range(1, 10)), // q1..q10
                array_fill(0, 10, 'required|integer|min:1|max:5')
            )
        ));

        $validated['student_id'] = $student->id;
        $validated['counselor_id'] = $session->counselor_id;
        $validated['counseling_session_id'] = $session->id;

        Feedback::create($validated);

        return redirect()->route('student.counseling-history.index')
                        ->with('success', 'Feedback submitted successfully.');
    }
}
