<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class CounselingReportExport implements FromArray, WithHeadings, WithMultipleSheets
{
    use Exportable;

    protected $analytics;
    protected $filters;

    public function __construct($analytics, $filters)
    {
        $this->analytics = $analytics;
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];

        // KPIs Sheet
        $sheets[] = new class($this->analytics['kpis']) implements FromArray, WithTitle, WithHeadings {
            private $kpis;

            public function __construct($kpis)
            {
                $this->kpis = $kpis;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->kpis as $key => $value) {
                    $data[] = [ucwords(str_replace('_', ' ', $key)), $value];
                }
                return $data;
            }

            public function headings(): array
            {
                return ['Metric', 'Value'];
            }

            public function title(): string
            {
                return 'KPIs';
            }
        };

        // Charts Sheets
        foreach ($this->analytics['charts'] as $name => $chart) {
            $sheets[] = new class($name, $chart) implements FromArray, WithTitle, WithHeadings {
                private $name;
                private $chart;

                public function __construct($name, $chart)
                {
                    $this->name = $name;
                    $this->chart = $chart;
                }

                public function array(): array
                {
                    $data = [];
                    for ($i = 0; $i < count($this->chart['labels']); $i++) {
                        $data[] = [$this->chart['labels'][$i], $this->chart['data'][$i]];
                    }
                    return $data;
                }

                public function headings(): array
                {
                    return ['Label', 'Value'];
                }

                public function title(): string
                {
                    return ucwords(str_replace('_', ' ', $this->name));
                }
            };
        }

        return $sheets;
    }

    public function array(): array
    {
        return []; // Not used since we're using multiple sheets
    }

    public function headings(): array
    {
        return []; // Not used since we're using multiple sheets
    }
}
