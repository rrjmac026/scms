<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Counselor;
use App\Models\Student;
use App\Models\Feedback;
use App\Models\Offense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Exports\PdfReport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = $this->getFilterParameters($request);
        
        // Get analytics data with caching
        $analytics = Cache::remember(
            'analytics_' . md5(json_encode($filters)), 
            now()->addMinutes(30),
            fn() => $this->generateAnalytics($filters)
        );

        // Get counselors for filter dropdown
        $counselors = Counselor::with('user')->get();
        
        // Get categories for filter dropdown
        $categories = CounselingSession::join('appointments', 'counseling_sessions.appointment_id', '=', 'appointments.id')
            ->join('counseling_categories', 'appointments.counseling_category_id', '=', 'counseling_categories.id')
            ->distinct()
            ->pluck('counseling_categories.name');

        return view('admin.reports.index', compact('analytics', 'counselors', 'categories', 'filters'));
    }

    protected function getFilterParameters(Request $request)
    {
        return [
            'start_date' => $request->start_date ?? now()->startOfMonth()->format('Y-m-d'),
            'end_date' => $request->end_date ?? now()->format('Y-m-d'),
            'counselor_id' => $request->counselor_id,
            'category' => $request->category,
        ];
    }

    protected function generateAnalytics($filters)
    {
        $query = $this->baseQuery($filters);
        $sessionQuery = $this->baseSessionQuery($filters);

        return [
            'kpis' => [
                'total_appointments' => $query->count(),
                'completed_appointments' => $query->where('status', 'completed')->count(),
                'canceled_appointments' => $query->where('status', 'canceled')->count(),
                'total_sessions' => $sessionQuery->count(),
                'total_students_counseled' => $sessionQuery->distinct('student_id')->count(),
                'active_counselors' => Counselor::whereHas('appointments')->count(),
                'average_feedback_rating' => number_format(Feedback::avg('rating') ?? 0, 1),
            ],
            'charts' => [
                'sessions_per_month' => $this->getSessionsPerMonth($filters),
                'appointments_by_status' => $this->getAppointmentsByStatus($filters),
                'feedback_trends' => $this->getFeedbackTrends($filters),
                'sessions_by_category' => $this->getSessionsByCategory($filters),
                'common_offenses' => $this->getCommonOffenses($filters),
                'counselor_workload' => $this->getCounselorWorkload($filters),
            ],
        ];
    }

    protected function baseQuery($filters)
    {
        $query = Appointment::query();

        if ($filters['start_date']) {
            $query->whereDate('preferred_date', '>=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->whereDate('preferred_date', '<=', $filters['end_date']);
        }
        if ($filters['counselor_id']) {
            $query->where('counselor_id', $filters['counselor_id']);
        }

        return $query;
    }

    protected function baseSessionQuery($filters)
    {
        $query = CounselingSession::query();

        if ($filters['start_date']) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if ($filters['end_date']) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if ($filters['counselor_id']) {
            $query->where('counselor_id', $filters['counselor_id']);
        }
        if ($filters['category']) {
            $query->where('category', $filters['category']);
        }

        return $query;
    }

    protected function getSessionsPerMonth($filters)
    {
        $sessions = CounselingSession::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $sessions->pluck('month'),
            'data' => $sessions->pluck('count'),
        ];
    }

    protected function getAppointmentsByStatus($filters)
    {
        $statuses = Appointment::whereBetween('preferred_date', [$filters['start_date'], $filters['end_date']])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $statuses->pluck('status'),
            'data' => $statuses->pluck('count'),
        ];
    }

    protected function getFeedbackTrends($filters)
    {
        $feedback = Feedback::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(rating) as average')
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $feedback->pluck('month'),
            'data' => $feedback->pluck('average'),
        ];
    }

    protected function getSessionsByCategory($filters)
    {
        $categories = CounselingSession::join('appointments', 'counseling_sessions.appointment_id', '=', 'appointments.id')
            ->join('counseling_categories', 'appointments.counseling_category_id', '=', 'counseling_categories.id')
            ->whereBetween('counseling_sessions.created_at', [$filters['start_date'], $filters['end_date']])
            ->selectRaw('counseling_categories.name as category, COUNT(*) as count')
            ->groupBy('counseling_categories.name')
            ->get();

        return [
            'labels' => $categories->pluck('category'),
            'data' => $categories->pluck('count'),
        ];
    }


    protected function getCommonOffenses($filters)
    {
        $offenses = Offense::selectRaw('offense, COUNT(*) as count')
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']])
            ->groupBy('offense')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $offenses->pluck('offense'),
            'data' => $offenses->pluck('count'),
        ];
    }



    protected function getCounselorWorkload($filters)
    {
        $workload = CounselingSession::with('counselor.user')
            ->selectRaw('counselor_id, COUNT(*) as count')
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']])
            ->groupBy('counselor_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $workload->map(fn($item) => $item->counselor->user->name ?? 'Unknown'),
            'data' => $workload->pluck('count'),
        ];
    }

    public function generateReport(Request $request)
    {
        $filters = $this->getFilterParameters($request);
        $analytics = $this->generateAnalytics($filters);
        
        return view('admin.reports.detailed', compact('analytics', 'filters'));
    }

    public function exportPDF(Request $request)
    {
        $filters = $this->getFilterParameters($request);
        $analytics = $this->generateAnalytics($filters);
        
        $pdf = new PdfReport($analytics, $filters);
        $pdf->generate();
        
        return $pdf->Output('counseling-report.pdf', 'D');
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->getFilterParameters($request);
        $analytics = $this->generateAnalytics($filters);
        
        return Excel::download(new \App\Exports\CounselingReportExport($analytics), 'counseling-report.xlsx');
    }

    public function analytics(Request $request)
    {
        $filters = $this->getFilterParameters($request);
        $analytics = $this->generateAnalytics($filters);
        
        return response()->json($analytics);
    }
}
