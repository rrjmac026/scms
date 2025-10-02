<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offense;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOffenseController extends Controller 
{
    /**
     * Display a listing of offenses.
     */
    public function index(Request $request)
    {
        $query = Offense::with(['student.user', 'counselor.user', 'counselingSession']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('offense', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('resolved', $request->status === 'resolved');
        }

        $offenses = $query->latest()->paginate(15)->appends($request->all());

        return view('admin.offenses.index', compact('offenses'));
    }

    /**
     * Show a single offense.
     */
    public function show(Offense $offense)
    {
        $offense->load([
            'student.user', 
            'counselor.user', 
            'counselingSession',
        ]);

        return view('admin.offenses.show', compact('offense'));
    }

    /**
     * Show the form to create a new offense.
     */
    public function create()
    {
        $students = Student::with('user')->get();
        return view('admin.offenses.create', compact('students'));
    }

    /**
     * Store a new offense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counseling_session_id' => 'nullable|exists:counseling_sessions,id',
            'offense' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'date' => 'required|date',
            'status' => 'nullable|string|max:50',
            'solution' => 'nullable|string|max:500',
            'resolved' => 'nullable|boolean',
        ]);

        $validated['resolved'] = $validated['resolved'] ?? false;
        $validated['created_by'] = Auth::id();

        Offense::create($validated);

        return redirect()->route('admin.offenses.index')
                         ->with('success', 'Offense recorded successfully.');
    }

    /**
     * Show the form to edit an existing offense.
     */
    public function edit(Offense $offense)
    {
        $students = Student::with('user')->get();
        return view('admin.offenses.edit', compact('offense', 'students'));
    }

    /**
     * Update an existing offense.
     */
    public function update(Request $request, Offense $offense)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counseling_session_id' => 'nullable|exists:counseling_sessions,id',
            'offense' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
            'date' => 'required|date',
            'status' => 'nullable|string|max:50',
            'solution' => 'nullable|string|max:500',
            'resolved' => 'nullable|boolean',
        ]);

        $offense->update($validated);

        return redirect()->route('admin.offenses.index')
                         ->with('success', 'Offense updated successfully.');
    }

    /**
     * Delete an offense.
     */
    public function destroy(Offense $offense)
    {
        $offense->delete();
        return redirect()->route('admin.offenses.index')
                         ->with('success', 'Offense deleted successfully.');
    }

    /**
     * Mark an offense as resolved.
     */
    public function resolve(Offense $offense)
    {
        $offense->update([
            'resolved' => true,
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->route('admin.offenses.index')
                         ->with('success', 'Offense marked as resolved.');
    }
}
