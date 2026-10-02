@extends('layouts.app')
@section('title', __('lang_v1.product_sell_report'))

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
@endphp

@section('css')
<style>
    /* Header styling */
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    /* Page container */
    .product-sell-report-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    /* Cards */
    .search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        position: relative;
        z-index: 25;
        overflow: visible;
    }

    .datatable-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    .search-section .form-group {
        margin-bottom: 0;
    }

    .search-section label {
        font-weight: 500;
        color: #475569;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .search-section .form-control,
    .search-section .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        height: 42px !important;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
    }

    .search-section .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
    }

    .search-section .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .psr-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .psr-filter-field {
        min-width: 0;
        width: 100%;
    }

    #collapsible_psr_filters {
        display: none;
        margin-bottom: 16px;
    }

    #toggle_psr_filters {
        height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 0 16px !important;
        border-radius: 8px !important;
        border: 1px solid {{ $primary_hex }} !important;
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease-in-out !important;
    }

    #toggle_psr_filters:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    #toggle_psr_filters.filter-active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
        opacity: 1 !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    @media (min-width: 768px) {
        .psr-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .psr-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .product-sell-report-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
    }

    /* Hide default DataTables controls */
    #product_sell_report_table_wrapper > .row:first-child,
    #product_sell_report_table_wrapper .dataTables_length,
    #product_sell_report_table_wrapper .dt-buttons,
    #product_sell_report_table_wrapper .dataTables_filter,
    #product_sell_report_with_purchase_table_wrapper > .row:first-child,
    #product_sell_report_with_purchase_table_wrapper .dataTables_length,
    #product_sell_report_with_purchase_table_wrapper .dt-buttons,
    #product_sell_report_with_purchase_table_wrapper .dataTables_filter,
    #product_sell_grouped_report_table_wrapper > .row:first-child,
    #product_sell_grouped_report_table_wrapper .dataTables_length,
    #product_sell_grouped_report_table_wrapper .dt-buttons,
    #product_sell_grouped_report_table_wrapper .dataTables_filter,
    #product_sell_report_by_category_wrapper > .row:first-child,
    #product_sell_report_by_category_wrapper .dataTables_length,
    #product_sell_report_by_category_wrapper .dt-buttons,
    #product_sell_report_by_category_wrapper .dataTables_filter,
    #product_sell_report_by_brand_wrapper > .row:first-child,
    #product_sell_report_by_brand_wrapper .dataTables_length,
    #product_sell_report_by_brand_wrapper .dt-buttons,
    #product_sell_report_by_brand_wrapper .dataTables_filter {
        display: none !important;
    }

    /* Modern tabs styling */
    .psr-nav-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 2px solid #e2e8f0 !important;
        margin-bottom: 20px;
        padding-bottom: 0;
        list-style: none;
        padding-left: 0;
    }
    .psr-nav-tabs > li {
        margin-bottom: -2px;
    }
    .psr-nav-tabs > li > a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px !important;
        font-size: 14px;
        font-weight: 600;
        color: #64748b !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        background: transparent !important;
        border-radius: 8px 8px 0 0 !important;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }
    .psr-nav-tabs > li > a:hover {
        color: {{ $primary_hex }} !important;
        background-color: {{ $primary_hex }}0a !important;
    }
    .psr-nav-tabs > li.active > a,
    .psr-nav-tabs > li.active > a:hover,
    .psr-nav-tabs > li.active > a:focus {
        color: {{ $primary_hex }} !important;
        border-bottom: 3px solid {{ $primary_hex }} !important;
        background-color: transparent !important;
        font-weight: 700;
    }

    /* Responsive table */
    .psr-table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #ffffff;
        margin-bottom: 0;
    }

    .psr-table-responsive table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0 !important;
    }

    .psr-table-responsive th,
    .psr-table-responsive th *,
    .psr-table-responsive td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .psr-table-responsive tfoot tr.footer-total td {
        background-color: #f8fafc !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        border-top: 2px solid #cbd5e1 !important;
    }

    .dataTables_processing,
    #product_sell_report_table_processing,
    #product_sell_report_with_purchase_table_processing,
    #product_sell_grouped_report_table_processing {
        display: none !important;
    }

    /* Skeleton rows */
    .skeleton-row td {
        padding: 16px !important;
    }

    .skeleton-block {
        height: 16px;
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .daterangepicker.psr-daterangepicker {
        z-index: 4000;
    }
    .daterangepicker.psr-daterangepicker .drp-buttons {
        display: block !important;
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 2;
    }
</style>
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">{{ __('lang_v1.product_sell_report') }}</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Detailed report of sold products and performance metrics</p>
    </div>
</section>

<!-- Main content -->
<section class="content product-sell-report-content no-print">
    <!-- Search, Action & Filters Box -->
    <div class="search-section">
        {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'product_sell_report_form' ]) !!}
        <!-- Row 1: Search & Filter Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="hidden" value="" id="variation_id">
                    {!! Form::text('search_product', null, [
                        'class' => 'form-control',
                        'id' => 'search_product',
                        'placeholder' => __('lang_v1.search_product_placeholder'),
                        'style' => 'padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;',
                        'autofocus'
                    ]); !!}
                </div>
            </div>
            <button type="button" id="toggle_psr_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_psr_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
        </div>

        <!-- Collapsible Filters Panel -->
        <div id="collapsible_psr_filters">
            <div class="psr-filters-row">
                <div class="form-group psr-filter-field">
                    {!! Form::label('location_id', __('purchase.business_location').':') !!}
                    <div class="input-group" style="width: 100%;">
                        {!! Form::select('location_id', $business_locations, $default_location_id ?? null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'id' => 'location_id', 'style' => 'width:100%', 'data-location-filter' => '1']); !!}
                    </div>
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('product_sr_date_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'product_sr_date_filter', 'readonly']); !!}
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('product_sr_start_time', __('lang_v1.time_range') . ':') !!}
                    @php
                        $startDay = Carbon::now()->startOfDay();
                        $endDay   = $startDay->copy()->endOfDay();
                    @endphp
                    <div style="display: flex; gap: 8px;">
                        {!! Form::text('start_time', @format_time($startDay), ['class' => 'form-control', 'style' => 'width: 50%;', 'id' => 'product_sr_start_time']); !!}
                        {!! Form::text('end_time', @format_time($endDay), ['class' => 'form-control', 'style' => 'width: 50%;', 'id' => 'product_sr_end_time']); !!}
                    </div>
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                    <div class="input-group" style="width: 100%;">
                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'id' => 'customer_id', 'style' => 'width:100%', 'data-depends-on-location' => 'customers']); !!}
                    </div>
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('psr_customer_group_id', __( 'lang_v1.customer_group_name' ) . ':') !!}
                    {!! Form::select('psr_customer_group_id', $customer_group, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'psr_customer_group_id']); !!}
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('category_id', __('product.category') . ':') !!}
                    {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'psr_filter_category_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
                <div class="form-group psr-filter-field">
                    {!! Form::label('brand_id', __('product.brand') . ':') !!}
                    {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'psr_filter_brand_id', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        </div>
        {!! Form::close() !!}

        <!-- Row 2: Show Entries & Export -->
        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_psr_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="-1">@lang('lang_v1.all')</option>
                    </select>
                    @lang('lang_v1.entries')
                </label>
            </div>

            <div style="display: inline-block;">
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px;">
                        <i class="fa fa-share-square"></i> @lang('lang_v1.export') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 8px; min-width: 120px;">
                        <li><a href="#" class="psr-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="psr-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="datatable-section">
        <!-- Navigation Tabs -->
        <ul class="nav psr-nav-tabs">
            <li class="active">
                <a href="#psr_detailed_tab" data-toggle="tab" aria-expanded="true">
                    <i class="fa fa-list" aria-hidden="true"></i> @lang('lang_v1.detailed')
                </a>
            </li>
            <li>
                <a href="#psr_detailed_with_purchase_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-file-invoice" aria-hidden="true"></i> @lang('lang_v1.detailed_with_purchase')
                </a>
            </li>
            <li>
                <a href="#psr_grouped_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-layer-group" aria-hidden="true"></i> @lang('lang_v1.grouped')
                </a>
            </li>
            <li>
                <a href="#psr_by_cat_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-tags" aria-hidden="true"></i> @lang('lang_v1.by_category')
                </a>
            </li>
            <li>
                <a href="#psr_by_brand_tab" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-bookmark" aria-hidden="true"></i> @lang('lang_v1.by_brand')
                </a>
            </li>
        </ul>

        <div class="tab-content" style="border: none; padding: 0; box-shadow: none;">
            <!-- Tab 1: Detailed -->
            <div class="tab-pane active" id="psr_detailed_tab">
                <div class="psr-table-responsive">
                    <table class="table table-bordered table-striped" id="product_sell_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.sku')</th>
                                <th id="psr_product_custom_field1">{{$product_custom_field1}}</th>
                                <th id="psr_product_custom_field2">{{$product_custom_field2}}</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('lang_v1.contact_id')</th>
                                <th>@lang('lang_v1.contact_no')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.qty')</th>
                                <th>@lang('sale.unit_price')</th>
                                <th>@lang('sale.discount')</th>
                                <th>@lang('sale.tax')</th>
                                <th>@lang('sale.price_inc_tax')</th>
                                <th>@lang('sale.total')</th>
                                <th>@lang('lang_v1.payment_method')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr class="skeleton-row">
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 65%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 40%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 55%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 55%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="9"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_sold"></td>
                                <td></td>
                                <td></td>
                                <td id="footer_tax"></td>
                                <td></td>
                                <td><span class="display_currency" id="footer_subtotal" data-currency_symbol ="true"></span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Detailed With Purchase -->
            <div class="tab-pane" id="psr_detailed_with_purchase_tab">
                <div class="psr-table-responsive">
                    @if(session('business.enable_lot_number'))
                        <input type="hidden" id="lot_enabled">
                    @endif
                    <table class="table table-bordered table-striped" id="product_sell_report_with_purchase_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('lang_v1.contact_no')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('lang_v1.purchase_ref_no')</th>
                                <th>@lang('lang_v1.lot_number')</th>
                                <th>@lang('lang_v1.supplier_name')</th>
                                <th>@lang('sale.qty')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr class="skeleton-row">
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 65%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 55%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Grouped -->
            <div class="tab-pane" id="psr_grouped_tab">
                <div class="psr-table-responsive">
                    <table class="table table-bordered table-striped" id="product_sell_grouped_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('report.current_stock')</th>
                                <th>@lang('report.total_unit_sold')</th>
                                <th>@lang('sale.total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                                <tr class="skeleton-row">
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 55%;"></div></td>
                                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_grouped_sold"></td>
                                <td><span class="display_currency" id="footer_grouped_subtotal" data-currency_symbol ="true"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tab 4: By Category -->
            @include('report.partials.product_sell_report_by_category')

            <!-- Tab 5: By Brand -->
            @include('report.partials.product_sell_report_by_brand')
        </div>
    </div>
</section>
<!-- /.content -->

<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(
        '#product_sell_report_form #location_id, #product_sell_report_form #customer_id, #psr_filter_brand_id, #psr_filter_category_id, #psr_customer_group_id'
    ).change(function() {
        $('.psr-nav-tabs li.active, .nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
    });

        $(document).ready( function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('href');
                if ( target == '#psr_by_cat_tab') {
                    if(typeof product_sell_report_by_category_datatable == 'undefined') {
                        product_sell_report_by_category_datatable = $('table#product_sell_report_by_category').DataTable({
                                processing: true,
                                serverSide: true,
                                scrollX: false,
                                autoWidth: false,
                                fixedHeader: false,
                                dom: 'lBrtip',
                                pageLength: 25,
                                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
                                initComplete: function () {
                                    $('table#product_sell_report_by_category .skeleton-row').remove();
                                    if (this.api().columns) {
                                        this.api().columns.adjust();
                                    }
                                },
                                buttons: [
                                    {
                                        extend: 'csv',
                                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> CSV',
                                        className: 'btn btn-default btn-sm',
                                        exportOptions: { columns: ':visible' }
                                    },
                                    {
                                        extend: 'excel',
                                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> Excel',
                                        className: 'btn btn-default btn-sm',
                                        exportOptions: { columns: ':visible' }
                                    },
                                ],
                                ajax: {
                                    url: '/reports/product-sell-grouped-by',
                                    data: function(d) {
                                        var start = '';
                                        var end = '';
                                        var start_time = $('#product_sr_start_time').val();
                                        var end_time = $('#product_sr_end_time').val();
                                        if ($('#product_sr_date_filter').val()) {
                                            start = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .startDate.format('YYYY-MM-DD');
                                            end = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .endDate.format('YYYY-MM-DD');

                                            start = moment(start + " " + start_time, "YYYY-MM-DD" + " " + moment_time_format).format('YYYY-MM-DD HH:mm');
                                            end = moment(end + " " + end_time, "YYYY-MM-DD" + " " + moment_time_format).format('YYYY-MM-DD HH:mm');
                                        }
                                        d.start_date = start;
                                        d.end_date = end;
                                        d.group_by = 'category';
                                        d.category_id = $('select#psr_filter_category_id').val();
                                        d.brand_id = $('select#psr_filter_brand_id').val();
                                        d.customer_id = $('select#customer_id').val();
                                        d.location_id = $('select#location_id').val();
                                        d.customer_group_id = $('#psr_customer_group_id').val();
                                    },
                                },
                                columns: [
                                    { data: 'category_name', name: 'cat.name' },
                                    { data: 'current_stock', name: 'current_stock', searchable: false, orderable: false },
                                    { data: 'total_qty_sold', name: 'total_qty_sold', searchable: false },
                                    { data: 'subtotal', name: 'subtotal', searchable: false },
                                ],
                                fnDrawCallback: function(oSettings) {
                                    $('#footer_psr_by_cat_total_sell').text(
                                        sum_table_col($('#product_sell_report_by_category'), 'row_subtotal')
                                    );
                                    $('#footer_psr_by_cat_total_sold').html(
                                        __sum_stock($('#product_sell_report_by_category'), 'sell_qty')
                                    );

                                    $('#footer_psr_by_cat_total_stock').html(
                                        __sum_stock($('#product_sell_report_by_category'), 'current_stock')
                                    );
                                    __currency_convert_recursively($('#product_sell_report_by_category'));
                                },
                            });

                        $('table#product_sell_report_by_category').on('preXhr.dt', function () {
                            $('table#product_sell_report_by_category tbody').html(getPsrSkeletonRows(4));
                        });
                        $('table#product_sell_report_by_category').on('draw.dt', function () {
                            $('table#product_sell_report_by_category .skeleton-row').remove();
                        });
                    } else {
                        product_sell_report_by_category_datatable.ajax.reload();
                    }
                } else if ( target == '#psr_by_brand_tab') {
                    if(typeof product_sell_report_by_brand_datatable == 'undefined') {
                        product_sell_report_by_brand_datatable = $('table#product_sell_report_by_brand').DataTable({
                                processing: true,
                                serverSide: true,
                                scrollX: false,
                                autoWidth: false,
                                fixedHeader: false,
                                dom: 'lBrtip',
                                pageLength: 25,
                                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
                                initComplete: function () {
                                    $('table#product_sell_report_by_brand .skeleton-row').remove();
                                    if (this.api().columns) {
                                        this.api().columns.adjust();
                                    }
                                },
                                buttons: [
                                    {
                                        extend: 'csv',
                                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> CSV',
                                        className: 'btn btn-default btn-sm',
                                        exportOptions: { columns: ':visible' }
                                    },
                                    {
                                        extend: 'excel',
                                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> Excel',
                                        className: 'btn btn-default btn-sm',
                                        exportOptions: { columns: ':visible' }
                                    },
                                ],
                                ajax: {
                                    url: '/reports/product-sell-grouped-by',
                                    data: function(d) {
                                        var start = '';
                                        var end = '';
                                        var start_time = $('#product_sr_start_time').val();
                                        var end_time = $('#product_sr_end_time').val();
                                        if ($('#product_sr_date_filter').val()) {
                                            start = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .startDate.format('YYYY-MM-DD');
                                            end = $('input#product_sr_date_filter')
                                                .data('daterangepicker')
                                                .endDate.format('YYYY-MM-DD');

                                            start = moment(start + " " + start_time, "YYYY-MM-DD" + " " + moment_time_format).format('YYYY-MM-DD HH:mm');
                                            end = moment(end + " " + end_time, "YYYY-MM-DD" + " " + moment_time_format).format('YYYY-MM-DD HH:mm');
                                        }
                                        d.start_date = start;
                                        d.end_date = end;
                                        d.group_by = 'brand';
                                        d.category_id = $('select#psr_filter_category_id').val();
                                        d.brand_id = $('select#psr_filter_brand_id').val();
                                        d.customer_id = $('select#customer_id').val();
                                        d.location_id = $('select#location_id').val();
                                        d.customer_group_id = $('#psr_customer_group_id').val();
                                    },
                                },
                                columns: [
                                    { data: 'brand_name', name: 'b.name' },
                                    { data: 'current_stock', name: 'current_stock', searchable: false, orderable: false },
                                    { data: 'total_qty_sold', name: 'total_qty_sold', searchable: false },
                                    { data: 'subtotal', name: 'subtotal', searchable: false },
                                ],
                                fnDrawCallback: function(oSettings) {
                                    $('#footer_psr_by_brand_total_sell').text(
                                        sum_table_col($('#product_sell_report_by_brand'), 'row_subtotal')
                                    );
                                    $('#footer_psr_by_brand_total_sold').html(
                                        __sum_stock($('#product_sell_report_by_brand'), 'sell_qty')
                                    );

                                    $('#footer_psr_by_cat_total_stock').html(
                                        __sum_stock($('#product_sell_report_by_brand'), 'current_stock')
                                    );
                                    __currency_convert_recursively($('#product_sell_report_by_brand'));
                                },
                            });

                        $('table#product_sell_report_by_brand').on('preXhr.dt', function () {
                            $('table#product_sell_report_by_brand tbody').html(getPsrSkeletonRows(4));
                        });
                        $('table#product_sell_report_by_brand').on('draw.dt', function () {
                            $('table#product_sell_report_by_brand .skeleton-row').remove();
                        });
                    } else {
                        product_sell_report_by_brand_datatable.ajax.reload();
                    }
                }
            });
        });
    </script>
@endsection
