<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Appointment;
use Illuminate\Http\Request;

class StudentFeedbackController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $feedbacks = Feedback::with('counselor', 'appointment')
                             ->where('student_id', $student->id)
                             ->latest()
                             ->get();

        return view('students.feedback.index', compact('feedbacks'));
    }
    /**
     * Show feedback form for a specific appointment.
     */
    public function create(Appointment $appointment)
    {
        $student = auth()->user()->student;

        // Make sure the appointment belongs to the logged-in student
        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('students.feedback.create', compact('appointment'));
    }

    /**
     * Store the submitted feedback for the appointment.
     */
    public function store(Request $request, Appointment $appointment)
    {
        $student = auth()->user()->student;

        if ($appointment->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the 10 question ratings (1-5) and optional overall rating/comments
        $validated = $request->validate(array_merge(
            [
                'rating'   => 'nullable|integer|min:1|max:5',   // optional overall rating
                'comments' => 'nullable|string|max:1000',       // optional free-text comments
            ],
            array_combine(
                array_map(fn($i) => "q$i", range(1, 10)),      // q1..q10 for each question
                array_fill(0, 10, 'required|integer|min:1|max:5')
            )
        ));

        $validated['student_id'] = $student->id;
        $validated['counselor_id'] = $appointment->counselor_id;
        $validated['appointment_id'] = $appointment->id;

        Feedback::create($validated);

        return redirect()->route('student.appointments.index')
                         ->with('success', 'Feedback submitted successfully.');
    }

    /**
     * Optionally: show feedback submitted by the student
     */
    
}
