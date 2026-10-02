@extends('layouts.app')
@section('title', __('lang_v1.warehouse_dashboard'))

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
    .warehouse-dashboard-content {
        background-color: {{ $primary_hex }}0d;
        padding: 24px;
        min-height: calc(100vh - 100px);
    }

    .warehouse-theme-btn {
        background: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    .warehouse-theme-btn:hover {
        opacity: 0.92;
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .warehouse-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 991px) {
        .warehouse-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .warehouse-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .warehouse-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .warehouse-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .warehouse-kpi-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 4px;
    }

    .warehouse-kpi-value {
        display: block;
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    .warehouse-search-section {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .warehouse-search-top-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        width: 100%;
        flex-wrap: wrap;
    }

    .warehouse-search-input-wrap {
        position: relative;
        flex: 1 1 320px;
        min-width: 250px;
    }

    .warehouse-search-icon {
        position: absolute;
        left: 14px;
        top: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .warehouse-search-input {
        padding-left: 42px !important;
        border-radius: 8px !important;
        height: 42px !important;
        width: 100% !important;
        border: 1px solid #cbd5e1 !important;
        outline: none !important;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05) !important;
        font-size: 14px !important;
    }

    .warehouse-search-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .warehouse-filter-btn {
        height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 0 16px !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
    }

    .warehouse-search-bottom-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
    }

    .warehouse-entries-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #475569;
        font-size: 13px;
        margin: 0;
        white-space: nowrap;
    }

    .warehouse-entries-select {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 6px 12px !important;
        background-color: #ffffff !important;
        outline: none !important;
        height: auto !important;
        display: inline-block !important;
        width: auto !important;
    }

    .warehouse-filters-panel {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    #warehouse_stock_table_wrapper .dataTables_filter,
    #warehouse_stock_table_wrapper .dataTables_length,
    #warehouse_stock_table_wrapper .dt-buttons,
    #warehouse_stock_table_processing {
        display: none !important;
    }

    .warehouse-table-wrap,
    #warehouse_stock_table_wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow-x: auto;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
        background: #ffffff;
    }

    #warehouse_stock_table {
        background-color: #ffffff;
        border: none !important;
        margin-bottom: 0 !important;
        width: 100% !important;
    }

    #warehouse_stock_table th,
    #warehouse_stock_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #warehouse_stock_table th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.06em !important;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 14px 16px !important;
    }

    #warehouse_stock_table td {
        border-bottom: 1px solid #e8edf3 !important;
        color: #334155 !important;
        font-size: 13px !important;
        padding: 11px 16px !important;
    }

    #warehouse_stock_table tbody tr:nth-child(even) {
        background-color: #fafbfd !important;
    }

    #warehouse_stock_table tbody tr:hover {
        background-color: #f0f7ff !important;
    }

    #warehouse_actions_dropdown_group.open .warehouse-actions-menu {
        display: block !important;
    }

    .warehouse-actions-menu {
        border-radius: 10px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
        border: 1px solid #e2e8f0 !important;
        padding: 6px !important;
        min-width: 220px !important;
        z-index: 1050;
    }

    .warehouse-actions-menu li a {
        border-radius: 6px !important;
        padding: 8px 14px !important;
        font-size: 13px !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .warehouse-actions-menu li a:hover {
        background: #f0f7ff !important;
        color: #1d4ed8 !important;
        text-decoration: none !important;
    }

    #warehouse_stock_table_wrapper .dataTables_paginate .paginate_button.current {
        background: {{ $primary_hex }} !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
    }
</style>

<section class="content-header tw-bg-white tw-flex tw-justify-between tw-items-center"
    style="background-color: #ffffff !important; padding: 20px 24px !important; border-bottom: 1px solid #cbd5e1 !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black tw-m-0">@lang('lang_v1.warehouse_dashboard')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('lang_v1.warehouse_dashboard_subtitle')</p>
    </div>
</section>

<section class="content warehouse-dashboard-content">
    @if (empty($warehouse_ids))
        <div class="alert alert-warning">
            @lang('lang_v1.warehouse_no_locations_help')
        </div>
    @else
        <div class="warehouse-kpi-grid">
            <div class="warehouse-kpi-card">
                <span class="warehouse-kpi-icon" style="background: {{ $primary_hex }};"><i class="fa fa-building"></i></span>
                <div>
                    <span class="warehouse-kpi-label">@lang('lang_v1.warehouse_locations')</span>
                    <span class="warehouse-kpi-value">{{ $stats['warehouse_count'] }}</span>
                </div>
            </div>
            <div class="warehouse-kpi-card">
                <span class="warehouse-kpi-icon" style="background: #10b981;"><i class="fa fa-cubes"></i></span>
                <div>
                    <span class="warehouse-kpi-label">@lang('lang_v1.warehouse_assigned_products')</span>
                    <span class="warehouse-kpi-value">{{ $stats['product_count'] }}</span>
                </div>
            </div>
            <div class="warehouse-kpi-card">
                <span class="warehouse-kpi-icon" style="background: #f59e0b;"><i class="fa fa-truck"></i></span>
                <div>
                    <span class="warehouse-kpi-label">@lang('lang_v1.warehouse_pending_dispatches')</span>
                    <span class="warehouse-kpi-value">{{ $stats['pending_dispatches'] + $stats['in_transit_dispatches'] }}</span>
                </div>
            </div>
            <div class="warehouse-kpi-card">
                <span class="warehouse-kpi-icon" style="background: #0ea5e9;"><i class="fa fa-boxes"></i></span>
                <div>
                    <span class="warehouse-kpi-label">@lang('lang_v1.warehouse_total_units')</span>
                    <span class="warehouse-kpi-value">{{ @num_format($stats['total_units']) }}</span>
                </div>
            </div>
        </div>

        @can('warehouse.view_stock')
            @include('warehouse.partials.stock_table')
        @endcan
    @endif
</section>
@endsection

@section('javascript')
    <script src="{{ asset('js/warehouse.js?v=' . $asset_v) }}"></script>
@endsection
