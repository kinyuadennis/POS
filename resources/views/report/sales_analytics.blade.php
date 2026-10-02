@extends('layouts.app')
@section('title', 'Sales Report')

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
        'primary' => '#3c8dbc',
    ];
    $primary_hex = $color_map[$theme_color] ?? '#3c8dbc';
@endphp

@section('content')

<style>
    .sa-page,
    .sa-modal {
        --sa-primary: {{ $primary_hex }};
        --sa-primary-soft: {{ $primary_hex }}14;
        --sa-card: #ffffff;
        --sa-border: #e5e7eb;
        --sa-text: #111827;
        --sa-muted: #6b7280;
        --sa-subtle: #9ca3af;
        --sa-head: #f9fafb;
        --sa-row-hover: #f8fafc;
        --sa-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.04);
        --sa-ring: #e5e7eb;
        --sa-empty: #f3f4f6;
        --sa-chart-grid: #e5e7eb;
        --sa-chart-label: #6b7280;
        --sa-skeleton-from: #f3f4f6;
        --sa-skeleton-to: #e5e7eb;
    }

    .sa-card {
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 12px;
        box-shadow: var(--sa-shadow);
    }

    .sa-kpi {
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 10px;
        box-shadow: var(--sa-shadow);
        padding: 0.75rem 0.9rem;
        min-width: max-content;
        flex: 1 1 auto;
    }

    .sa-kpi-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--sa-muted);
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin: 0;
        white-space: nowrap;
    }

    .sa-kpi-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--sa-text);
        letter-spacing: -0.02em;
        margin: 0.15rem 0 0;
        font-variant-numeric: tabular-nums;
        line-height: 1.3;
        white-space: nowrap;
        overflow: visible;
    }

    .sa-kpi-icon {
        width: 2.1rem;
        height: 2.1rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.8rem;
    }

    .sa-kpi-sales {
        border-left: 3px solid #3c8dbc;
        background: #f4f8fb;
    }
    .sa-kpi-sales .sa-kpi-icon { background: #d7e8f3; color: #2e6f96; }
    .sa-kpi-sales .sa-kpi-value { color: #24597a; }

    .sa-kpi-orders {
        border-left: 3px solid #2a9d8f;
        background: #f3faf8;
    }
    .sa-kpi-orders .sa-kpi-icon { background: #d5efe9; color: #1f7a70; }
    .sa-kpi-orders .sa-kpi-value { color: #1a635b; }

    .sa-kpi-items {
        border-left: 3px solid #c4922a;
        background: #fbf8f2;
    }
    .sa-kpi-items .sa-kpi-icon { background: #f3e7cc; color: #9a7420; }
    .sa-kpi-items .sa-kpi-value { color: #7c5d18; }

    .sa-kpi-discounts {
        border-left: 3px solid #c45c6a;
        background: #fbf5f6;
    }
    .sa-kpi-discounts .sa-kpi-icon { background: #f3dce0; color: #a34855; }
    .sa-kpi-discounts .sa-kpi-value { color: #8a3c47; }

    .sa-section-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--sa-text);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .sa-section-sub {
        font-size: 0.75rem;
        color: var(--sa-muted);
        margin: 0.2rem 0 0;
    }

    .sa-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--sa-muted);
        margin-bottom: 0.35rem;
    }

    .sa-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sa-table thead th {
        background: var(--sa-head);
        color: var(--sa-muted);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--sa-border);
        white-space: nowrap;
    }

    .sa-modal .sa-table thead th {
        font-weight: 700;
        color: var(--sa-text);
    }

    .sa-table tbody td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--sa-border);
        font-size: 0.8125rem;
        color: var(--sa-text);
        vertical-align: middle;
    }

    .sa-table tbody tr.sa-period-row,
    .sa-table tbody tr.modal-invoice-row {
        cursor: pointer;
    }

    .sa-table tbody tr.sa-period-row:hover,
    .sa-table tbody tr.modal-invoice-row:hover {
        background: var(--sa-row-hover);
    }

    .sa-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sa-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    #grouped_sales_table {
        width: 100%;
        min-width: 640px;
        table-layout: auto;
    }

    #grouped_sales_table th,
    #grouped_sales_table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    #grouped_sales_table th.sa-col-period,
    #grouped_sales_table td.sa-col-period,
    #grouped_sales_table th.sa-col-sales,
    #grouped_sales_table td.sa-col-sales {
        white-space: nowrap !important;
        overflow: visible;
        text-overflow: clip;
    }

    .sa-skeleton {
        background: linear-gradient(90deg, var(--sa-skeleton-from) 25%, var(--sa-skeleton-to) 50%, var(--sa-skeleton-from) 75%);
        background-size: 200% 100%;
        animation: sa-loading 1.4s infinite;
        border-radius: 6px;
    }

    @keyframes sa-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .sa-empty {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: var(--sa-muted);
    }

    .sa-empty-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 9999px;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--sa-empty);
        color: var(--sa-subtle);
    }

    .sa-filters-card {
        position: relative;
        z-index: 25;
        overflow: visible;
    }

    .sa-date-filter-anchor {
        position: relative;
    }

    body > .daterangepicker.sa-daterangepicker {
        z-index: 4000;
        width: auto !important;
        min-width: 640px;
        box-sizing: border-box;
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        overflow: auto;
    }

    body > .daterangepicker.sa-daterangepicker .ranges,
    body > .daterangepicker.sa-daterangepicker .drp-calendar {
        float: left;
    }

    body > .daterangepicker.sa-daterangepicker .drp-calendar.left {
        clear: none !important;
    }

    body > .daterangepicker.sa-daterangepicker .drp-buttons {
        display: block !important;
        width: 100%;
        clear: both;
        text-align: right;
        position: sticky;
        bottom: 0;
        z-index: 2;
        background: #fff;
        padding: 10px 12px;
        border-top: 1px solid #e5e7eb;
    }

    .sa-date-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--sa-text);
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 0.5rem;
        transition: background 0.15s ease, border-color 0.15s ease;
        white-space: nowrap;
        max-width: 100%;
    }

    .sa-date-btn:hover {
        background: var(--sa-head);
        border-color: var(--sa-primary);
        color: var(--sa-text);
    }

    .sa-page .select2-container .select2-selection--single {
        height: 36px;
        border-color: var(--sa-border);
        border-radius: 0.5rem;
        background: var(--sa-card);
    }

    .sa-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        color: var(--sa-text);
        font-size: 0.8125rem;
    }

    .sa-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .sa-page .dataTables_wrapper .dataTables_info,
    .sa-page .dataTables_wrapper .dataTables_paginate {
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        color: var(--sa-muted);
    }

    .sa-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.6rem !important;
        border-radius: 0.375rem !important;
        border: 1px solid transparent !important;
        color: var(--sa-text) !important;
    }

    .sa-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--sa-primary) !important;
        color: #fff !important;
        border-color: var(--sa-primary) !important;
    }

    .sa-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--sa-head) !important;
        color: var(--sa-text) !important;
        border-color: var(--sa-border) !important;
    }

    .sa-page .dataTables_wrapper .dataTables_length {
        padding: 0.85rem 1rem 0;
        font-size: 0.75rem;
        color: var(--sa-muted);
    }

    .sa-page .dataTables_wrapper .dataTables_length select {
        border-radius: 0.375rem;
        border-color: var(--sa-border);
        font-size: 0.75rem;
        padding: 0.15rem 0.4rem;
        background: var(--sa-card);
        color: var(--sa-text);
    }

    .sa-modal {
        z-index: 1060;
    }

    .sa-modal .modal-dialog {
        width: min(960px, calc(100% - 24px));
        margin: 24px auto;
    }

    .sa-modal .modal-content {
        background: var(--sa-card);
        color: var(--sa-text);
        border: 1px solid var(--sa-border);
        border-radius: 12px;
        box-shadow: 0 20px 40px rgb(0 0 0 / 0.12);
        overflow: hidden;
    }

    .sa-modal .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        background: var(--sa-head);
        border-bottom: 1px solid var(--sa-border);
        padding: 1rem 1.25rem;
        border-radius: 0;
    }

    .sa-modal .modal-header::before,
    .sa-modal .modal-header::after {
        display: none;
    }

    .sa-modal .modal-header > div {
        flex: 1 1 auto;
        min-width: 0;
    }

    .sa-modal .modal-title {
        color: var(--sa-text);
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    .sa-modal .modal-header .close {
        float: none;
        margin: 0;
        padding: 0;
        font-size: 1.5rem;
        line-height: 1;
        color: var(--sa-muted);
        opacity: 1;
        order: 2;
    }

    .sa-modal .modal-body {
        padding: 1rem 1.25rem;
        max-height: calc(100vh - 210px);
        overflow-y: auto;
        background: var(--sa-card);
    }

    .sa-modal .modal-footer {
        background: var(--sa-head);
        border-top: 1px solid var(--sa-border);
        padding: 0.75rem 1.25rem;
        border-radius: 0;
    }

    .sa-product-box {
        background: var(--sa-head);
        border: 1px solid var(--sa-border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    .sa-chart {
        height: 280px;
        width: 100%;
    }

    .sa-pager {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--sa-border);
        font-size: 0.75rem;
        color: var(--sa-muted);
    }

    .sa-pager-btn {
        border: 1px solid var(--sa-border);
        background: var(--sa-card);
        color: var(--sa-text);
        border-radius: 0.375rem;
        padding: 0.25rem 0.65rem;
        font-size: 0.75rem;
    }

    .sa-pager-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    @media (max-width: 767px) {
        .sa-chart { height: 220px; }
        .sa-kpi-icon { display: none; }
        .sa-kpi { min-width: max-content; }

        .sa-filters-card .daterangepicker,
        body > .daterangepicker.sa-daterangepicker {
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
            overflow: auto !important;
            -webkit-overflow-scrolling: touch;
            z-index: 4000;
        }

        .sa-filters-card .daterangepicker .ranges,
        .sa-filters-card .daterangepicker .drp-calendar,
        body > .daterangepicker.sa-daterangepicker .ranges,
        body > .daterangepicker.sa-daterangepicker .drp-calendar {
            float: none !important;
            display: block !important;
            width: 100% !important;
            max-width: 100%;
            margin: 0 !important;
        }

        .sa-filters-card .daterangepicker .ranges ul,
        body > .daterangepicker.sa-daterangepicker .ranges ul {
            width: 100%;
        }

        .sa-filters-card .daterangepicker .drp-calendar.left,
        .sa-filters-card .daterangepicker .drp-calendar.right,
        body > .daterangepicker.sa-daterangepicker .drp-calendar.left,
        body > .daterangepicker.sa-daterangepicker .drp-calendar.right {
            clear: both !important;
            padding: 8px 8px 0 !important;
        }

        .sa-filters-card .daterangepicker .calendar-table,
        body > .daterangepicker.sa-daterangepicker .calendar-table {
            width: 100%;
        }

        .sa-filters-card .daterangepicker .calendar-table th,
        .sa-filters-card .daterangepicker .calendar-table td,
        body > .daterangepicker.sa-daterangepicker .calendar-table th,
        body > .daterangepicker.sa-daterangepicker .calendar-table td {
            min-width: 0;
            padding: 4px 2px;
            font-size: 12px;
        }

        .sa-filters-card .daterangepicker .drp-buttons,
        body > .daterangepicker.sa-daterangepicker .drp-buttons {
            display: block !important;
            width: 100%;
            text-align: center;
            position: sticky;
            bottom: 0;
            background: #fff;
        }
    }

    .sa-export-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 5.5rem;
        height: 32px;
        padding: 0 0.85rem !important;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        gap: 0.35rem;
    }

    .sa-export-btn i {
        margin: 0 !important;
    }

    .sa-page #grouped_sales_table_wrapper .dt-buttons {
        display: none !important;
    }

    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .daterangepicker select.monthselect,
    .daterangepicker select.yearselect,
    body > .daterangepicker.sa-daterangepicker select.monthselect,
    body > .daterangepicker.sa-daterangepicker select.yearselect {
        color: #111827 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 4px;
        color-scheme: light;
        accent-color: #3c8dbc;
    }

    .daterangepicker select.monthselect option,
    .daterangepicker select.yearselect option,
    body > .daterangepicker.sa-daterangepicker select.monthselect option,
    body > .daterangepicker.sa-daterangepicker select.yearselect option {
        color: #111827 !important;
        background-color: #ffffff !important;
        color-scheme: light;
    }
</style>

<div class="sa-page">
    <section class="content-header tw-flex tw-justify-between tw-items-center tw-flex-wrap tw-gap-3">
        <div>
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">Sales Report</h1>
            <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0" id="sa_range_caption">
                Showing sales for the selected period
            </p>
        </div>
    </section>

    <section class="content tw-px-4 tw-pb-8">

        <div class="sa-card sa-filters-card tw-p-4 md:tw-p-5 tw-mb-5">
            <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between tw-gap-3 tw-mb-4">
                <div>
                    <h2 class="sa-section-title">Filters</h2>
                    <p class="sa-section-sub">Narrow results by date, location, customer, or cashier.</p>
                </div>
                <div class="sa-date-filter-anchor tw-w-full md:tw-w-auto">
                <button type="button" id="analytics_date_filter" class="sa-date-btn tw-w-full">
                    <svg aria-hidden="true" class="tw-size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                        <path d="M16 3v4" />
                        <path d="M8 3v4" />
                        <path d="M4 11h16" />
                        <path d="M7 14h.013" />
                        <path d="M10.01 14h.005" />
                        <path d="M13.01 14h.005" />
                        <path d="M16.015 14h.005" />
                        <path d="M13.015 17h.005" />
                        <path d="M7.01 17h.005" />
                        <path d="M10.01 17h.005" />
                    </svg>
                    <span>{{ __('messages.filter_by_date') }}</span>
                    <svg aria-hidden="true" class="tw-size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M6 9l6 6l6 -6" />
                    </svg>
                </button>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 xl:tw-grid-cols-4 tw-gap-4">
                <div>
                    <label class="sa-label" for="group_by_select">Grouping</label>
                    <select id="group_by_select" class="form-control sa-filter-select" style="width:100%">
                        <option value="daily" selected>Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="sa-label" for="location_id_select">Location</label>
                    <select id="location_id_select" class="form-control sa-filter-select" style="width:100%" data-location-filter="1">
                        @foreach($business_locations as $key => $value)
                            <option value="{{ $key }}" @if((string) $key === (string) $default_location_id) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sa-label" for="customer_id_select">Customer</label>
                    <select id="customer_id_select" class="form-control sa-filter-select" style="width:100%" data-depends-on-location="customers">
                        @foreach($customers as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sa-label" for="created_by_select">Cashier / Staff</label>
                    <select id="created_by_select" class="form-control sa-filter-select" style="width:100%" data-depends-on-location="cashiers">
                        @foreach($cashiers as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="tw-flex tw-flex-row tw-items-stretch tw-w-full tw-gap-3 tw-mb-5 tw-overflow-x-auto" id="kpi_cards_container">
            <div class="sa-kpi sa-kpi-sales tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div>
                    <p class="sa-kpi-label">Total Sales</p>
                    <p class="sa-kpi-value" id="kpi_total_sales">—</p>
                </div>
                <div class="sa-kpi-icon"><i class="fas fa-coins"></i></div>
            </div>
            <div class="sa-kpi sa-kpi-orders tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div>
                    <p class="sa-kpi-label">Orders</p>
                    <p class="sa-kpi-value" id="kpi_total_orders">—</p>
                </div>
                <div class="sa-kpi-icon"><i class="fas fa-receipt"></i></div>
            </div>
            <div class="sa-kpi sa-kpi-items tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div>
                    <p class="sa-kpi-label">Items Sold</p>
                    <p class="sa-kpi-value" id="kpi_items_sold">—</p>
                </div>
                <div class="sa-kpi-icon"><i class="fas fa-box"></i></div>
            </div>
            <div class="sa-kpi sa-kpi-discounts tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div>
                    <p class="sa-kpi-label">Discounts</p>
                    <p class="sa-kpi-value" id="kpi_total_discounts">—</p>
                </div>
                <div class="sa-kpi-icon"><i class="fas fa-tag"></i></div>
            </div>
        </div>

        <div class="sa-card tw-mb-5">
            <div class="tw-p-4 md:tw-px-5 tw-border-b tw-flex tw-flex-col lg:tw-flex-row lg:tw-items-center tw-gap-3" style="border-color: var(--sa-border)">
                <div class="tw-min-w-0 tw-flex-1">
                    <h3 class="sa-section-title" id="grouped_table_heading">Period performance</h3>
                    <p class="sa-section-sub">Select a row to view sales and line items for that period.</p>
                </div>
                <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-end tw-gap-2 tw-ml-auto">
                    <label class="sa-label tw-mb-0" for="sort_by_select">Sort</label>
                    <select id="sort_by_select" class="form-control input-sm" style="width:auto; min-width: 140px;">
                        <option value="date">Period</option>
                        <option value="sales">Sales amount</option>
                        <option value="orders">Orders</option>
                    </select>
                    <select id="sort_dir_select" class="form-control input-sm" style="width:auto; min-width: 120px;">
                        <option value="desc">Newest / high first</option>
                        <option value="asc">Oldest / low first</option>
                    </select>
                    <button type="button" onclick="exportReport('pdf')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 sa-export-btn">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button type="button" onclick="exportReport('xlsx')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 sa-export-btn">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" onclick="exportReport('csv')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 sa-export-btn">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                </div>
            </div>

            <div id="table_skeleton_loader" class="tw-p-5 tw-space-y-3 tw-hidden">
                <div class="sa-skeleton tw-h-8 tw-w-full"></div>
                <div class="sa-skeleton tw-h-8 tw-w-full"></div>
                <div class="sa-skeleton tw-h-8 tw-w-full"></div>
                <div class="sa-skeleton tw-h-8 tw-w-5/6"></div>
            </div>

            <div class="sa-table-wrap tw-hidden" id="grouped_table_wrap">
                <table class="sa-table" id="grouped_sales_table">
                    <thead>
                        <tr>
                            <th class="sa-col-period">Period</th>
                            <th class="tw-text-center">Orders</th>
                            <th class="tw-text-right">Items sold</th>
                            <th class="tw-text-right sa-col-sales">Total sales</th>
                            <th class="tw-text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody id="grouped_table_tbody"></tbody>
                </table>
            </div>

            <div id="table_empty_state" class="sa-empty tw-hidden">
                <div class="sa-empty-icon"><i class="fas fa-inbox"></i></div>
                <p class="tw-text-sm tw-font-medium tw-mb-0" style="color: var(--sa-text)">No completed sales</p>
                <p class="tw-text-xs tw-mt-1 tw-mb-0">Nothing matches the selected date range and filters.</p>
            </div>
        </div>

        <div class="sa-card tw-p-4 md:tw-p-5 tw-mb-5">
            <div class="tw-mb-3">
                <h3 class="sa-section-title">Sales trend</h3>
                <p class="sa-section-sub">Totals by the selected grouping period.</p>
            </div>
            <div id="sales_trend_chart" class="sa-chart"></div>
            <div id="sales_trend_empty" class="sa-empty tw-hidden">
                <div class="sa-empty-icon"><i class="fas fa-chart-area"></i></div>
                <p class="tw-text-sm tw-font-medium tw-mb-0" style="color: var(--sa-text)">No sales to chart</p>
                <p class="tw-text-xs tw-mt-1 tw-mb-0">Try a different date range or filter.</p>
            </div>
        </div>
    </section>
</div>

<div class="modal fade sa-modal" id="periodTransactionsModal" tabindex="-1" role="dialog" aria-labelledby="periodModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title" id="periodModalTitle">Period sales</h4>
                    <p class="sa-section-sub" id="periodModalSubtitle">Sales for the selected period</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center sm:tw-justify-between tw-gap-2 tw-mb-3">
                    <input type="text" id="modal_search_input" onkeyup="filterModalTransactions()" placeholder="Search receipt or customer" class="form-control input-sm" style="max-width: 260px;">
                    <div class="tw-text-xs" id="modal_transaction_count" style="color: var(--sa-muted)">Loading…</div>
                </div>

                <div id="modal_table_wrap" class="tw-overflow-x-auto tw-rounded-lg tw-hidden" style="border: 1px solid var(--sa-border);">
                    <table class="sa-table">
                        <thead>
                            <tr>
                                <th>Receipt No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Cashier</th>
                                <th>Payment</th>
                                <th class="tw-text-right">Total</th>
                                <th class="tw-text-center">Items</th>
                            </tr>
                        </thead>
                        <tbody id="modal_transactions_tbody"></tbody>
                    </table>
                </div>

                <div id="modal_pager" class="sa-pager tw-hidden">
                    <span id="modal_pager_info"></span>
                    <div class="tw-flex tw-gap-2">
                        <button type="button" class="sa-pager-btn" id="modal_prev_page">Previous</button>
                        <button type="button" class="sa-pager-btn" id="modal_next_page">Next</button>
                    </div>
                </div>

                <div id="modal_skeleton_loader" class="tw-p-4 tw-space-y-2 tw-hidden">
                    <div class="sa-skeleton tw-h-6 tw-w-full"></div>
                    <div class="sa-skeleton tw-h-6 tw-w-full"></div>
                    <div class="sa-skeleton tw-h-6 tw-w-4/5"></div>
                </div>

                <div id="modal_empty_state" class="sa-empty tw-hidden">
                    <div class="sa-empty-icon"><i class="fas fa-receipt"></i></div>
                    <p class="tw-text-sm tw-mb-0">No sales found for this period.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    let currentStartDate = moment().format('YYYY-MM-DD');
    let currentEndDate = moment().format('YYYY-MM-DD');
    let cachedPeriodTransactions = [];
    let filteredModalTransactions = [];
    let groupedTable = null;
    let salesTrendChart = null;
    let modalPage = 1;
    const modalPageSize = 25;
    const primaryColor = @json($primary_hex);

    $(document).ready(function() {
        $('.sa-filter-select').select2({ width: '100%' });
        $('.sa-filter-select').not('#location_id_select').add('#sort_by_select, #sort_dir_select').on('change', loadAnalyticsData);

        $('#location_id_select').on('change', function() {
            loadAnalyticsData();
        });

        if ($('#analytics_date_filter').length == 1) {
            function isMobileSa() {
                return window.matchMedia('(max-width: 767px)').matches;
            }

            function setSaPageScrollLock(locked) {
                var $scroll = $('#scrollable-container');
                if (!$scroll.length) {
                    return;
                }
                if (locked) {
                    $scroll.data('sa-prev-overflow', $scroll.css('overflow-y'));
                    $scroll.css('overflow-y', 'hidden');
                } else {
                    $scroll.css('overflow-y', $scroll.data('sa-prev-overflow') || '');
                    $scroll.removeData('sa-prev-overflow');
                }
            }

            function fitSaMobilePicker(picker) {
                if (!picker || !picker.container || !isMobileSa()) {
                    return;
                }

                var $c = picker.container.addClass('sa-daterangepicker');
                $c.addClass('show-calendar');
                var margin = 12;
                var vh = window.innerHeight || document.documentElement.clientHeight;
                var vw = window.innerWidth || document.documentElement.clientWidth;
                var el = $c.get(0);
                var styles = {
                    position: 'fixed',
                    top: margin + 'px',
                    left: margin + 'px',
                    right: margin + 'px',
                    bottom: 'auto',
                    width: 'auto',
                    'min-width': '0',
                    'max-width': (vw - margin * 2) + 'px',
                    'max-height': (vh - margin * 2) + 'px',
                    overflow: 'auto',
                    'z-index': '4000'
                };
                Object.keys(styles).forEach(function (prop) {
                    el.style.setProperty(prop, styles[prop], 'important');
                });
            }

            function closeSaPicker(picker) {
                setSaPageScrollLock(false);
                if (!picker || !picker.container) {
                    return;
                }
                picker.container.hide();
            }

            const analyticsDateRangeSettings = $.extend(true, {}, dateRangeSettings, {
                parentEl: 'body',
                alwaysShowCalendars: true,
                drops: 'down',
                opens: isMobileSa() ? 'center' : 'left',
                startDate: moment(),
                endDate: moment()
            });
            $('#analytics_date_filter').daterangepicker(analyticsDateRangeSettings, function(start, end) {
                $('#analytics_date_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                currentStartDate = start.format('YYYY-MM-DD');
                currentEndDate = end.format('YYYY-MM-DD');
                loadAnalyticsData();
            });

            $('#analytics_date_filter').on('show.daterangepicker showCalendar.daterangepicker', function(ev, picker) {
                picker.container.addClass('sa-daterangepicker show-calendar');
                window.requestAnimationFrame(function() {
                    fitSaMobilePicker(picker);
                });
                if (isMobileSa()) {
                    setSaPageScrollLock(true);
                }
            });

            $('#analytics_date_filter').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
                closeSaPicker(picker);
            });

            $(window).on('resize.saPicker orientationchange.saPicker', function() {
                var picker = $('#analytics_date_filter').data('daterangepicker');
                if (picker && picker.isShowing) {
                    fitSaMobilePicker(picker);
                }
            });

            $('#analytics_date_filter span').html(
                moment().format(moment_date_format) + ' ~ ' + moment().format(moment_date_format)
            );
        }

        $('#grouped_sales_table').on('click', 'tr.sa-period-row', function() {
            openPeriodModal($(this).attr('data-label'), $(this).attr('data-start'), $(this).attr('data-end'));
        });

        $('#grouped_sales_table').on('click', '.sa-view-details', function(e) {
            e.stopPropagation();
            const row = $(this).closest('tr');
            openPeriodModal(row.attr('data-label'), row.attr('data-start'), row.attr('data-end'));
        });

        $('#modal_prev_page').on('click', function() {
            if (modalPage > 1) {
                modalPage -= 1;
                paintModalPage();
            }
        });

        $('#modal_next_page').on('click', function() {
            const maxPage = Math.max(1, Math.ceil(filteredModalTransactions.length / modalPageSize));
            if (modalPage < maxPage) {
                modalPage += 1;
                paintModalPage();
            }
        });

        $('#modal_transactions_tbody').on('click', 'tr.modal-invoice-row', function() {
            toggleInvoiceProducts($(this).attr('data-id'));
        });

        loadAnalyticsData();
    });

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : String(str)).html();
    }

    function chartTheme() {
        return {
            background: '#ffffff',
            text: '#111827',
            muted: '#6b7280',
            grid: '#e5e7eb',
        };
    }

    function destroyChart(chart) {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
        return null;
    }

    function loadAnalyticsData() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#grouped_sales_table')) {
            $('#grouped_sales_table').DataTable().clear().destroy();
            groupedTable = null;
        }

        $('#grouped_table_tbody').html('');
        $('#table_empty_state').addClass('tw-hidden');
        $('#grouped_table_wrap').addClass('tw-hidden');
        $('#table_skeleton_loader').removeClass('tw-hidden');
        $('.sa-kpi-value').text('—');

        const groupBy = $('#group_by_select').val();
        const sortBy = $('#sort_by_select').val();
        const sortDir = $('#sort_dir_select').val();
        const locationId = $('#location_id_select').val();
        const customerId = $('#customer_id_select').val();
        const createdBy = $('#created_by_select').val();
        const groupLabel = groupBy.charAt(0).toUpperCase() + groupBy.slice(1);

        $('#grouped_table_heading').text('Period performance · ' + groupLabel);

        const params = {
            start_date: currentStartDate,
            end_date: currentEndDate,
            group_by: groupBy,
            sort_by: sortBy,
            sort_dir: sortDir,
            location_id: locationId,
            customer_id: customerId,
            created_by: createdBy
        };

        $.ajax({
            url: "{{ route('reports.sales_analytics.data') }}",
            type: "GET",
            data: params,
            success: function(response) {
                $('#table_skeleton_loader').addClass('tw-hidden');

                if (response.summary) {
                    $('#kpi_total_sales').text(response.summary.formatted_total_sales);
                    $('#kpi_total_orders').text(response.summary.total_orders);
                    $('#kpi_items_sold').text(response.summary.formatted_total_items_sold);
                    $('#kpi_total_discounts').text(response.summary.formatted_total_discounts);

                    const startLabel = moment(response.summary.start_date).format(moment_date_format);
                    const endLabel = moment(response.summary.end_date).format(moment_date_format);
                    $('#sa_range_caption').text('Showing ' + startLabel + ' – ' + endLabel);
                }

                const records = response.period_records || [];
                window._saLastRecords = records;
                renderCharts(records);

                if (records.length === 0) {
                    $('#table_empty_state').removeClass('tw-hidden');
                    return;
                }

                let html = '';
                records.forEach(function(rec) {
                    html += `
                        <tr class="sa-period-row" data-label="${escapeHtml(rec.period_label)}" data-start="${escapeHtml(toDateParam(rec.start_date))}" data-end="${escapeHtml(toDateParam(rec.end_date))}">
                            <td class="tw-font-medium sa-col-period">${escapeHtml(rec.period_label)}</td>
                            <td class="tw-text-center">${escapeHtml(rec.order_count)}</td>
                            <td class="tw-text-right">${escapeHtml(rec.formatted_items)}</td>
                            <td class="tw-text-right tw-font-semibold sa-col-sales">${escapeHtml(rec.formatted_sales)}</td>
                            <td class="tw-text-center" onclick="event.stopPropagation()">
                                <button type="button" class="sa-view-details tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline">View</button>
                            </td>
                        </tr>
                    `;
                });

                $('#grouped_table_tbody').html(html);
                $('#grouped_table_wrap').removeClass('tw-hidden');

                groupedTable = $('#grouped_sales_table').DataTable({
                    paging: true,
                    pageLength: 25,
                    lengthMenu: [25, 50, 100],
                    searching: false,
                    ordering: false,
                    info: true,
                    autoWidth: false,
                    scrollX: true,
                    buttons: [],
                    dom: 'lrtip',
                    language: {
                        lengthMenu: 'Show _MENU_ periods',
                        info: 'Showing _START_ to _END_ of _TOTAL_ periods',
                        infoEmpty: 'No periods',
                        paginate: { previous: 'Previous', next: 'Next' }
                    },
                    drawCallback: function() {
                        this.api().columns.adjust();
                    }
                });
            },
            error: function() {
                $('#table_skeleton_loader').addClass('tw-hidden');
                $('#table_empty_state').removeClass('tw-hidden');
                toastr && toastr.error ? toastr.error('Could not load sales analytics.') : alert('Error loading sales analytics data. Please try again.');
            }
        });
    }

    function renderCharts(records) {
        salesTrendChart = destroyChart(salesTrendChart);

        const hasData = records && records.length > 0;
        $('#sales_trend_chart').toggleClass('tw-hidden', !hasData);
        $('#sales_trend_empty').toggleClass('tw-hidden', hasData);

        if (!hasData || typeof Highcharts === 'undefined') {
            return;
        }

        const chronological = records.slice().sort(function(a, b) {
            return String(a.start_date).localeCompare(String(b.start_date));
        });
        const categories = chronological.map(function(r) { return r.period_label; });
        const sales = chronological.map(function(r) { return Number(r.total_sales) || 0; });
        const theme = chartTheme();

        salesTrendChart = Highcharts.chart('sales_trend_chart', {
            chart: { type: 'areaspline', backgroundColor: theme.background, height: 280, spacing: [8, 8, 8, 8] },
            title: { text: null },
            credits: { enabled: false },
            exporting: { enabled: false },
            legend: { enabled: false },
            xAxis: {
                categories: categories,
                labels: { style: { color: theme.muted, fontSize: '11px' }, rotation: categories.length > 8 ? -35 : 0 },
                lineColor: theme.grid,
                tickColor: theme.grid
            },
            yAxis: {
                title: { text: null },
                gridLineColor: theme.grid,
                labels: { style: { color: theme.muted, fontSize: '11px' } }
            },
            tooltip: { shared: false, valueDecimals: 2 },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.12,
                    marker: { radius: 3, lineWidth: 0 },
                    lineWidth: 2
                }
            },
            series: [{
                name: 'Sales',
                data: sales,
                color: primaryColor
            }]
        });
    }

    function toDateParam(value) {
        if (!value) {
            return '';
        }
        return String(value).slice(0, 10);
    }

    function formatModalRange(startDate, endDate) {
        const start = moment(startDate, 'YYYY-MM-DD', true);
        const end = moment(endDate, 'YYYY-MM-DD', true);
        if (start.isValid() && end.isValid()) {
            return start.format(moment_date_format) + ' – ' + end.format(moment_date_format);
        }
        return '';
    }

    function openPeriodModal(periodLabel, startDate, endDate) {
        modalPage = 1;
        $('#periodModalTitle').text(periodLabel || 'Period sales');
        $('#periodModalSubtitle').text(formatModalRange(startDate, endDate) || 'Sales for the selected period');
        $('#modal_search_input').val('');
        $('#modal_transactions_tbody').html('');
        $('#modal_empty_state').addClass('tw-hidden');
        $('#modal_pager').addClass('tw-hidden');
        $('#modal_table_wrap').addClass('tw-hidden');
        $('#modal_skeleton_loader').removeClass('tw-hidden');
        $('#modal_transaction_count').text('Loading sales…');

        const $modal = $('#periodTransactionsModal');
        if ($modal.parent()[0] !== document.body) {
            $modal.appendTo('body');
        }
        $modal.modal({
            backdrop: true,
            keyboard: true,
            show: true
        });

        const params = {
            start_date: startDate,
            end_date: endDate,
            location_id: $('#location_id_select').val(),
            customer_id: $('#customer_id_select').val(),
            created_by: $('#created_by_select').val()
        };

        $.ajax({
            url: "{{ route('reports.sales_analytics.period_transactions') }}",
            type: "GET",
            data: params,
            success: function(response) {
                $('#modal_skeleton_loader').addClass('tw-hidden');
                cachedPeriodTransactions = response.transactions || [];
                filteredModalTransactions = cachedPeriodTransactions.slice();
                renderModalTransactions();
            },
            error: function() {
                $('#modal_skeleton_loader').addClass('tw-hidden');
                toastr && toastr.error ? toastr.error('Could not load sales.') : alert('Error fetching period transactions.');
            }
        });
    }

    function renderModalTransactions() {
        $('#modal_transaction_count').text(filteredModalTransactions.length + ' sale' + (filteredModalTransactions.length === 1 ? '' : 's'));
        if (filteredModalTransactions.length === 0) {
            $('#modal_transactions_tbody').html('');
            $('#modal_table_wrap').addClass('tw-hidden');
            $('#modal_empty_state').removeClass('tw-hidden');
            $('#modal_pager').addClass('tw-hidden');
            return;
        }

        $('#modal_empty_state').addClass('tw-hidden');
        $('#modal_table_wrap').removeClass('tw-hidden');
        const maxPage = Math.max(1, Math.ceil(filteredModalTransactions.length / modalPageSize));
        if (modalPage > maxPage) {
            modalPage = maxPage;
        }
        paintModalPage();
    }

    function paintModalPage() {
        const start = (modalPage - 1) * modalPageSize;
        const pageItems = filteredModalTransactions.slice(start, start + modalPageSize);
        const maxPage = Math.max(1, Math.ceil(filteredModalTransactions.length / modalPageSize));
        let html = '';

        pageItems.forEach(function(t) {
            html += `
                <tr class="modal-invoice-row" data-id="${t.id}">
                    <td class="tw-font-medium">${escapeHtml(t.invoice_no)}</td>
                    <td style="color: var(--sa-muted)">${escapeHtml(t.transaction_date)}</td>
                    <td>${escapeHtml(t.customer_name)}</td>
                    <td style="color: var(--sa-muted)">${escapeHtml(t.cashier)}</td>
                    <td style="color: var(--sa-muted)">${escapeHtml(t.payment_methods)}</td>
                    <td class="tw-text-right tw-font-semibold">${escapeHtml(t.formatted_total)}</td>
                    <td class="tw-text-center">
                        <button type="button" class="sa-items-toggle tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline">
                            <i class="fas fa-chevron-down tw-mr-1"></i> Items
                        </button>
                    </td>
                </tr>
                <tr id="invoice_products_row_${t.id}" class="tw-hidden">
                    <td colspan="7" class="tw-px-4 tw-py-3">
                        <div class="sa-product-box" id="invoice_products_content_${t.id}">
                            <div class="sa-skeleton tw-h-5 tw-w-full"></div>
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#modal_transactions_tbody').html(html);
        $('#modal_pager').removeClass('tw-hidden');
        $('#modal_pager_info').text('Showing ' + (start + 1) + '–' + (start + pageItems.length) + ' of ' + filteredModalTransactions.length);
        $('#modal_prev_page').prop('disabled', modalPage <= 1);
        $('#modal_next_page').prop('disabled', modalPage >= maxPage);
    }

    function filterModalTransactions() {
        const query = $('#modal_search_input').val().toLowerCase();
        modalPage = 1;
        if (!query) {
            filteredModalTransactions = cachedPeriodTransactions.slice();
            renderModalTransactions();
            return;
        }

        filteredModalTransactions = cachedPeriodTransactions.filter(function(t) {
            return t.invoice_no.toLowerCase().includes(query) || t.customer_name.toLowerCase().includes(query);
        });
        renderModalTransactions();
    }

    function toggleInvoiceProducts(transactionId) {
        const row = $('#invoice_products_row_' + transactionId);
        const icon = $('tr.modal-invoice-row[data-id="' + transactionId + '"] .sa-items-toggle i');

        if (row.hasClass('tw-hidden')) {
            row.removeClass('tw-hidden');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');

            const contentBox = $('#invoice_products_content_' + transactionId);
            if (contentBox.find('.table-products').length === 0) {
                $.ajax({
                    url: "/reports/sales-analytics/transaction-details/" + transactionId,
                    type: "GET",
                    success: function(res) {
                        let prodHtml = `
                            <div class="tw-mb-2 tw-flex tw-items-center tw-justify-between">
                                <span class="tw-text-xs tw-font-semibold" style="color: var(--sa-text)">Products on receipt ${escapeHtml(res.invoice_no)}</span>
                            </div>
                            <table class="sa-table table-products">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th class="tw-text-center">Qty</th>
                                        <th class="tw-text-right">Unit price</th>
                                        <th class="tw-text-right">Discount</th>
                                        <th class="tw-text-right">Line total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        (res.sell_lines || []).forEach(function(line) {
                            prodHtml += `
                                <tr>
                                    <td>${escapeHtml(line.product_name)}</td>
                                    <td style="color: var(--sa-muted)">${escapeHtml(line.sku)}</td>
                                    <td class="tw-text-center">${escapeHtml(line.formatted_quantity)}</td>
                                    <td class="tw-text-right">${escapeHtml(line.formatted_unit_price)}</td>
                                    <td class="tw-text-right">${escapeHtml(line.formatted_discount)}</td>
                                    <td class="tw-text-right tw-font-semibold">${escapeHtml(line.formatted_line_total)}</td>
                                </tr>
                            `;
                        });

                        prodHtml += `</tbody></table>`;
                        contentBox.html(prodHtml);
                    },
                    error: function() {
                        contentBox.html('<p class="tw-text-xs tw-mb-0" style="color:#b91c1c">Failed to load sale products.</p>');
                    }
                });
            }
        } else {
            row.addClass('tw-hidden');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    }

    function exportReport(format) {
        const groupBy = $('#group_by_select').val();
        const sortBy = $('#sort_by_select').val();
        const sortDir = $('#sort_dir_select').val();
        const locationId = $('#location_id_select').val();
        const customerId = $('#customer_id_select').val();
        const createdBy = $('#created_by_select').val();

        let query = `?start_date=${currentStartDate}&end_date=${currentEndDate}&group_by=${groupBy}&sort_by=${sortBy}&sort_dir=${sortDir}&location_id=${locationId}&customer_id=${customerId}&created_by=${createdBy}`;

        window.open("/reports/sales-analytics/export/" + format + query, '_blank');
    }
</script>
@endsection
