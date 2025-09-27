<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Counselor;
use App\Models\User;
use App\Models\CounselingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CounselorManagementController extends Controller
{
    /**
     * Display a listing of counselors.
     */
    public function index()
    {
        $counselors = Counselor::with('user')->latest()->paginate(10);
        return view('admin.counselors.index', compact('counselors'));
    }

    /**
     * Show the form for creating a new counselor.
     */
    public function create()
    {
        // No need to select existing users anymore
        return view('admin.counselors.create');
    }

    /**
     * Store a newly created counselor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            
            'employee_number' => 'required|string|max:50|unique:counselors',
            'specialization' => 'nullable|string|max:255',
            'availability_schedule' => 'nullable|array',
        ]);

        
        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'counselor',
        ]);

        
        Counselor::create([
            'user_id' => $user->id,
            'employee_number' => $validated['employee_number'],
            'specialization' => $validated['specialization'] ?? null,
            'availability_schedule' => $validated['availability_schedule'] ?? [],        ]);

        return redirect()->route('admin.counselors.index')
                         ->with('success', 'Counselor created successfully.');
    }

    /**
     * Show the form for editing the specified counselor.
     */
    public function edit(Counselor $counselor)
    {
        return view('admin.counselors.edit', compact('counselor'));
    }

    /**
     * Update the specified counselor in storage.
     */
    public function update(Request $request, Counselor $counselor)
    {
        $validated = $request->validate([
            
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $counselor->user_id,
            'password' => 'nullable|string|min:8|confirmed',

            
            'employee_number' => 'required|string|max:50|unique:counselors,employee_number,' . $counselor->id,
            'specialization' => 'nullable|string|max:255',
            'availability_schedule' => 'nullable|array',
        ]);

        
        $userData = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $counselor->user->update($userData);

        
        $counselor->update([
            'employee_number' => $validated['employee_number'],
            'specialization' => $validated['specialization'] ?? null,
            'availability_schedule' => $validated['availability_schedule'] ?? [],        ]);

        return redirect()->route('admin.counselors.index')
                         ->with('success', 'Counselor updated successfully.');
    }

    /**
     * Remove the specified counselor from storage.
     */
    public function destroy(Counselor $counselor)
    {
        
        $counselor->user->delete();

        return redirect()->route('admin.counselors.index')
                         ->with('success', 'Counselor deleted successfully.');
    }

    /**
     * Display the specified counselor.
     */
    public function show(Counselor $counselor)
    {
        $counselor->load(['user', 'appointments', 'counselingSessions', 'feedbacks']);
        return view('admin.counselors.show', compact('counselor'));
    }
}
