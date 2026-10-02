@extends('layouts.app')
@section('title', 'Discounts Allowed Report')

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

    .discounts-allowed-content {
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

    .da-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .da-filter-field {
        min-width: 0;
        width: 100%;
    }

    .da-date-filter-anchor {
        position: relative;
    }

    .search-section .daterangepicker {
        z-index: 40;
        width: auto !important;
        min-width: 640px;
    }

    .search-section .daterangepicker .ranges,
    .search-section .daterangepicker .drp-calendar {
        float: left;
    }

    .search-section .daterangepicker .drp-calendar.left {
        clear: none !important;
    }

    .search-section .daterangepicker select.monthselect,
    .search-section .daterangepicker select.yearselect,
    body > .daterangepicker.da-daterangepicker select.monthselect,
    body > .daterangepicker.da-daterangepicker select.yearselect {
        color: #111827 !important;
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 4px;
        color-scheme: light;
        accent-color: #3c8dbc;
    }

    .search-section .daterangepicker select.monthselect option,
    .search-section .daterangepicker select.yearselect option,
    body > .daterangepicker.da-daterangepicker select.monthselect option,
    body > .daterangepicker.da-daterangepicker select.yearselect option {
        color: #111827 !important;
        background-color: #ffffff !important;
        color-scheme: light;
    }

    @media (min-width: 768px) {
        .da-filters-row {
            flex-direction: row;
            align-items: flex-end;
        }

        .da-filter-field {
            flex: 1 1 0;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }

        .discounts-allowed-content {
            padding: 12px !important;
        }

        .search-section,
        .datatable-section {
            padding: 14px !important;
        }

        .da-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -4px;
        }

        #discounts_allowed_table {
            table-layout: auto !important;
            width: auto !important;
            min-width: 780px;
        }

        #discounts_allowed_table th,
        #discounts_allowed_table td {
            padding: 12px 14px !important;
            font-size: 13px;
        }

        #discounts_allowed_table th.da-products-col,
        #discounts_allowed_table td.da-products-cell {
            min-width: 220px;
            max-width: 260px;
            width: auto;
        }

        .search-section .daterangepicker,
        body > .daterangepicker.da-daterangepicker {
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
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            z-index: 4000;
        }

        .search-section .daterangepicker .ranges,
        .search-section .daterangepicker .drp-calendar,
        body > .daterangepicker.da-daterangepicker .ranges,
        body > .daterangepicker.da-daterangepicker .drp-calendar {
            float: none !important;
            display: block !important;
            width: 100% !important;
            max-width: 100%;
            margin: 0 !important;
        }

        .search-section .daterangepicker .ranges ul,
        body > .daterangepicker.da-daterangepicker .ranges ul {
            width: 100%;
        }

        .search-section .daterangepicker .drp-calendar.left,
        .search-section .daterangepicker .drp-calendar.right,
        body > .daterangepicker.da-daterangepicker .drp-calendar.left,
        body > .daterangepicker.da-daterangepicker .drp-calendar.right {
            clear: both !important;
            padding: 8px 8px 0 !important;
        }

        .search-section .daterangepicker .calendar-table,
        body > .daterangepicker.da-daterangepicker .calendar-table {
            width: 100%;
        }

        .search-section .daterangepicker .calendar-table th,
        .search-section .daterangepicker .calendar-table td,
        body > .daterangepicker.da-daterangepicker .calendar-table th,
        body > .daterangepicker.da-daterangepicker .calendar-table td {
            min-width: 0;
            padding: 4px 2px;
            font-size: 12px;
        }

        .search-section .daterangepicker .drp-buttons,
        body > .daterangepicker.da-daterangepicker .drp-buttons {
            width: 100%;
            text-align: center;
            position: sticky;
            bottom: 0;
            background: #fff;
        }
    }

    #discounts_allowed_table {
        width: 100%;
        table-layout: fixed;
    }

    #discounts_allowed_table_wrapper > .row:first-child,
    #discounts_allowed_table_wrapper .dataTables_length,
    #discounts_allowed_table_wrapper .dt-buttons {
        display: none !important;
    }

    #discounts_allowed_table th,
    #discounts_allowed_table th *,
    #discounts_allowed_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    #discounts_allowed_table th.da-products-col,
    #discounts_allowed_table td.da-products-cell {
        width: 28%;
        max-width: 280px;
        min-width: 180px;
        vertical-align: top !important;
        line-height: 1.4;
    }

    .da-products {
        display: block;
        max-width: 100%;
    }

    .da-product-line {
        display: block;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .da-product-line + .da-product-line {
        margin-top: 2px;
    }

    .dataTables_processing,
    #discounts_allowed_table_processing {
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
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">Discounts Allowed Report</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Review discounts applied on sales by product, cashier, and receipt</p>
    </div>
</section>

<section class="content discounts-allowed-content">

    <div class="search-section">
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_discount_search"
                           placeholder="Search by receipt, cashier, or product..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
        </div>

        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4 tw-mb-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%; margin-bottom: 16px;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_discount_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="discount-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="discount-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="da-filters-row">
            <div class="form-group da-filter-field">
                {!! Form::label('da_location_filter', __('purchase.business_location')) !!}
                {!! Form::select('da_location_filter', $business_locations, $default_location_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'da_location_filter', 'data-location-filter' => '1']); !!}
            </div>
            <div class="form-group da-filter-field da-date-filter-anchor">
                {!! Form::label('da_date_range', __('report.date_range')) !!}
                {!! Form::text('da_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'da_date_range']); !!}
            </div>
            <div class="form-group da-filter-field">
                {!! Form::label('da_cashier_filter', 'Cashier') !!}
                {!! Form::select('da_cashier_filter', $cashiers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'da_cashier_filter', 'data-depends-on-location' => 'cashiers']); !!}
            </div>
        </div>
    </div>

    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">All discounts</h3>
        <div class="table-responsive da-table-wrap">
            <table class="table table-bordered table-striped dataTable" id="discounts_allowed_table">
                <thead>
                    <tr>
                        <th class="sorting">Date &amp; time</th>
                        <th class="sorting">Receipt</th>
                        <th class="sorting">Cashier</th>
                        <th class="sorting da-products-col">Product(s)</th>
                        <th class="sorting">Discount on</th>
                        <th class="sorting">Discount amount</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            <td><div class="skeleton-block" style="width: 70%;"></div></td>
                            <td><div class="skeleton-block" style="width: 50%;"></div></td>
                            <td><div class="skeleton-block" style="width: 60%;"></div></td>
                            <td><div class="skeleton-block" style="width: 80%;"></div></td>
                            <td><div class="skeleton-block" style="width: 40%;"></div></td>
                            <td><div class="skeleton-block" style="width: 55%;"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

</section>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    function isMobileDa() {
        return window.matchMedia('(max-width: 767px)').matches;
    }

    function setDaPageScrollLock(locked) {
        var $scroll = $('#scrollable-container');
        if (!$scroll.length) {
            return;
        }
        if (locked) {
            $scroll.data('da-prev-overflow', $scroll.css('overflow-y'));
            $scroll.css('overflow-y', 'hidden');
        } else {
            $scroll.css('overflow-y', $scroll.data('da-prev-overflow') || '');
            $scroll.removeData('da-prev-overflow');
        }
    }

    function fitDaMobilePicker(picker) {
        if (!picker || !picker.container || !isMobileDa()) {
            return;
        }

        var $c = picker.container.addClass('da-daterangepicker');
        var margin = 12;
        var vh = window.innerHeight || document.documentElement.clientHeight;
        var vw = window.innerWidth || document.documentElement.clientWidth;
        var input = document.getElementById('da_date_range');
        var top = margin;
        var available = vh - (margin * 2);

        if (input) {
            var inputRect = input.getBoundingClientRect();
            var below = vh - inputRect.bottom - margin;
            if (below >= 240) {
                top = Math.round(inputRect.bottom + 8);
                available = vh - top - margin;
            }
        }

        var el = $c.get(0);
        var styles = {
            position: 'fixed',
            top: top + 'px',
            left: margin + 'px',
            right: margin + 'px',
            bottom: 'auto',
            width: 'auto',
            'min-width': '0',
            'max-width': (vw - margin * 2) + 'px',
            'max-height': available + 'px',
            'overflow-x': 'hidden',
            'overflow-y': 'auto',
            'z-index': '4000'
        };
        Object.keys(styles).forEach(function (prop) {
            el.style.setProperty(prop, styles[prop], 'important');
        });
    }

    var daDateSettings = $.extend(true, {}, dateRangeSettings, {
        alwaysShowCalendars: !isMobileDa(),
        parentEl: isMobileDa() ? 'body' : '.search-section',
        drops: 'down',
        opens: isMobileDa() ? 'center' : 'left'
    });
    daDateSettings.startDate = moment().startOf('month');
    daDateSettings.endDate = moment();

    $('#da_date_range').daterangepicker(
        daDateSettings,
        function (start, end) {
            $('#da_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            discounts_allowed_table.ajax.reload();
        }
    );
    $('#da_date_range').val(moment().startOf('month').format(moment_date_format) + ' ~ ' + moment().format(moment_date_format));

    $('#da_date_range').on('show.daterangepicker showCalendar.daterangepicker', function(ev, picker) {
        window.requestAnimationFrame(function() {
            fitDaMobilePicker(picker);
        });
        if (isMobileDa()) {
            setDaPageScrollLock(true);
        }
    });

    $('#da_date_range').on('hide.daterangepicker', function() {
        setDaPageScrollLock(false);
    });

    $(window).on('resize.daPicker orientationchange.daPicker', function() {
        var picker = $('#da_date_range').data('daterangepicker');
        if (picker && picker.isShowing) {
            fitDaMobilePicker(picker);
        }
    });

    $('#da_date_range').on('cancel.daterangepicker', function() {
        $('#da_date_range').val('');
        discounts_allowed_table.ajax.reload();
    });

    var discountExportOptions = {
        columns: [0, 1, 2, 3, 4, 5],
        stripHtml: true,
        format: {
            body: function (data, row, column, node) {
                if (column === 3) {
                    var lines = $(node).find('.da-product-line');
                    if (lines.length) {
                        return lines.map(function () { return $(this).text().trim(); }).get().join(', ');
                    }
                    return $(node).text().replace(/\s+/g, ' ').trim();
                }
                return $('<div>').html(data).text();
            }
        }
    };

    var discounts_allowed_table = $('#discounts_allowed_table').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: false,
        scrollX: true,
        autoWidth: false,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
        ajax: {
            url: "{{ route('reports.discounts_allowed') }}",
            data: function (d) {
                d.location_id = $('#da_location_filter').val();
                d.created_by = $('#da_cashier_filter').val();
                if ($('#da_date_range').val()) {
                    var picker = $('#da_date_range').data('daterangepicker');
                    d.start_date = picker.startDate.format('YYYY-MM-DD');
                    d.end_date = picker.endDate.format('YYYY-MM-DD');
                }
            }
        },
        columns: [
            { data: 'discounted_at', name: 'discounted_at' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'cashier_name', name: 'cashier_name' },
            { data: 'product_name', name: 'product_name', className: 'da-products-cell' },
            { data: 'discount_on', name: 'discount_on' },
            { data: 'discount_amount', name: 'discount_amount' }
        ],
        order: [[0, 'desc']],
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fa fa-share-square"></i> Export',
                className: 'btn btn-default btn-sm',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv"></i> CSV',
                        title: 'Discounts Allowed Report',
                        exportOptions: discountExportOptions
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Excel',
                        title: 'Discounts Allowed Report',
                        exportOptions: discountExportOptions
                    }
                ]
            }
        ],
        dom: 'lBrtip',
        initComplete: function() {
            $('#discounts_allowed_table .skeleton-row').remove();
            this.api().columns.adjust();
        },
        fnDrawCallback: function() {
            __currency_convert_recursively($('#discounts_allowed_table'));
            this.api().columns.adjust();
        }
    });

    $('#custom_discount_length').on('change', function() {
        discounts_allowed_table.page.len($(this).val()).draw();
    });

    $(document).on('click', '.discount-export-opt', function(e) {
        e.preventDefault();
        var format = $(this).data('format');
        discounts_allowed_table.button('.buttons-' + format).trigger();
    });

    $('#custom_discount_search').on('keyup', function() {
        discounts_allowed_table.search(this.value).draw();
    });

    $('#da_location_filter').change(function() {
        discounts_allowed_table.ajax.reload();
    });

    $('#da_cashier_filter').change(function() {
        discounts_allowed_table.ajax.reload();
    });
});
</script>
@endsection
