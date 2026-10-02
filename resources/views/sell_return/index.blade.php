@extends('layouts.app')
@section('title', __('lang_v1.list_sell_return'))

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

    .sell-return-list-content {
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

    .sr-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .sr-filter-field {
        min-width: 0;
        width: 100%;
    }
    #collapsible_sell_return_filters {
        display: none;
        margin-bottom: 16px;
    }
    #toggle_sell_return_filters {
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
    #toggle_sell_return_filters:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    #toggle_sell_return_filters.filter-active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
        opacity: 1 !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    @media (min-width: 768px) {
        .sr-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .sr-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .sell-return-list-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
        .sr-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -4px;
        }
        #sell_return_table {
            width: auto !important;
            min-width: 900px;
        }
        #sell_return_table th,
        #sell_return_table td {
            padding: 12px 14px !important;
            font-size: 13px;
        }
    }

    #sell_return_table_wrapper > .row:first-child,
    #sell_return_table_wrapper .dataTables_length,
    #sell_return_table_wrapper .dt-buttons,
    #sell_return_table_wrapper .dataTables_filter {
        display: none !important;
    }

    #sell_return_table th,
    #sell_return_table th *,
    #sell_return_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .dataTables_processing,
    #sell_return_table_processing {
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

    body > .daterangepicker.sr-daterangepicker {
        z-index: 4000;
    }
    body > .daterangepicker.sr-daterangepicker .drp-buttons {
        display: block !important;
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 2;
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.list_sell_return')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">View and manage sales returns</p>
    </div>
</section>

<section class="content sell-return-list-content no-print">
    <div class="search-section">
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_sell_return_search"
                           placeholder="Search by return no, receipt, customer or location..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <button type="button" id="toggle_sell_return_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_sell_return_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
        </div>

        <div id="collapsible_sell_return_filters">
            <div class="sr-filters-row">
                <div class="form-group sr-filter-field">
                    {!! Form::label('sell_list_filter_location_id', __('purchase.business_location')) !!}
                    {!! Form::select('sell_list_filter_location_id', $business_locations, $default_location_id ?? null, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-location-filter' => '1']); !!}
                </div>
                <div class="form-group sr-filter-field">
                    {!! Form::label('sell_list_filter_customer_id', __('contact.customer')) !!}
                    {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-depends-on-location' => 'customers']); !!}
                </div>
                <div class="form-group sr-filter-field">
                    {!! Form::label('sell_list_filter_date_range', __('report.date_range')) !!}
                    {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
                @can('access_sell_return')
                <div class="form-group sr-filter-field">
                    {!! Form::label('created_by', __('report.user')) !!}
                    {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-depends-on-location' => 'users']); !!}
                </div>
                @endcan
            </div>
        </div>

        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_sell_return_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="sell-return-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="sell-return-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">All sale returns</h3>
        @include('sell_return.partials.sell_return_list')
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function(){
        $('#toggle_sell_return_filters').on('click', function() {
            var $btn = $(this);
            var $panel = $('#collapsible_sell_return_filters');
            $panel.slideToggle(300, function() {
                var isOpen = $panel.is(':visible');
                $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                if (isOpen) {
                    $panel.find('.select2-container').css('width', '100%');
                }
            });
            $btn.toggleClass('filter-active');
        });

        var srDateSettings = $.extend(true, {}, dateRangeSettings, {
            alwaysShowCalendars: true,
            parentEl: 'body',
            drops: 'down'
        });

        $('#sell_list_filter_date_range').daterangepicker(
            srDateSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_return_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('show.daterangepicker', function(ev, picker) {
            if (picker && picker.container) {
                picker.container.addClass('sr-daterangepicker');
            }
        });
        $('#sell_list_filter_date_range').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
            if (picker && picker.container) {
                picker.container.hide();
            }
        });
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function() {
            $('#sell_list_filter_date_range').val('');
            sell_return_table.ajax.reload();
        });

        var srExportOptions = {
            columns: [0, 1, 2, 3, 4, 5, 6, 7],
            stripHtml: true
        };

        sell_return_table = $('#sell_return_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            scrollX: true,
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
            aaSorting: [[0, 'desc']],
            ajax: {
                url: "/sell-return",
                data: function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
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
                            title: 'Sale Return List',
                            exportOptions: srExportOptions
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel"></i> Excel',
                            title: 'Sale Return List',
                            exportOptions: srExportOptions
                        }
                    ]
                }
            ],
            dom: 'lBrtip',
            initComplete: function() {
                $('#sell_return_table .skeleton-row').remove();
                this.api().columns.adjust();
            },
            fnDrawCallback: function (oSettings) {
                var api = this.api();
                var $table = $(api.table().node());
                var $wrapper = $(api.table().container());

                var total_sell = sum_table_col($table, 'final_total');
                var total_due = sum_table_col($table, 'payment_due');
                var status_html = __sum_status_html($table, 'payment-status-label');

                $wrapper.find('.footer_sell_return_total, [id="footer_sell_return_total"]').text(total_sell);
                $wrapper.find('.footer_total_due_sr, [id="footer_total_due_sr"]').text(total_due);
                $wrapper.find('.footer_payment_status_count_sr, [id="footer_payment_status_count_sr"]').html(status_html);

                __currency_convert_recursively($wrapper);
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });

        $('#custom_sell_return_length').on('change', function() {
            sell_return_table.page.len($(this).val()).draw();
        });

        $(document).on('click', '.sell-return-export-opt', function(e) {
            e.preventDefault();
            var format = $(this).data('format');
            sell_return_table.button('.buttons-' + format).trigger();
        });

        $('#custom_sell_return_search').on('keyup', function() {
            sell_return_table.search(this.value).draw();
        });

        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by', function() {
            sell_return_table.ajax.reload();
        });
    })

    $(document).on('click', 'a.delete_sell_return', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            sell_return_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
</script>
@endsection
