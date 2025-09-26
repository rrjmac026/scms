<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\CounselingSession;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'totalUsers' => User::count(),
            'activeStudents' => Student::count(),
            'totalSessions' => CounselingSession::count(),
            'pendingAppointments' => Appointment::where('status', 'pending')->count(),
        ];
        
        return view('admin.dashboard', $data);
    }
}
