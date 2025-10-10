<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student;

        // Safely handle users without a student record
        $appointmentsCount = $student ? $student->appointments()->count() : 0;
        $pendingCount = $student ? $student->appointments()->where('status', 'pending')->count() : 0;
        $feedbackCount = $student ? $student->feedbacks()->count() : 0;
        $sessionsCount = $student ? $student->counselingSessions()->count() : 0;

        return view('students.dashboard', compact(
            'student',
            'appointmentsCount',
            'pendingCount',
            'feedbackCount',
            'sessionsCount'
        ));
    }
}
