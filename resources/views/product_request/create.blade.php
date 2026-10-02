@extends('layouts.app')
@section('title', __('Request Stock from Main Store'))

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

    .product-request-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    .form-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        overflow: visible !important;
    }

    .form-card-title {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 20px !important;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-label {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
        display: block;
    }

    .form-control-custom {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 14px !important;
        box-shadow: none !important;
    }

    .select2-container .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding-top: 6px !important;
    }

    .btn-submit-request {
        background: {{ $primary_hex }} !important;
        color: #ffffff !important;
        height: 42px !important;
        padding: 0 28px !important;
        border-radius: 20px !important;
        font-weight: 700 !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-submit-request:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    .btn-save-draft {
        background: #ffffff !important;
        color: #475569 !important;
        height: 42px !important;
        padding: 0 24px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-save-draft:hover {
        background: #f8fafc !important;
        color: #1e293b !important;
    }

    #request_lines_table th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        padding: 12px 14px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    #request_lines_table td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
    }

    /* Datepicker container & dropdown positioning */
    #required_date_container {
        position: relative !important;
    }
    #required_date_container .datepicker {
        z-index: 1050 !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        padding: 8px !important;
    }

    /* Product Search Autocomplete Anchored Dropdown */
    #product_search_wrapper {
        position: relative !important;
        width: 100% !important;
    }

    #product_search_wrapper .ui-autocomplete {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        max-height: 320px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        z-index: 99999 !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        margin-top: 4px !important;
        padding: 4px 0 !important;
        list-style: none !important;
    }

    #product_search_wrapper .ui-autocomplete .ui-menu-item {
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }

    #product_search_wrapper .ui-autocomplete .ui-menu-item a {
        display: block !important;
        padding: 10px 16px !important;
        color: #1e293b !important;
        text-decoration: none !important;
        border-bottom: 1px solid #f1f5f9 !important;
        cursor: pointer !important;
    }

    #product_search_wrapper .ui-autocomplete .ui-menu-item:last-child a {
        border-bottom: none !important;
    }

    #product_search_wrapper .ui-autocomplete .ui-menu-item a:hover,
    #product_search_wrapper .ui-autocomplete .ui-menu-item a.ui-state-active,
    #product_search_wrapper .ui-autocomplete .ui-menu-item.ui-state-focus a {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    @media (max-width: 767px) {
        .content-header { padding: 12px 16px !important; }
        .product-request-content { padding: 12px !important; }
        .form-card { padding: 16px !important; }
    }
</style>

<!-- Header -->
<section class="content-header tw-flex tw-flex-col md:tw-flex-row md:tw-justify-between md:tw-items-center tw-gap-4 no-print">
    <div class="tw-w-full">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('Request Stock from Main Store')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('Create a new product requisition for your retail shop')</p>
    </div>
    <div class="tw-w-full md:tw-w-auto">
        <a href="{{ action([\App\Http\Controllers\ProductRequestController::class, 'index']) }}" class="btn btn-default tw-w-full md:tw-w-auto tw-justify-center md:tw-justify-start" style="height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa fa-arrow-left"></i> @lang('Back to Requests')
        </a>
    </div>
</section>

<!-- Content -->
<section class="content product-request-content no-print">
    {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'store']), 'method' => 'post', 'id' => 'product_request_form']) !!}
    <input type="hidden" name="submit_request" id="submit_request" value="1">

    <!-- Card 1: Requisition Details -->
    <div class="form-card">
        <h3 class="form-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $primary_hex }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
            @lang('Requisition Details')
        </h3>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label">{!! __('Destination Shop') . ':*' !!}</label>
                    {!! Form::select('requesting_location_id', $business_locations, $default_requesting_location_id, [
                        'class' => 'form-control select2',
                        'required',
                        'placeholder' => __('Choose Business Location'),
                        'id' => 'requesting_location_id',
                        'style' => 'width: 100%;'
                    ]) !!}
                    <p class="help-block tw-text-xs tw-text-gray-500 tw-mt-1">
                        @if($business_locations->isEmpty())
                            <span class="text-danger"><i class="fa fa-exclamation-circle"></i> @lang('You have no assigned retail shops. Contact an administrator.')</span>
                        @else
                            @lang('Select your assigned retail shop receiving the stock.')
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label">{!! __('Source Main Store') . ':*' !!}</label>
                    {!! Form::select('source_location_id', $source_locations, $default_warehouse_id, [
                        'class' => 'form-control select2',
                        'required',
                        'id' => 'source_location_id',
                        'style' => 'width: 100%;'
                    ]) !!}
                    <p class="help-block tw-text-xs tw-text-gray-500 tw-mt-1">@lang('Central warehouse supplying inventory.')</p>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label">@lang('Priority'):</label>
                    {!! Form::select('priority', [
                        'low' => __('Low'),
                        'medium' => __('Medium'),
                        'high' => __('High'),
                        'urgent' => __('Urgent')
                    ], 'medium', [
                        'class' => 'form-control select2',
                        'id' => 'priority',
                        'style' => 'width: 100%;'
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="row tw-mt-2">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label">@lang('Date Needed By'):*</label>
                    <div class="tw-relative" id="required_date_container" style="position: relative;">
                        <input type="text" 
                               name="required_date" 
                               id="required_date" 
                               class="form-control form-control-custom" 
                               value="{{ @format_date('now') }}" 
                               placeholder="YYYY-MM-DD" 
                               autocomplete="off" 
                               required
                               style="padding-left: 38px; cursor: pointer;">
                        <span style="position: absolute; left: 12px; top: 11px; color: #94a3b8; pointer-events: none;">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="form-group">
                    <label class="form-label">@lang('Request Notes / Justification'):</label>
                    {!! Form::text('notes', null, [
                        'class' => 'form-control form-control-custom',
                        'placeholder' => __('e.g. Weekend replenishment for fast moving perfumes...')
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Product Selection & Lines -->
    <div class="form-card">
        <h3 class="form-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $primary_hex }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
            @lang('Requested Products')
        </h3>

        <!-- Search input with Anchored Container -->
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 tw-mb-4">
                <div class="tw-relative" id="product_search_wrapper">
                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="search_product_for_request"
                           placeholder="Type product name or SKU to search live stock..."
                           class="form-control"
                           autocomplete="off"
                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px;">
                </div>
                <p class="help-block tw-text-xs tw-text-gray-500 tw-mt-2 text-center">
                    <i class="fa fa-info-circle tw-text-primary"></i> @lang('Search shows live stock levels in your shop and the Main Store simultaneously.')
                </p>
            </div>
        </div>

        <!-- Table of Lines -->
        <div class="table-responsive tw-mt-2">
            <table class="table table-bordered table-hover" id="request_lines_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 32%;">@lang('Product Name & SKU')</th>
                        <th style="width: 14%;" class="text-center">@lang('Your Shop Stock')</th>
                        <th style="width: 14%;" class="text-center">@lang('Main Store Stock')</th>
                        <th style="width: 15%;" class="text-center">@lang('Requested Qty') *</th>
                        <th style="width: 20%;">@lang('Line Note / Comment')</th>
                        <th style="width: 5%;" class="text-center"><i class="fa fa-trash text-muted"></i></th>
                    </tr>
                </thead>
                <tbody id="request_lines_tbody">
                    <tr id="empty_row">
                        <td colspan="6" class="text-center text-muted" style="padding: 35px 20px;">
                            <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;">
                                <i class="fa fa-box-open"></i>
                            </div>
                            <div style="font-weight: 600; color: #64748b; font-size: 14px;">@lang('No items added yet')</div>
                            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">@lang('Select a Destination Shop and use the search bar to add items')</div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td colspan="3" class="text-right" style="padding: 14px; color: #1e293b;">@lang('Total Requested Units'):</td>
                        <td class="text-center" style="padding: 14px;">
                            <span id="total_requested_units" style="font-size: 16px; color: {{ $primary_hex }}; font-weight: 700;">0.00</span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="row tw-mt-6 tw-mb-8">
        <div class="col-md-12 tw-flex tw-flex-col sm:tw-flex-row tw-justify-center tw-items-center tw-gap-4">
            <button type="button" id="btn_save_draft" class="btn-save-draft tw-w-full sm:tw-w-auto tw-justify-center">
                <i class="fa fa-save"></i> @lang('Save as Draft')
            </button>
            <button type="button" id="btn_submit_request" class="btn-submit-request tw-w-full sm:tw-w-auto tw-justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg>
                @lang('Submit Request for Approval')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    let rowCount = 0;
    const addedVariations = new Set();

    // 1. Date Needed By: Initialize datepicker anchored to #required_date_container
    if ($('#required_date').length) {
        $('#required_date').datepicker({
            autoclose: true,
            format: typeof datepicker_date_format !== 'undefined' ? datepicker_date_format : 'yyyy-mm-dd',
            startDate: 'today',
            container: '#required_date_container',
            orientation: 'bottom auto'
        });

        // When scrolling the page, close the calendar so it never floats independently
        $('#scrollable-container, window').on('scroll', function() {
            $('#required_date').datepicker('hide');
        });
    }

    // 2. Product Search: Autocomplete anchored inside #product_search_wrapper
    $('#search_product_for_request').autocomplete({
        appendTo: '#product_search_wrapper',
        position: { my: "left top+4", at: "left bottom", of: "#search_product_for_request", collision: "flipfit" },
        source: function(request, response) {
            const requestingId = $('#requesting_location_id').val();
            const sourceId = $('#source_location_id').val();
            if (!requestingId) {
                toastr.warning('Please select a Destination Shop first.');
                $('#requesting_location_id').select2('open');
                response([]);
                return;
            }
            $.ajax({
                url: "{{ action([\App\Http\Controllers\ProductRequestController::class, 'searchProducts']) }}",
                dataType: 'json',
                data: {
                    term: request.term,
                    requesting_location_id: requestingId,
                    source_location_id: sourceId
                },
                success: function(data) {
                    response($.map(data, function(item) {
                        const prodName = item.product_name || item.name || 'Product';
                        const varName = (item.variation_name && item.variation_name !== 'DUMMY') ? ' (' + item.variation_name + ')' : '';
                        const sku = item.sub_sku ? ' [' + item.sub_sku + ']' : '';
                        return {
                            label: prodName + varName + sku,
                            value: prodName + (item.variation_name && item.variation_name !== 'DUMMY' ? ' - ' + item.variation_name : ''),
                            item: item
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            const productData = ui.item ? (ui.item.item || ui.item) : null;
            if (productData) {
                addProductRow(productData);
            }
            $(this).val('');
            return false;
        }
    }).autocomplete('instance')._renderItem = function(ul, item) {
        const p = item.item || item;
        const prodName = p.product_name || p.name || item.label || 'Product';
        const varName = (p.variation_name && p.variation_name !== 'DUMMY') ? `<span style="color:#64748b;font-size:12px;">(${p.variation_name})</span>` : '';
        const mainStock = parseFloat(p.source_stock || 0);
        const shopStock = parseFloat(p.shop_stock || 0);
        const mainStockClass = mainStock > 0 ? '#16a34a' : '#dc2626';
        const sku = p.sub_sku || '';
        
        const html = `<a>
            <div style="font-weight: 600; color: #1e293b;">
                ${prodName} ${varName}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                ${sku ? `<span>SKU: <code>${sku}</code></span> | ` : ''}
                <span>Shop Stock: <strong>${shopStock.toFixed(2)}</strong></span> | 
                <span style="color: ${mainStockClass}; font-weight: 600;">Main Store: ${mainStock.toFixed(2)}</span>
            </div>
        </a>`;

        return $('<li>').append(html).appendTo(ul);
    };

    function addProductRow(item) {
        if (!item || !item.variation_id) {
            toastr.error('Unable to select product.');
            return;
        }

        if (addedVariations.has(item.variation_id)) {
            toastr.warning('Product already added to request. Adjust quantity instead.');
            $(`#qty_input_${item.variation_id}`).focus();
            return;
        }

        $('#empty_row').hide();
        addedVariations.add(item.variation_id);
        const index = rowCount++;

        const prodName = item.product_name || item.name || 'Product';
        const varName = (item.variation_name && item.variation_name !== 'DUMMY') ? item.variation_name : '';
        const shopStockDisplay = parseFloat(item.shop_stock || 0).toFixed(2);
        const sourceStockDisplay = parseFloat(item.source_stock || 0).toFixed(2);
        const unitDisplay = item.unit || 'units';

        const tr = `
            <tr id="row_${item.variation_id}">
                <td>
                    <div style="font-weight: 600; color: #1e293b;">${prodName}</div>
                    ${varName ? '<div style="color: #64748b; font-size: 12px;">' + varName + '</div>' : ''}
                    <small style="color: #94a3b8; font-size: 11px;">SKU: ${item.sub_sku || ''}</small>
                    <input type="hidden" name="products[${index}][variation_id]" value="${item.variation_id}">
                </td>
                <td class="text-center">
                    <span style="display:inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: #475569;">
                        ${shopStockDisplay} ${item.unit || ''}
                    </span>
                </td>
                <td class="text-center">
                    <span style="display:inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; background: ${item.source_stock > 0 ? '#dcfce7' : '#fee2e2'}; color: ${item.source_stock > 0 ? '#16a34a' : '#dc2626'};">
                        ${sourceStockDisplay} ${item.unit || ''}
                    </span>
                    <input type="hidden" id="source_stock_${item.variation_id}" value="${item.source_stock}">
                </td>
                <td class="text-center">
                    <div class="input-group input-group-sm" style="max-width: 140px; margin: 0 auto;">
                        <input type="number" 
                               name="products[${index}][quantity]" 
                               id="qty_input_${item.variation_id}"
                               data-var-id="${item.variation_id}"
                               class="form-control text-center requested_qty_input" 
                               value="1" 
                               min="0.01" 
                               step="any" 
                               style="height: 38px; border-radius: 6px 0 0 6px; border: 1px solid #cbd5e1; font-weight: 600;"
                               required>
                        <span class="input-group-addon" style="border-radius: 0 6px 6px 0; background: #f8fafc; border: 1px solid #cbd5e1; border-left: 0; font-size: 12px;">${item.unit || 'units'}</span>
                    </div>
                    <span class="stock-warning text-warning tw-text-xs" id="warning_${item.variation_id}" style="display: none; font-size: 11px; margin-top: 4px;">
                        <i class="fa fa-exclamation-triangle"></i> Exceeds Main Store
                    </span>
                </td>
                <td>
                    <input type="text" name="products[${index}][notes]" class="form-control input-sm" placeholder="Optional note" style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs btn-remove-row" data-var-id="${item.variation_id}" style="border-radius: 6px; padding: 4px 8px;">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#request_lines_tbody').append(tr);
        checkRowStockWarning(item.variation_id);
        recalcTotals();
    }

    $(document).on('click', '.btn-remove-row', function() {
        const varId = $(this).data('var-id');
        $(`#row_${varId}`).remove();
        addedVariations.delete(varId);
        if (addedVariations.size === 0) {
            $('#empty_row').show();
        }
        recalcTotals();
    });

    $(document).on('input change', '.requested_qty_input', function() {
        const varId = $(this).data('var-id');
        checkRowStockWarning(varId);
        recalcTotals();
    });

    function checkRowStockWarning(varId) {
        const requested = parseFloat($(`#qty_input_${varId}`).val()) || 0;
        const sourceStock = parseFloat($(`#source_stock_${varId}`).val()) || 0;
        if (requested > sourceStock) {
            $(`#warning_${varId}`).show();
        } else {
            $(`#warning_${varId}`).hide();
        }
    }

    function recalcTotals() {
        let total = 0;
        $('.requested_qty_input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_requested_units').text(total.toFixed(2));
    }

    function validateDestinationShop() {
        const locId = $('#requesting_location_id').val();
        if (!locId) {
            toastr.error('Please select a Destination Shop before proceeding.');
            $('#requesting_location_id').select2('open');
            return false;
        }
        return true;
    }

    $('#btn_save_draft').click(function(e) {
        e.preventDefault();
        if (!validateDestinationShop()) {
            return;
        }
        if (addedVariations.size === 0) {
            toastr.error('Please add at least one product to the request.');
            $('#search_product_for_request').focus();
            return;
        }
        $('#submit_request').val('0');
        $('#product_request_form').submit();
    });

    $('#btn_submit_request').click(function(e) {
        e.preventDefault();
        if (!validateDestinationShop()) {
            return;
        }
        if (addedVariations.size === 0) {
            toastr.error('Please add at least one product to the request.');
            $('#search_product_for_request').focus();
            return;
        }
        $('#submit_request').val('1');
        $('#product_request_form').submit();
    });

    $('#requesting_location_id, #source_location_id').change(function() {
        if (addedVariations.size > 0) {
            swal({
                title: 'Change Locations?',
                text: 'Changing locations will reset current items to refresh live stock balances.',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willChange) => {
                if (willChange) {
                    $('#request_lines_tbody').find('tr:not(#empty_row)').remove();
                    addedVariations.clear();
                    $('#empty_row').show();
                    recalcTotals();
                }
            });
        }
    });
});
</script>
@endsection