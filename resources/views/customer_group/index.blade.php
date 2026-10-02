@extends('layouts.app')
@section('title', __('lang_v1.customer_groups'))

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

    .manage-customer-groups-content {
        background-color: {{ $primary_hex }}0d !important; /* Soft 5% opacity tint of business theme color */
        padding: 24px !important;
        min-height: calc(100vh - 100px);
    }
    .search-section {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e140 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .datatable-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    #customer_groups_table_wrapper > .row:first-child,
    #customer_groups_table_wrapper .dataTables_length,
    #customer_groups_table_wrapper .dt-buttons {
        display: none !important;
    }
    #custom_export_container .btn-group {
        margin: 0 !important;
    }
    #custom_export_container .buttons-collection {
        background-color: #ffffff !important;
        background-image: none !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        white-space: nowrap !important;
    }
    #custom_export_container .buttons-collection:hover,
    #custom_export_container .buttons-collection:focus,
    #custom_export_container .buttons-collection:active,
    #custom_export_container .buttons-collection.dt-button-active,
    #custom_export_container .buttons-collection.dt-button-active:hover {
        background-color: #f8fafc !important;
        background-image: none !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    #custom_length_container .dataTables_length {
        margin: 0 !important;
    }
    #custom_length_container .dataTables_length label {
        margin: 0 !important;
        font-weight: 500 !important;
        color: #475569 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 13px !important;
        white-space: nowrap !important;
    }
    #custom_length_container .dataTables_length select {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 6px 12px !important;
        background-color: #ffffff !important;
        outline: none !important;
        height: auto !important;
    }
    div.dt-button-collection {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        padding: 8px !important;
        z-index: 1050 !important;
    }
    div.dt-button-collection a.dt-button {
        background: none !important;
        border: none !important;
        color: #334155 !important;
        padding: 8px 16px !important;
        text-align: left !important;
        display: block !important;
        width: 100% !important;
        font-size: 14px !important;
        border-radius: 4px !important;
    }
    div.dt-button-collection a.dt-button:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    /* Add Group Button styling & hover animation */
    .add-group-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-group-btn:hover {
        filter: brightness(90%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.15) !important;
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
    
    /* Styled datatable processing alert */
    #customer_groups_table_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 200px;
        margin-left: -100px;
        margin-top: -26px;
        text-align: center;
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

    /* Enforce single-line text and uniform row heights */
    #customer_groups_table th,
    #customer_groups_table th *,
    #customer_groups_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Hide native Datatables processing alert */
    .dataTables_processing,
    #customer_groups_table_processing {
        display: none !important;
    }

    /* Prevent header row vertical stretching during initialization/loading */
    .dataTables_scrollHead #customer_groups_table thead tr {
        height: 50px !important;
    }
    .dataTables_scrollHead #customer_groups_table thead th {
        height: 50px !important;
        vertical-align: middle !important;
    }

    /* Force scrollBody duplicate headers to collapse to 0px height */
    .dataTables_scrollBody #customer_groups_table thead tr,
    .dataTables_scrollBody #customer_groups_table thead th,
    .dataTables_scrollBody #customer_groups_table thead td {
        height: 0px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        border-top-width: 0px !important;
        border-bottom-width: 0px !important;
    }

    /* Customer group action buttons horizontal flex styling */
    .contact-actions-wrapper {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 6px !important;
        align-items: center !important;
    }
    .contact-actions-wrapper button.tw-dw-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        padding: 0 !important;
        border-radius: 6px !important;
        transition: all 0.2s ease-in-out !important;
        font-size: 12px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
    }
    .contact-actions-wrapper button.tw-dw-btn-warning {
        color: #f59e0b !important;
    }
    .contact-actions-wrapper button.tw-dw-btn-warning:hover {
        background-color: #f59e0b !important;
        color: #ffffff !important;
        border-color: #f59e0b !important;
    }
    .contact-actions-wrapper button.tw-dw-btn-error {
        color: #ef4444 !important;
    }
    .contact-actions-wrapper button.tw-dw-btn-error:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.customer_groups')</h1>
    </div>
</section>

<!-- Main content -->
<section class="content manage-customer-groups-content">
    
    <!-- Search & Controls Section -->
    <div class="search-section">
        <!-- Top Row: Search Input + Add Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; width: 100%;">
            <!-- Search Input -->
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_group_search" 
                           placeholder="Search customer groups..." 
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <!-- Add Button right to it -->
            @can('customer.create')
                <a class="tw-dw-btn add-group-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0 btn-modal" 
                   data-href="{{ action([\App\Http\Controllers\CustomerGroupController::class, 'create']) }}"
                   data-container=".customer_groups_modal"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang( 'messages.add' )
                </a>
            @endcan
        </div>

        <!-- Divider Line -->
        <hr style="border-top: 1px solid #cbd5e160; margin: 15px 0;">

        <!-- Bottom Row: Show Entries and Export dropdown below search on the left -->
        <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
            <!-- Static length selector -->
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show') 
                    <select id="custom_group_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="group-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="group-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Section -->
    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('lang_v1.all_your_customer_groups')</h3>
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable" id="customer_groups_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="sorting">@lang('lang_v1.customer_group_name')</th>
                            <th class="sorting">@lang('lang_v1.calculation_percentage')</th>
                            <th>@lang('lang_v1.selling_price_group')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                @for ($j = 0; $j < 4; $j++)
                                    <td><div class="skeleton-block" style="width: {{ rand(40, 85) }}%;"></div></td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        @endcan
    </div>

    <div class="modal fade customer_groups_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Bind custom search input to Datatable filter
            $('#custom_group_search').on('keyup', function() {
                if (typeof customer_groups_table !== 'undefined') {
                    customer_groups_table.search(this.value).draw();
                }
            });

            // Initialize delegated tooltips for customer group action icon buttons
            $('body').tooltip({
                selector: '[data-toggle="tooltip"]',
                trigger: 'hover'
            });

            // Bind static show entries select to Datatable length
            $('#custom_group_length').on('change', function() {
                if (typeof customer_groups_table !== 'undefined') {
                    customer_groups_table.page.len($(this).val()).draw();
                }
            });

            // Bind static Export options to Datatable triggers
            $(document).on('click', '.group-export-opt', function(e) {
                e.preventDefault();
                var format = $(this).data('format');
                if (typeof customer_groups_table !== 'undefined') {
                    customer_groups_table.button('.buttons-' + format).trigger();
                }
            });
        });

        $(document).on('change', '#price_calculation_type', function() {
            var price_calculation_type = $(this).val();

            if (price_calculation_type == 'percentage') {
                $('.percentage-field').removeClass('hide');
                $('.selling_price_group-field').addClass('hide');
            } else {
                $('.percentage-field').addClass('hide');
                $('.selling_price_group-field').removeClass('hide');
            }
        });
    </script>
@endsection
