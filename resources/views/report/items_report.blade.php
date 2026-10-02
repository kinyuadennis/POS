@extends('layouts.app')
@section('title', __('lang_v1.items_report'))

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
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .items-report-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

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

    .ir-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .ir-filter-field {
        min-width: 0;
        width: 100%;
    }

    #collapsible_items_report_filters {
        display: none;
        margin-bottom: 16px;
    }

    #toggle_items_report_filters {
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

    #toggle_items_report_filters:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    #toggle_items_report_filters.filter-active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
        opacity: 1 !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    @media (min-width: 768px) {
        .ir-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .ir-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .items-report-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
    }

    #items_report_table_wrapper > .row:first-child,
    #items_report_table_wrapper .dataTables_length,
    #items_report_table_wrapper .dt-buttons,
    #items_report_table_wrapper .dataTables_filter {
        display: none !important;
    }

    .ir-table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #ffffff;
        margin-bottom: 0;
    }

    #items_report_table {
        width: 100% !important;
        min-width: 1200px;
        border-collapse: collapse !important;
        margin: 0 !important;
    }

    #items_report_table th,
    #items_report_table th *,
    #items_report_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #items_report_table tfoot tr.footer-total td {
        background-color: #f8fafc !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        border-top: 2px solid #cbd5e1 !important;
    }

    .dataTables_processing,
    #items_report_table_processing {
        display: none !important;
    }

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

    .daterangepicker.ir-daterangepicker {
        z-index: 4000;
    }
    .daterangepicker.ir-daterangepicker .drp-buttons {
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
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">{{ __('lang_v1.items_report') }}</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Detailed report of purchased and sold items</p>
    </div>
</section>

<!-- Main content -->
<section class="content items-report-content no-print">
    <!-- Search, Action & Filters Box -->
    <div class="search-section">
        <!-- Row 1: Search & Filter Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_items_report_search"
                           placeholder="Search by product, SKU, invoice, ref no, or customer..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_items_report_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_items_report_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
        </div>

        <!-- Collapsible Filters Panel -->
        <div id="collapsible_items_report_filters">
            <div class="ir-filters-row">
                <div class="form-group ir-filter-field">
                    {!! Form::label('ir_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('ir_location_id', $business_locations, $default_location_id ?? null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'ir_location_id', 'data-location-filter' => '1']); !!}
                </div>
                <div class="form-group ir-filter-field">
                    {!! Form::label('ir_purchase_date_filter', __('purchase.purchase_date') . ':') !!}
                    {!! Form::text('ir_purchase_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ir_purchase_date_filter']); !!}
                </div>
                <div class="form-group ir-filter-field">
                    {!! Form::label('ir_sale_date_filter', __('lang_v1.sell_date') . ':') !!}
                    {!! Form::text('ir_sale_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ir_sale_date_filter']); !!}
                </div>
                <div class="form-group ir-filter-field">
                    {!! Form::label('ir_supplier_id', __('purchase.supplier') . ':') !!}
                    {!! Form::select('ir_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'ir_supplier_id', 'data-depends-on-location' => 'suppliers']); !!}
                </div>
                <div class="form-group ir-filter-field">
                    {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                    {!! Form::select('ir_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'id' => 'ir_customer_id', 'data-depends-on-location' => 'customers']); !!}
                </div>
                @if(Module::has('Manufacturing'))
                    <div class="form-group ir-filter-field" style="display: flex; align-items: center; padding-bottom: 8px;">
                        <label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; cursor: pointer; margin: 0;">
                            {!! Form::checkbox('only_mfg', 1, false, ['class' => 'input-icheck', 'id' => 'only_mfg_products']); !!}
                            {{ __('manufacturing::lang.only_mfg_products') }}
                        </label>
                    </div>
                @endif
            </div>
        </div>

        <!-- Row 2: Show Entries & Export -->
        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_items_report_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="items-report-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="items-report-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">All items</h3>
        <div class="ir-table-responsive">
            <table class="table table-bordered table-striped" id="items_report_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('sale.product')</th>
                        <th>@lang('product.sku')</th>
                        <th>@lang('purchase.purchase_date')</th>
                        <th>@lang('lang_v1.purchase')</th>
                        <th class="text-right">@lang('lang_v1.purchase_price')</th>
                        <th>@lang('lang_v1.sell_date')</th>
                        <th>@lang('business.sale')</th>
                        <th>@lang('contact.customer')</th>
                        <th>@lang('sale.location')</th>
                        <th class="text-right">@lang('lang_v1.sell_quantity')</th>
                        <th class="text-right">@lang('lang_v1.selling_price')</th>
                        <th class="text-right">@lang('sale.subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            <td><div class="skeleton-block" style="width: {{ 60 + (($i * 7) % 25) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 50 + (($i * 9) % 25) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 65 + (($i * 5) % 20) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 50 + (($i * 13) % 25) }}%;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 55 + (($i * 10) % 25) }}%; margin-left: auto;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 65 + (($i * 4) % 20) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 55 + (($i * 12) % 25) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 60 + (($i * 7) % 25) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 50 + (($i * 9) % 25) }}%;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 45 + (($i * 14) % 30) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 55 + (($i * 8) % 25) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 60 + (($i * 5) % 30) }}%; margin-left: auto;"></div></td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total" style="background-color: #f8fafc; font-weight: 700; color: #1e293b;">
                        <td colspan="4" class="text-right" style="text-align: right; font-weight: 700;"><strong>@lang('sale.total'):</strong></td>
                        <td id="footer_total_pp" class="footer_total_pp display_currency text-right" data-currency_symbol="true" style="text-align: right; font-weight: 700; color: #0f172a;"></td>
                        <td colspan="4"></td>
                        <td id="footer_total_qty" class="footer_total_qty text-right" style="text-align: right; font-weight: 700; color: #0f172a;"></td>
                        <td id="footer_total_sp" class="footer_total_sp display_currency text-right" data-currency_symbol="true" style="text-align: right; font-weight: 700; color: #0f172a;"></td>
                        <td id="footer_total_subtotal" class="footer_total_subtotal display_currency text-right" data-currency_symbol="true" style="text-align: right; font-weight: 700; color: #0f172a;"></td>
                    </tr>
                </tfoot>
            </table>
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
@endsection
