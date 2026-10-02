@extends('layouts.app')
@section('title', __('lang_v1.purchase_return'))

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
    $pr_col_count = 9;
@endphp

<style>
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .purchase-return-list-content {
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

    .pr-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .pr-filter-field {
        min-width: 0;
        width: 100%;
    }
    #collapsible_purchase_return_filters {
        display: none;
        margin-bottom: 16px;
    }
    #toggle_purchase_return_filters,
    .add-purchase-return-btn {
        height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 0 16px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        transition: all 0.2s ease-in-out !important;
        color: #ffffff !important;
        border: none !important;
        text-decoration: none !important;
    }
    #toggle_purchase_return_filters {
        border-radius: 8px !important;
        border: 1px solid {{ $primary_hex }} !important;
        background-color: {{ $primary_hex }} !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    #toggle_purchase_return_filters:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    #toggle_purchase_return_filters.filter-active {
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
        opacity: 1 !important;
    }
    .add-purchase-return-btn {
        border-radius: 20px !important;
        background-color: {{ $primary_hex }} !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-purchase-return-btn:hover,
    .add-purchase-return-btn:focus {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    @media (min-width: 768px) {
        .pr-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .pr-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .purchase-return-list-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
        .pr-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -4px;
            padding-bottom: 8px;
        }
        #purchase_return_datatable {
            width: auto !important;
            min-width: 900px;
        }
        #purchase_return_datatable th,
        #purchase_return_datatable td {
            padding: 12px 14px !important;
            font-size: 13px;
        }
    }

    @media (max-width: 1024px) {
        .pr-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 8px;
        }
        #purchase_return_datatable_wrapper .dataTables_scroll,
        #purchase_return_datatable_wrapper .dataTables_scrollHead,
        #purchase_return_datatable_wrapper .dataTables_scrollBody,
        #purchase_return_datatable_wrapper .dataTables_scrollFoot {
            overflow: visible !important;
        }
        #purchase_return_datatable_wrapper .dataTables_scrollBody {
            border-bottom: 0 !important;
        }
        #purchase_return_datatable_wrapper .dataTables_scrollBody tfoot {
            display: none !important;
        }
        #purchase_return_datatable_wrapper .dataTables_scrollFoot {
            margin-top: 0;
            border-top: 0;
        }
        #purchase_return_datatable_wrapper .dataTables_scrollFootInner,
        #purchase_return_datatable_wrapper .dataTables_scrollHeadInner {
            width: auto !important;
        }
    }

    #purchase_return_datatable_wrapper > .row:first-child,
    #purchase_return_datatable_wrapper .dataTables_length,
    #purchase_return_datatable_wrapper .dt-buttons,
    #purchase_return_datatable_wrapper .dataTables_filter {
        display: none !important;
    }

    #purchase_return_datatable th,
    #purchase_return_datatable th *,
    #purchase_return_datatable td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .dataTables_processing,
    #purchase_return_datatable_processing {
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

    #collapsible_purchase_return_filters,
    .pr-filter-field {
        overflow: visible;
    }
    .search-section .daterangepicker.pr-daterangepicker {
        position: absolute !important;
        z-index: 4000;
    }
    .search-section .daterangepicker.pr-daterangepicker .drp-buttons {
        display: block !important;
        position: static;
        background: #fff;
    }
    .search-section .daterangepicker select.monthselect,
    .search-section .daterangepicker select.yearselect {
        color: #111827 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 4px;
        color-scheme: light;
        -webkit-text-fill-color: #111827;
    }
    .search-section .daterangepicker select.monthselect option,
    .search-section .daterangepicker select.yearselect option {
        color: #111827 !important;
        background-color: #ffffff !important;
        color-scheme: light;
    }

    @media (max-width: 1024px) {
        .search-section .daterangepicker.pr-daterangepicker {
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
            margin: 0 !important;
            box-sizing: border-box !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            z-index: 4000 !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .ranges,
        .search-section .daterangepicker.pr-daterangepicker .drp-calendar {
            float: none !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .ranges ul {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
            padding: 8px !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .ranges li {
            flex: 1 1 calc(50% - 4px) !important;
            text-align: center !important;
            margin: 0 !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            padding: 7px 8px !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .drp-calendar.left,
        .search-section .daterangepicker.pr-daterangepicker .drp-calendar.right {
            clear: both !important;
            padding: 8px 8px 0 !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .calendar-table {
            width: 100% !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .calendar-table th,
        .search-section .daterangepicker.pr-daterangepicker .calendar-table td {
            min-width: 0 !important;
            padding: 4px 2px !important;
            font-size: 12px !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .drp-buttons {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            position: sticky !important;
            bottom: 0 !important;
            background: #fff !important;
            padding: 10px 12px !important;
            z-index: 2 !important;
        }
        .search-section .daterangepicker.pr-daterangepicker .drp-selected {
            display: block;
            padding: 0 0 8px;
        }
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.purchase_return')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">View and manage purchase returns</p>
    </div>
</section>

<section class="content purchase-return-list-content no-print">
    <div class="search-section">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%; flex-wrap: wrap;">
            <div style="flex-grow: 1; min-width: 220px;">
                <div style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_purchase_return_search"
                           placeholder="Search by ref no, purchase, supplier or location..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_purchase_return_filters" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_purchase_return_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
            @can('purchase.update')
                <a class="add-purchase-return-btn" href="{{ action([\App\Http\Controllers\CombinedPurchaseReturnController::class, 'create']) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang('messages.add')
                </a>
            @endcan
        </div>

        <div id="collapsible_purchase_return_filters">
            <div class="pr-filters-row">
                <div class="form-group pr-filter-field">
                    {!! Form::label('purchase_list_filter_location_id', __('purchase.business_location')) !!}
                    {!! Form::select('purchase_list_filter_location_id', $business_locations, $default_location_id ?? null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                        'data-location-filter' => '1',
                    ]) !!}
                </div>
                <div class="form-group pr-filter-field">
                    {!! Form::label('purchase_list_filter_date_range', __('report.date_range')) !!}
                    {!! Form::text('purchase_list_filter_date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'readonly',
                    ]) !!}
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_purchase_return_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="purchase-return-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="purchase-return-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @can('purchase.view')
        <div class="datatable-section">
            <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('lang_v1.all_purchase_returns')</h3>
            <div class="table-responsive pr-table-wrap">
                <table class="table table-bordered table-striped ajax_view" id="purchase_return_datatable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('lang_v1.parent_purchase')</th>
                            <th>@lang('purchase.location')</th>
                            <th>@lang('purchase.supplier')</th>
                            <th>@lang('purchase.payment_status')</th>
                            <th>@lang('purchase.grand_total')</th>
                            <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                @for ($c = 0; $c < $pr_col_count; $c++)
                                    <td><div class="skeleton-block"></div></td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count" id="footer_payment_status_count"></td>
                            <td><span class="display_currency footer_purchase_return_total" id="footer_purchase_return_total" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency footer_total_due" id="footer_total_due" data-currency_symbol="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endcan

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

@stop
@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        $(document).ready(function() {
            $('#toggle_purchase_return_filters').on('click', function() {
                var $btn = $(this);
                var $panel = $('#collapsible_purchase_return_filters');
                $panel.slideToggle(300, function() {
                    var isOpen = $panel.is(':visible');
                    $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                    if (isOpen) {
                        $panel.find('.select2-container').css('width', '100%');
                    } else {
                        var drp = $('#purchase_list_filter_date_range').data('daterangepicker');
                        if (drp && drp.isShowing) {
                            drp.hide();
                        }
                    }
                });
                $btn.toggleClass('filter-active');
            });

            var $prDateInput = $('#purchase_list_filter_date_range');
            var isCompactPrDate = function() {
                return window.matchMedia('(max-width: 1024px)').matches;
            };
            var setPrDateScrollLock = function(locked) {
                var $scroll = $('#scrollable-container');
                if (!$scroll.length) {
                    return;
                }
                if (locked) {
                    $scroll.data('pr-prev-overflow', $scroll.css('overflow-y'));
                    $scroll.css('overflow-y', 'hidden');
                } else {
                    $scroll.css('overflow-y', $scroll.data('pr-prev-overflow') || '');
                    $scroll.removeData('pr-prev-overflow');
                }
            };
            var fitPrDatePicker = function(picker) {
                if (!picker || !picker.container || !isCompactPrDate()) {
                    return;
                }
                var $c = picker.container.addClass('pr-daterangepicker show-calendar');
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
                Object.keys(styles).forEach(function(prop) {
                    el.style.setProperty(prop, styles[prop], 'important');
                });
            };

            var prDateSettings = $.extend(true, {}, dateRangeSettings, {
                alwaysShowCalendars: true,
                parentEl: '.search-section',
                drops: 'down',
                opens: isCompactPrDate() ? 'center' : 'left'
            });

            $prDateInput.daterangepicker(
                prDateSettings,
                function(start, end) {
                    $prDateInput.val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    if (typeof purchase_return_table !== 'undefined') {
                        purchase_return_table.ajax.reload();
                    }
                }
            );
            $prDateInput.on('show.daterangepicker showCalendar.daterangepicker', function(ev, picker) {
                if (!picker || !picker.container) {
                    return;
                }
                picker.container.addClass('pr-daterangepicker show-calendar');
                if (isCompactPrDate()) {
                    window.requestAnimationFrame(function() {
                        fitPrDatePicker(picker);
                    });
                    setPrDateScrollLock(true);
                } else {
                    picker.container.css({ position: 'absolute', top: '', left: '', right: 'auto' });
                    picker.move();
                }
            });
            $prDateInput.on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
                setPrDateScrollLock(false);
                if (picker && picker.container) {
                    picker.container.hide();
                }
            });
            $prDateInput.on('cancel.daterangepicker', function() {
                $prDateInput.val('');
                if (typeof purchase_return_table !== 'undefined') {
                    purchase_return_table.ajax.reload();
                }
            });

            var hidePrDatePickerIfScrolledAway = function(e) {
                if (isCompactPrDate()) {
                    return;
                }
                var drp = $prDateInput.data('daterangepicker');
                if (!drp || !drp.isShowing) {
                    return;
                }
                if (e && e.target && $(e.target).closest('.daterangepicker').length) {
                    return;
                }
                if (!$prDateInput.is(':visible')) {
                    drp.hide();
                    return;
                }
                var inputRect = $prDateInput[0].getBoundingClientRect();
                var $scroll = $('#scrollable-container');
                var viewTop = $scroll.length ? $scroll[0].getBoundingClientRect().top : 0;
                var viewBottom = $scroll.length ? $scroll[0].getBoundingClientRect().bottom : (window.innerHeight || document.documentElement.clientHeight);
                if (inputRect.bottom < viewTop || inputRect.top > viewBottom) {
                    drp.hide();
                }
            };
            $('#scrollable-container').on('scroll.purchaseReturnDateRange', hidePrDatePickerIfScrolledAway);
            $(window).on('resize.purchaseReturnDateRange', hidePrDatePickerIfScrolledAway);

            var keepPrFooterClearOfScrollbar = function($wrapper) {
                if (!window.matchMedia('(max-width: 1024px)').matches || !$wrapper || !$wrapper.length) {
                    return;
                }
                $wrapper.find('.dataTables_scroll, .dataTables_scrollHead, .dataTables_scrollBody, .dataTables_scrollFoot')
                    .css({ overflow: 'visible', 'overflow-x': 'visible', 'overflow-y': 'visible' });
                $wrapper.find('.dataTables_scrollBody tfoot').hide();
            };

            @can('purchase.view')
            var prExportOptions = {
                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                stripHtml: true
            };

            purchase_return_table = $('#purchase_return_datatable').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                pageLength: 25,
                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
                aaSorting: [[0, 'desc']],
                ajax: {
                    url: '/purchase-return',
                    data: function(d) {
                        if ($('#purchase_list_filter_location_id').length) {
                            d.location_id = $('#purchase_list_filter_location_id').val();
                        }

                        var start = '';
                        var end = '';
                        if ($('#purchase_list_filter_date_range').val()) {
                            start = $('input#purchase_list_filter_date_range')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            end = $('input#purchase_list_filter_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                        d.start_date = start;
                        d.end_date = end;
                    }
                },
                scrollX: true,
                scrollCollapse: true,
                columnDefs: [{
                    targets: [7, 8],
                    orderable: false,
                    searchable: false
                }],
                columns: [
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'parent_purchase', name: 'T.ref_no' },
                    { data: 'location_name', name: 'BS.name' },
                    { data: 'name', name: 'contacts.name' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'payment_due', name: 'payment_due' },
                    { data: 'action', name: 'action' }
                ],
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="fa fa-share-square"></i> Export',
                        className: 'btn btn-default btn-sm',
                        buttons: [
                            {
                                extend: 'csv',
                                text: '<i class="fa fa-file-csv"></i> CSV',
                                title: 'Purchase Returns',
                                exportOptions: prExportOptions
                            },
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel"></i> Excel',
                                title: 'Purchase Returns',
                                exportOptions: prExportOptions
                            }
                        ]
                    }
                ],
                dom: 'lBrtip',
                initComplete: function() {
                    $('#purchase_return_datatable .skeleton-row').remove();
                    this.api().columns.adjust();
                    keepPrFooterClearOfScrollbar($(this.api().table().container()));
                },
                fnDrawCallback: function() {
                    var api = this.api();
                    var $table = $(api.table().node());
                    var $wrapper = $(api.table().container());

                    var total_purchase = sum_table_col($table, 'final_total');
                    var total_due = sum_table_col($table, 'payment_due');
                    $wrapper.find('.footer_purchase_return_total, [id="footer_purchase_return_total"]').text(total_purchase);
                    $wrapper.find('.footer_total_due, [id="footer_total_due"]').text(total_due);
                    $wrapper.find('.footer_payment_status_count, [id="footer_payment_status_count"]').html(__sum_status_html($table, 'payment-status-label'));
                    __currency_convert_recursively($wrapper);
                    keepPrFooterClearOfScrollbar($wrapper);
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(5)').attr('class', 'clickable_td');
                }
            });

            $('#custom_purchase_return_length').on('change', function() {
                purchase_return_table.page.len($(this).val()).draw();
            });

            $(document).on('click', '.purchase-return-export-opt', function(e) {
                e.preventDefault();
                purchase_return_table.button('.buttons-' + $(this).data('format')).trigger();
            });

            $('#custom_purchase_return_search').on('keyup', function() {
                purchase_return_table.search(this.value).draw();
            });
            @endcan

            $(document).on('change', '#purchase_list_filter_location_id', function() {
                if (typeof purchase_return_table !== 'undefined') {
                    purchase_return_table.ajax.reload();
                }
            });
        });
    </script>
@endsection
