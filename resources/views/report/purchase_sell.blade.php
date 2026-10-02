@extends('layouts.app')
@section('title', __('report.purchase_sell'))

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
    .ps-page {
        --ps-primary: {{ $primary_hex }};
        --ps-card: #ffffff;
        --ps-border: #e5e7eb;
        --ps-text: #111827;
        --ps-muted: #6b7280;
        --ps-head: #f9fafb;
        --ps-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.04);
    }

    .ps-page .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .ps-card {
        background: var(--ps-card);
        border: 1px solid var(--ps-border);
        border-radius: 12px;
        box-shadow: var(--ps-shadow);
    }

    .ps-filters-card {
        position: relative;
        z-index: 25;
        overflow: visible;
    }

    .ps-section-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--ps-text);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .ps-section-sub {
        font-size: 0.75rem;
        color: var(--ps-muted);
        margin: 0.2rem 0 0;
    }

    .ps-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--ps-muted);
        margin-bottom: 0.35rem;
    }

    .ps-date-filter-anchor {
        position: relative;
        width: 100%;
    }

    .ps-date-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        height: 36px;
        width: 100%;
        padding: 0 0.85rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--ps-text);
        background: var(--ps-card);
        border: 1px solid var(--ps-border);
        border-radius: 0.5rem;
        transition: background 0.15s ease, border-color 0.15s ease;
        white-space: nowrap;
        max-width: 100%;
    }

    .ps-date-btn:hover {
        background: var(--ps-head);
        border-color: var(--ps-primary);
        color: var(--ps-text);
    }

    .ps-export-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 5.5rem;
        height: 36px;
        padding: 0 0.85rem !important;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        gap: 0.35rem;
    }

    .ps-export-btn i {
        margin: 0 !important;
        width: 14px;
        height: 14px;
    }

    .ps-kpi {
        background: var(--ps-card);
        border: 1px solid var(--ps-border);
        border-radius: 10px;
        box-shadow: var(--ps-shadow);
        padding: 0.75rem 0.9rem;
        min-width: 0;
        flex: 1 1 0;
    }

    .ps-kpi-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--ps-muted);
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin: 0;
        white-space: nowrap;
    }

    .ps-kpi-value {
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: -0.02em;
        margin: 0.15rem 0 0;
        font-variant-numeric: tabular-nums;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--ps-text);
    }

    .ps-kpi-icon {
        width: 2.1rem;
        height: 2.1rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.8rem;
    }

    .ps-kpi-purchase {
        border-left: 3px solid #3c8dbc;
        background: #f4f8fb;
    }
    .ps-kpi-purchase .ps-kpi-icon { background: #d7e8f3; color: #2e6f96; }
    .ps-kpi-purchase .ps-kpi-value { color: #24597a; }

    .ps-kpi-purchase-due {
        border-left: 3px solid #c45c6a;
        background: #fbf5f6;
    }
    .ps-kpi-purchase-due .ps-kpi-icon { background: #f3dce0; color: #a34855; }
    .ps-kpi-purchase-due .ps-kpi-value { color: #8a3c47; }

    .ps-kpi-sale {
        border-left: 3px solid #2a9d8f;
        background: #f3faf8;
    }
    .ps-kpi-sale .ps-kpi-icon { background: #d5efe9; color: #1f7a70; }
    .ps-kpi-sale .ps-kpi-value { color: #1a635b; }

    .ps-kpi-sale-due {
        border-left: 3px solid #c4922a;
        background: #fbf8f2;
    }
    .ps-kpi-sale-due .ps-kpi-icon { background: #f3e7cc; color: #9a7420; }
    .ps-kpi-sale-due .ps-kpi-value { color: #7c5d18; }

    .ps-overall-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.7rem 0;
        border-bottom: 1px solid var(--ps-border);
    }

    .ps-overall-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .ps-overall-row:first-child {
        padding-top: 0;
    }

    .ps-overall-row.is-result {
        border-top: 1px solid var(--ps-border);
        margin-top: 0.25rem;
        padding-top: 0.9rem;
    }

    .ps-overall-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--ps-muted);
        margin: 0;
        letter-spacing: 0.03em;
    }

    .ps-overall-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--ps-text);
        margin: 0;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
        text-align: right;
    }

    .ps-overall-row.is-result .ps-overall-label {
        text-transform: uppercase;
        color: var(--ps-text);
    }

    .ps-overall-row.is-result .ps-overall-value {
        font-size: 1.25rem;
    }

    .ps-location-filter {
        width: 100%;
        max-width: 100%;
    }

    .ps-location-filter .select2-container {
        width: 100% !important;
    }

    .ps-page .select2-container .select2-selection--single {
        height: 36px;
        border-color: var(--ps-border);
        border-radius: 0.5rem;
        background: var(--ps-card);
    }

    .ps-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        color: var(--ps-text);
        font-size: 0.8125rem;
    }

    .ps-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .ps-filters-card .daterangepicker {
        width: auto !important;
        min-width: 640px;
    }

    .ps-filters-card .daterangepicker .ranges,
    .ps-filters-card .daterangepicker .drp-calendar {
        float: left;
    }

    .ps-filters-card .daterangepicker .drp-calendar.left {
        clear: none !important;
    }
    .daterangepicker select.yearselect {
        color: #111827 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db;
        color-scheme: light;
    }

    .daterangepicker select.monthselect option,
    .daterangepicker select.yearselect option {
        color: #111827 !important;
        background-color: #ffffff !important;
    }

    @media (max-width: 767px) {
        .ps-kpi-icon { display: none; }
        .ps-kpi { min-width: 7.5rem; }
        .ps-overall-value { font-size: 1.1rem; }
    }

    @media print {
        .ps-page .no-print,
        .ps-page .content-header {
            display: none !important;
        }
        .ps-card,
        .ps-kpi {
            box-shadow: none !important;
            break-inside: avoid;
        }
    }
</style>

<div class="ps-page">
    <section class="content-header tw-flex tw-justify-between tw-items-center tw-flex-wrap tw-gap-3">
        <div>
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('report.purchase_sell')</h1>
            <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('report.purchase_sell_msg')</p>
        </div>
    </section>

    <section class="content tw-px-4 tw-pb-8">
        <div class="print_section"><h2>{{ session()->get('business.name') }} - @lang('report.purchase_sell')</h2></div>

        <div class="ps-card ps-filters-card tw-p-4 md:tw-px-5 tw-mb-5 no-print">
            <div class="tw-mb-3">
                <h2 class="ps-section-title">Filters</h2>
                <p class="ps-section-sub">Narrow results by date or location.</p>
            </div>
            <div class="tw-flex tw-flex-col tw-gap-3">
                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3">
                    <div class="ps-date-filter-anchor tw-flex-1 tw-min-w-0">
                        <button type="button" id="purchase_sell_date_filter" class="ps-date-btn">
                            <i class="fa fa-calendar"></i>
                            <span>{{ __('messages.filter_by_date') }}</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                    <div class="ps-location-filter tw-flex-1 tw-min-w-0">
                        <select class="form-control select2" id="purchase_sell_location_filter" style="width:100%">
                            @foreach($business_locations as $key => $value)
                                <option value="{{ $key }}" @if((string) $key === (string) $default_location_id) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-end tw-gap-2">
                    <button type="button" onclick="exportPurchaseSell('xlsx')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 ps-export-btn">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" onclick="exportPurchaseSell('csv')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 ps-export-btn">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button type="button" onclick="exportPurchaseSell('pdf')" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-border-gray-300 ps-export-btn">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="tw-flex tw-flex-row tw-items-stretch tw-w-full tw-gap-3 tw-mb-5 tw-overflow-x-auto">
            <div class="ps-kpi ps-kpi-purchase tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div class="tw-min-w-0">
                    <p class="ps-kpi-label">{{ __('report.total_purchase') }}</p>
                    <p class="ps-kpi-value total_purchase"><i class="fas fa-sync fa-spin fa-fw"></i></p>
                </div>
                <div class="ps-kpi-icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <div class="ps-kpi ps-kpi-purchase-due tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div class="tw-min-w-0">
                    <p class="ps-kpi-label">{{ __('report.purchase_due') }} @show_tooltip(__('tooltip.purchase_due'))</p>
                    <p class="ps-kpi-value purchase_due"><i class="fas fa-sync fa-spin fa-fw"></i></p>
                </div>
                <div class="ps-kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
            <div class="ps-kpi ps-kpi-sale tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div class="tw-min-w-0">
                    <p class="ps-kpi-label">{{ __('report.total_sell') }}</p>
                    <p class="ps-kpi-value total_sell"><i class="fas fa-sync fa-spin fa-fw"></i></p>
                </div>
                <div class="ps-kpi-icon"><i class="fas fa-coins"></i></div>
            </div>
            <div class="ps-kpi ps-kpi-sale-due tw-flex tw-items-center tw-justify-between tw-gap-2">
                <div class="tw-min-w-0">
                    <p class="ps-kpi-label">{{ __('report.sell_due') }} @show_tooltip(__('tooltip.sell_due'))</p>
                    <p class="ps-kpi-value sell_due"><i class="fas fa-sync fa-spin fa-fw"></i></p>
                </div>
                <div class="ps-kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
        </div>

        <div class="ps-card tw-p-4 md:tw-p-5">
            <div class="tw-mb-4">
                <h2 class="ps-section-title">{{ __('lang_v1.overall') }}</h2>
                <p class="ps-section-sub">
                    How Sale − Purchase is calculated for the selected period.
                    @show_tooltip(__('tooltip.over_all_sell_purchase'))
                </p>
            </div>
            <div class="ps-overall-row">
                <p class="ps-overall-label">{{ __('report.total_sell') }}</p>
                <p class="ps-overall-value overall_sell"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
            <div class="ps-overall-row">
                <p class="ps-overall-label">− {{ __('lang_v1.sell_return') }}</p>
                <p class="ps-overall-value overall_sell_return"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
            <div class="ps-overall-row">
                <p class="ps-overall-label">− {{ __('report.total_purchase') }}</p>
                <p class="ps-overall-value overall_purchase"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
            <div class="ps-overall-row">
                <p class="ps-overall-label">+ {{ __('lang_v1.purchase_return') }}</p>
                <p class="ps-overall-value overall_purchase_return"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
            <div class="ps-overall-row is-result">
                <p class="ps-overall-label">{{ __('report.sell_minus_purchase') }}</p>
                <p class="ps-overall-value sell_minus_purchase"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
            <div class="ps-overall-row">
                <p class="ps-overall-label">{{ __('report.difference_due') }}</p>
                <p class="ps-overall-value difference_due"><i class="fas fa-sync fa-spin fa-fw"></i></p>
            </div>
        </div>
    </section>
</div>

@stop
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    function exportPurchaseSell(format) {
        var picker = $('#purchase_sell_date_filter').data('daterangepicker');
        if (!picker) {
            return;
        }
        var start = picker.startDate.format('YYYY-MM-DD');
        var end = picker.endDate.format('YYYY-MM-DD');
        var locationId = $('#purchase_sell_location_filter').val() || '';
        window.open(
            "{{ url('/reports/purchase-sell/export') }}/" + format +
            "?start_date=" + encodeURIComponent(start) +
            "&end_date=" + encodeURIComponent(end) +
            "&location_id=" + encodeURIComponent(locationId),
            '_blank'
        );
    }
</script>
@endsection
