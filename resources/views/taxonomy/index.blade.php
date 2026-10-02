@extends('layouts.app')
@php
    $heading = !empty($module_category_data['heading']) ? $module_category_data['heading'] : __('category.categories');
    $navbar = !empty($module_category_data['navbar']) ? $module_category_data['navbar'] : null;
@endphp
@section('title', $heading)

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

    .manage-category-content {
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
    /* Add Category Button styling & hover animation */
    .add-category-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-category-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    /* Hide default Datatables show entries length select and export buttons */
    #category_table_wrapper > .row:first-child,
    #category_table_wrapper .dataTables_length,
    #category_table_wrapper .dt-buttons {
        display: none !important;
    }

    /* Enforce single-line text and uniform row heights */
    #category_table th,
    #category_table th *,
    #category_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Hide native Datatables processing alert */
    .dataTables_processing,
    #category_table_processing {
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
</style>

    @if (!empty($navbar))
        @include($navbar)
    @endif

    <!-- Content Header (Page header) -->
    <section class="content-header tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                {{ $heading }}
                @if (isset($module_category_data['heading_tooltip']))
                    @show_tooltip($module_category_data['heading_tooltip'])
                @endif
            </h1>
            <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">
                {{ $module_category_data['sub_heading'] ?? __('category.manage_your_categories') }}
            </p>
        </div>
    </section>

    <!-- Main content -->
    <section class="content manage-category-content">
        @php
            $cat_code_enabled =
                isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code']
                    ? false
                    : true;
        @endphp
        <input type="hidden" id="category_type" value="{{ request()->get('type') }}">
        @php
            $can_add = true;
            if (request()->get('type') == 'product' && !auth()->user()->can('category.create')) {
                $can_add = false;
            }
        @endphp

        <!-- Search & Controls Section -->
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
                        <input type="text" id="custom_category_search" 
                               placeholder="Search categories..." 
                               class="form-control"
                               style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>
                @if ($can_add)
                    <!-- Add Button right to search -->
                    <button class="tw-dw-btn add-category-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0 btn-modal" 
                            data-href="{{action([\App\Http\Controllers\TaxonomyController::class, 'create'])}}?type={{request()->get('type')}}" 
                            data-container=".category_modal"
                            style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        @lang( 'messages.add' )
                    </button>
                @endif
            </div>

            <!-- Bottom Row: Show Entries and Export dropdown below search on the left -->
            <div class="tw-flex tw-items-center tw-justify-start tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px; width: 100%;">
                <!-- Static length selector -->
                <div style="display: inline-block;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                        @lang('lang_v1.show') 
                        <select id="custom_category_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                            <li><a href="#" class="category-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                            <li><a href="#" class="category-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Section -->
        <div class="datatable-section">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4" style="margin-top: 0; margin-bottom: 20px;">
                <h3 class="tw-text-lg tw-font-bold tw-text-slate-800" style="margin: 0; font-weight: 700; color: #1e293b;">
                    {{ $heading }}
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable" id="category_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>
                                @if (!empty($module_category_data['taxonomy_label']))
                                    {{ $module_category_data['taxonomy_label'] }}
                                @else
                                    @lang('category.category')
                                @endif
                            </th>
                            @if ($cat_code_enabled)
                                <th>{{ $module_category_data['taxonomy_code_label'] ?? __('category.code') }}</th>
                            @endif
                            <th>@lang('lang_v1.description')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                    <tbody id="category_table_body">
                        {{-- Skeleton loading placeholder rows --}}
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="skeleton-row">
                                <td><div class="skeleton-block" style="width: 65%;"></div></td>
                                @if ($cat_code_enabled)
                                    <td><div class="skeleton-block" style="width: 35%;"></div></td>
                                @endif
                                <td><div class="skeleton-block" style="width: 50%;"></div></td>
                                <td><div class="skeleton-block" style="width: 40%;"></div></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop

@section('javascript')
    @includeIf('taxonomy.taxonomies_js')
@endsection
