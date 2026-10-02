@extends('layouts.app')
@section('title', __('report.register_report'))

@section('content')

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

<style>
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .register-report-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    .kpi-cards-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .kpi-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 14px 14px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
    }

    .kpi-card-content {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .kpi-card-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        word-break: break-word;
        line-height: 1.3;
    }

    .kpi-card-value {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.25;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: normal;
        overflow: visible;
    }

    .kpi-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-left: auto;
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

    .dataTables_processing,
    #register_report_table_processing {
        display: none !important;
    }

    .skeleton-row td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
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
        color: #1e293b !important;
    }
    .search-section .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    #toggle_register_report_filters {
        background-color: {{ $primary_hex }};
        color: #ffffff;
        border: none;
        height: 42px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    #toggle_register_report_filters:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    #toggle_register_report_filters.filter-active {
        box-shadow: 0 0 0 3px {{ $primary_hex }}40;
    }

    #collapsible_register_report_filters {
        display: none;
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
        margin-top: 16px;
        margin-bottom: 16px;
    }

    .rr-filters-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .rr-filter-field {
        flex: 1 1 220px;
        min-width: 0;
    }

    #register_report_table_wrapper > .row:first-child,
    #register_report_table_wrapper .dataTables_length,
    #register_report_table_wrapper .dt-buttons,
    #register_report_table_wrapper .dataTables_filter {
        display: none !important;
    }

    .rr-table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #ffffff;
        margin-bottom: 0;
    }

    #register_report_table {
        width: 100% !important;
        min-width: 1060px;
        border-collapse: collapse !important;
        margin: 0 !important;
    }

    #register_report_table th,
    #register_report_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
        padding: 12px 16px !important;
        font-size: 13.5px !important;
        border: 1px solid #e2e8f0 !important;
        box-sizing: border-box !important;
    }

    #register_report_table thead th {
        font-weight: 600 !important;
        color: #475569 !important;
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
    }

    #register_report_table thead th:first-child,
    #register_report_table tbody td:first-child,
    #register_report_table tfoot td:first-child {
        border-left: none !important;
    }

    #register_report_table thead th:last-child,
    #register_report_table tbody td:last-child,
    #register_report_table tfoot td:last-child {
        border-right: none !important;
    }

    #register_report_table tfoot td {
        font-weight: 700 !important;
        background-color: #f8fafc !important;
        border-top: 2px solid #cbd5e1 !important;
        border-bottom: none !important;
    }

    #register_report_table .text-right {
        text-align: right !important;
    }

    #register_report_table .text-center {
        text-align: center !important;
    }

    /* Date Range Picker custom styles */
    body > .daterangepicker.rr-daterangepicker {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        z-index: 99999 !important;
        font-family: inherit !important;
    }

    body > .daterangepicker.rr-daterangepicker select.monthselect,
    body > .daterangepicker.rr-daterangepicker select.yearselect {
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 4px 6px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        cursor: pointer;
    }

    body > .daterangepicker.rr-daterangepicker .ranges li.active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
    }

    body > .daterangepicker.rr-daterangepicker td.active,
    body > .daterangepicker.rr-daterangepicker td.active:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
    }

    body > .daterangepicker.rr-daterangepicker .drp-buttons .btn-primary,
    body > .daterangepicker.rr-daterangepicker .drp-buttons .applyBtn {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-radius: 6px !important;
        padding: 6px 16px !important;
        font-weight: 600 !important;
    }

    @media (max-width: 767px) {
        body > .daterangepicker.rr-daterangepicker {
            position: fixed !important;
            top: 12px !important;
            left: 12px !important;
            right: 12px !important;
            bottom: auto !important;
            min-width: 0 !important;
            width: auto !important;
            max-width: calc(100vw - 24px) !important;
            max-height: calc(100vh - 24px) !important;
            max-height: calc(100dvh - 24px) !important;
            overscroll-behavior: contain;
            box-sizing: border-box;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
            z-index: 99999 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            margin: 0 !important;
        }

        body > .daterangepicker.rr-daterangepicker .ranges,
        body > .daterangepicker.rr-daterangepicker .drp-calendar {
            float: none !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        body > .daterangepicker.rr-daterangepicker .ranges ul {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
            padding: 8px !important;
        }

        body > .daterangepicker.rr-daterangepicker .ranges li {
            flex: 1 1 calc(50% - 4px) !important;
            text-align: center !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            padding: 7px 8px !important;
            margin: 0 !important;
        }

        body > .daterangepicker.rr-daterangepicker .drp-calendar.left,
        body > .daterangepicker.rr-daterangepicker .drp-calendar.right {
            clear: both !important;
            padding: 8px 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        body > .daterangepicker.rr-daterangepicker .calendar-table {
            width: 100% !important;
        }

        body > .daterangepicker.rr-daterangepicker .calendar-table th,
        body > .daterangepicker.rr-daterangepicker .calendar-table td {
            min-width: 0 !important;
            padding: 6px 2px !important;
            font-size: 12px !important;
            height: 28px !important;
            line-height: 28px !important;
        }

        body > .daterangepicker.rr-daterangepicker select.monthselect,
        body > .daterangepicker.rr-daterangepicker select.yearselect {
            height: 34px !important;
            padding: 2px 6px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #fff !important;
            color: #1e293b !important;
        }

        body > .daterangepicker.rr-daterangepicker .drp-buttons {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
            width: 100% !important;
            position: sticky !important;
            bottom: 0 !important;
            background: #ffffff !important;
            padding: 10px 14px !important;
            border-top: 1px solid #e2e8f0 !important;
            box-sizing: border-box !important;
            z-index: 10 !important;
        }

        body > .daterangepicker.rr-daterangepicker .drp-buttons .btn {
            margin: 0 !important;
            padding: 7px 16px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
        }

        body > .daterangepicker.rr-daterangepicker .drp-selected {
            font-size: 11px !important;
            color: #64748b !important;
            padding: 0 !important;
            margin-right: auto !important;
            text-align: left !important;
        }
    }

    #register_report_table thead th.sorting,
    #register_report_table thead th.sorting_asc,
    #register_report_table thead th.sorting_desc {
        padding-right: 32px !important;
        position: relative !important;
    }

    @media (max-width: 1400px) {
        .kpi-cards-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }
        .kpi-card {
            padding: 12px 14px !important;
            gap: 10px;
        }
        .kpi-card-value {
            font-size: 16px;
        }
    }

    @media (max-width: 900px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .register-report-content {
            padding: 14px !important;
        }
        .kpi-cards-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
        .kpi-card {
            padding: 12px 12px !important;
            gap: 8px;
        }
        .kpi-card-label {
            font-size: 11px;
        }
        .kpi-card-value {
            font-size: 15px;
        }
        .kpi-card-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
        }
        .kpi-card-icon i {
            font-size: 14px !important;
        }
        .rr-filter-field {
            flex: 1 1 100%;
        }
    }

    @media (max-width: 540px) {
        .kpi-cards-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .kpi-card {
            padding: 14px 16px !important;
            gap: 12px;
        }
        .kpi-card-icon {
            width: 40px;
            height: 40px;
        }
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('report.register_report')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('cash_register.manage_your_cash_register')</p>
    </div>
</section>

<!-- Main content -->
<section class="content register-report-content no-print">

    <!-- KPI Summary Cards Grid -->
    <div class="kpi-cards-grid">
        <div class="kpi-card">
            <div class="kpi-card-content">
                <p class="kpi-card-label">@lang('cash_register.total_cash')</p>
                <h3 class="kpi-card-value" style="color: #16a34a;">
                    <span class="footer_total_cash_payment">0</span>
                </h3>
            </div>
            <div class="kpi-card-icon" style="background-color: #10b98118;">
                <i class="fa fa-money-bill-wave text-success" style="font-size: 18px;"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-content">
                <p class="kpi-card-label">@lang('lang_v1.card')</p>
                <h3 class="kpi-card-value" style="color: #0284c7;">
                    <span class="footer_total_card_payment">0</span>
                </h3>
            </div>
            <div class="kpi-card-icon" style="background-color: #0284c718;">
                <i class="fa fa-credit-card text-info" style="font-size: 18px;"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-content">
                <p class="kpi-card-label">{{ $payment_types['paybill'] ?? 'I&M Bank' }}</p>
                <h3 class="kpi-card-value" style="color: #2563eb;">
                    <span class="footer_total_paybill_payment">0</span>
                </h3>
            </div>
            <div class="kpi-card-icon" style="background-color: #3b82f618;">
                <i class="fa fa-university text-primary" style="font-size: 18px;"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-content">
                <p class="kpi-card-label">{{ $payment_types['till'] ?? 'Till Number' }}</p>
                <h3 class="kpi-card-value" style="color: #d97706;">
                    <span class="footer_total_till_payment">0</span>
                </h3>
            </div>
            <div class="kpi-card-icon" style="background-color: #f59e0b18;">
                <i class="fa fa-store text-warning" style="font-size: 18px;"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-content">
                <p class="kpi-card-label">@lang('sale.total')</p>
                <h3 class="kpi-card-value" style="color: #0f172a;">
                    <span class="footer_total">0</span>
                </h3>
            </div>
            <div class="kpi-card-icon" style="background-color: #6366f118;">
                <i class="fa fa-receipt" style="font-size: 18px; color: {{ $primary_hex }};"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filters Section -->
    <div class="search-section">
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_register_report_search"
                           placeholder="Search by cashier, location, or status..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_register_report_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_register_report_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
        </div>

        <div id="collapsible_register_report_filters">
            {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getRegisterReport']), 'method' => 'get', 'id' => 'register_report_filter_form' ]) !!}
            <div class="rr-filters-row">
                <div class="form-group rr-filter-field">
                    {!! Form::label('register_user_id', __('report.user') . ':') !!}
                    {!! Form::select('register_user_id', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all_users')]); !!}
                </div>
                <div class="form-group rr-filter-field">
                    {!! Form::label('register_status', __('sale.status') . ':') !!}
                    {!! Form::select('register_status', ['open' => __('cash_register.open'), 'close' => __('cash_register.close')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('report.all')]); !!}
                </div>
                <div class="form-group rr-filter-field">
                    {!! Form::label('register_report_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('register_report_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'register_report_date_range', 'readonly']); !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_register_report_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="register-report-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="register-report-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('cash_register.all_your_cash_register')</h3>
        <div class="rr-table-responsive">
            <table class="table table-bordered table-striped" id="register_report_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('messages.action')</th>
                        <th>@lang('report.open_time')</th>
                        <th>@lang('report.close_time')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('report.user')</th>
                        <th class="text-right">@lang('cash_register.total_cash')</th>
                        <th class="text-right">@lang('lang_v1.card')</th>
                        <th class="text-right">{{ $payment_types['paybill'] ?? 'I&M Bank' }}</th>
                        <th class="text-right">{{ $payment_types['till'] ?? 'Till Number' }}</th>
                        <th class="text-right">@lang('sale.total')</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            <td class="text-center"><div class="skeleton-block" style="width: 55px; height: 26px; border-radius: 6px; margin: 0 auto;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 60 + (($i * 7) % 25) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 55 + (($i * 11) % 30) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 70 + (($i * 5) % 20) }}%;"></div></td>
                            <td><div class="skeleton-block" style="width: {{ 65 + (($i * 13) % 25) }}%;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 50 + (($i * 9) % 30) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 45 + (($i * 8) % 35) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 55 + (($i * 14) % 25) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 40 + (($i * 17) % 40) }}%; margin-left: auto;"></div></td>
                            <td class="text-right"><div class="skeleton-block" style="width: {{ 60 + (($i * 6) % 30) }}%; margin-left: auto;"></div></td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total" style="background-color: #f8fafc; font-weight: 700; color: #1e293b;">
                        <td class="text-center"></td>
                        <td colspan="4" class="text-right" style="text-align: right; font-weight: 700;">@lang('sale.total'):</td>
                        <td class="text-right footer_total_cash_payment" style="text-align: right; font-weight: 700; color: #16a34a;"></td>
                        <td class="text-right footer_total_card_payment" style="text-align: right; font-weight: 700; color: #0284c7;"></td>
                        <td class="text-right footer_total_paybill_payment" style="text-align: right; font-weight: 700; color: #2563eb;"></td>
                        <td class="text-right footer_total_till_payment" style="text-align: right; font-weight: 700; color: #d97706;"></td>
                        <td class="text-right footer_total" style="text-align: right; font-weight: 700; color: #0f172a;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
