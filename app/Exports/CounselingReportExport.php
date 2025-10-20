<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;

class CounselingReportExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $analytics;
    protected $filters;

    public function __construct(array $analytics, array $filters)
    {
        $this->analytics = $analytics;
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Summary Sheet
        $sheets[] = new SummarySheet($this->analytics, $this->filters);

        // Appointments Sheet
        if (!empty($this->analytics['detailed_data']['appointments'])) {
            $sheets[] = new AppointmentsSheet($this->analytics['detailed_data']['appointments']);
        }

        // Sessions Sheet
        if (!empty($this->analytics['detailed_data']['sessions'])) {
            $sheets[] = new SessionsSheet($this->analytics['detailed_data']['sessions']);
        }

        // Feedbacks Sheet
        if (!empty($this->analytics['detailed_data']['feedbacks'])) {
            $sheets[] = new FeedbacksSheet($this->analytics['detailed_data']['feedbacks']);
        }

        return $sheets;
    }
}

class SummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $analytics;
    protected $filters;

    public function __construct(array $analytics, array $filters)
    {
        $this->analytics = $analytics;
        $this->filters = $filters;
    }

    public function array(): array
    {
        $data = [];

        // Header
        $data[] = ['Counseling Report'];
        $data[] = ['Period:', $this->filters['start_date'] . ' to ' . $this->filters['end_date']];
        $data[] = ['Counselor:', $this->filters['counselor_name']];
        $data[] = []; // Empty row

        // Statistics
        $data[] = ['Statistics Summary'];
        $data[] = ['Metric', 'Value'];
        
        foreach ($this->analytics['kpis'] as $metric => $value) {
            $formattedMetric = ucwords(str_replace('_', ' ', $metric));
            $formattedValue = is_numeric($value) ? number_format($value, 2) : $value;
            $data[] = [$formattedMetric, $formattedValue];
        }

        $data[] = []; // Empty row

        // Charts data preview
        if (isset($this->analytics['charts']['sessions_per_month'])) {
            $data[] = ['Sessions per Month'];
            $data[] = ['Month', 'Sessions Count'];
            foreach ($this->analytics['charts']['sessions_per_month']['labels'] as $index => $label) {
                $data[] = [$label, $this->analytics['charts']['sessions_per_month']['data'][$index] ?? 0];
            }
        }

        $data[] = []; // Empty row

        if (isset($this->analytics['charts']['appointments_by_status'])) {
            $data[] = ['Appointments by Status'];
            $data[] = ['Status', 'Count'];
            foreach ($this->analytics['charts']['appointments_by_status']['labels'] as $index => $label) {
                $data[] = [ucfirst($label), $this->analytics['charts']['appointments_by_status']['data'][$index] ?? 0];
            }
        }

        return $data;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true, 'size' => 14]],
            12 => ['font' => ['bold' => true]],
            17 => ['font' => ['bold' => true, 'size' => 14]],
            18 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
        ];
    }
}

class AppointmentsSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $appointments;

    public function __construct(array $appointments)
    {
        $this->appointments = $appointments;
    }

    public function array(): array
    {
        return $this->appointments;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Counselor Name',
            'Status',
            'Notes'
        ];
    }

    public function title(): string
    {
        return 'Appointments';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 25,
            'D' => 15,
            'E' => 40,
        ];
    }
}

class SessionsSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $sessions;

    public function __construct(array $sessions)
    {
        $this->sessions = $sessions;
    }

    public function array(): array
    {
        return $this->sessions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Counselor Name',
            'Category',
            'Duration',
            'Notes'
        ];
    }

    public function title(): string
    {
        return 'Sessions';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 25,
            'D' => 20,
            'E' => 15,
            'F' => 40,
        ];
    }
}

class FeedbacksSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $feedbacks;

    public function __construct(array $feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }

    public function array(): array
    {
        return $this->feedbacks;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Counselor Name',
            'Rating',
            'Comments'
        ];
    }

    public function title(): string
    {
        return 'Feedbacks';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 25,
            'D' => 15,
            'E' => 50,
        ];
    }
}