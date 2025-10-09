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
        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->format('F j, Y')),
            'end_date' => $request->input('end_date', now()->endOfMonth()->format('F j, Y')),
            'counselor_id' => $request->input('counselor_id', '')
        ];

        $counselors = Counselor::with('user')->get();

        return view('admin.reports.generate', compact('filters', 'counselors'));
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

    // Export Excel using existing export class
    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date', now()->startOfMonth()->format('F j, Y')),
            'end_date' => $request->input('end_date', now()->endOfMonth()->format('F j, Y')),
            'counselor_id' => $request->input('counselor_id', '')
        ];

        $startDate = Carbon::parse($filters['start_date'])->startOfDay();
        $endDate = Carbon::parse($filters['end_date'])->endOfDay();
        $counselorId = $filters['counselor_id'];

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

        $analytics = [
            'kpis' => [
                'total_appointments' => $appointments->count(),
                'completed_appointments' => $appointments->where('status','completed')->count(),
                'pending_appointments' => $appointments->where('status','pending')->count(),
                'cancelled_appointments' => $appointments->where('status','cancelled')->count(),
                'total_sessions' => $sessions->count(),
                'unique_students' => $sessions->pluck('student_id')->unique()->count(),
                'average_rating' => $feedbacks->avg('rating') ?? 0,
                'total_feedbacks' => $feedbacks->count(),
            ],
            'charts' => [
                'sessions_per_month' => [
                    'labels' => $sessions->pluck('started_at')->map->format('F')->unique()->toArray(),
                    'data' => $sessions->groupBy(fn($s) => $s->started_at->format('F'))->map->count()->values()->toArray()
                ],
                'appointments_by_status' => [
                    'labels' => ['completed','pending','cancelled'],
                    'data' => [
                        $appointments->where('status','completed')->count(),
                        $appointments->where('status','pending')->count(),
                        $appointments->where('status','cancelled')->count()
                    ]
                ]
            ]
        ];

        return (new \App\Exports\CounselingReportExport($analytics, $filters))
            ->download('report_'.$startDate->format('F j, Y').'_to_'.$endDate->format('F j, Y').'.xlsx');
    }
}