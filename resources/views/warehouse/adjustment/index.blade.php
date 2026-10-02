@extends('layouts.app')
@section('title', __('lang_v1.warehouse_adjustments'))

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

    .warehouse-adjustment-content {
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

    .wa-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .wa-filter-field {
        min-width: 0;
        width: 100%;
    }
    #collapsible_warehouse_adjustment_filters {
        display: none;
        margin-bottom: 16px;
    }
    #toggle_warehouse_adjustment_filters {
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
    #toggle_warehouse_adjustment_filters:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    #toggle_warehouse_adjustment_filters.filter-active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
        opacity: 1 !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    .add-adjustment-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-adjustment-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    @media (min-width: 768px) {
        .wa-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .wa-filter-field {
            flex: 1 1 180px;
        }
    }

    #warehouse_adjustment_table_wrapper > .row:first-child,
    #warehouse_adjustment_table_wrapper .dataTables_length,
    #warehouse_adjustment_table_wrapper .dt-buttons,
    #warehouse_adjustment_table_wrapper .dataTables_filter {
        display: none !important;
    }

    #warehouse_adjustment_table th,
    #warehouse_adjustment_table th *,
    #warehouse_adjustment_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #warehouse_adjustment_table tbody tr[data-href] {
        cursor: pointer;
    }

    .dataTables_processing,
    #warehouse_adjustment_table_processing {
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

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .warehouse-adjustment-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.warehouse_adjustments')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('lang_v1.warehouse_adjustments_subtitle')</p>
    </div>
</section>

<section class="content warehouse-adjustment-content no-print">
    <div class="search-section">
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_warehouse_adjustment_search"
                           placeholder="@lang('lang_v1.warehouse_adjustment_search_placeholder')"
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_warehouse_adjustment_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_warehouse_adjustment_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
            @can('warehouse.adjust_stock')
                <a class="tw-dw-btn add-adjustment-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0"
                   href="{{ action([\App\Http\Controllers\Warehouse\WarehouseAdjustmentController::class, 'create']) }}"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang('lang_v1.warehouse_new_adjustment')
                </a>
            @endcan
        </div>

        <div id="collapsible_warehouse_adjustment_filters">
            <div class="wa-filters-row">
                <div class="form-group wa-filter-field">
                    {!! Form::label('warehouse_adjustment_location_filter', __('business.business_location')) !!}
                    {!! Form::select('warehouse_adjustment_location_filter', $warehouse_locations, $default_warehouse_id, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'id' => 'warehouse_adjustment_location_filter',
                    ]) !!}
                </div>
            </div>
            <p class="text-muted" style="margin: 12px 0 0; font-size: 13px;">@lang('lang_v1.warehouse_adjustment_help')</p>
        </div>

        <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_warehouse_adjustment_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="warehouse-adjustment-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="warehouse-adjustment-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('lang_v1.all_warehouse_adjustments')</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="warehouse_adjustment_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('stock_adjustment.adjustment_type')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('stock_adjustment.total_amount_recovered')</th>
                        <th>@lang('stock_adjustment.reason_for_stock_adjustment')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            <td><div class="skeleton-block" style="width: 40%;"></div></td>
                            <td><div class="skeleton-block" style="width: 70%;"></div></td>
                            <td><div class="skeleton-block" style="width: 50%;"></div></td>
                            <td><div class="skeleton-block" style="width: 60%;"></div></td>
                            <td><div class="skeleton-block" style="width: 45%;"></div></td>
                            <td><div class="skeleton-block" style="width: 50%;"></div></td>
                            <td><div class="skeleton-block" style="width: 50%;"></div></td>
                            <td><div class="skeleton-block" style="width: 65%;"></div></td>
                            <td><div class="skeleton-block" style="width: 55%;"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javascript')
    <script src="{{ asset('js/warehouse.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#toggle_warehouse_adjustment_filters').on('click', function() {
                var $btn = $(this);
                var $panel = $('#collapsible_warehouse_adjustment_filters');
                $panel.slideToggle(300, function() {
                    var isOpen = $panel.is(':visible');
                    $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                    if (isOpen) {
                        $panel.find('.select2-container').css('width', '100%');
                    }
                });
                $btn.toggleClass('filter-active');
            });
        });
    </script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot
