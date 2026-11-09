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
                <th>Software Title</th>
                <th>Purchased Seats</th>
                <th>Assigned Seats</th>
                <th>Available Seats</th>
                <th>Utilization (%)</th>
                <th>Compliance Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $lic)
            <tr>
                <td><strong>{{ $lic->software_name }}</strong></td>
                <td>{{ $lic->purchased_seats }}</td>
                <td>{{ $lic->assigned_seats }}</td>
                <td>{{ $lic->available_seats }}</td>
                <td>{{ $lic->utilization_percent }}%</td>
                <td>{{ $lic->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
