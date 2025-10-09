<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Counselor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Optional: filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    // public function create()
    // {
    //     return view('admin.users.create');
    // }

    /**
     * Store a newly created user in storage.
     */
    // public function store(Request $request)
    // {
        
    //     $rules = [
    //         'first_name'      => 'required|string|max:255',
    //         'middle_name'     => 'nullable|string|max:255',
    //         'last_name'       => 'required|string|max:255',
    //         'email'           => 'required|string|email|max:255|unique:users',
    //         'password'        => 'required|string|min:8|confirmed',
    //         'role'            => ['required', Rule::in(['admin','counselor','student'])],
    //         'contact_number'  => 'nullable|string|max:20',
    //         'address'         => 'nullable|string|max:255',
    //     ];

        
    //     if ($request->role === 'student') {
    //         $rules = array_merge($rules, [
    //             'student_number'       => 'required|string|max:50|unique:students,student_number',
    //             'lrn'                  => 'nullable|string|max:50|unique:students,lrn',
    //             'strand'               => 'nullable|string|max:100',
    //             'grade_level'          => 'nullable|string|max:50',
    //             'special_needs'        => 'nullable|string|max:500',

    //             // Personal info
    //             'birthdate'            => 'nullable|date',
    //             'gender'               => 'nullable|string|max:20',
    //             'civil_status'         => 'nullable|string|max:50',
    //             'nationality'          => 'nullable|string|max:100',
    //             'religion'             => 'nullable|string|max:100',

    //             // Parent/guardian info
    //             'father_name'          => 'nullable|string|max:255',
    //             'father_contact'       => 'nullable|string|max:20',
    //             'father_occupation'    => 'nullable|string|max:255',
    //             'mother_name'          => 'nullable|string|max:255',
    //             'mother_contact'       => 'nullable|string|max:20',
    //             'mother_occupation'    => 'nullable|string|max:255',
    //             'guardian_name'        => 'nullable|string|max:255',
    //             'guardian_contact'     => 'nullable|string|max:20',
    //             'guardian_relationship'=> 'nullable|string|max:100',
    //         ]);
    //     } elseif ($request->role === 'counselor') {
    //         $rules = array_merge($rules, [
    //             'employee_number'       => 'required|string|max:50|unique:counselors,employee_number',
    //             'specialization'        => 'nullable|string|max:255',
    //             'gender'                => 'nullable|string|max:20',
    //             'birth_date'            => 'nullable|date',
    //             'bio'                   => 'nullable|string|max:1000',
    //             'availability_schedule' => 'nullable|array',
    //         ]);
    //     }


    //     $validated = $request->validate($rules);

        
    //     $user = User::create([
    //         'first_name'     => $validated['first_name'],
    //         'middle_name'    => $validated['middle_name'] ?? null,
    //         'last_name'      => $validated['last_name'],
    //         'email'          => $validated['email'],
    //         'password'       => Hash::make($validated['password']),
    //         'role'           => $validated['role'],
    //         'contact_number' => $validated['contact_number'] ?? null,
    //         'address'        => $validated['address'] ?? null,
    //     ]);

        
    //     if ($validated['role'] === 'student') {
    //         Student::create([
    //             'user_id'              => $user->id,
    //             'student_number'       => $validated['student_number'],
    //             'lrn'                  => $validated['lrn'] ?? null,
    //             'strand'               => $validated['strand'] ?? null,
    //             'grade_level'          => $validated['grade_level'] ?? null,
    //             'special_needs'        => $validated['special_needs'] ?? null,

    //             'birthdate'            => $validated['birthdate'] ?? null,
    //             'gender'               => $validated['gender'] ?? null,
    //             'civil_status'         => $validated['civil_status'] ?? null,
    //             'nationality'          => $validated['nationality'] ?? null,
    //             'religion'             => $validated['religion'] ?? null,

    //             'father_name'          => $validated['father_name'] ?? null,
    //             'father_contact'       => $validated['father_contact'] ?? null,
    //             'father_occupation'    => $validated['father_occupation'] ?? null,
    //             'mother_name'          => $validated['mother_name'] ?? null,
    //             'mother_contact'       => $validated['mother_contact'] ?? null,
    //             'mother_occupation'    => $validated['mother_occupation'] ?? null,
    //             'guardian_name'        => $validated['guardian_name'] ?? null,
    //             'guardian_contact'     => $validated['guardian_contact'] ?? null,
    //             'guardian_relationship'=> $validated['guardian_relationship'] ?? null,
    //         ]);

    //     } elseif ($validated['role'] === 'counselor') {
    //         Counselor::create([
    //             'user_id'               => $user->id,
    //             'employee_number'       => $validated['employee_number'],
    //             'specialization'        => $validated['specialization'] ?? null,
    //             'gender'                => $validated['gender'] ?? null,
    //             'birth_date'            => $validated['birth_date'] ?? null,
    //             'bio'                   => $validated['bio'] ?? null,
    //             'availability_schedule' => $validated['availability_schedule'] ?? [],
    //         ]);
    //     }

    //     // ✅ Redirect
    //     return redirect()->route('admin.users.index')
    //                     ->with('success', ucfirst($validated['role']) . ' created successfully.');
    // }


    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Load role-specific data
        $user->load(['student', 'counselor']);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Base validation rules
        $rules = [
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => ['required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password'        => 'nullable|string|min:8|confirmed',
            'role'            => ['required', Rule::in(['admin','counselor','student'])],
            'contact_number'  => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
        ];

        // Add role-specific validation rules
        if ($request->role === 'student') {
            $rules = array_merge($rules, [
                'student_number' => 'required|string|max:50|unique:students,student_number,' . ($user->student->id ?? 0),
                'course'         => 'nullable|string|max:255',
                'year_level'     => 'nullable|string|max:50',
                'special_needs'  => 'nullable|string|max:500',
            ]);
        } elseif ($request->role === 'counselor') {
            $rules = array_merge($rules, [
                'employee_number'       => 'required|string|max:50|unique:counselors,employee_number,' . ($user->counselor->id ?? 0),
                'specialization'        => 'nullable|string|max:255',
                'availability_schedule' => 'nullable|array',
            ]);
        }

        $validated = $request->validate($rules);

        // Update user data
        $userData = [
            'first_name'     => $validated['first_name'],
            'middle_name'    => $validated['middle_name'] ?? null,
            'last_name'      => $validated['last_name'],
            'email'          => $validated['email'],
            'role'           => $validated['role'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address'        => $validated['address'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // update shits
        if ($validated['role'] === 'student') {
            $studentData = [
                'student_number'       => $validated['student_number'],
                'lrn'                  => $validated['lrn'] ?? null,
                'strand'               => $validated['strand'] ?? null,
                'grade_level'          => $validated['grade_level'] ?? null,
                'special_needs'        => $validated['special_needs'] ?? null,

                'birthdate'            => $validated['birthdate'] ?? null,
                'gender'               => $validated['gender'] ?? null,
                'address'              => $validated['address'] ?? null,
                'contact_number'       => $validated['contact_number'] ?? null,
                'civil_status'         => $validated['civil_status'] ?? null,
                'nationality'          => $validated['nationality'] ?? null,
                'religion'             => $validated['religion'] ?? null,
                'father_name'          => $validated['father_name'] ?? null,
                'father_contact'       => $validated['father_contact'] ?? null,
                'father_occupation'    => $validated['father_occupation'] ?? null,
                'mother_name'          => $validated['mother_name'] ?? null,
                'mother_contact'       => $validated['mother_contact'] ?? null,
                'mother_occupation'    => $validated['mother_occupation'] ?? null,
                'guardian_name'        => $validated['guardian_name'] ?? null,
                'guardian_contact'     => $validated['guardian_contact'] ?? null,
                'guardian_relationship'=> $validated['guardian_relationship'] ?? null,
            ];

            if ($user->student) {
                $user->student->update($studentData);
            } else {
                Student::create(array_merge($studentData, ['user_id' => $user->id]));
            }

            // Remove counselor record if exists
            if ($user->counselor) {
                $user->counselor->delete();
            }

        } elseif ($validated['role'] === 'counselor') {
            $counselorData = [
                'employee_number'       => $validated['employee_number'],
                'specialization'        => $validated['specialization'] ?? null,
                'availability_schedule' => $validated['availability_schedule'] ?? [],
            ];

            if ($user->counselor) {
                $user->counselor->update($counselorData);
            } else {
                Counselor::create(array_merge($counselorData, ['user_id' => $user->id]));
            }

            // Remove student record if exists
            if ($user->student) {
                $user->student->delete();
            }

        } else {
            // Admin role - remove both student and counselor records
            if ($user->student) {
                $user->student->delete();
            }
            if ($user->counselor) {
                $user->counselor->delete();
            }
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deactivating yourself (optional)
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['status' => 'inactive']);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User account deactivated successfully.');
    }

    public function reactivate(User $user)
    {
        $user->update(['status' => 'active']);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User account reactivated successfully.');
    }

    /**
     * Show a single user profile.
     */
    public function show(User $user)
    {
        $user->load(['student', 'counselor']);
        return view('admin.users.show', compact('user'));
    }


    public function editPassword(User $user)
    {
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Password updated successfully for ' . $user->first_name . ' ' . $user->last_name);
    }
}
