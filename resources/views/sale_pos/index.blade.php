@extends('layouts.app')
@section('title', __('sale.list_pos'))

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
    $custom_labels = json_decode(session('business.custom_labels'), true);
    $pos_col_count = 22;
@endphp

<style>
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .pos-list-content {
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

    .pos-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .pos-filter-field {
        min-width: 0;
        width: 100%;
    }
    #collapsible_pos_filters {
        display: none;
        margin-bottom: 16px;
    }
    #toggle_pos_filters,
    .add-pos-sale-btn {
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
    #toggle_pos_filters {
        border-radius: 8px !important;
        border: 1px solid {{ $primary_hex }} !important;
        background-color: {{ $primary_hex }} !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    #toggle_pos_filters:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    #toggle_pos_filters.filter-active {
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
        opacity: 1 !important;
    }
    .add-pos-sale-btn {
        border-radius: 20px !important;
        background-color: {{ $primary_hex }} !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-pos-sale-btn:hover,
    .add-pos-sale-btn:focus {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    .pos-checkbox-field {
        display: flex;
        align-items: flex-end;
        min-height: 68px;
    }
    .pos-checkbox-field label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-weight: 500;
        color: #475569;
        font-size: 13px;
    }

    @media (min-width: 768px) {
        .pos-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .pos-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .pos-list-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
        .pos-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -4px;
        }
        #sell_table {
            width: auto !important;
            min-width: 1100px;
        }
        #sell_table th,
        #sell_table td {
            padding: 12px 14px !important;
            font-size: 13px;
        }
    }

    #sell_table_wrapper > .row:first-child,
    #sell_table_wrapper .dataTables_length,
    #sell_table_wrapper .dt-buttons,
    #sell_table_wrapper .dataTables_filter {
        display: none !important;
    }

    #sell_table th,
    #sell_table th *,
    #sell_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .dataTables_processing,
    #sell_table_processing {
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

    body > .daterangepicker.pos-daterangepicker {
        z-index: 4000;
    }
    body > .daterangepicker.pos-daterangepicker .drp-buttons {
        display: block !important;
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 2;
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('sale.pos_sale')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">View and manage POS sales</p>
    </div>
</section>

<section class="content pos-list-content no-print">
    <div class="search-section">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%; flex-wrap: wrap;">
            <div style="flex-grow: 1; min-width: 220px;">
                <div style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_pos_search"
                           placeholder="Search by receipt, customer, contact or location..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_pos_filters" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_pos_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
            @can('sell.create')
                <a class="add-pos-sale-btn" href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    @lang('messages.add')
                </a>
            @endcan
        </div>

        <div id="collapsible_pos_filters">
            <div class="pos-filters-row">
                <div class="form-group pos-filter-field">
                    {!! Form::label('sell_list_filter_location_id', __('purchase.business_location')) !!}
                    {!! Form::select('sell_list_filter_location_id', $business_locations, $default_location_id ?? null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                        'data-location-filter' => '1',
                    ]) !!}
                </div>
                <div class="form-group pos-filter-field">
                    {!! Form::label('sell_list_filter_date_range', __('report.date_range')) !!}
                    {!! Form::text('sell_list_filter_date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'readonly',
                    ]) !!}
                </div>
                <div class="form-group pos-filter-field">
                    {!! Form::label('sell_list_filter_customer_id', __('contact.customer')) !!}
                    {!! Form::select('sell_list_filter_customer_id', $customers, null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                        'data-depends-on-location' => 'customers',
                    ]) !!}
                </div>
                <div class="form-group pos-filter-field">
                    {!! Form::label('sell_list_filter_payment_status', __('purchase.payment_status')) !!}
                    {!! Form::select('sell_list_filter_payment_status', [
                        'paid' => __('lang_v1.paid'),
                        'due' => __('lang_v1.due'),
                        'partial' => __('lang_v1.partial'),
                        'overdue' => __('lang_v1.overdue'),
                    ], null, [
                        'class' => 'form-control select2',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all'),
                    ]) !!}
                </div>
                @if (!empty($sales_representative))
                    <div class="form-group pos-filter-field">
                        {!! Form::label('created_by', __('report.user')) !!}
                        {!! Form::select('created_by', $sales_representative, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'data-depends-on-location' => 'users',
                        ]) !!}
                    </div>
                @endif
                @if (!empty($is_cmsn_agent_enabled))
                    <div class="form-group pos-filter-field">
                        {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent')) !!}
                        {!! Form::select('sales_cmsn_agnt', $commission_agents, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'data-depends-on-location' => 'users',
                        ]) !!}
                    </div>
                @endif
                @if (!empty($service_staffs))
                    <div class="form-group pos-filter-field">
                        {!! Form::label('service_staffs', __('restaurant.service_staff')) !!}
                        {!! Form::select('service_staffs', $service_staffs, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                @endif
                @if (!empty($shipping_statuses))
                    <div class="form-group pos-filter-field">
                        {!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
                        {!! Form::select('shipping_status', $shipping_statuses, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                @endif
                <div class="form-group pos-filter-field pos-checkbox-field">
                    <label>
                        {!! Form::checkbox('only_subscriptions', 1, false, [
                            'class' => 'input-icheck',
                            'id' => 'only_subscriptions',
                        ]) !!}
                        {{ __('lang_v1.subscriptions') }}
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_pos_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="pos-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="pos-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @can('sell.view')
        <input type="hidden" name="is_direct_sale" id="is_direct_sale" value="0">
        <div class="datatable-section">
            <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('sale.list_pos')</h3>
            <div class="table-responsive pos-table-wrap">
                <table class="table table-bordered table-striped ajax_view" id="sell_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.customer_name')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('lang_v1.payment_method')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.sell_return_due')</th>
                            <th>@lang('lang_v1.shipping_status')</th>
                            <th>@lang('lang_v1.total_items')</th>
                            <th>@lang('lang_v1.types_of_service')</th>
                            <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}</th>
                            <th>@lang('lang_v1.added_by')</th>
                            <th>@lang('sale.sell_note')</th>
                            <th>@lang('sale.staff_note')</th>
                            <th>@lang('sale.shipping_details')</th>
                            <th>@lang('restaurant.table')</th>
                            <th>@lang('restaurant.service_staff')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                @for ($c = 0; $c < $pos_col_count; $c++)
                                    <td><div class="skeleton-block"></div></td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                            <td class="footer_payment_status_count" id="footer_payment_status_count"></td>
                            <td class="payment_method_count" id="payment_method_count"></td>
                            <td class="footer_sale_total" id="footer_sale_total"></td>
                            <td class="footer_total_paid" id="footer_total_paid"></td>
                            <td class="footer_total_remaining" id="footer_total_remaining"></td>
                            <td class="footer_total_sell_return_due" id="footer_total_sell_return_due"></td>
                            <td colspan="2"></td>
                            <td class="service_type_count" id="service_type_count"></td>
                            <td colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endcan
</section>

<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<section class="invoice print_section" id="receipt_section">
</section>

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#toggle_pos_filters').on('click', function() {
                var $btn = $(this);
                var $panel = $('#collapsible_pos_filters');
                $panel.slideToggle(300, function() {
                    var isOpen = $panel.is(':visible');
                    $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                    if (isOpen) {
                        $panel.find('.select2-container').css('width', '100%');
                    }
                });
                $btn.toggleClass('filter-active');
            });

            var posDateSettings = $.extend(true, {}, dateRangeSettings, {
                alwaysShowCalendars: true,
                parentEl: 'body',
                drops: 'down'
            });

            $('#sell_list_filter_date_range').daterangepicker(
                posDateSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    if (typeof sell_table !== 'undefined') {
                        sell_table.ajax.reload();
                    }
                }
            );
            $('#sell_list_filter_date_range').on('show.daterangepicker', function(ev, picker) {
                if (picker && picker.container) {
                    picker.container.addClass('pos-daterangepicker');
                }
            });
            $('#sell_list_filter_date_range').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
                if (picker && picker.container) {
                    picker.container.hide();
                }
            });
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function() {
                $('#sell_list_filter_date_range').val('');
                if (typeof sell_table !== 'undefined') {
                    sell_table.ajax.reload();
                }
            });

            @can('sell.view')
            var posExportOptions = {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
                stripHtml: true
            };

            sell_table = $('#sell_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                pageLength: 25,
                lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
                aaSorting: [[1, 'desc']],
                ajax: {
                    url: '/sells',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        if ($('#is_direct_sale').length) {
                            d.is_direct_sale = $('#is_direct_sale').val();
                        }
                        if ($('#sell_list_filter_location_id').length) {
                            d.location_id = $('#sell_list_filter_location_id').val();
                        }
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        if ($('#sell_list_filter_payment_status').length) {
                            d.payment_status = $('#sell_list_filter_payment_status').val();
                        }
                        if ($('#created_by').length) {
                            d.created_by = $('#created_by').val();
                        }
                        if ($('#sales_cmsn_agnt').length) {
                            d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        }
                        if ($('#service_staffs').length) {
                            d.service_staffs = $('#service_staffs').val();
                        }
                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }
                        if ($('#only_subscriptions').length && $('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }
                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollX: true,
                scrollCollapse: true,
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'invoice_no', name: 'invoice_no' },
                    { data: 'conatct_name', name: 'conatct_name' },
                    { data: 'mobile', name: 'contacts.mobile' },
                    { data: 'business_location', name: 'bl.name' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'payment_methods', orderable: false, searchable: false },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'total_paid', name: 'total_paid', searchable: false },
                    { data: 'total_remaining', name: 'total_remaining' },
                    { data: 'return_due', orderable: false, searchable: false },
                    { data: 'shipping_status', name: 'shipping_status' },
                    { data: 'total_items', name: 'total_items', searchable: false },
                    { data: 'types_of_service_name', name: 'tos.name', @if (empty($is_types_service_enabled)) visible: false @endif },
                    { data: 'service_custom_field_1', name: 'service_custom_field_1', @if (empty($is_types_service_enabled)) visible: false @endif },
                    { data: 'added_by', name: 'u.first_name' },
                    { data: 'additional_notes', name: 'additional_notes' },
                    { data: 'staff_note', name: 'staff_note' },
                    { data: 'shipping_details', name: 'shipping_details' },
                    { data: 'table_name', name: 'tables.name', @if (empty($is_tables_enabled)) visible: false @endif },
                    { data: 'waiter', name: 'ss.first_name', @if (empty($is_service_staff_enabled)) visible: false @endif }
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
                                title: 'POS Sales',
                                exportOptions: posExportOptions
                            },
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel"></i> Excel',
                                title: 'POS Sales',
                                exportOptions: posExportOptions
                            }
                        ]
                    }
                ],
                dom: 'lBrtip',
                initComplete: function() {
                    $('#sell_table .skeleton-row').remove();
                    this.api().columns.adjust();
                },
                fnDrawCallback: function() {
                    var api = this.api();
                    var $wrapper = $(api.table().container());
                    __currency_convert_recursively($wrapper);
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var $wrapper = $(api.table().container());
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data) {
                        footer_sale_total += ($(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0) - ($(data[r].final_total).attr('data-parcel-value') ? parseFloat($(data[r].final_total).attr('data-parcel-value')) : 0);
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ? parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due').data('orig-value') ? parseFloat($(data[r].return_due).find('.sell_return_due').data('orig-value')) : 0;
                    }

                    $wrapper.find('.footer_total_sell_return_due, [id="footer_total_sell_return_due"]').html(__currency_trans_from_en(footer_total_sell_return_due));
                    $wrapper.find('.footer_total_remaining, [id="footer_total_remaining"]').html(__currency_trans_from_en(footer_total_remaining));
                    $wrapper.find('.footer_total_paid, [id="footer_total_paid"]').html(__currency_trans_from_en(footer_total_paid));
                    $wrapper.find('.footer_sale_total, [id="footer_sale_total"]').html(__currency_trans_from_en(footer_sale_total));
                    $wrapper.find('.footer_payment_status_count, [id="footer_payment_status_count"]').html(__count_status(data, 'payment_status'));
                    $wrapper.find('.service_type_count, [id="service_type_count"]').html(__count_status(data, 'types_of_service_name'));
                    $wrapper.find('.payment_method_count, [id="payment_method_count"]').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $('#custom_pos_length').on('change', function() {
                sell_table.page.len($(this).val()).draw();
            });

            $(document).on('click', '.pos-export-opt', function(e) {
                e.preventDefault();
                sell_table.button('.buttons-' + $(this).data('format')).trigger();
            });

            $('#custom_pos_search').on('keyup', function() {
                sell_table.search(this.value).draw();
            });
            @endcan

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',
                function() {
                    if (typeof sell_table !== 'undefined') {
                        sell_table.ajax.reload();
                    }
                });

            $('#only_subscriptions').on('ifChanged change', function() {
                if (typeof sell_table !== 'undefined') {
                    sell_table.ajax.reload();
                }
            });
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
