<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Feedback;
use App\Models\Counselor;
use Carbon\Carbon;
use App\Exports\CounselingReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use FPDF;

class CounselorGenerateReportController extends Controller
{
    // Display the report generation form
    public function index(Request $request)
    {
        $user = auth()->user();

        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->input('end_date', now()->endOfMonth()->format('Y-m-d')),
            'counselor_id' => $request->input('counselor_id', '')
        ];

        // Default: all counselors (for admin)
        $counselors = Counselor::with('user')->get();

        // Restrict if logged in as counselor
        if ($user->counselor) {
            $counselors = collect([$user->counselor]);
            // Force the counselor_id filter to their own ID
            $filters['counselor_id'] = $user->counselor->id;
        }

        return view('counselors.reports.index', compact('filters', 'counselors'));
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

        // FIXED: Changed to created_at to match admin functionality
        $appointments = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        if ($appointments->isEmpty() && $sessions->isEmpty()) {
            return back()->with('error', 'No data found within the selected date range.');
        }

        // FIXED: Added all appointment statuses to match admin
        $statistics = [
            'total_appointments' => $appointments->count(),
            'completed_appointments' => $appointments->where('status', 'completed')->count(),
            'pending_appointments' => $appointments->where('status', 'pending')->count(),
            'approved_appointments' => $appointments->where('status', 'approved')->count(),
            'accepted_appointments' => $appointments->where('status', 'accepted')->count(),
            'rejected_appointments' => $appointments->where('status', 'rejected')->count(),
            'declined_appointments' => $appointments->where('status', 'declined')->count(),
            'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
            'total_sessions' => $sessions->count(),
            'unique_students' => $sessions->pluck('student_id')->unique()->count(),
            'average_rating' => number_format($feedbacks->avg('rating') ?? 0, 2),
            'total_feedbacks' => $feedbacks->count(),
        ];

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        return view('counselors.reports.detailed', compact(
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

        // FIXED: Changed to created_at to match admin and Excel export
        $appointments = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        // FIXED: Added all appointment statuses to match admin
        $statistics = [
            'Total Appointments' => $appointments->count(),
            'Completed Appointments' => $appointments->where('status','completed')->count(),
            'Pending Appointments' => $appointments->where('status','pending')->count(),
            'Approved Appointments' => $appointments->where('status','approved')->count(),
            'Accepted Appointments' => $appointments->where('status','accepted')->count(),
            'Rejected Appointments' => $appointments->where('status','rejected')->count(),
            'Declined Appointments' => $appointments->where('status','declined')->count(),
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
                // FIXED: Changed to created_at to match admin
                $pdf->Cell(30,8,$app->created_at->format('F j, Y'),1);
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

        // FIXED: Fetch appointments using created_at to match PDF
        $appointments = Appointment::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
            ->get();

        $feedbacks = Feedback::whereBetween('created_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->whereHas('counselingSession', fn($s) => $s->where('counselor_id', $counselorId)))
            ->with(['counselingSession.counselor.user', 'student.user'])
            ->get();

        // FIXED: Match data structure with PDF (4 columns each, matching admin)
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
            new CounselingReportExport($analytics, $filters), 
            $filename
        );
    }
}