<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\User;
use App\Models\Counselor;
use App\Models\CounselingSession;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::with('user')->latest()->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        // get users who are students but not yet linked in Student table
        $users = User::where('role', 'student')
                    ->whereDoesntHave('student')
                    ->get();

        return view('admin.students.create', compact('users'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // user fields
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8|confirmed',

            // student fields
            'student_number'  => 'required|string|max:50|unique:students,student_number',
            'course'          => 'nullable|string|max:255',
            'year_level'      => 'nullable|string|max:50',
            'special_needs'   => 'nullable|string|max:500',
        ]);

        // create user
        $user = \App\Models\User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => \Hash::make($validated['password']),
            'role' => 'student',
        ]);

        // create student linked to user
        Student::create([
            'user_id'        => $user->id,
            'student_number' => $validated['student_number'],
            'course'         => $validated['course'] ?? null,
            'year_level'     => $validated['year_level'] ?? null,
            'special_needs'  => $validated['special_needs'] ?? null,
        ]);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Student created successfully.');
    }


    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'appointments', 'counselingSessions', 'behaviorIncidents', 'feedbacks']);
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $users = User::where('role', 'student')->get();
        return view('admin.students.edit', compact('student', 'users'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'student_number' => 'required|string|max:50|unique:students,student_number,' . $student->id,
            'course'         => 'nullable|string|max:255',
            'year_level'     => 'nullable|string|max:50',
            'special_needs'  => 'nullable|string|max:500',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student deleted successfully.');
    }
}
