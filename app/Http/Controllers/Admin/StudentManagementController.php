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
    public function index(Request $request)
    {
        $search = $request->get('search');

        $students = Student::with('user')
            ->whereHas('user', function ($query) use ($search) {
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            })
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.last_name', 'asc') // sort alphabetically
            ->select('students.*') // prevent column collision
            ->paginate(10);

        return view('admin.students.index', compact('students', 'search'));
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
            'lrn'             => 'nullable|string|max:50',
            'strand'          => 'nullable|string|max:255',
            'grade_level'     => 'nullable|string|max:50',
            'special_needs'   => 'nullable|string|max:500',

            // Personal Info
            'birthdate'       => 'nullable|date',
            'gender'          => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:500',
            'contact_number'  => 'nullable|string|max:50',
            'civil_status'    => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:100',
            'religion'        => 'nullable|string|max:100',

            // Parent/Guardian Info
            'father_name'     => 'nullable|string|max:255',
            'father_contact'  => 'nullable|string|max:50',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'mother_contact'  => 'nullable|string|max:50',
            'mother_occupation' => 'nullable|string|max:255',
            'guardian_name'   => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:50',
            'guardian_relationship' => 'nullable|string|max:100',
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
            'lrn'            => $validated['lrn'] ?? null,
            'strand'         => $validated['strand'] ?? null,
            'grade_level'    => $validated['grade_level'] ?? null,
            'special_needs'  => $validated['special_needs'] ?? null,

            // Personal Info
            'birthdate'      => $validated['birthdate'] ?? null,
            'gender'         => $validated['gender'] ?? null,
            'address'        => $validated['address'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'civil_status'   => $validated['civil_status'] ?? null,
            'nationality'    => $validated['nationality'] ?? null,
            'religion'       => $validated['religion'] ?? null,

            // Parent/Guardian Info
            'father_name'    => $validated['father_name'] ?? null,
            'father_contact' => $validated['father_contact'] ?? null,
            'father_occupation' => $validated['father_occupation'] ?? null,
            'mother_name'    => $validated['mother_name'] ?? null,
            'mother_contact' => $validated['mother_contact'] ?? null,
            'mother_occupation' => $validated['mother_occupation'] ?? null,
            'guardian_name'  => $validated['guardian_name'] ?? null,
            'guardian_contact' => $validated['guardian_contact'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
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
            'lrn'            => 'nullable|string|max:50',
            'strand'         => 'nullable|string|max:255',
            'grade_level'    => 'nullable|string|max:50',
            'special_needs'  => 'nullable|string|max:500',

            // Personal Info
            'birthdate'      => 'nullable|date',
            'gender'         => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:50',
            'civil_status'   => 'nullable|string|max:50',
            'nationality'    => 'nullable|string|max:100',
            'religion'       => 'nullable|string|max:100',

            // Parent/Guardian Info
            'father_name'    => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:50',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name'    => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:50',
            'mother_occupation' => 'nullable|string|max:255',
            'guardian_name'  => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:50',
            'guardian_relationship' => 'nullable|string|max:100',
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