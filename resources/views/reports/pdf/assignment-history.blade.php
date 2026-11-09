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
                <th>Type</th>
                <th>Asset ID</th>
                <th>From Custodian</th>
                <th>To Custodian</th>
                <th>Location</th>
                <th>Date</th>
                <th>Performed By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $tx)
            <tr>
                <td><strong>{{ $tx->type }}</strong></td>
                <td>{{ $tx->asset_id }}</td>
                <td>{{ $tx->from_user ?: '-' }}</td>
                <td>{{ $tx->to_user ?: '-' }}</td>
                <td>{{ $tx->to_location ?: $tx->from_location }}</td>
                <td>{{ $tx->transaction_date }}</td>
                <td>{{ $tx->performed_by }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:15px; color:#69717d;">No transaction logs recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
