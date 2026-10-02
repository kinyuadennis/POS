@extends('layouts.app')
@section('title', __('Product Requests'))

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

    .product-requests-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    /* KPI Cards */
    .kpi-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 18px 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
    }
    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .search-section,
    .datatable-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .search-section {
        padding: 20px !important;
        margin-bottom: 20px !important;
        position: relative;
        z-index: 25;
        overflow: visible;
    }
    .datatable-section {
        padding: 24px !important;
    }

    /* Buttons */
    .btn-theme-primary {
        background: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        border: none !important;
        font-weight: 600 !important;
    }
    .btn-theme-primary:hover, .btn-theme-primary:focus {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }
    .btn-theme-filter.filter-active {
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    .add-request-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 20px;
        border-radius: 20px;
        text-decoration: none;
        color: #ffffff !important;
        flex-shrink: 0;
        font-weight: 700;
        border: none;
    }
    .add-request-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    /* Filters Panel */
    .filter-panel {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .filter-label {
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }
    .filter-control {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
    }

    /* DataTables Overrides */
    #product_requests_table_wrapper > .row:first-child,
    #product_requests_table_wrapper .dataTables_length,
    #product_requests_table_wrapper .dt-buttons,
    #product_requests_table_wrapper .dataTables_filter {
        display: none !important;
    }

    #product_requests_table th,
    #product_requests_table th *,
    #product_requests_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .dataTables_processing,
    #product_requests_table_processing {
        display: none !important;
    }

    /* Skeleton Loading */
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

    /* Search Row Responsive */
    .search-action-wrapper {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 15px;
    }
    .search-input-col {
        flex-grow: 1;
        min-width: 0; /* Prevents flex blowout from long placeholders */
    }
    .search-buttons-col {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    @media (min-width: 768px) {
        .search-action-wrapper {
            flex-direction: row;
            align-items: center;
        }
        .search-buttons-col {
            flex-shrink: 0;
        }
    }

    @media (max-width: 767px) {
        .search-buttons-col {
            flex-direction: row;
        }
        .search-buttons-col > button {
            flex: 1;
            justify-content: center;
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        .search-buttons-col > a {
            flex: 3;
            justify-content: center;
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        .content-header {
            padding: 12px 16px !important;
        }
        .product-requests-content {
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
        }
        #product_requests_table {
            width: auto !important;
            min-width: 980px;
        }
        #product_requests_table th,
        #product_requests_table td {
            padding: 12px 14px !important;
            font-size: 13px;
        }
    }
</style>

<!-- Header Section -->
<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('Product Requests')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('Request products from Main Store, track approvals, and confirm arrivals')</p>
    </div>
</section>

<!-- Main Tinted Content -->
<section class="content product-requests-content no-print">
    <!-- Stats KPI Grid -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #fef3c7; color: #d97706;">
                    <i class="fa fa-clock"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $stats['pending'] }}</div>
                    <div style="font-size: 13px; font-weight: 500; color: #64748b;">@lang('Pending Review')</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $stats['approved'] }}</div>
                    <div style="font-size: 13px; font-weight: 500; color: #64748b;">@lang('Approved & Ready')</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #f3e8ff; color: #9333ea;">
                    <i class="fa fa-shipping-fast"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $stats['in_transit'] }}</div>
                    <div style="font-size: 13px; font-weight: 500; color: #64748b;">@lang('In Transit')</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-clipboard-check"></i>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2;">{{ $stats['received'] }}</div>
                    <div style="font-size: 13px; font-weight: 500; color: #64748b;">@lang('Received & Complete')</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters Card -->
    <div class="search-section">
        <!-- Top Search Row (Responsive) -->
        <div class="search-action-wrapper">
            <!-- Search Input -->
            <div class="search-input-col">
                <div style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_product_request_search"
                           placeholder="Search by request number, destination shop, source location..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <!-- Buttons Row -->
            <div class="search-buttons-col">
                <!-- Filters Toggle Button -->
                <button type="button" id="btn_toggle_filters" class="btn btn-theme-primary btn-theme-filter" style="height: 42px; padding: 0 16px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; flex-shrink: 0;">
                    <i class="fa fa-filter"></i> @lang('report.filters')
                </button>

                <!-- Pill Add Button -->
                @can('product_request.create')
                    <a class="add-request-btn"
                       href="{{ action([\App\Http\Controllers\ProductRequestController::class, 'create']) }}" style="flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        @lang('Request Products')
                    </a>
                @endcan
            </div>
        </div>

        <!-- Collapsible Filters Panel -->
        <div id="product_request_filters_panel" class="filter-panel" style="display: none;">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">@lang('Destination Shop'):</label>
                    {!! Form::select('location_filter', $business_locations, null, [
                        'class' => 'form-control select2 filter-control',
                        'id' => 'location_filter',
                        'placeholder' => __('All Shops'),
                        'style' => 'width: 100%;'
                    ]) !!}
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">@lang('Status'):</label>
                    {!! Form::select('status_filter', $statuses, 'all', [
                        'class' => 'form-control select2 filter-control',
                        'id' => 'status_filter',
                        'style' => 'width: 100%;'
                    ]) !!}
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">@lang('Priority'):</label>
                    {!! Form::select('priority_filter', ['' => __('All Priorities'), 'low' => __('Low'), 'medium' => __('Medium'), 'high' => __('High'), 'urgent' => __('Urgent')], null, [
                        'class' => 'form-control select2 filter-control',
                        'id' => 'priority_filter',
                        'style' => 'width: 100%;'
                    ]) !!}
                </div>
                <div class="col-md-3 col-sm-6" style="padding-top: 26px;">
                    <button type="button" id="btn_reset_filters" class="btn btn-default" style="height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; width: 100%;">
                        <i class="fa fa-sync-alt"></i> @lang('messages.reset')
                    </button>
                </div>
            </div>
        </div>

        <!-- Show entries + Export Row -->
        <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_product_request_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="product-request-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="product-request-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('All Product Requests')</h3>
        <div class="table-responsive pr-table-wrap">
            <table class="table table-bordered table-striped ajax_view" id="product_requests_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('Request #')</th>
                        <th>@lang('Destination Shop')</th>
                        <th>@lang('Source Main Store')</th>
                        <th>@lang('Requested By')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Items / Units')</th>
                        <th>@lang('Priority')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            <td><div class="skeleton-block" style="width: 70%;"></div></td>
                            <td><div class="skeleton-block" style="width: 65%;"></div></td>
                            <td><div class="skeleton-block" style="width: 65%;"></div></td>
                            <td><div class="skeleton-block" style="width: 55%;"></div></td>
                            <td><div class="skeleton-block" style="width: 45%;"></div></td>
                            <td><div class="skeleton-block" style="width: 60%;"></div></td>
                            <td><div class="skeleton-block" style="width: 40%;"></div></td>
                            <td><div class="skeleton-block" style="width: 50%;"></div></td>
                            <td><div class="skeleton-block" style="width: 60%;"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var product_requests_table = $('#product_requests_table').DataTable({
        processing: false,
        serverSide: true,
        aaSorting: [[4, 'desc']],
        dom: 'lBrtip',
        pageLength: 25,
        buttons: [
            {
                extend: 'csv',
                text: 'CSV',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        ajax: {
            url: "{{ action([\App\Http\Controllers\ProductRequestController::class, 'index']) }}",
            data: function(d) {
                d.status = $('#status_filter').val();
                d.location_id = $('#location_filter').val();
                d.priority = $('#priority_filter').val();
            }
        },
        columns: [
            { data: 'request_no', name: 'request_no' },
            { data: 'requesting_location_name', name: 'bl_req.name' },
            { data: 'source_location_name', name: 'bl_src.name' },
            { data: 'requested_by', name: 'u.first_name' },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'total_items',
                name: 'total_items',
                searchable: false,
                render: function(data, type, row) {
                    return '<span style="font-weight:600;">' + data + ' items</span> <span style="color:#64748b;font-size:11px;">(' + parseFloat(row.total_units || 0) + ' units)</span>';
                }
            },
            { data: 'priority', name: 'priority' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        initComplete: function() {
            $('#product_requests_table tbody .skeleton-row').remove();
        },
        fnDrawCallback: function(oSettings) {
            $('#product_requests_table [data-toggle="tooltip"]').tooltip();
        }
    });

    // Custom Live Search
    $('#custom_product_request_search').on('keyup', function() {
        product_requests_table.search(this.value).draw();
    });

    // Custom Page Length
    $('#custom_product_request_length').on('change', function() {
        product_requests_table.page.len($(this).val()).draw();
    });

    // Custom Export triggers
    $(document).on('click', '.product-request-export-opt', function(e) {
        e.preventDefault();
        product_requests_table.button('.buttons-' + $(this).data('format')).trigger();
    });

    // Toggle Filters Panel
    $('#btn_toggle_filters').on('click', function() {
        var $panel = $('#product_request_filters_panel');
        $(this).toggleClass('filter-active');
        $panel.slideToggle(300, function() {
            if ($panel.is(':visible')) {
                $panel.find('.select2-container').css('width', '100%');
            }
        });
    });

    // Filters Change
    $('#status_filter, #location_filter, #priority_filter').change(function() {
        product_requests_table.ajax.reload();
    });

    // Reset Filters
    $('#btn_reset_filters').click(function() {
        $('#status_filter').val('all').trigger('change');
        $('#location_filter').val('').trigger('change');
        $('#priority_filter').val('').trigger('change');
        $('#custom_product_request_search').val('');
        product_requests_table.search('').ajax.reload();
    });
});
</script>
@endsection