@extends('layouts.app')
@section('title', __('lang_v1.add_stock_transfer'))

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

    /* Page container with background tint */
    .stock-transfer-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    /* Form card */
    .form-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .form-card-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 20px !important;
        padding-bottom: 12px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .form-card-title i {
        color: {{ $primary_hex }} !important;
        font-size: 18px !important;
    }

    /* Labels & Form Controls */
    .content label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
    }
    .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        box-shadow: none !important;
        color: #1e293b !important;
        font-size: 14px !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    .form-control:focus {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
        outline: none !important;
    }
    textarea.form-control {
        height: auto !important;
    }

    /* Input Group styling */
    .input-group {
        border-radius: 8px !important;
    }
    .input-group-addon {
        border: 1px solid #cbd5e1 !important;
        border-right: 0 !important;
        border-radius: 8px 0 0 8px !important;
        background-color: #f8fafc !important;
        color: #64748b !important;
    }
    .input-group .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    /* Select2 controls */
    .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        padding: 5px 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
    }

    /* Product Search Input Box */
    .product-search-wrapper {
        position: relative;
        max-width: 680px;
        margin: 0 auto 24px auto;
    }
    .product-search-wrapper .input-group-addon {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
        padding: 0 16px !important;
        font-size: 16px !important;
    }
    .product-search-input {
        height: 44px !important;
        font-size: 14px !important;
        border-left: 0 !important;
    }
    .product-search-input:disabled {
        background-color: #f8fafc !important;
        cursor: not-allowed;
    }
    .search-hint {
        font-size: 12.5px;
        color: #64748b;
        margin-top: 6px;
        text-align: center;
    }

    /* Table styling */
    .st-table-responsive {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background-color: #ffffff;
    }
    #stock_adjustment_product_table {
        margin-bottom: 0 !important;
        border: none !important;
    }
    #stock_adjustment_product_table thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-top: none !important;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 12px 14px !important;
        vertical-align: middle !important;
    }
    #stock_adjustment_product_table tbody td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #1e293b !important;
        font-size: 14px !important;
    }
    #stock_adjustment_product_table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    #stock_adjustment_product_table tfoot {
        background-color: #f8fafc !important;
        border-top: 2px solid #e2e8f0 !important;
    }
    #stock_adjustment_product_table tfoot td {
        padding: 14px !important;
        font-weight: 700 !important;
        font-size: 15px !important;
        color: #1e293b !important;
        border: none !important;
    }
    .remove_product_row {
        color: #ef4444;
        transition: color 0.15s ease-in-out, transform 0.15s ease-in-out;
        cursor: pointer;
        font-size: 16px;
    }
    .remove_product_row:hover {
        color: #dc2626;
        transform: scale(1.15);
    }

    /* Summary callout & action bar */
    .summary-card {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 16px;
    }
    .summary-total-label {
        font-size: 15px;
        font-weight: 600;
        color: #475569;
    }
    .summary-total-value {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
    }

    /* Action buttons */
    .form-actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-submit-st {
        background-color: {{ $primary_hex }} !important;
        border: 1px solid {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        height: 42px !important;
        padding: 0 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
        transition: all 0.2s ease-in-out !important;
    }
    .btn-submit-st:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12) !important;
    }
    .btn-cancel-st {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        height: 42px !important;
        padding: 0 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease-in-out !important;
        text-decoration: none !important;
    }
    .btn-cancel-st:hover {
        background-color: #f8fafc !important;
        color: #1e293b !important;
        border-color: #94a3b8 !important;
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .stock-transfer-content {
            padding: 14px 12px !important;
        }
        .form-card {
            padding: 16px 14px !important;
            margin-bottom: 16px !important;
        }
        .form-actions-bar {
            flex-direction: column-reverse;
            gap: 10px;
        }
        .btn-submit-st, .btn-cancel-st {
            width: 100% !important;
        }
        .summary-card {
            justify-content: space-between;
        }
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.add_stock_transfer')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Transfer inventory between business locations</p>
    </div>
</section>

<!-- Main content -->
<section class="content stock-transfer-content no-print">
    {!! Form::open([
        'url' => action([\App\Http\Controllers\StockTransferController::class, 'store']),
        'method' => 'post',
        'id' => 'stock_transfer_form',
    ]) !!}

    <!-- Transfer Details Card -->
    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-truck"></i>
            @lang('lang_v1.stock_transfer_details')
        </h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                    {!! Form::text('ref_no', null, ['class' => 'form-control', 'placeholder' => __('purchase.ref_no')]) !!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('status', __('sale.status') . ':*') !!} @show_tooltip(__('lang_v1.completed_status_help'))
                    {!! Form::select('status', $statuses, null, [
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.please_select'),
                        'required',
                        'id' => 'status',
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('location_id', __('lang_v1.location_from') . ':*') !!}
                    {!! Form::select('location_id', $business_locations, null, [
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.please_select'),
                        'required',
                        'id' => 'location_id',
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('transfer_location_id', __('lang_v1.location_to') . ':*') !!}
                    {!! Form::select('transfer_location_id', $business_locations, null, [
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.please_select'),
                        'required',
                        'id' => 'transfer_location_id',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Product Search & Transfer Table Card -->
    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-cubes"></i>
            @lang('sale.products')
        </h3>

        <div class="product-search-wrapper">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-search"></i>
                </span>
                {!! Form::text('search_product', null, [
                    'class' => 'form-control product-search-input',
                    'id' => 'search_product_for_srock_adjustment',
                    'placeholder' => __('stock_adjustment.search_product'),
                    'disabled',
                ]) !!}
            </div>
            <div class="search-hint">
                <i class="fa fa-info-circle text-muted"></i> @lang('stock_adjustment.search_products') (Select <b>@lang('lang_v1.location_from')</b> first to enable product search)
            </div>
        </div>

        <input type="hidden" id="product_row_index" value="0">
        <input type="hidden" id="total_amount" name="final_total" value="0">

        <div class="st-table-responsive">
            <table class="table" id="stock_adjustment_product_table">
                <thead>
                    <tr>
                        <th class="col-sm-4 text-left">
                            @lang('sale.product')
                        </th>
                        <th class="col-sm-2 text-center">
                            @lang('sale.qty')
                        </th>
                        <th class="col-sm-2 text-center show_price_with_permission">
                            @lang('sale.unit_price')
                        </th>
                        <th class="col-sm-2 text-center show_price_with_permission">
                            @lang('sale.subtotal')
                        </th>
                        <th class="col-sm-2 text-center">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr class="show_price_with_permission">
                        <td colspan="3" class="text-right">
                            <b>@lang('sale.total'):</b>
                        </td>
                        <td class="text-center">
                            <span id="total_adjustment">0.00</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Shipping & Additional Notes Card -->
    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-file-text-o"></i>
            @lang('lang_v1.additional_notes') &amp; @lang('lang_v1.shipping_charges')
        </h3>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('shipping_charges', __('lang_v1.shipping_charges') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-truck"></i>
                        </span>
                        {!! Form::text('shipping_charges', 0, [
                            'class' => 'form-control input_number',
                            'id' => 'shipping_charges',
                            'placeholder' => __('lang_v1.shipping_charges'),
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('additional_notes', __('purchase.additional_notes') . ':') !!}
                    {!! Form::textarea('additional_notes', null, [
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => __('purchase.additional_notes')
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="summary-card show_price_with_permission">
            <span class="summary-total-label">@lang('stock_adjustment.total_amount'):</span>
            <span id="final_total_text" class="summary-total-value">0.00</span>
        </div>

        <div class="form-actions-bar">
            <a href="{{ action([\App\Http\Controllers\StockTransferController::class, 'index']) }}" class="btn btn-cancel-st">
                @lang('messages.cancel')
            </a>
            <button type="submit" id="save_stock_transfer" class="btn btn-submit-st">
                <i class="fa fa-check"></i>
                @lang('messages.save')
            </button>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@stop

@section('javascript')
    <script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        __page_leave_confirmation('#stock_transfer_form');
    </script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot
