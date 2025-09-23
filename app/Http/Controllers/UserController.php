<?php

namespace App\Http\Controllers;


use App\Models\User;
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
        $users = User::paginate(10);
        $query = User::query();

        // Optional: filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8|confirmed',
            'role'            => ['required', Rule::in(['admin','counselor','student'])],
            'contact_number'  => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => ['required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password'        => 'nullable|string|min:8|confirmed',
            'role'            => ['required', Rule::in(['admin','counselor','student'])],
            'contact_number'  => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show a single user profile.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function import(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
            $header = fgetcsv($handle); // skip header row
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $role = $data[5] ?? 'student';

                $userData = [
                    'first_name'     => $data[0] ?? null,
                    'middle_name'    => $data[1] ?? null,
                    'last_name'      => $data[2] ?? null,
                    'email'          => $data[3] ?? null,
                    'password'       => Hash::make($data[4] ?? 'password123'),
                    'role'           => $role,
                    'grade_level'    => $role === 'student' ? ($data[6] ?? null) : null,
                    'strand'         => $role === 'student' ? ($data[7] ?? null) : null,
                    'school_section' => $role === 'student' ? ($data[8] ?? null) : null,
                    'counselor_id'   => $role === 'student' ? ($data[9] ?? null) : null,
                    'phone'          => $data[10] ?? null,
                    'availability'   => $data[11] ?? 'available',
                    'status'         => $data[12] ?? 'active',
                    'profile_picture'=> null,
                    'last_login_at'  => null,
                ];

                // skip if no valid email
                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) continue;

                // skip if already exists
                if (!User::where('email', $userData['email'])->exists()) {
                    User::create($userData);
                }
            }
            fclose($handle);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'Users imported successfully!');
    }
    
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'First Name',
                'Middle Name',
                'Last Name',
                'Email',
                'Password',
                'Role',
                'Grade Level',
                'Strand',
                'School Section',
                'Counselor ID',
                'Phone',
                'Availability',
                'Status'
            ]);
            
            // Add example row
            fputcsv($file, [
                'John',
                'Doe',
                'Smith',
                'john@example.com',
                'password123',
                'student',
                'Grade 11',
                'STEM',
                'Senior High',
                '1',
                '1234567890',
                'available',
                'active'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
