<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #3b4048; margin: 20px; }
        .header { border-bottom: 2px solid #8f2428; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { color: #a92d31; margin: 0; font-size: 20px; }
        .header p { color: #69717d; margin: 3px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #8f2428; color: #ffffff; padding: 7px 9px; text-align: left; font-size: 11px; }
        td { padding: 6px 9px; border-bottom: 1px solid #eadada; font-size: 10px; }
        tr:nth-child(even) td { background-color: #fbf8f8; }
        .badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; }
        .badge-assigned { background: #dbeafe; color: #1e40af; }
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-repair { background: #fef3c7; color: #92400e; }
        .badge-retired { background: #f1f5f9; color: #475569; }
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
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Serial Number</th>
                <th>Assigned To</th>
                <th>Location</th>
                @if ($showFinancial ?? true)
                    <th>Cost (MYR)</th>
                @endif
                <th>Warranty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $asset)
            <tr>
                <td><strong>{{ $asset->id }}</strong></td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->serial_number }}</td>
                <td>{{ $asset->assigned_user ?: '-' }}</td>
                <td>{{ $asset->assigned_location }}</td>
                @if ($showFinancial ?? true)
                    <td>RM {{ number_format($asset->purchase_cost, 2) }}</td>
                @endif
                <td>{{ $asset->warranty_end ?: '-' }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($asset->status) }}">{{ $asset->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
