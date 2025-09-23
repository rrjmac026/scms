<?php

namespace App\Http\Controllers;

use App\Models\Offense;
use App\Models\Student;
use Illuminate\Http\Request;

class OffenseController extends Controller
{
    /**
     * Display a listing of offenses.
     */
    public function index()
    {
        $offenses = Offense::with(['student.user', 'counselor.user'])
                           ->latest()
                           ->paginate(10);

        return view('counselor.offenses.index', compact('offenses'));
    }

    /**
     * Show the form for creating a new offense.
     */
    public function create()
    {
        $students = Student::with('user')->get();
        return view('counselor.offenses.create', compact('students'));
    }

    /**
     * Store a newly created offense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'offense'    => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:500',
            'date'       => 'required|date',
            'status'     => 'nullable|string|max:50',
            'solution'   => 'nullable|string|max:500',
            'resolved'   => 'nullable|boolean',
        ]);

        $validated['counselor_id'] = auth()->user()->counselor->id;

        Offense::create($validated);

        return redirect()->route('counselor.offenses.index')
                         ->with('success', 'Offense recorded successfully.');
    }

    /**
     * Display the specified offense.
     */
    public function show(Offense $offense)
    {
        $offense->load(['student.user', 'counselor.user']);
        return view('counselor.offenses.show', compact('offense'));
    }

    /**
     * Show the form for editing the specified offense.
     */
    public function edit(Offense $offense)
    {
        $students = Student::with('user')->get();
        return view('counselor.offenses.edit', compact('offense', 'students'));
    }

    /**
     * Update the specified offense in storage.
     */
    public function update(Request $request, Offense $offense)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'offense'    => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:500',
            'date'       => 'required|date',
            'status'     => 'nullable|string|max:50',
            'solution'   => 'nullable|string|max:500',
            'resolved'   => 'nullable|boolean',
        ]);

        $offense->update($validated);

        return redirect()->route('counselor.offenses.index')
                         ->with('success', 'Offense updated successfully.');
    }

    /**
     * Remove the specified offense from storage.
     */
    public function destroy(Offense $offense)
    {
        $offense->delete();

        return redirect()->route('counselor.offenses.index')
                         ->with('success', 'Offense deleted successfully.');
    }

    /**
     * Mark offense as resolved.
     */
    public function resolve(Offense $offense)
    {
        $offense->update([
            'resolved' => true,
            'status' => 'resolved',
        ]);

        return redirect()->route('counselor.offenses.index')
                         ->with('success', 'Offense marked as resolved.');
    }
}
