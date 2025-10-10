<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\CounselingSession;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // === Key Metrics ===
        $totalUsers = User::count();

        // Count active students (users with student role + active status)
        $activeStudents = User::where('role', 'student')
            ->where('status', 'active')
            ->count();

        // Count total sessions
        $totalSessions = CounselingSession::count();

        // Count pending appointments
        $pendingAppointments = Appointment::where('status', 'pending')->count();

        // === Additional Insights ===
        $todaysAppointments = Appointment::whereDate('preferred_date', Carbon::today())->count();

        $completedSessions = Appointment::where('status', 'completed')->count();

        // === Recent Sessions (with relationships) ===
        $recentSessions = CounselingSession::with([
                'student.user:id,first_name,last_name',
                'counselor.user:id,first_name,last_name'
            ])
            ->latest()
            ->take(5)
            ->get();

        // === Data for View ===
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'activeStudents' => $activeStudents,
            'totalSessions' => $totalSessions,
            'pendingAppointments' => $pendingAppointments,
            'todaysAppointments' => $todaysAppointments,
            'completedSessions' => $completedSessions,
            'recentSessions' => $recentSessions,
        ]);
    }
}
