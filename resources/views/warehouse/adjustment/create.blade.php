@extends('layouts.app')
@section('title', __('lang_v1.warehouse_new_adjustment'))

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

    .warehouse-adjustment-create-content {
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

    .warehouse-adjustment-create-content label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
    }
    .warehouse-adjustment-create-content .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        box-shadow: none !important;
        color: #1e293b !important;
        font-size: 14px !important;
    }
    .warehouse-adjustment-create-content .form-control:focus {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
        outline: none !important;
    }
    .warehouse-adjustment-create-content textarea.form-control {
        height: auto !important;
    }

    .warehouse-adjustment-create-content .input-group-addon {
        border: 1px solid #cbd5e1 !important;
        border-right: 0 !important;
        border-radius: 8px 0 0 8px !important;
        background-color: #f8fafc !important;
        color: #64748b !important;
    }
    .warehouse-adjustment-create-content .input-group .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    .warehouse-adjustment-create-content .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
    }
    .warehouse-adjustment-create-content .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: #1e293b !important;
    }
    .warehouse-adjustment-create-content .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    .product-search-wrapper {
        position: relative;
        max-width: 720px;
        margin: 0 auto 16px auto;
    }
    .product-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: auto;
        background-color: #ffffff;
    }
    #stock_adjustment_product_table thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        vertical-align: middle !important;
    }

    #save_warehouse_adjustment {
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
        transition: all 0.2s ease-in-out !important;
    }
    #save_warehouse_adjustment:hover,
    #save_warehouse_adjustment:focus {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .warehouse-adjustment-create-content {
            padding: 14px 12px !important;
        }
        .form-card {
            padding: 16px 14px !important;
            margin-bottom: 16px !important;
        }
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.warehouse_new_adjustment')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('lang_v1.warehouse_new_adjustment_subtitle')</p>
    </div>
</section>

<section class="content warehouse-adjustment-create-content no-print">
    {!! Form::open([
        'url' => action([\App\Http\Controllers\Warehouse\WarehouseAdjustmentController::class, 'store']),
        'method' => 'post',
        'id' => 'stock_adjustment_form',
    ]) !!}

    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-sliders-h"></i>
            @lang('lang_v1.warehouse_adjustment_details')
        </h3>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('lang_v1.warehouse_location') . ':*') !!}
                    {!! Form::select('location_id', $warehouse_locations, $default_warehouse_id, [
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
                    {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required', 'id' => 'transaction_date']) !!}
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
                        ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'],
                    ) !!}
                </div>
            </div>
        </div>
        <p class="text-muted" style="margin: 0; font-size: 13px;">@lang('lang_v1.warehouse_adjustment_help')</p>
    </div>

    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-cubes"></i>
            @lang('sale.products')
        </h3>
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="form-group product-search-wrapper">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        {!! Form::text('search_product', null, [
                            'class' => 'form-control',
                            'id' => 'search_product_for_srock_adjustment',
                            'placeholder' => __('stock_adjustment.search_product'),
                            'disabled' => empty($default_warehouse_id),
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <input type="hidden" id="product_row_index" value="0">
                <input type="hidden" id="total_amount" name="final_total" value="0">
                <div class="table-responsive product-table-wrap">
                    <table class="table table-bordered table-striped table-condensed" id="stock_adjustment_product_table">
                        <thead>
                            <tr>
                                <th class="col-sm-4 text-center">@lang('sale.product')</th>
                                <th class="col-sm-2 text-center">@lang('lang_v1.type')</th>
                                <th class="col-sm-2 text-center">@lang('sale.qty')</th>
                                <th class="col-sm-2 text-center show_price_with_permission">@lang('sale.unit_price')</th>
                                <th class="col-sm-2 text-center show_price_with_permission">@lang('sale.subtotal')</th>
                                <th class="col-sm-2 text-center"><i class="fa fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="text-center show_price_with_permission">
                                <td colspan="3"></td>
                                <td><div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment">0.00</span></div></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3 class="form-card-title">
            <i class="fa fa-sticky-note-o"></i>
            @lang('stock_adjustment.reason_for_stock_adjustment')
        </h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
                    {!! Form::text('total_amount_recovered', 0, ['class' => 'form-control input_number']) !!}
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                    {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-center">
                <button type="submit" id="save_warehouse_adjustment" class="tw-dw-btn tw-text-white">@lang('messages.save')</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        __page_leave_confirmation('#stock_adjustment_form');

        function stock_adjustment_product_row(variation_id) {
            var row_index = parseInt($('#product_row_index').val());
            var location_id = $('select#location_id').val();
            $.ajax({
                method: 'POST',
                url: '/stock-adjustments/get_product_row',
                data: {
                    row_index: row_index,
                    variation_id: variation_id,
                    location_id: location_id,
                    type: 'warehouse_adjustment',
                },
                dataType: 'html',
                success: function(result) {
                    $('table#stock_adjustment_product_table tbody').append(result);
                    var tr = $('table#stock_adjustment_product_table tbody tr').last();
                    update_table_row(tr);
                    update_table_total();
                    $('#product_row_index').val(row_index + 1);
                },
            });
        }
    </script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission { display: none !important; }
    </style>
@endcannot
