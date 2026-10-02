@extends('layouts.app')
@section('title', __('expense.expenses'))

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

    $default_location = '';
    foreach ($business_locations as $id => $name) {
        if ($id !== '' && $id !== null && $name === 'Vawa Scents') {
            $default_location = $id;
            break;
        }
    }
    if ($default_location === '' || $default_location === null) {
        foreach ($business_locations as $id => $name) {
            if ($id !== '' && $id !== null && stripos($name, 'Vawa Scents') !== false) {
                $default_location = $id;
                break;
            }
        }
    }
@endphp

<style>
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .expenses-list-content {
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

    .expense-filters-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .expense-filter-field {
        min-width: 0;
        width: 100%;
    }
    #collapsible_expense_filters {
        display: none;
        margin-bottom: 16px;
    }
    #toggle_expense_filters {
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
    #toggle_expense_filters:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    #toggle_expense_filters.filter-active {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
        opacity: 1 !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}40 !important;
    }

    .add-expense-btn {
        background: {{ $primary_hex }} !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .add-expense-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    .expense-search-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .expense-filters-row {
            flex-direction: row;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .expense-filter-field {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .expenses-list-content {
            padding: 12px !important;
        }
        .search-section,
        .datatable-section {
            padding: 14px !important;
        }
        .expense-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -4px;
        }
        .expense-search-row {
            flex-wrap: wrap !important;
            align-items: stretch !important;
        }
        .expense-search-input {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
        .expense-search-actions {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        .expense-search-actions #toggle_expense_filters,
        .expense-search-actions .add-expense-btn {
            flex: 1 1 auto;
            min-width: 0;
            justify-content: center !important;
        }
        #expense_table {
            width: auto !important;
            min-width: 900px;
        }
        #expense_table th,
        #expense_table td {
            padding: 12px 14px !important;
            font-size: 13px;
        }
    }

    #expense_table_wrapper > .row:first-child,
    #expense_table_wrapper .dataTables_length,
    #expense_table_wrapper .dt-buttons,
    #expense_table_wrapper .dataTables_filter {
        display: none !important;
    }

    #expense_table th,
    #expense_table th *,
    #expense_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .dataTables_processing,
    #expense_table_processing {
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

    body > .daterangepicker.expense-daterangepicker {
        z-index: 4000;
    }
    body > .daterangepicker.expense-daterangepicker .drp-buttons {
        display: block !important;
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 2;
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('expense.expenses')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('expense.manage_expenses')</p>
    </div>
</section>

<section class="content expenses-list-content">
    <div class="search-section">
        <div class="expense-search-row tw-flex tw-items-center tw-gap-4 tw-mb-4" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%;">
            <div class="expense-search-input" style="flex-grow: 1;">
                <div class="tw-relative" style="position: relative; width: 100%;">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="custom_expense_search"
                           placeholder="Search by reference, category, location or note..."
                           class="form-control"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                </div>
            </div>
            <div class="expense-search-actions">
            <button type="button" id="toggle_expense_filters" class="tw-flex-shrink-0" title="Toggle filters" aria-expanded="false" aria-controls="collapsible_expense_filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-5.828 5.828a2 2 0 0 0 -.586 1.414v4.586a1 1 0 0 1 -.4 0.8l-4 3a1 1 0 0 1 -1.6 -.8v-7.586a2 2 0 0 0 -.586 -1.414l-5.828 -5.828a2 2 0 0 1 -.586 -1.414v-2.172z" /></svg>
                @lang('report.filters')
            </button>
            @can('expense.add')
                <a class="tw-dw-btn add-expense-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0"
                   href="{{action([\App\Http\Controllers\ExpenseController::class, 'create'])}}"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang('messages.add')
                </a>
                <a class="tw-dw-btn add-expense-btn tw-text-white tw-border-none tw-rounded-full tw-flex-shrink-0"
                   href="{{action([\App\Http\Controllers\ExpenseController::class, 'importExpense'])}}"
                   style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 20px; border-radius: 20px; text-decoration: none; color: #ffffff; flex-shrink: 0; font-weight: 700;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    @lang('expense.import_expense')
                </a>
            @endcan
            </div>
        </div>

        <div id="collapsible_expense_filters">
            <div class="expense-filters-row">
                @if(auth()->user()->can('all_expense.access'))
                    <div class="form-group expense-filter-field">
                        {!! Form::label('location_id', __('purchase.business_location')) !!}
                        {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-location-filter' => '1']); !!}
                    </div>
                @endif
                <div class="form-group expense-filter-field">
                    {!! Form::label('expense_date_range', __('report.date_range')) !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                </div>
                @if(auth()->user()->can('all_expense.access'))
                    <div class="form-group expense-filter-field">
                        {!! Form::label('expense_for', __('expense.expense_for')) !!}
                        {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-depends-on-location' => 'users']); !!}
                    </div>
                    <div class="form-group expense-filter-field">
                        {!! Form::label('created_by', __('lang_v1.added_by')) !!}
                        {!! Form::select('created_by', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'data-depends-on-location' => 'users']); !!}
                    </div>
                    <div class="form-group expense-filter-field">
                        {!! Form::label('expense_contact_filter', __('contact.contact')) !!}
                        {!! Form::select('expense_contact_filter', $contacts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'data-depends-on-location' => 'contacts']); !!}
                    </div>
                @endif
                <div class="form-group expense-filter-field">
                    {!! Form::label('expense_category_id', __('expense.expense_category')) !!}
                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_category_id']); !!}
                </div>
                <div class="form-group expense-filter-field">
                    {!! Form::label('expense_sub_category_id_filter', __('product.sub_category')) !!}
                    {!! Form::select('expense_sub_category_id_filter', $sub_categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'expense_sub_category_id_filter']); !!}
                </div>
                <div class="form-group expense-filter-field">
                    {!! Form::label('expense_payment_status', __('purchase.payment_status')) !!}
                    {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        </div>

        <div class="tw-flex tw-items-center tw-justify-start tw-flex-wrap tw-gap-4" style="display: flex; align-items: center; justify-content: flex-start; flex-wrap: wrap; gap: 16px; width: 100%;">
            <div style="display: inline-block;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                    @lang('lang_v1.show')
                    <select id="custom_expense_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
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
                        <li><a href="#" class="expense-export-opt" data-format="csv" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-csv" style="margin-right: 8px;"></i> CSV</a></li>
                        <li><a href="#" class="expense-export-opt" data-format="excel" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-file-excel" style="margin-right: 8px;"></i> Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="datatable-section">
        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 tw-mb-4" style="margin-top: 0; margin-bottom: 20px; font-weight: 700; color: #1e293b;">@lang('expense.all_expenses')</h3>
        <div class="table-responsive expense-table-wrap">
            <table class="table table-bordered table-striped expenses-dt-owned" id="expense_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('expense.expense_category')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('purchase.payment_due')</th>
                        <th>@lang('expense.expense_for')</th>
                        <th>@lang('expense.expense_note')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="skeleton-row">
                            @for ($c = 0; $c < 11; $c++)
                                <td><div class="skeleton-block" style="width: {{ 40 + (($c * 13) % 40) }}%;"></div></td>
                            @endfor
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17 text-center footer-total">
                        <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_payment_status_count" id="footer_payment_status_count"></td>
                        <td class="footer_expense_total" id="footer_expense_total"></td>
                        <td class="footer_total_due" id="footer_total_due"></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<div class="modal fade payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>
<style>
    .payment_modal .modal-dialog.expense-pay-dialog,
    .edit_payment_modal .modal-dialog.expense-pay-dialog {
        width: 92%;
        max-width: 860px;
        margin: 20px auto;
    }
    @media (max-width: 767px) {
        .payment_modal .modal-dialog.expense-pay-dialog,
        .edit_payment_modal .modal-dialog.expense-pay-dialog {
            width: auto;
            margin: 10px;
        }
    }
</style>

@stop
@section('javascript')
 <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    $(document).ready(function() {
        $(document).on('shown.bs.modal', '.payment_modal, .edit_payment_modal', function() {
            var $modal = $(this);
            $modal.find('select.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
                $(this).select2({
                    width: '100%',
                    dropdownParent: $modal
                });
            });
        });

        $('#toggle_expense_filters').on('click', function() {
            var $btn = $(this);
            var $panel = $('#collapsible_expense_filters');
            $panel.slideToggle(300, function() {
                var isOpen = $panel.is(':visible');
                $btn.attr('aria-expanded', isOpen ? 'true' : 'false');
                if (isOpen) {
                    $panel.find('.select2-container').css('width', '100%');
                }
            });
            $btn.toggleClass('filter-active');
        });

        var expenseDateSettings = $.extend(true, {}, dateRangeSettings, {
            alwaysShowCalendars: true,
            parentEl: 'body',
            drops: 'down'
        });

        $('#expense_date_range').daterangepicker(
            expenseDateSettings,
            function(start, end) {
                $('#expense_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                expense_table.ajax.reload();
            }
        );
        $('#expense_date_range').on('show.daterangepicker', function(ev, picker) {
            if (picker && picker.container) {
                picker.container.addClass('expense-daterangepicker');
            }
        });
        $('#expense_date_range').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
            if (picker && picker.container) {
                picker.container.hide();
            }
        });
        $('#expense_date_range').on('cancel.daterangepicker', function() {
            $('#expense_date_range').val('');
            expense_table.ajax.reload();
        });

        var picker = $('#expense_date_range').data('daterangepicker');
        if (picker && !$('#expense_date_range').val()) {
            $('#expense_date_range').val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
        }

        var expenseExportOptions = {
            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            stripHtml: true
        };

        expense_table = $('#expense_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            scrollX: true,
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
            aaSorting: [[1, 'desc']],
            ajax: {
                url: '/expenses',
                data: function(d) {
                    d.expense_for = $('select#expense_for').val();
                    d.created_by = $('select#created_by').val();
                    d.contact_id = $('select#expense_contact_filter').val();
                    d.location_id = $('select#location_id').val();
                    d.expense_category_id = $('select#expense_category_id').val();
                    d.expense_sub_category_id = $('select#expense_sub_category_id_filter').val();
                    d.payment_status = $('select#expense_payment_status').val();
                    var picker = $('input#expense_date_range').data('daterangepicker');
                    if (picker && $('#expense_date_range').val()) {
                        d.start_date = picker.startDate.format('YYYY-MM-DD');
                        d.end_date = picker.endDate.format('YYYY-MM-DD');
                    }
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'category', name: 'ec.name' },
                { data: 'location_name', name: 'bl.name' },
                { data: 'payment_status', name: 'payment_status', orderable: false },
                { data: 'final_total', name: 'final_total' },
                { data: 'payment_due', name: 'payment_due' },
                { data: 'expense_for', name: 'expense_for' },
                { data: 'additional_notes', name: 'additional_notes' },
                { data: 'added_by', name: 'usr.first_name'}
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
                            title: 'Expenses',
                            exportOptions: expenseExportOptions
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel"></i> Excel',
                            title: 'Expenses',
                            exportOptions: expenseExportOptions
                        }
                    ]
                }
            ],
            dom: 'lBrtip',
            initComplete: function() {
                $('#expense_table .skeleton-row').remove();
                this.api().columns.adjust();
            },
            fnDrawCallback: function() {
                var api = this.api();
                var $table = $(api.table().node());
                var $wrapper = $(api.table().container());

                var expense_total = sum_table_col($table, 'final-total');
                var total_due = sum_table_col($table, 'payment_due');

                $wrapper.find('.footer_expense_total, [id="footer_expense_total"]').html(__currency_trans_from_en(expense_total));
                $wrapper.find('.footer_total_due, [id="footer_total_due"]').html(__currency_trans_from_en(total_due));
                $wrapper.find('.footer_payment_status_count, [id="footer_payment_status_count"]').html(
                    __sum_status_html($table, 'payment-status')
                );

                __currency_convert_recursively($wrapper);
            },
            createdRow: function(row, data, dataIndex) {
                $(row)
                    .find('td:eq(5)')
                    .attr('class', 'clickable_td');
            }
        });

        $('#custom_expense_length').on('change', function() {
            expense_table.page.len($(this).val()).draw();
        });

        $(document).on('click', '.expense-export-opt', function(e) {
            e.preventDefault();
            expense_table.button('.buttons-' + $(this).data('format')).trigger();
        });

        $('#custom_expense_search').on('keyup', function() {
            expense_table.search(this.value).draw();
        });

        $(document).on('change', '#location_id, #expense_for, #created_by, #expense_contact_filter, #expense_category_id, #expense_payment_status, #expense_sub_category_id_filter', function() {
            expense_table.ajax.reload();
        });
    });
</script>
@endsection
