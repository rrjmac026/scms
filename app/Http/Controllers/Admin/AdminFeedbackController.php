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
        $feedbacks = Feedback::with('student.user', 'counselor.user', 'appointment')
            ->latest()
            ->paginate(20);

        $totalFeedbacks = Feedback::count();

        // Average across all stored ratings (optional)
        $averageRating = Feedback::avg('rating') ?? 0;

        // Overall detailed average across all feedbacks (q1–q12)
        $detailedAverage = $this->calculateDetailedAverage();

        return view('admin.feedbacks.index', compact('feedbacks', 'totalFeedbacks', 'averageRating', 'detailedAverage'));
    }

    /**
     * Show details of a specific feedback
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('student.user', 'counselor.user', 'appointment');

        // Calculate detailed average
        $detailedAvg = $feedback->detailed_average;

        // Count answered questions
        $answeredQuestionsCount = 0;
        for ($i = 1; $i <= 12; $i++) {
            $key = "q{$i}";
            if (!empty($feedback->$key) && is_numeric($feedback->$key)) {
                $answeredQuestionsCount++;
            }
        }

        return view('admin.feedbacks.show', compact('feedback', 'detailedAvg', 'answeredQuestionsCount'));
    }

    /**
     * Calculate average across all detailed question ratings (q1-q12)
     */
    private function calculateDetailedAverage()
    {
        $feedbacks = Feedback::all();
        $totalRatings = 0;
        $ratingCount = 0;

        foreach ($feedbacks as $feedback) {
            for ($i = 1; $i <= 12; $i++) {
                $key = "q{$i}";
                if (!empty($feedback->$key) && is_numeric($feedback->$key)) {
                    $totalRatings += $feedback->$key;
                    $ratingCount++;
                }
            }
        }

        return $ratingCount > 0 ? round($totalRatings / $ratingCount, 2) : 0;
    }
}