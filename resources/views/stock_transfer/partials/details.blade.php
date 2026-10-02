@php
    $theme_color = session('business.theme_color', 'blue');
    $color_map = [
        'blue' => '#3c8dbc',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'orange' => '#ff851b',
        'sky' => '#00c0ef',
        'primary' => '#3c8dbc'
    ];
    $primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';

    $display_status = $sell_transfer->status == 'final' ? 'completed' : $sell_transfer->status;
    $status_label = $statuses[$display_status] ?? $statuses[$sell_transfer->status] ?? ucfirst($display_status);

    $status_badge_class = 'badge-secondary';
    $status_bg = '#f1f5f9';
    $status_fg = '#475569';
    $status_border = '#cbd5e1';

    if (in_array($display_status, ['completed', 'final'])) {
        $status_bg = '#ecfdf5';
        $status_fg = '#065f46';
        $status_border = '#a7f3d0';
    } elseif ($display_status == 'in_transit') {
        $status_bg = '#fffbeb';
        $status_fg = '#92400e';
        $status_border = '#fde68a';
    } elseif ($display_status == 'pending') {
        $status_bg = '#fef2f2';
        $status_fg = '#991b1b';
        $status_border = '#fecaca';
    }

    $total_qty = 0;
    foreach ($sell_transfer->sell_lines as $line) {
        $total_qty += $line->quantity;
    }
@endphp

<style>
    .st-details-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: #1e293b;
    }
    .st-meta-strip {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .st-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid;
    }
    .st-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px 20px;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .st-card-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .st-location-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .st-location-meta {
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
    }
    .st-timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .st-timeline-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .st-timeline-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 12px;
    }
    .st-table-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 20px;
        margin-bottom: 20px;
        background: #ffffff;
    }
    .st-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .st-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
    }
    .st-table tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #1e293b !important;
        font-size: 13px !important;
        vertical-align: middle !important;
    }
    .st-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .st-table tfoot td, .st-table tfoot th {
        background: #f8fafc !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        color: #0f172a !important;
        padding: 12px 16px !important;
        border-top: 2px solid #e2e8f0 !important;
    }
    .st-callout {
        background: #f8fafc;
        border-left: 4px solid {{ $primary_hex }};
        padding: 12px 16px;
        border-radius: 0 8px 8px 0;
        margin-top: 16px;
        font-size: 13px;
        color: #334155;
    }
    @media (max-width: 768px) {
        .st-meta-strip {
            flex-direction: column;
            align-items: flex-start;
        }
        .st-card {
            margin-bottom: 16px;
        }
    }
</style>

<div class="st-details-container">
    <!-- Meta Summary Strip -->
    <div class="st-meta-strip">
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div>
                <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.05em; display: block;">@lang('purchase.ref_no')</span>
                <span style="font-size: 18px; font-weight: 800; color: #0f172a;">#{{ $sell_transfer->ref_no }}</span>
            </div>

            <div class="st-meta-pill" style="background: {{ $status_bg }}; color: {{ $status_fg }}; border-color: {{ $status_border }};">
                @if($display_status == 'in_transit')
                    <i class="fa fa-truck"></i>
                @elseif(in_array($display_status, ['completed', 'final']))
                    <i class="fa fa-check-circle"></i>
                @else
                    <i class="fa fa-clock-o"></i>
                @endif
                {{ $status_label }}
            </div>

            @if(!empty($product_request))
                <div class="st-meta-pill" style="background: #eff6ff; color: #1e40af; border-color: #bfdbfe;">
                    <i class="fa fa-file-text-o"></i>
                    @lang('Requisition') #{{ $product_request->request_no }}
                </div>
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: #475569;">
            <div>
                <strong style="color: #0f172a;">@lang('messages.date'):</strong>
                {{ @format_date($sell_transfer->transaction_date) }}
            </div>
            <div>
                <strong style="color: #0f172a;">@lang('Total Items'):</strong>
                {{ @format_quantity($total_qty) }}
            </div>
        </div>
    </div>

    <!-- Location & Dispatch Routing Grid -->
    <div class="row">
        <!-- Origin / Source Location -->
        <div class="col-sm-4" style="margin-bottom: 16px;">
            <div class="st-card">
                <div class="st-card-title">
                    <i class="fa fa-arrow-circle-up text-primary" style="font-size: 14px;"></i>
                    @lang('lang_v1.location_from') (@lang('Source Main Store'))
                </div>
                <div class="st-location-name">{{ $location_details['sell']->name }}</div>
                <div class="st-location-meta">
                    @if(!empty($location_details['sell']->landmark))
                        <div><i class="fa fa-map-marker text-muted" style="width: 14px;"></i> {{ $location_details['sell']->landmark }}</div>
                    @endif
                    @if(!empty($location_details['sell']->city) || !empty($location_details['sell']->state) || !empty($location_details['sell']->country))
                        <div>{{ implode(', ', array_filter([$location_details['sell']->city, $location_details['sell']->state, $location_details['sell']->country])) }}</div>
                    @endif
                    @if(!empty($location_details['sell']->mobile))
                        <div style="margin-top: 4px;"><i class="fa fa-phone text-muted" style="width: 14px;"></i> {{ $location_details['sell']->mobile }}</div>
                    @endif
                    @if(!empty($location_details['sell']->email))
                        <div><i class="fa fa-envelope text-muted" style="width: 14px;"></i> {{ $location_details['sell']->email }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Destination / Target Location -->
        <div class="col-sm-4" style="margin-bottom: 16px;">
            <div class="st-card">
                <div class="st-card-title">
                    <i class="fa fa-arrow-circle-down text-success" style="font-size: 14px;"></i>
                    @lang('lang_v1.location_to') (@lang('Destination Shop'))
                </div>
                <div class="st-location-name">{{ $location_details['purchase']->name ?? 'N/A' }}</div>
                <div class="st-location-meta">
                    @if(!empty($location_details['purchase']->landmark))
                        <div><i class="fa fa-map-marker text-muted" style="width: 14px;"></i> {{ $location_details['purchase']->landmark }}</div>
                    @endif
                    @if(!empty($location_details['purchase']->city) || !empty($location_details['purchase']->state) || !empty($location_details['purchase']->country))
                        <div>{{ implode(', ', array_filter([$location_details['purchase']->city, $location_details['purchase']->state, $location_details['purchase']->country])) }}</div>
                    @endif
                    @if(!empty($location_details['purchase']->mobile))
                        <div style="margin-top: 4px;"><i class="fa fa-phone text-muted" style="width: 14px;"></i> {{ $location_details['purchase']->mobile }}</div>
                    @endif
                    @if(!empty($location_details['purchase']->email))
                        <div><i class="fa fa-envelope text-muted" style="width: 14px;"></i> {{ $location_details['purchase']->email }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transfer Lifecycle & Dates -->
        <div class="col-sm-4" style="margin-bottom: 16px;">
            <div class="st-card">
                <div class="st-card-title">
                    <i class="fa fa-history text-info" style="font-size: 14px;"></i>
                    @lang('Lifecycle & Delivery')
                </div>

                <div class="st-timeline-item">
                    <div class="st-timeline-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <div style="font-size: 12px; line-height: 1.3;">
                        <span style="color: #64748b; font-weight: 600;">@lang('Created / Requested'):</span>
                        <div style="color: #0f172a; font-weight: 700;">{{ @format_datetime($sell_transfer->transaction_date) }}</div>
                    </div>
                </div>

                <div class="st-timeline-item">
                    <div class="st-timeline-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <div style="font-size: 12px; line-height: 1.3;">
                        <span style="color: #64748b; font-weight: 600;">@lang('Dispatched Date'):</span>
                        @if(!empty($product_request) && !empty($product_request->dispatched_at))
                            <div style="color: #0f172a; font-weight: 700;">{{ @format_datetime($product_request->dispatched_at) }}</div>
                            @if($product_request->dispatcher)
                                <div style="color: #64748b; font-size: 11px;">by {{ $product_request->dispatcher->user_full_name }}</div>
                            @endif
                        @else
                            <div style="color: #0f172a; font-weight: 700;">{{ @format_datetime($sell_transfer->transaction_date) }}</div>
                        @endif
                    </div>
                </div>

                <div class="st-timeline-item">
                    <div class="st-timeline-icon" style="background: {{ (!empty($product_request) && !empty($product_request->received_at)) || in_array($display_status, ['completed', 'final']) ? '#dcfce7; color: #16a34a;' : '#f1f5f9; color: #94a3b8;' }}">
                        <i class="fa fa-inbox"></i>
                    </div>
                    <div style="font-size: 12px; line-height: 1.3;">
                        <span style="color: #64748b; font-weight: 600;">@lang('Received Date'):</span>
                        @if(!empty($product_request) && !empty($product_request->received_at))
                            <div style="color: #16a34a; font-weight: 700;">{{ @format_datetime($product_request->received_at) }}</div>
                            @if($product_request->receiver)
                                <div style="color: #64748b; font-size: 11px;">by {{ $product_request->receiver->user_full_name }}</div>
                            @endif
                        @elseif(in_array($display_status, ['completed', 'final']))
                            <div style="color: #16a34a; font-weight: 700;">@lang('Received & Stock Updated')</div>
                        @else
                            <div style="color: #d97706; font-weight: 600;">@lang('Pending Physical Receipt')</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Transferred Table -->
    <div class="st-table-wrapper table-responsive">
        <table class="table st-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th>@lang('sale.product')</th>
                    <th style="text-align: right; width: 140px;">@lang('sale.qty')</th>
                    <th class="@cannot('view_purchase_price') show_price_with_permission no-print @endcan" style="text-align: right; width: 150px;">
                        @lang('sale.subtotal')
                    </th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $total = 0.00;
                @endphp
                @forelse($sell_transfer->sell_lines as $sell_lines)
                    <tr>
                        <td style="text-align: center; color: #64748b; font-weight: 600;">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $sell_lines->product->name ?? 'N/A' }}</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                @if(!empty($sell_lines->product) && $sell_lines->product->type == 'variable')
                                    <span style="color: #3b82f6; font-weight: 500;">
                                        {{ $sell_lines->variations->product_variation->name ?? '' }} - {{ $sell_lines->variations->name ?? '' }}
                                    </span>
                                    <span style="color: #cbd5e1; margin: 0 4px;">•</span>
                                @endif
                                <span><strong>SKU:</strong> {{ $sell_lines->variations->sub_sku ?? 'N/A' }}</span>

                                @if($lot_n_exp_enabled && !empty($sell_lines->lot_details))
                                    <span style="color: #cbd5e1; margin: 0 4px;">•</span>
                                    @if(!empty($sell_lines->lot_details->lot_number))
                                        <strong>@lang('lang_v1.lot_number'):</strong> {{ $sell_lines->lot_details->lot_number }}
                                    @endif
                                    @if(!empty($sell_lines->lot_details->exp_date))
                                        <strong>@lang('lang_v1.expiry'):</strong> {{ @format_date($sell_lines->lot_details->exp_date) }}
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a; font-size: 14px;">
                            {{ @format_quantity($sell_lines->quantity) }} 
                            <span style="font-size: 11px; font-weight: 500; color: #64748b;">
                                @if(!empty($sell_lines->sub_unit)) 
                                    {{ $sell_lines->sub_unit->short_name }} 
                                @else 
                                    {{ $sell_lines->product->unit->short_name ?? '' }} 
                                @endif
                            </span>
                        </td>
                        <td class="@cannot('view_purchase_price') show_price_with_permission no-print @endcan" style="text-align: right; font-weight: 700; color: #0f172a;">
                            <span class="display_currency" data-currency_symbol="true">{{ $sell_lines->unit_price_inc_tax * $sell_lines->quantity }}</span>
                        </td>
                    </tr>
                    @php 
                        $total += ($sell_lines->unit_price_inc_tax * $sell_lines->quantity);
                    @endphp
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">
                            @lang('lang_v1.no_products_added')
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: 700; color: #334155;">
                        @lang('Total Quantity Transferred'):
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #0f172a; font-size: 14px;">
                        {{ @format_quantity($total_qty) }}
                    </td>
                    <td class="@cannot('view_purchase_price') show_price_with_permission no-print @endcan" style="text-align: right; font-weight: 800; color: #0f172a; font-size: 14px;">
                        <span class="display_currency" data-currency_symbol="true">{{ $sell_transfer->final_total ?? $total }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Notes & Shipping Details Grid -->
    <div class="row">
        <div class="col-sm-6">
            @if(!empty($sell_transfer->additional_notes) || (!empty($product_request) && !empty($product_request->notes)))
                <div class="st-card" style="margin-bottom: 16px;">
                    <div class="st-card-title">
                        <i class="fa fa-sticky-note-o text-warning" style="font-size: 14px;"></i>
                        @lang('purchase.additional_notes')
                    </div>
                    <div style="font-size: 13px; color: #334155; line-height: 1.5;">
                        @if(!empty($sell_transfer->additional_notes))
                            <p style="margin-bottom: 8px;">{{ $sell_transfer->additional_notes }}</p>
                        @endif
                        @if(!empty($product_request) && !empty($product_request->notes))
                            <p style="margin-bottom: 0; color: #475569; font-style: italic;">
                                <strong>@lang('Requisition Note'):</strong> {{ $product_request->notes }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($sell_transfer->shipping_details) || (!empty($product_request) && !empty($product_request->shipping_details)))
                <div class="st-card" style="margin-bottom: 16px;">
                    <div class="st-card-title">
                        <i class="fa fa-truck text-info" style="font-size: 14px;"></i>
                        @lang('Shipping / Courier Information')
                    </div>
                    <div style="font-size: 13px; color: #334155; line-height: 1.5;">
                        {{ $sell_transfer->shipping_details ?: ($product_request->shipping_details ?? '--') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-sm-6">
            @if(!empty($activities) && $activities->count() > 0)
                <div class="st-card" style="margin-bottom: 16px;">
                    <div class="st-card-title">
                        <i class="fa fa-clock-o text-muted" style="font-size: 14px;"></i>
                        @lang('lang_v1.activities')
                    </div>
                    <div style="max-height: 180px; overflow-y: auto;">
                        @includeIf('activity_log.activities', ['activity_type' => 'sell'])
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Barcode Section (for scanning and printouts) -->
    <div class="row print_section" style="margin-top: 20px; text-align: center;">
        <div class="col-xs-12">
            <img class="center-block" style="margin: 0 auto; display: inline-block;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($sell_transfer->ref_no, 'C128', 2, 30, array(39, 48, 54), true)}}">
            <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-top: 4px;">#{{ $sell_transfer->ref_no }}</div>
        </div>
    </div>
</div>