<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Alert - {{ config('author.product_name', 'ImaraPOS') }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a; /* Slate 900 */
            padding: 32px;
            text-align: center;
            border-bottom: 3px solid #d97706; /* Amber 600 */
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            color: #94a3b8;
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .content {
            padding: 32px;
        }
        .intro {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
            color: #334155;
        }
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        .stock-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 12px 16px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }
        .stock-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            line-height: 1.5;
        }
        .product-name {
            font-weight: 600;
            color: #0f172a;
        }
        .product-sku {
            font-size: 12px;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }
        .badge-location {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        .stock-low {
            color: #dc2626;
            font-weight: 700;
            background-color: #fef2f2;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }
        .alert-limit {
            color: #475569;
        }
        .cta-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .btn-cta {
            background-color: #d97706; /* Amber 600 */
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            display: inline-block;
            font-size: 15px;
            box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.2), 0 2px 4px -1px rgba(217, 119, 6, 0.1);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>⚠️ Low Stock Alert</h1>
                <p>{{ $business->name }} &bull; Inventory Replenishment Report</p>
            </div>
            <div class="content">
                <p class="intro">Hello,</p>
                <p class="intro">This is an automated notification to inform you that the following perfume products have dropped below their defined alert thresholds. Please review and coordinate with your suppliers to replenish stock.</p>
                
                <table class="stock-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Location</th>
                            <th style="text-align: center;">Current Stock</th>
                            <th style="text-align: center;">Alert Limit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    <span class="product-name">
                                        @if ($item->type == 'single')
                                            {{ $item->product }}
                                        @else
                                            {{ $item->product }} - {{ $item->product_variation }} - {{ $item->variation }}
                                        @endif
                                    </span>
                                    <span class="product-sku">SKU: {{ $item->sub_sku ?: $item->sku }}</span>
                                </td>
                                <td>
                                    <span class="badge-location">{{ $item->location }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="stock-low">{{ (float) $item->stock }} {{ $item->unit }}</span>
                                </td>
                                <td style="text-align: center;" class="alert-limit">
                                    {{ (float) $item->alert_quantity }} {{ $item->unit }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="cta-container">
                    <a href="{{ config('app.url') }}" class="btn-cta">Open POS Dashboard</a>
                </div>
            </div>
            <div class="footer">
                <p>This is a system generated email from your <strong>{{ config('author.product_name', 'ImaraPOS') }}</strong>.</p>
                <p>&copy; {{ date('Y') }} {{ $business->name }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
