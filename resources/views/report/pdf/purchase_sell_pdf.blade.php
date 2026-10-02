<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('report.purchase_sell') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e4b6e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .business-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e4b6e;
        }
        .report-title {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            margin-top: 4px;
        }
        .meta-info {
            margin-top: 6px;
            font-size: 11px;
            color: #6b7280;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .table th {
            background-color: #1e4b6e;
            color: #ffffff;
            font-weight: 600;
            font-size: 11px;
            padding: 8px 10px;
            text-align: left;
        }
        .table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 12px;
        }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-title">{{ $business->name ?? __('report.purchase_sell') }}</div>
        <div class="report-title">{{ __('report.purchase_sell') }}</div>
        <div class="meta-info">
            <strong>Date range:</strong> {{ $meta['date_range'] }} |
            <strong>Location:</strong> {{ $meta['location'] }} |
            <strong>Generated:</strong> {{ $meta['generated_at'] }}
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Metric</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="text-right">{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
