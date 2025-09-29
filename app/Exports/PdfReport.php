<?php

namespace App\Exports;

use FPDF;

class PdfReport extends FPDF
{
    protected $analytics;
    protected $filters;

    public function __construct($analytics, $filters)
    {
        parent::__construct();
        $this->analytics = $analytics;
        $this->filters = $filters;
    }

    public function generate()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 16);
        
        // Title
        $this->Cell(0, 10, 'Counseling Analytics Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Period: ' . $this->filters['start_date'] . ' to ' . $this->filters['end_date'], 0, 1, 'C');
        $this->Ln(10);

        // KPI Section
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Key Performance Indicators', 0, 1, 'L');
        $this->SetFont('Arial', '', 12);
        
        foreach ($this->analytics['kpis'] as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $this->Cell(0, 8, $label . ': ' . $value, 0, 1);
        }

        $this->Ln(10);

        // Add more sections for charts data as needed
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Analytics Breakdown', 0, 1, 'L');
        $this->SetFont('Arial', '', 12);

        foreach ($this->analytics['charts'] as $key => $data) {
            $title = ucwords(str_replace('_', ' ', $key));
            $this->Cell(0, 8, $title . ':', 0, 1);
            
            // Create a table-like structure for the data
            for ($i = 0; $i < count($data['labels']); $i++) {
                $this->Cell(100, 8, $data['labels'][$i], 1);
                $this->Cell(90, 8, $data['data'][$i], 1);
                $this->Ln();
            }
            $this->Ln(5);
        }
    }
}
