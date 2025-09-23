<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    /**
     * Show all feedback
     */
    public function index()
    {
        $feedbacks = Feedback::with('student', 'counselor', 'appointment')
                             ->latest()
                             ->paginate(20); // optional pagination

        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    /**
     * Show details of a specific feedback
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('student', 'counselor', 'appointment');

        return view('admin.feedbacks.show', compact('feedback'));
    }
}
