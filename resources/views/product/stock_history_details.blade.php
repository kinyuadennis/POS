@php
    $common_settings = session()->get('business.common_settings');
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
@endphp

<style>
    .sh-summary-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
        height: 100%;
    }
    .sh-summary-card-header {
        padding: 14px 18px;
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .sh-summary-card-header.in {
        background-color: #ecfdf5;
        color: #047857;
        border-top: 4px solid #10b981;
    }
    .sh-summary-card-header.out {
        background-color: #fef2f2;
        color: #b91c1c;
        border-top: 4px solid #ef4444;
    }
    .sh-summary-card-header.total {
        background-color: #f8fafc;
        color: #0f172a;
        border-top: 4px solid #334155;
    }
    .sh-summary-card-body {
        padding: 16px 18px;
    }
    .sh-summary-table {
        width: 100%;
        margin-bottom: 0;
    }
    .sh-summary-table tr th {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        padding: 7px 0;
        border-bottom: 1px dashed #f1f5f9;
    }
    .sh-summary-table tr td {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        text-align: right;
        padding: 7px 0;
        border-bottom: 1px dashed #f1f5f9;
    }
    .sh-summary-table tr:last-child th,
    .sh-summary-table tr:last-child td {
        border-bottom: none;
    }

    .sh-datatable-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 24px;
        margin-bottom: 24px;
    }
    .sh-datatable-header {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sh-table-responsive {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    #stock_history_table {
        margin-bottom: 0 !important;
    }
    #stock_history_table thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 12px 14px !important;
    }
    #stock_history_table tbody td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        font-size: 13.5px !important;
        color: #1e293b !important;
        white-space: nowrap !important;
    }
    #stock_history_table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .badge-history-type {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .badge-history-add {
        background-color: #d1fae5;
        color: #065f46;
    }
    .badge-history-sub {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-history-neutral {
        background-color: #e0f2fe;
        color: #075985;
    }
</style>

<div class="row">
    <div class="col-md-12" style="margin-bottom: 16px;">
        <h3 class="tw-text-lg tw-font-bold tw-text-black" style="margin: 0;">
            <i class="fa fa-cubes tw-mr-1" style="color: {{ $primary_hex }};"></i>
            {{$stock_details['variation']}}
        </h3>
    </div>
</div>

<div class="row" style="margin-bottom: 8px;">
    <div class="col-md-4 col-sm-6" style="margin-bottom: 16px;">
        <div class="sh-summary-card">
            <div class="sh-summary-card-header in">
                <i class="fa fa-arrow-circle-down"></i>
                @lang('lang_v1.quantities_in')
            </div>
            <div class="sh-summary-card-body">
                <table class="sh-summary-table">
                    <tr>
                        <th>@lang('report.total_purchase')</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang_v1.opening_stock')</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_opening_stock']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang_v1.total_sell_return')</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_return']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.in'))</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_transfer']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('report.total_stock_adjustment') (@lang('messages.add'))</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_adjusted_add']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6" style="margin-bottom: 16px;">
        <div class="sh-summary-card">
            <div class="sh-summary-card-header out">
                <i class="fa fa-arrow-circle-up"></i>
                @lang('lang_v1.quantities_out')
            </div>
            <div class="sh-summary-card-body">
                <table class="sh-summary-table">
                    <tr>
                        <th>@lang('lang_v1.total_sold')</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_sold']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('report.total_stock_adjustment') (Sub)</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_adjusted_sub']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang_v1.total_purchase_return')</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_return']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.out'))</th>
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_transfer']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-12" style="margin-bottom: 16px;">
        <div class="sh-summary-card">
            <div class="sh-summary-card-header total">
                <i class="fa fa-balance-scale"></i>
                @lang('lang_v1.totals')
            </div>
            <div class="sh-summary-card-body">
                <table class="sh-summary-table">
                    <tr>
                        <th>@lang('report.current_stock')</th>
                        <td>
                            <span class="display_currency tw-text-lg tw-font-bold" data-is_quantity="true" style="color: #0f172a;">{{$stock_details['current_stock']}}</span> {{$stock_details['unit']}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="sh-datatable-card">
    <div class="tw-flex tw-justify-between tw-items-center tw-mb-5 tw-gap-4 flex-wrap" style="margin-bottom: 20px;">
        <h3 class="sh-datatable-header" style="margin-bottom: 0;">
            <i class="fa fa-history" style="color: {{ $primary_hex }};"></i>
            Stock Movement History
        </h3>
        <div class="tw-flex tw-items-center tw-gap-3 flex-wrap">
            <div class="tw-flex tw-items-center tw-gap-2" style="margin-right: 12px;">
                <label for="custom_stock_history_length" class="tw-text-xs md:tw-text-sm tw-font-semibold tw-text-gray-700 tw-m-0">Show</label>
                <select id="custom_stock_history_length" class="form-control input-sm tw-w-auto" style="height: 36px !important; border-radius: 8px !important; padding: 4px 10px !important; margin: 0 4px !important; border: 1px solid #cbd5e1 !important; display: inline-block;">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="-1">All</option>
                </select>
                <span class="tw-text-xs md:tw-text-sm tw-font-semibold tw-text-gray-700">entries</span>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <button type="button" class="btn btn-sm btn-default stock-history-export-btn" data-format="excel" style="height: 36px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; background-color: #ffffff;">
                    <i class="fa fa-file-excel-o text-success" style="color: #10b981;"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-sm btn-default stock-history-export-btn" data-format="pdf" style="height: 36px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; background-color: #ffffff;">
                    <i class="fa fa-file-pdf-o text-danger" style="color: #ef4444;"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive sh-table-responsive">
        <table class="table table-slim" id="stock_history_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('lang_v1.type')</th>
                    <th>@lang('lang_v1.quantity_change')</th>
                    @if(!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.quantity_change') (@lang('lang_v1.secondary_unit'))</th>
                    @endif
                    <th>@lang('lang_v1.new_quantity')</th>
                    @if(!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.new_quantity') (@lang('lang_v1.secondary_unit'))</th>
                    @endif
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('lang_v1.customer_supplier_info')</th>
                </tr>
            </thead>
            <tbody>
            @forelse($stock_history as $history)
                <tr>
                    @php
                        $badge_class = 'badge-history-neutral';
                        $val_color_style = 'color: #075985;';
                        if (strpos($history['type_label'], '(Add)') !== false || $history['quantity_change'] > 0) {
                            $badge_class = 'badge-history-add';
                            $val_color_style = 'color: #065f46;';
                        } elseif (strpos($history['type_label'], '(Sub)') !== false || $history['quantity_change'] < 0) {
                            $badge_class = 'badge-history-sub';
                            $val_color_style = 'color: #991b1b;';
                        }
                    @endphp
                    <td>
                        <span class="badge-history-type {{ $badge_class }}">{{$history['type_label']}}</span>
                    </td>
                    @if($history['quantity_change'] > 0 )
                        <td class="tw-font-bold" style="{{ $val_color_style }}"> +<span class="display_currency" data-is_quantity="true" style="{{ $val_color_style }}">{{$history['quantity_change']}}</span>
                        </td>
                    @else
                        <td class="tw-font-bold" style="{{ $val_color_style }}"><span class="display_currency" data-is_quantity="true" style="{{ $val_color_style }}">{{$history['quantity_change']}}</span>
                        </td>
                    @endif

                    @if(!empty($common_settings['enable_secondary_unit']))
                        @if($history['quantity_change'] > 0 )
                            <td class="tw-font-bold" style="{{ $val_color_style }}">
                                @if(!empty($history['purchase_secondary_unit_quantity']))
                                    +<span class="display_currency" data-is_quantity="true" style="{{ $val_color_style }}">{{$history['purchase_secondary_unit_quantity']}}</span> {{$stock_details['second_unit']}}
                                @endif
                            </td>
                        @else
                            <td class="tw-font-bold" style="{{ $val_color_style }}">
                                @if(!empty($history['sell_secondary_unit_quantity']))
                                    -<span class="display_currency" data-is_quantity="true" style="{{ $val_color_style }}">{{$history['sell_secondary_unit_quantity']}}</span> {{$stock_details['second_unit']}}
                                @endif
                            </td>
                        @endif
                    @endif
                    <td class="tw-font-bold">
                        <span class="display_currency" data-is_quantity="true">{{$history['stock']}}</span>
                    </td>
                    @if(!empty($common_settings['enable_secondary_unit']))
                        <td>
                            @if(!empty($stock_details['second_unit']))
                                <span class="display_currency" data-is_quantity="true">{{$history['stock_in_second_unit']}}</span> {{$stock_details['second_unit']}}
                            @endif
                        </td>
                    @endif
                    <td>{{@format_datetime($history['date'])}}</td>
                    <td>
                        <span class="tw-font-medium">{{$history['ref_no']}}</span>
                        @if(!empty($history['additional_notes']))
                            @if(!empty($history['ref_no']))
                            <br>
                            @endif
                            <small class="text-muted">{{$history['additional_notes']}}</small>
                        @endif
                    </td>
                    <td>
                        {{$history['contact_name'] ?? '--'}} 
                        @if(!empty($history['supplier_business_name']))
                         - {{$history['supplier_business_name']}}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ !empty($common_settings['enable_secondary_unit']) ? 8 : 6 }}" class="text-center text-muted" style="padding: 24px !important;">
                        <i class="fa fa-info-circle fa-lg tw-mr-1"></i>
                        @lang('lang_v1.no_stock_history_found')
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
