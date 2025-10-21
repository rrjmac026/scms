<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Feedback;
use App\Models\Counselor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use FPDF;

class GenerateReportController extends Controller
{
    // Display the report generation form
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

        // Prepare chart data with FIXED structure
        $analytics = [
            'kpis' => [
                'total_appointments' => Appointment::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_appointments' => Appointment::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->count(),
                'canceled_appointments' => Appointment::whereBetween('created_at', [$startDate, $endDate])->where('status', 'canceled')->count(),
                'total_sessions' => CounselingSession::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_students_counseled' => CounselingSession::whereBetween('created_at', [$startDate, $endDate])->distinct('student_id')->count('student_id'),
                'active_counselors' => Counselor::count(),
                'average_feedback_rating' => round(Feedback::whereBetween('created_at', [$startDate, $endDate])->avg('rating') ?? 0, 2),
            ],
            'charts' => [
                'sessionsPerMonth' => $this->getSessionsPerMonth($startDate, $endDate),
                'appointmentsByStatus' => $this->getAppointmentsByStatus($startDate, $endDate),
                'feedbackTrends' => $this->getFeedbackTrends($startDate, $endDate),
                'topOffenses' => $this->getTopOffenses($startDate, $endDate),
                'counselorWorkload' => $this->getCounselorWorkload($startDate, $endDate),
                'categoryDistribution' => $this->getCategoryDistribution($startDate, $endDate),
            ]
        ];

        $counselors = Counselor::with('user')->get();
        $categories = CounselingCategory::all();

        $filters = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'counselor_id' => $request->counselor_id,
            'category' => $request->category,
        ];

        // For debugging
        \Log::info('Analytics Data:', $analytics);

        return view('admin.reports.index', compact('analytics', 'filters', 'counselors', 'categories'));
    }

    // FIXED: Updated all chart methods to return proper data structure
    protected function getSessionsPerMonth($startDate, $endDate)
    {
        $sessions = CounselingSession::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $monthNames[$i - 1];
            $session = $sessions->where('month', $i)->first();
            $data[] = $session ? $session->count : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getAppointmentsByStatus($startDate, $endDate)
    {
        $statuses = Appointment::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        $labels = $statuses->pluck('status')->map(function($status) {
            return ucfirst($status);
        })->toArray();

        $data = $statuses->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getFeedbackTrends($startDate, $endDate)
    {
        $feedbacks = Feedback::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('AVG(rating) as average')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $monthNames[$i - 1];
            $feedback = $feedbacks->where('month', $i)->first();
            $data[] = $feedback ? round($feedback->average, 2) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getTopOffenses($startDate, $endDate)
    {
        $offenses = Offense::select(
                'offense',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('offense')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'labels' => $offenses->pluck('offense')->toArray(),
            'data' => $offenses->pluck('count')->toArray()
        ];
    }

    protected function getCounselorWorkload($startDate, $endDate)
    {
        $workload = CounselingSession::with('counselor.user')
            ->select(
                'counselor_id',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('counselor_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $labels = $workload->map(function($item) {
            return $item->counselor->user->name ?? 'Unknown';
        })->toArray();

        $data = $workload->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    protected function getCategoryDistribution($startDate, $endDate)
    {
        $categories = CounselingCategory::withCount([
            'appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
        ->orderByDesc('appointments_count')
        ->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('appointments_count')->toArray()
        ];
    }

    // Generate detailed report view
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $counselorId = $request->counselor_id;

        $appointments = Appointment::whereBetween('preferred_date', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        // FIXED: Changed 'session' to 'counselingSession'
        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        if ($appointments->isEmpty() && $sessions->isEmpty()) {
            return back()->with('error', 'No data found within the selected date range.');
        }

        $statistics = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'pending_appointments' => $appointments->where('status', 'pending')->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'total_sessions' => $sessions->count(),
            'unique_students' => $sessions->pluck('student_id')->unique()->count(),
            'average_rating' => $feedbacks->avg('rating') ?? 0,
            'total_feedbacks' => $feedbacks->count(),
        ];

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        return view('admin.reports.detailed', compact(
            'appointments', 'sessions', 'feedbacks', 
            'statistics', 'startDate', 'endDate', 
            'counselorId', 'counselorName'
        ));
    }

    // Export PDF using FPDF
    public function exportPDF(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();
        $counselorId = $request->input('counselor_id');

        $appointments = Appointment::whereBetween('preferred_date', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        // FIXED: Changed 'session' to 'counselingSession'
        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        $statistics = [
            'Total Appointments' => $appointments->count(),
            'Completed Appointments' => $appointments->where('status','completed')->count(),
            'Pending Appointments' => $appointments->where('status','pending')->count(),
            'Cancelled Appointments' => $appointments->where('status','cancelled')->count(),
            'Total Sessions' => $sessions->count(),
            'Total Students' => $sessions->pluck('student_id')->unique()->count(),
            'Total Feedbacks' => $feedbacks->count(),
            'Average Rating' => number_format($feedbacks->avg('rating') ?? 0, 2),
        ];

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'Counseling Report',0,1,'C');
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,8,'Period: '.$startDate->format('F j, Y').' to '.$endDate->format('F j, Y'),0,1);
        $pdf->Cell(0,8,'Counselor: '.$counselorName,0,1);
        $pdf->Ln(5);

        // Statistics Table
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(100,8,'Metric',1);
        $pdf->Cell(50,8,'Value',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','',12);
        foreach ($statistics as $metric => $value) {
            $pdf->Cell(100,8,$metric,1);
            $pdf->Cell(50,8,(string)$value,1);
            $pdf->Ln();
        }

        // Add appointments table
        if($appointments->isNotEmpty()) {
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,'Appointments Details',0,1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,8,'Date',1);
            $pdf->Cell(60,8,'Student',1);
            $pdf->Cell(60,8,'Counselor',1);
            $pdf->Cell(30,8,'Status',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','',9);
            foreach($appointments as $app) {
                $pdf->Cell(30,8,$app->preferred_date->format('F j, Y'),1);
                $pdf->Cell(60,8,substr($app->student->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(60,8,substr($app->counselor->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(30,8,ucfirst($app->status),1);
                $pdf->Ln();
            }
        }

        // Add sessions table
        if($sessions->isNotEmpty()) {
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,'Counseling Sessions Details',0,1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,8,'Date',1);
            $pdf->Cell(60,8,'Student',1);
            $pdf->Cell(60,8,'Counselor',1);
            $pdf->Cell(30,8,'Duration',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','',9);
            foreach($sessions as $session) {
                $pdf->Cell(30,8,$session->started_at->format('F j, Y'),1);
                $pdf->Cell(60,8,substr($session->student->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(60,8,substr($session->counselor->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(30,8,$session->formatted_duration ?? 'N/A',1);
                $pdf->Ln();
            }
        }

        // Add feedbacks table
        if($feedbacks->isNotEmpty()) {
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,'Feedback Summary',0,1);
            $pdf->Ln(2);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40,8,'Date',1);
            $pdf->Cell(60,8,'Student',1);
            $pdf->Cell(30,8,'Rating',1);
            $pdf->Cell(50,8,'Comments',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','',9);
            foreach($feedbacks as $feedback) {
                $pdf->Cell(40,8,$feedback->created_at->format('F j, Y'),1);
                $pdf->Cell(60,8,substr($feedback->student->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(30,8,$feedback->rating . '/5',1);
                $pdf->Cell(50,8,substr($feedback->comments ?? 'No comment', 0, 25),1);
                $pdf->Ln();
            }
        }

        $filename = 'counseling_report_'.$startDate->format('Ymd').'_to_'.$endDate->format('Ymd').'.pdf';
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->Output('S');
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }


    
    public function exportExcel(Request $request)
    {
        // Parse dates properly
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()))->endOfDay();
        $counselorId = $request->input('counselor_id', '');

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        $filters = [
            'start_date' => $startDate->format('F j, Y'),
            'end_date' => $endDate->format('F j, Y'),
            'counselor_name' => $counselorName 
        ];

        // Fetch appointments using created_at to match the dashboard query
        $appointments = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();
        
        // Debug: Log the statuses to check what we're getting
        \Log::info('Appointment Statuses:', $appointments->pluck('status')->toArray());

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        $analytics = [
            'kpis' => [
                'total_appointments' => $appointments->count(),
                'completed_appointments' => $appointments->where('status','completed')->count(),
                'pending_appointments' => $appointments->where('status','pending')->count(),
                'approved_appointments' => $appointments->where('status','approved')->count(),
                'accepted_appointments' => $appointments->where('status','accepted')->count(),
                'rejected_appointments' => $appointments->where('status','rejected')->count(),
                'declined_appointments' => $appointments->where('status','declined')->count(),
                'cancelled_appointments' => $appointments->where('status','cancelled')->count(),
                'total_sessions' => $sessions->count(),
                'unique_students' => $sessions->pluck('student_id')->unique()->count(),
                'average_rating' => $feedbacks->avg('rating') ?? 0,
                'total_feedbacks' => $feedbacks->count(),
            ],
            'detailed_data' => [
                // Matching PDF appointments table structure (4 columns)
                'appointments' => $appointments->map(function($app) {
                    return [
                        $app->created_at->format('F j, Y'),
                        substr($app->student->user->name ?? 'N/A', 0, 30),
                        substr($app->counselor->user->name ?? 'N/A', 0, 30),
                        ucfirst($app->status)
                    ];
                })->toArray(),
                // Matching PDF sessions table structure (4 columns)
                'sessions' => $sessions->map(function($session) {
                    return [
                        $session->started_at->format('F j, Y'),
                        substr($session->student->user->name ?? 'N/A', 0, 30),
                        substr($session->counselor->user->name ?? 'N/A', 0, 30),
                        $session->formatted_duration ?? 'N/A'
                    ];
                })->toArray(),
                // Matching PDF feedbacks table structure (4 columns)
                'feedbacks' => $feedbacks->map(function($feedback) {
                    return [
                        $feedback->created_at->format('F j, Y'),
                        substr($feedback->student->user->name ?? 'N/A', 0, 30),
                        $feedback->rating . '/5',
                        substr($feedback->comments ?? 'No comment', 0, 25)
                    ];
                })->toArray()
            ]
        ];

        $filename = 'counseling_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CounselingReportExport($analytics, $filters), 
            $filename
        );
    }
}