<?php

namespace App\Http\Controllers;

use App\Models\CounselingSession;
use Illuminate\Http\Request;

class CounselingSessionController extends Controller
{
    public function index()
    {
        return CounselingSession::with(['student', 'counselor'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'counselor_id' => 'required|exists:counselors,id',
            'notes' => 'nullable|string',
            'session_date' => 'required|date',
        ]);

        return CounselingSession::create($data);
    }

    public function show(CounselingSession $counselingSession)
    {
        return $counselingSession->load(['student', 'counselor']);
    }

    public function update(Request $request, CounselingSession $counselingSession)
    {
        $data = $request->validate([
            'notes' => 'nullable|string',
            'session_date' => 'required|date',
        ]);

        $counselingSession->update($data);

        return $counselingSession;
    }

    public function destroy(CounselingSession $counselingSession)
    {
        $counselingSession->delete();

        return response()->noContent();
    }
}
