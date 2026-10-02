@extends('layouts.app')
@section('title', __('lang_v1.' . $type . 's'))
@php
    $api_key = env('GOOGLE_MAP_API_KEY');
@endphp
@if (!empty($api_key))
    @section('css')
        @include('contact.partials.google_map_styles')
    @endsection
@endif

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

    .manage-contacts-content {
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
    #contact_table_wrapper > .row:first-child,
    #contact_table_wrapper .dataTables_length,
    #contact_table_wrapper .dt-buttons {
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

    /* Add Contact Button styling & hover animation */
    .add-contact-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-contact-btn:hover {
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
    #contact_table_processing {
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
    #contact_table th,
    #contact_table th *,
    #contact_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Contact action buttons horizontal flex styling */
    .contact-actions-wrapper {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 6px !important;
        align-items: center !important;
    }
    .contact-actions-wrapper a.tw-dw-btn {
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
    .contact-actions-wrapper a.tw-dw-btn-primary {
        color: {{ $primary_hex }} !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-primary:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-info {
        color: #0ea5e9 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-info:hover {
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
        border-color: #0ea5e9 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-warning {
        color: #f59e0b !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-warning:hover {
        background-color: #f59e0b !important;
        color: #ffffff !important;
        border-color: #f59e0b !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-error {
        color: #ef4444 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-error:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-accent {
        color: #8b5cf6 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-accent:hover {
        background-color: #8b5cf6 !important;
        color: #ffffff !important;
        border-color: #8b5cf6 !important;
    }
    #toggle_contact_filters {
        height: 42px !important;
        width: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
    }
    #toggle_contact_filters:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    #toggle_contact_filters.filter-active {
        background-color: {{ $primary_hex }}1a !important;
        color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }}4d !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
    }

    /* Hide native Datatables processing alert */
    .dataTables_processing,
    #contact_table_processing {
        display: none !important;
    }

    /* Prevent header row vertical stretching during initialization/loading */
    .dataTables_scrollHead #contact_table thead tr {
        height: 50px !important;
    }
    .dataTables_scrollHead #contact_table thead th {
        height: 50px !important;
        vertical-align: middle !important;
    }

    /* Force scrollBody duplicate headers to collapse to 0px height */
    .dataTables_scrollBody #contact_table thead tr,
    .dataTables_scrollBody #contact_table thead th,
    .dataTables_scrollBody #contact_table thead td {
        height: 0px !important;
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        border-top-width: 0px !important;
        border-bottom-width: 0px !important;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.' . $type . 's')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('contact.manage_your_contact', ['contacts' => __('lang_v1.' . $type . 's')])</p>
    </div>
</section>

<!-- Main content -->
<section class="content manage-contacts-content">
    
    <!-- Search & Controls Section -->
    <div class="search-section">
        <!-- Top Row: Search Input + Toggle Filter + Add Button -->
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; width: 100%;">
            <!-- Search Input -->
            <div style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_contact_search" 
                           placeholder="Search contacts..." 
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <!-- Toggle Filters Icon Button next to the add button -->
            <button type="button" id="toggle_contact_filters" class="tw-dw-btn tw-flex-shrink-0" title="Toggle Filters" data-toggle="tooltip">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
            </button>

            <!-- Add Button right to it -->
            @if (auth()->user()->can('supplier.create') ||
                    auth()->user()->can('customer.create') ||
                    auth()->user()->can('supplier.view_own') ||
                    auth()->user()->can('customer.view_own'))
                <a class="tw-dw-btn add-contact-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0 btn-modal" 
                   data-href="{{ action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => $type]) }}"
                   data-container=".contact_modal"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang( 'messages.add' )
                </a>
            @endif
        </div>

        <!-- Collapsible Filters Section wrapper -->
        <div id="collapsible_contact_filters" style="display: none;">
            <!-- Integrated Filters Section -->
            <div class="row" style="margin-bottom: 10px;">
                @if ($type == 'customer')
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                                {!! Form::checkbox('has_sell_due', 1, false, ['class' => 'input-icheck', 'id' => 'has_sell_due']) !!} 
                                <strong>@lang('lang_v1.sell_due')</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                                {!! Form::checkbox('has_sell_return', 1, false, ['class' => 'input-icheck', 'id' => 'has_sell_return']) !!} 
                                <strong>@lang('lang_v1.sell_return')</strong>
                            </label>
                        </div>
                    </div>
                @elseif($type == 'supplier')
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                                {!! Form::checkbox('has_purchase_due', 1, false, ['class' => 'input-icheck', 'id' => 'has_purchase_due']) !!} 
                                <strong>@lang('report.purchase_due')</strong>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                                {!! Form::checkbox('has_purchase_return', 1, false, ['class' => 'input-icheck', 'id' => 'has_purchase_return']) !!} 
                                <strong>@lang('lang_v1.purchase_return')</strong>
                            </label>
                        </div>
                    </div>
                @endif
                <div class="col-md-3 col-sm-6">
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                            {!! Form::checkbox('has_advance_balance', 1, false, ['class' => 'input-icheck', 'id' => 'has_advance_balance']) !!} 
                            <strong>@lang('lang_v1.advance_balance')</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px; color: #475569; cursor: pointer;">
                            {!! Form::checkbox('has_opening_balance', 1, false, ['class' => 'input-icheck', 'id' => 'has_opening_balance']) !!} 
                            <strong>@lang('lang_v1.opening_balance')</strong>
                        </label>
                    </div>
                </div>

                @if ($type == 'customer')
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label for="has_no_sell_from" style="font-weight: 600; font-size: 12px; color: #475569; display: block; margin-bottom: 4px;">@lang('lang_v1.has_no_sell_from'):</label>
                            {!! Form::select(
                                'has_no_sell_from',
                                [
                                    'one_month' => __('lang_v1.one_month'),
                                    'three_months' => __('lang_v1.three_months'),
                                    'six_months' => __('lang_v1.six_months'),
                                    'one_year' => __('lang_v1.one_year'),
                                ],
                                null,
                                ['class' => 'form-control', 'id' => 'has_no_sell_from', 'placeholder' => __('messages.please_select'), 'style' => 'border-radius: 8px; border: 1px solid #cbd5e1; height: 38px; font-size: 13px;'],
                            ) !!}
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label for="cg_filter" style="font-weight: 600; font-size: 12px; color: #475569; display: block; margin-bottom: 4px;">@lang('lang_v1.customer_group'):</label>
                            {!! Form::select('cg_filter', $customer_groups, null, ['class' => 'form-control', 'id' => 'cg_filter', 'style' => 'border-radius: 8px; border: 1px solid #cbd5e1; height: 38px; font-size: 13px;']) !!}
                        </div>
                    </div>
                @endif

                @if (config('constants.enable_contact_assign') === true)
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom: 12px;">
                            {!! Form::label('assigned_to', __('lang_v1.assigned_to') . ':', ['style' => 'font-weight: 600; font-size: 12px; color: #475569; display: block; margin-bottom: 4px;']) !!}
                            {!! Form::select('assigned_to', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%; border-radius: 8px; height: 38px;']) !!}
                        </div>
                    </div>
                @endif

                <div class="col-md-3 col-sm-6">
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label for="status_filter" style="font-weight: 600; font-size: 12px; color: #475569; display: block; margin-bottom: 4px;">@lang('sale.status'):</label>
                        {!! Form::select(
                            'status_filter',
                            ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')],
                            null,
                            ['class' => 'form-control', 'id' => 'status_filter', 'placeholder' => __('lang_v1.none'), 'style' => 'border-radius: 8px; border: 1px solid #cbd5e1; height: 38px; font-size: 13px;'],
                        ) !!}
                    </div>
                </div>
            </div>
            <!-- Divider Line inside the collapsible wrapper -->
            <hr style="border-top: 1px solid #cbd5e160; margin: 15px 0;">
        </div>

        <!-- Bottom Row: Show Entries and Export dropdown below search on the left -->
        <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
            <!-- Static length selector -->
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show') 
                    <select id="custom_contact_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="contact-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="contact-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" value="{{ $type }}" id="contact_type">

    <!-- DataTable Section -->
    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('contact.all_your_contact', ['contacts' => __('lang_v1.' . $type . 's')])</h3>
        @if (auth()->user()->can('supplier.view') ||
                auth()->user()->can('customer.view') ||
                auth()->user()->can('supplier.view_own') ||
                auth()->user()->can('customer.view_own'))
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable" id="contact_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th class="sorting">@lang('lang_v1.contact_id')</th>
                            @if ($type == 'supplier')
                                <th class="sorting">@lang('business.business_name')</th>
                                <th class="sorting">@lang('contact.name')</th>
                                <th class="sorting">@lang('business.email')</th>
                                <th class="sorting">@lang('contact.tax_no')</th>
                                <th class="sorting">@lang('contact.pay_term')</th>
                                <th class="sorting">@lang('account.opening_balance')</th>
                                <th class="sorting">@lang('lang_v1.advance_balance')</th>
                                <th class="sorting">@lang('lang_v1.added_on')</th>
                                <th class="sorting">@lang('business.address')</th>
                                <th class="sorting">@lang('contact.mobile')</th>
                                <th class="sorting">@lang('contact.total_purchase_due')</th>
                                <th class="sorting">@lang('lang_v1.total_purchase_return_due')</th>
                            @elseif($type == 'customer')
                                <th class="sorting">@lang('business.business_name')</th>
                                <th class="sorting">@lang('user.name')</th>
                                <th class="sorting">@lang('business.email')</th>
                                <th class="sorting">@lang('contact.tax_no')</th>
                                <th class="sorting">@lang('lang_v1.credit_limit')</th>
                                <th class="sorting">@lang('contact.pay_term')</th>
                                <th class="sorting">@lang('account.opening_balance')</th>
                                <th class="sorting">@lang('lang_v1.advance_balance')</th>
                                <th class="sorting">@lang('lang_v1.added_on')</th>
                                @if ($reward_enabled)
                                    <th id="rp_col" class="sorting">{{ session('business.rp_name') }}</th>
                                @endif
                                <th class="sorting">@lang('lang_v1.customer_group')</th>
                                <th class="sorting">@lang('business.address')</th>
                                <th class="sorting">@lang('contact.mobile')</th>
                                <th class="sorting">@lang('contact.total_sale_due')</th>
                                <th class="sorting">@lang('lang_v1.total_sell_return_due')</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="contact_table_skeleton_rows">
                        @php
                            $col_count = $type == 'supplier' ? 14 : ($reward_enabled ? 17 : 16);
                        @endphp
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                @for ($j = 0; $j < $col_count; $j++)
                                    <td><div class="skeleton-block" style="width: {{ rand(40, 85) }}%;"></div></td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td @if ($type == 'supplier') colspan="6"
                        @elseif($type == 'customer')
                            @if ($reward_enabled)
                                colspan="9"
                            @else
                                colspan="8" @endif
                                @endif>
                                <strong>
                                    @lang('sale.total'):
                                </strong>
                            </td>
                            <td class="footer_contact_due"></td>
                            <td class="footer_contact_return_due"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Bind custom search input to Datatable filter
            $('#custom_contact_search').on('keyup', function() {
                if (typeof contact_table !== 'undefined') {
                    contact_table.search(this.value).draw();
                }
            });

            // Initialize delegated tooltips for contact action icon buttons
            $('body').tooltip({
                selector: '[data-toggle="tooltip"]',
                trigger: 'hover'
            });

            // Slide toggle collapsible contact filters panel
            $('#toggle_contact_filters').on('click', function() {
                $('#collapsible_contact_filters').slideToggle(300);
                $(this).toggleClass('filter-active');
            });

            // Bind static show entries select to Datatable length
            $('#custom_contact_length').on('change', function() {
                if (typeof contact_table !== 'undefined') {
                    contact_table.page.len($(this).val()).draw();
                }
            });

            // Bind static Export options to Datatable triggers
            $(document).on('click', '.contact-export-opt', function(e) {
                e.preventDefault();
                var format = $(this).data('format');
                if (typeof contact_table !== 'undefined') {
                    contact_table.button('.buttons-' + format).trigger();
                }
            });
        });
    </script>

    @if (!empty($api_key))
        <script>
            // This example adds a search box to a map, using the Google Place Autocomplete
            // feature. People can enter geographical searches. The search box will return a
            // pick list containing a mix of places and predicted search terms.

            // This example requires the Places library. Include the libraries=places
            // parameter when you first load the API. For example:
            // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

            function initAutocomplete() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: -33.8688,
                        lng: 151.2195
                    },
                    zoom: 10,
                    mapTypeId: 'roadmap'
                });

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        map.setCenter(initialLocation);
                    });
                }


                // Create the search box and link it to the UI element.
                var input = document.getElementById('shipping_address');
                var searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                // Bias the SearchBox results towards current map's viewport.
                map.addListener('bounds_changed', function() {
                    searchBox.setBounds(map.getBounds());
                });

                var markers = [];
                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
                searchBox.addListener('places_changed', function() {
                    var places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    // Clear out the old markers.
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [];

                    // For each place, get the icon, name and location.
                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function(place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25)
                        };

                        // Create a marker for each place.
                        markers.push(new google.maps.Marker({
                            map: map,
                            icon: icon,
                            title: place.name,
                            position: place.geometry.location
                        }));

                        //set position field value
                        var lat_long = [place.geometry.location.lat(), place.geometry.location.lng()]
                        $('#position').val(lat_long);

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $api_key }}&libraries=places" async defer></script>
        <script type="text/javascript">
            $(document).on('shown.bs.modal', '.contact_modal', function(e) {
                initAutocomplete();
            });
        </script>
    @endif
@endsection
