<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Analytics Report</title>
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
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .business-title {
            font-size: 20px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin-top: 4px;
        }
        .meta-info {
            margin-top: 6px;
            font-size: 11px;
            color: #6b7280;
        }
        .kpi-grid {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .kpi-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-top: 4px;
        }
        .section-heading {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 10px;
            border-left: 4px solid #4f46e5;
            padding-left: 8px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .table th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            font-size: 11px;
            padding: 8px 10px;
            text-align: left;
        }
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-title">{{ $business->name ?? 'Sales Analytics Report' }}</div>
        <div class="report-title">Sales Analytics & Grouped Performance Report</div>
        <div class="meta-info">
            <strong>Date Range:</strong> {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }} | 
            <strong>Grouping:</strong> {{ ucfirst($group_by) }} | 
            <strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}
        </div>
    </div>

    <!-- KPI Summary Section -->
    <div class="section-heading">Executive Summary</div>
    <table class="kpi-grid">
        <tr>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Total Sales Amount</div>
                <div class="kpi-value" style="color: #4f46e5;">{{ $summary['formatted_total_sales'] }}</div>
            </td>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Total Orders</div>
                <div class="kpi-value">{{ $summary['total_orders'] }}</div>
            </td>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Total Items Sold</div>
                <div class="kpi-value">{{ $summary['formatted_total_items_sold'] }}</div>
            </td>
        </tr>
        <tr><td colspan="3" style="height: 10px;"></td></tr>
        <tr>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Average Order Value</div>
                <div class="kpi-value">{{ $summary['formatted_avg_order_value'] }}</div>
            </td>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Total Discounts</div>
                <div class="kpi-value" style="color: #dc2626;">{{ $summary['formatted_total_discounts'] }}</div>
            </td>
            <td width="33%" class="kpi-card">
                <div class="kpi-label">Total Tax</div>
                <div class="kpi-value" style="color: #2563eb;">{{ $summary['formatted_total_tax'] }}</div>
            </td>
        </tr>
    </table>

    <!-- Grouped Sales Table -->
    <div class="section-heading">Sales Grouped by {{ ucfirst($group_by) }}</div>
    <table class="table">
        <thead>
            <tr>
                <th>Period / Date</th>
                <th class="text-center">Orders Count</th>
                <th class="text-right">Items Sold</th>
                <th class="text-right">Total Sales Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($period_records as $rec)
                <tr>
                    <td><strong>{{ $rec['period_label'] }}</strong></td>
                    <td class="text-center">{{ $rec['order_count'] }}</td>
                    <td class="text-right">{{ $rec['formatted_items'] }}</td>
                    <td class="text-right" style="font-weight: bold; color: #4f46e5;">{{ $rec['formatted_sales'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px; color: #9ca3af;">
                        No sales transactions recorded for the selected period filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential &mdash; Generated automatically by Sales Analytics System
    </div>
</body>
</html>
