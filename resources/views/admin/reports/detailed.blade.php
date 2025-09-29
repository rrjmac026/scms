<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Counseling Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 2rem; }
        .section { margin-bottom: 2rem; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .kpi-card { padding: 1rem; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Counseling Analytics Report</h1>
        <p>Period: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
    </div>

    <div class="section">
        <h2>Key Performance Indicators</h2>
        <div class="kpi-grid">
            @foreach($analytics['kpis'] as $key => $value)
                <div class="kpi-card">
                    <h3>{{ ucwords(str_replace('_', ' ', $key)) }}</h3>
                    <p>{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @foreach($analytics['charts'] as $key => $data)
        <div class="section">
            <h2>{{ ucwords(str_replace('_', ' ', $key)) }}</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < count($data['labels']); $i++)
                        <tr>
                            <td>{{ $data['labels'][$i] }}</td>
                            <td>{{ $data['data'][$i] }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>