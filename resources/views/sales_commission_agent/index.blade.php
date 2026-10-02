@extends('layouts.app')
@section('title', __('lang_v1.sales_commission_agents'))

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
    /* Content Header styling */
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .manage-agents-content {
        background-color: {{ $primary_hex }}0d !important; /* Soft 5% opacity tint of business theme color */
        padding: 24px !important;
        min-height: calc(100vh - 100px);
    }
    .search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .datatable-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    /* Add Agent Button styling & hover animation */
    .add-agent-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-agent-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    /* Hide default Datatables show entries length select and export buttons */
    #sales_commission_agent_table_wrapper > .row:first-child,
    #sales_commission_agent_table_wrapper .dataTables_length,
    #sales_commission_agent_table_wrapper .dt-buttons {
        display: none !important;
    }

    /* Enforce single-line text and uniform row heights */
    #sales_commission_agent_table th,
    #sales_commission_agent_table th *,
    #sales_commission_agent_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Hide native Datatables processing alert */
    .dataTables_processing,
    #sales_commission_agent_table_processing {
        display: none !important;
    }

    /* Skeleton Loader CSS */
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
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
        padding: 1em 0;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        color: #334155 !important;
        font-weight: 600 !important;
        z-index: 100;
        font-size: 14px;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang( 'lang_v1.sales_commission_agents' )</h1>
    </div>
</section>

<!-- Main content -->
<section class="content manage-agents-content">
    
    <!-- Search Section -->
    <div class="search-section">
        <!-- Top Row: Search Input + Add Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <!-- Search Input -->
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_agent_search" 
                           placeholder="Search sales commission agents..." 
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <!-- Add Button right to it -->
            @can('user.create')
                <a class="tw-dw-btn add-agent-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0 btn-modal" 
                   data-href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'create'])}}" 
                   data-container=".commission_agent_modal"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang( 'messages.add' )
                </a>
            @endcan
        </div>

        <!-- Bottom Row: Show Entries and Export dropdown below search on the left -->
        <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
            <!-- Static length selector -->
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show') 
                    <select id="custom_agent_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="-1">@lang('lang_v1.all')</option>
                    </select>
                    @lang('lang_v1.entries')
                </label>
            </div>

            <!-- Static Export button dropdown -->
            <div style="display: inline-block;">
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 8px 16px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px;">
                        <i class="fa fa-share-square"></i> @lang('lang_v1.export') <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 8px; min-width: 120px;">
                        <li><a href="#" class="agent-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="agent-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Section -->
    <div class="datatable-section">
        @can('user.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable" id="sales_commission_agent_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="sorting">@lang( 'user.name' )</th>
                            <th class="sorting">@lang( 'business.email' )</th>
                            <th class="sorting">@lang( 'lang_v1.contact_no' )</th>
                            <th class="sorting">@lang( 'business.address' )</th>
                            <th class="sorting">@lang( 'lang_v1.cmmsn_percent' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                    <tbody id="sales_commission_agent_skeleton">
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                <td><div class="skeleton-block" style="width: 60%;"></div></td>
                                <td><div class="skeleton-block" style="width: 75%;"></div></td>
                                <td><div class="skeleton-block" style="width: 40%;"></div></td>
                                <td><div class="skeleton-block" style="width: 70%;"></div></td>
                                <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                <td><div class="skeleton-block" style="width: 45%;"></div></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        @endcan
    </div>

    <div class="modal fade commission_agent_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Destroy the default DataTable initialized in app.js to avoid reinitialization errors
        if ($.fn.DataTable.isDataTable('#sales_commission_agent_table')) {
            $('#sales_commission_agent_table').DataTable().destroy();
        }

        var sales_commission_agent_table = $('#sales_commission_agent_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            ajax: '/sales-commission-agents',
            columnDefs: [ {
                "targets": 5,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'full_name' },
                { data: 'email' },
                { data: 'contact_no' },
                { data: 'address' },
                { data: 'cmmsn_percent' },
                { data: 'action' }
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
                            exportOptions: { columns: [0, 1, 2, 3, 4] }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel"></i> Excel',
                            exportOptions: { columns: [0, 1, 2, 3, 4] }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf"></i> PDF',
                            exportOptions: { columns: [0, 1, 2, 3, 4] }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            exportOptions: { columns: [0, 1, 2, 3, 4], stripHtml: true }
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fa fa-columns"></i> Columns'
                        }
                    ]
                }
            ],
            dom: 'lBrtip',
            initComplete: function(settings, json) {
                // Remove only the skeleton rows, preserving the table body container
                $('#sales_commission_agent_table .skeleton-row').remove();
            }
        });

        // Bind static show entries select to Datatable length
        $('#custom_agent_length').on('change', function() {
            if (typeof sales_commission_agent_table !== 'undefined') {
                sales_commission_agent_table.page.len($(this).val()).draw();
            }
        });

        // Bind static Export options to Datatable triggers
        $(document).on('click', '.agent-export-opt', function(e) {
            e.preventDefault();
            var format = $(this).data('format');
            if (typeof sales_commission_agent_table !== 'undefined') {
                sales_commission_agent_table.button('.buttons-' + format).trigger();
            }
        });

        // Custom search trigger
        $('#custom_agent_search').on('keyup', function() {
            sales_commission_agent_table.search(this.value).draw();
        });

        // Auto-reload datatable on successful create, update or delete actions
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url && settings.url.indexOf('/sales-commission-agents') !== -1) {
                if (settings.type === 'POST' || settings.type === 'DELETE' || settings.type === 'PUT') {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response && response.success === true) {
                            sales_commission_agent_table.ajax.reload();
                        }
                    } catch (e) {
                        if (xhr.status === 200) {
                            sales_commission_agent_table.ajax.reload();
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
