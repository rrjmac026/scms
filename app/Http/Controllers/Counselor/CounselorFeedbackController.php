<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class CounselorFeedbackController extends Controller
{
    /**
     * Show all feedback received by this counselor
     */
    public function index()
    {
        $counselor = auth()->user()->counselor;

        $feedbacks = Feedback::with('student', 'appointment')
                             ->where('counselor_id', $counselor->id)
                             ->latest()
                             ->get();

        return view('counselor.feedback.index', compact('feedbacks'));
    }

    /**
     * Show details of a specific feedback
     */
    public function show(Feedback $feedback)
    {
        $counselor = auth()->user()->counselor;

        if ($feedback->counselor_id !== $counselor->id) {
            abort(403, 'Unauthorized action.');
        }

        $feedback->load('student', 'appointment');

        return view('counselor.feedback.show', compact('feedback'));
    }
}
