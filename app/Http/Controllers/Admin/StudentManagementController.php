<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Counselor;
use App\Models\CounselingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            'middle_name'     => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8|confirmed',

            // student fields
            'student_number'  => 'required|string|max:50|unique:students,student_number',
            'lrn'             => 'required|string|max:50',
            'strand'          => 'required|string|max:255',
            'grade_level'     => 'required|string|max:50',
            'special_needs'   => 'required|string|max:500',

            // Personal Info
            'birthdate'       => 'required|date',
            'gender'          => 'required|string|max:50',
            'address'         => 'required|string|max:500',
            'contact_number'  => 'required|string|max:50',
            'civil_status'    => 'required|string|max:50',
            'nationality'     => 'required|string|max:100',
            'religion'        => 'required|string|max:100',

            // Parent/Guardian Info
            'father_name'     => 'required|string|max:255',
            'father_contact'  => 'required|string|max:50',
            'father_occupation' => 'required|string|max:255',
            'mother_name'     => 'required|string|max:255',
            'mother_contact'  => 'required|string|max:50',
            'mother_occupation' => 'required|string|max:255',
            'guardian_name'   => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:50',
            'guardian_relationship' => 'required|string|max:100',
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
            'lrn'            => 'required|string|max:50',
            'strand'         => 'required|string|max:255',
            'grade_level'    => 'required|string|max:50',
            'special_needs'  => 'required|string|max:500',

            // Personal Info
            'birthdate'      => 'required|date',
            'gender'         => 'required|string|max:50',
            'address'        => 'required|string|max:500',
            'contact_number' => 'required|string|max:50',
            'civil_status'   => 'required|string|max:50',
            'nationality'    => 'required|string|max:100',
            'religion'       => 'required|string|max:100',

            // Parent/Guardian Info
            'father_name'    => 'required|string|max:255',
            'father_contact' => 'required|string|max:50',
            'father_occupation' => 'required|string|max:255',
            'mother_name'    => 'required|string|max:255',
            'mother_contact' => 'required|string|max:50',
            'mother_occupation' => 'required|string|max:255',
            'guardian_name'  => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:50',
            'guardian_relationship' => 'required|string|max:100',
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

    /**
     * Import students from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $csv = array_map('str_getcsv', file($path));
        
        // Get headers from first row
        $headers = array_map('trim', $csv[0]);
        unset($csv[0]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($csv as $key => $row) {
                $rowNumber = $key + 2; // +2 because array starts at 0 and we removed header
                
                // Map CSV columns to array
                $data = array_combine($headers, $row);

                // Validate required fields including LRN uniqueness
                $validator = Validator::make($data, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'student_number' => 'required|string|max:50|unique:students,student_number',
                    'lrn' => 'required|string|max:20|unique:students,lrn', // Added LRN validation
                ]);

                if ($validator->fails()) {
                    $errorCount++;
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue; // Skip this row and continue with next
                }

                // Create user
                $user = User::create([
                    'first_name' => trim($data['first_name']),
                    'middle_name' => trim($data['middle_name'] ?? ''),
                    'last_name' => trim($data['last_name']),
                    'email' => trim($data['email']),
                    'password' => Hash::make($data['password'] ?? 'password123'),
                    'role' => 'student',
                ]);

                // Create student
                Student::create([
                    'user_id' => $user->id,
                    'student_number' => trim($data['student_number']),
                    'lrn' => !empty(trim($data['lrn'] ?? '')) ? trim($data['lrn']) : null, // Ensure empty strings become null
                    'strand' => trim($data['strand'] ?? ''),
                    'grade_level' => trim($data['grade_level'] ?? ''),
                    'special_needs' => trim($data['special_needs'] ?? ''),
                    'birthdate' => !empty($data['birthdate']) ? date('Y-m-d', strtotime($data['birthdate'])) : null,
                    'gender' => trim($data['gender'] ?? ''),
                    'address' => trim($data['address'] ?? ''),
                    'contact_number' => trim($data['contact_number'] ?? ''),
                    'civil_status' => trim($data['civil_status'] ?? ''),
                    'nationality' => trim($data['nationality'] ?? ''),
                    'religion' => trim($data['religion'] ?? ''),
                    'father_name' => trim($data['father_name'] ?? ''),
                    'father_contact' => trim($data['father_contact'] ?? ''),
                    'father_occupation' => trim($data['father_occupation'] ?? ''),
                    'mother_name' => trim($data['mother_name'] ?? ''),
                    'mother_contact' => trim($data['mother_contact'] ?? ''),
                    'mother_occupation' => trim($data['mother_occupation'] ?? ''),
                    'guardian_name' => trim($data['guardian_name'] ?? ''),
                    'guardian_contact' => trim($data['guardian_contact'] ?? ''),
                    'guardian_relationship' => trim($data['guardian_relationship'] ?? ''),
                ]);

                $successCount++;
            }

            DB::commit();

            $message = "Import completed: {$successCount} students imported successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} rows failed.";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template for student import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'password',
            'student_number',
            'lrn',
            'strand',
            'grade_level',
            'special_needs',
            'birthdate',
            'gender',
            'address',
            'contact_number',
            'civil_status',
            'nationality',
            'religion',
            'father_name',
            'father_contact',
            'father_occupation',
            'mother_name',
            'mother_contact',
            'mother_occupation',
            'guardian_name',
            'guardian_contact',
            'guardian_relationship',
        ];

        $filename = 'students_import_template.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, $headers);
        
        
        $sample = [
            'Juan',
            'Santos',
            'Dela Cruz',
            'juan.delacruz@lccdo.edu.ph',
            'password123',
            '2024-0001',
            '123456789012',
            'STEM',
            'Grade 11',
            '',
            '2007-01-15',
            'Male',
            '123 Main St, City',
            '09171234567',
            'Single',
            'Filipino',
            'Roman Catholic',
            'Pedro Dela Cruz',
            '09181234567',
            'Engineer',
            'Maria Dela Cruz',
            '09191234567',
            'Teacher',
            '',
            '',
            '',
        ];
        
        fputcsv($handle, $sample);
        fclose($handle);
        exit;
    }
}