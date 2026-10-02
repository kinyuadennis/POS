@extends('layouts.app')
@section('title', __('stock_adjustment.add'))

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

    .stock-adjustment-content {
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

    .sa-table-responsive {
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

    .form-actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-submit-sa {
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
    .btn-submit-sa:hover {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12) !important;
    }
    .btn-cancel-sa {
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
    .btn-cancel-sa:hover {
        background-color: #f8fafc !important;
        color: #1e293b !important;
        border-color: #94a3b8 !important;
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .stock-adjustment-content {
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
        .btn-submit-sa, .btn-cancel-sa {
            width: 100% !important;
        }
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center no-print">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('stock_adjustment.add')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Correct inventory quantities and record stock losses or gains</p>
    </div>
</section>

<section class="content stock-adjustment-content no-print">
    {!! Form::open([
        'url' => action([\App\Http\Controllers\StockAdjustmentController::class, 'store']),
        'method' => 'post',
        'id' => 'stock_adjustment_form',
    ]) !!}

    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-sliders"></i>
            @lang('stock_adjustment.stock_adjustments')
        </h3>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                    {!! Form::select('location_id', $business_locations, null, [
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.please_select'),
                        'required',
                        'id' => 'location_id',
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                    {!! Form::text('ref_no', null, ['class' => 'form-control', 'placeholder' => __('purchase.ref_no')]) !!}
                </div>
            </div>
            <div class="col-sm-3">
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
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('adjustment_type', __('stock_adjustment.adjustment_type') . ':*') !!} @show_tooltip(__('tooltip.adjustment_type'))
                    {!! Form::select(
                        'adjustment_type',
                        ['normal' => __('stock_adjustment.normal'), 'abnormal' => __('stock_adjustment.abnormal')],
                        null,
                        [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'id' => 'adjustment_type',
                        ],
                    ) !!}
                </div>
            </div>
        </div>
    </div>

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
                <i class="fa fa-info-circle text-muted"></i> @lang('stock_adjustment.search_products') (Select <b>@lang('purchase.business_location')</b> first to enable product search)
            </div>
        </div>

        <input type="hidden" id="product_row_index" value="0">
        <input type="hidden" id="total_amount" name="final_total" value="0">

        <div class="sa-table-responsive">
            <table class="table" id="stock_adjustment_product_table">
                <thead>
                    <tr>
                        <th class="col-sm-4 text-left">
                            @lang('sale.product')
                        </th>
                        <th class="col-sm-2 text-center">
                            @lang('stock_adjustment.adjustment_type')
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
                        <td colspan="4" class="text-right">
                            <b>@lang('stock_adjustment.total_amount'):</b>
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

    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-file-text-o"></i>
            @lang('stock_adjustment.reason_for_stock_adjustment')
        </h3>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
                    {!! Form::text('total_amount_recovered', 0, [
                        'class' => 'form-control input_number',
                        'placeholder' => __('stock_adjustment.total_amount_recovered'),
                    ]) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                    {!! Form::textarea('additional_notes', null, [
                        'class' => 'form-control',
                        'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'),
                        'rows' => 3,
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="form-actions-bar">
            <a href="{{ action([\App\Http\Controllers\StockAdjustmentController::class, 'index']) }}" class="btn btn-cancel-sa">
                @lang('messages.cancel')
            </a>
            <button type="submit" class="btn btn-submit-sa">
                <i class="fa fa-check"></i>
                @lang('messages.save')
            </button>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@stop

@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v . '_' . @filemtime(public_path('js/stock_adjustment.js'))) }}"></script>
    <script type="text/javascript">
        __page_leave_confirmation('#stock_adjustment_form');
    </script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot
