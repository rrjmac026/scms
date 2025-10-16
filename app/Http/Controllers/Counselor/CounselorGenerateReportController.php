<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CounselingSession;
use App\Models\Feedback;
use App\Models\Counselor;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $appointments = Appointment::whereBetween('preferred_date', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user'])
            ->get();

        $sessions = CounselingSession::whereBetween('started_at', [$startDate, $endDate])
            ->when($counselorId, fn($q) => $q->where('counselor_id', $counselorId))
            ->with(['student.user', 'counselor.user', 'category'])
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
        ];

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        return view('counselors.reports.detailed', compact(
            'appointments', 'sessions', 
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


        $statistics = [
            'Total Appointments' => $appointments->count(),
            'Completed Appointments' => $appointments->where('status','completed')->count(),
            'Pending Appointments' => $appointments->where('status','pending')->count(),
            'Cancelled Appointments' => $appointments->where('status','cancelled')->count(),
            'Total Sessions' => $sessions->count(),
            'Total Students' => $sessions->pluck('student_id')->unique()->count(),
        ];

        $counselorName = $counselorId ? Counselor::find($counselorId)->user->name : 'All Counselors';

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'Counseling Report',0,1,'C');
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,8,'Period: '.$startDate->format('Y-m-d').' to '.$endDate->format('Y-m-d'),0,1);
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
                $pdf->Cell(30,8,$app->preferred_date->format('Y-m-d'),1);
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
                $pdf->Cell(30,8,$session->started_at->format('Y-m-d'),1);
                $pdf->Cell(60,8,substr($session->student->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(60,8,substr($session->counselor->user->name ?? 'N/A', 0, 30),1);
                $pdf->Cell(30,8,$session->formatted_duration ?? 'N/A',1);
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
            'start_date' => $request->input('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->input('end_date', now()->endOfMonth()->format('Y-m-d')),
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


        $analytics = [
            'kpis' => [
                'total_appointments' => $appointments->count(),
                'completed_appointments' => $appointments->where('status','completed')->count(),
                'pending_appointments' => $appointments->where('status','pending')->count(),
                'cancelled_appointments' => $appointments->where('status','cancelled')->count(),
                'total_sessions' => $sessions->count(),
                'unique_students' => $sessions->pluck('student_id')->unique()->count(),
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
            ->download('report_'.$startDate->format('Y-m-d').'_to_'.$endDate->format('Y-m-d').'.xlsx');
    }
}