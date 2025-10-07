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
use FPDF;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfMonth();

        // Check if there's any data in the selected range
        $hasData = Appointment::whereBetween('created_at', [$startDate, $endDate])->exists();

        if (!$hasData && $request->has('start_date')) {
            return back()->with('error', 'No data available for the selected date range.');
        }

        // Initialize base analytics data
        $appointments = Appointment::whereBetween('created_at', [$startDate, $endDate]);
        $sessions = CounselingSession::whereBetween('created_at', [$startDate, $endDate]);
        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate]);

        // Prepare chart data
        $analytics = [
            'kpis' => [
                'total_appointments' => (clone $appointments)->count(),
                'completed_appointments' => (clone $appointments)->where('status', 'completed')->count(),
                'canceled_appointments' => (clone $appointments)->where('status', 'canceled')->count(),
                'total_sessions' => (clone $sessions)->count(),
                'total_students_counseled' => (clone $sessions)->distinct('student_id')->count('student_id'),
                'active_counselors' => Counselor::count(),
                'average_feedback_rating' => round($feedbacks->avg('rating') ?? 0, 2),
            ],
            'charts' => [
                'sessionsPerMonth' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    'data' => $this->getMonthlySessionCounts($startDate->year)
                ],
                'appointmentsByStatus' => [
                    'labels' => ['Completed', 'Pending', 'Cancelled'],
                    'data' => [
                        (clone $appointments)->where('status', 'completed')->count(),
                        (clone $appointments)->where('status', 'pending')->count(),
                        (clone $appointments)->where('status', 'canceled')->count()
                    ]
                ],
                'feedbackTrends' => [
                    'labels' => $this->getLastSixMonths(),
                    'data' => $this->getFeedbackTrendData()
                ],
                'topOffenses' => [
                    'labels' => $this->getTopOffenseLabels(),
                    'data' => $this->getTopOffenseCounts()
                ],
                'counselorWorkload' => [
                    'labels' => $this->getCounselorNames(),
                    'data' => $this->getCounselorSessionCounts()
                ],
                'categoryDistribution' => [
                    'labels' => $this->getCategoryNames(),
                    'data' => $this->getCategoryCounts()
                ]
            ]
        ];

        $counselors = Counselor::with('user')->get();
        $categories = \App\Models\CounselingCategory::all();

        $filters = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'counselor_id' => $request->counselor_id,
            'category' => $request->category,
        ];

        // For debugging
        \Log::info('Chart Data:', $analytics['charts']);

        return view('admin.reports.index', compact('analytics', 'filters', 'counselors', 'categories'));
    }

    public function generate()
    {
        $filters = [
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'counselor_id' => null,
        ];

        // Get all counselors with their user relation
        $counselors = Counselor::with('user')->get();

        return view('admin.reports.generateReport', compact('filters', 'counselors'));
    }

    protected function generateAnalytics($filters)
    {
        $appointments = Appointment::query()
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        $sessions = CounselingSession::query()
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);

        $analytics = [
            'kpis' => [
                'total_appointments' => (clone $appointments)->count(),
                'completed_appointments' => (clone $appointments)->where('status', 'completed')->count(),
                'canceled_appointments' => (clone $appointments)->where('status', 'canceled')->count(),
                'total_sessions' => (clone $sessions)->count(),
                'total_students_counseled' => (clone $sessions)->distinct('student_id')->count('student_id'),
                'active_counselors' => Counselor::count(),
                'average_feedback_rating' => number_format(Feedback::avg('rating') ?? 0, 1),
            ],
            'charts' => [
                'sessionsPerMonth' => $this->getSessionsPerMonth($filters),
                'appointmentsByStatus' => $this->getAppointmentsByStatus($filters),
                'feedbackTrends' => $this->getFeedbackTrends($filters),
                'topOffenses' => $this->getCommonOffenses($filters),
                'counselorWorkload' => $this->getCounselorWorkload($filters),
                'categoryDistribution' => $this->getSessionsByCategory($filters),
            ],
        ];

        return $analytics;
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

    public function analytics(Request $request)
    {
        $filters = $this->getFilterParameters($request);
        $analytics = $this->generateAnalytics($filters);
        
        return response()->json($analytics);
    }

    // Add these helper methods to the class
    private function getMonthlySessionCounts($year)
    {
        $counts = [];
        for ($month = 1; $month <= 12; $month++) {
            $counts[] = CounselingSession::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        }
        return $counts;
    }

    private function getLastSixMonths()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('M Y');
        }
        return $months;
    }

    private function getFeedbackTrendData()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = Feedback::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->avg('rating') ?? 0;
        }
        return $data;
    }

    private function getCategoryNames()
    {
        return \App\Models\CounselingCategory::pluck('name')->toArray();
    }

    private function getCategoryCounts()
    {
        return \App\Models\CounselingCategory::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->pluck('appointments_count')
            ->toArray();
    }

    private function getTopOffenseLabels()
    {
        return Offense::select('offense')
            ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
            ->groupBy('offense')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('offense')
            ->toArray();
    }

    private function getTopOffenseCounts()
    {
        return Offense::select(\DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
            ->groupBy('offense')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('count')
            ->toArray();
    }

    private function getCounselorNames()
    {
        return Counselor::with('user')
            ->get()
            ->map(function($counselor) {
                return $counselor->user->name ?? 'Unknown';
            })
            ->toArray();
    }

    private function getCounselorSessionCounts()
    {
        return Counselor::withCount('counselingSessions')
            ->orderBy('counseling_sessions_count', 'desc')
            ->get()
            ->pluck('counseling_sessions_count')
            ->toArray();
    }
}
