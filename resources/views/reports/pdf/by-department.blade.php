<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #3b4048; margin: 20px; }
        .header { border-bottom: 2px solid #8f2428; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { color: #a92d31; margin: 0; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #8f2428; color: #ffffff; padding: 7px 9px; text-align: left; font-size: 11px; }
        td { padding: 6px 9px; border-bottom: 1px solid #eadada; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FIRSTGRADE IT ASSET MANAGEMENT</h1>
        <p><strong>{{ $title }}</strong> &bull; Generated on: {{ $date }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Owner</th>
                <th>Active Assets</th>
                <th>Available</th>
                <th>In Repair</th>
                <th>Missing</th>
                @if ($showFinancial ?? true)
                    <th>Total Value (MYR)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data['departments'] as $dept)
            <tr>
                <td><strong>{{ $dept->name }}</strong></td>
                <td>{{ $dept->owner_title }}</td>
                <td>{{ $dept->active_assets_count }}</td>
                <td>{{ $dept->available_count }}</td>
                <td>{{ $dept->repair_count }}</td>
                <td>{{ $dept->missing_count }}</td>
                @if ($showFinancial ?? true)
                    <td>RM {{ number_format($dept->purchase_value, 2) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
