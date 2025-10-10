<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class CounselorController extends Controller
{
    public function dashboard()
    {
        $counselor = Auth::user()->counselor;

        // Prevent null counselor
        if (!$counselor) {
            return redirect()->route('profile.edit')
                ->with('error', 'Counselor profile not found. Please update your profile first.');
        }

        // === Dashboard stats ===
        $totalAppointments = $counselor->appointments()->count();

        $completedSessions = $counselor->appointments()
            ->where('status', 'completed')
            ->count();

        $todaysAppointments = $counselor->appointments()
            ->whereDate('preferred_date', Carbon::today())
            ->count();

        $activeStudents = $counselor->appointments()
            ->distinct('student_id')
            ->count('student_id');

        return view('counselors.dashboard', compact(
            'counselor',
            'totalAppointments',
            'completedSessions',
            'todaysAppointments',
            'activeStudents'
        ));
    }
}
