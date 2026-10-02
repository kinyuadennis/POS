@extends('layouts.app')
@section('title', __('lang_v1.stock_transfer_details') . ' - #' . $sell_transfer->ref_no)

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

@section('content')
<style>
    .stock-transfer-page-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
    }
    .st-page-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        padding: 24px !important;
    }
    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 38px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }
    .header-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    .header-btn-primary {
        background: {{ $primary_hex }};
        border-color: {{ $primary_hex }};
        color: #ffffff !important;
    }
    .header-btn-primary:hover {
        opacity: 0.92;
        color: #ffffff !important;
    }
</style>

<!-- Content Header -->
<section class="content-header" style="background: #ffffff; padding: 16px 24px; border-bottom: 1px solid #cbd5e160; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3;">
            @lang('lang_v1.stock_transfer_details')
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 0; margin-top: 2px;">
            @lang('purchase.ref_no'): <strong style="color: #1e293b;">#{{ $sell_transfer->ref_no }}</strong>
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        @if(!empty($product_request))
            <a href="{{ action([\App\Http\Controllers\ProductRequestController::class, 'show'], [$product_request->id]) }}" class="header-btn">
                <i class="fa fa-arrow-left"></i>
                <span>@lang('Back to Requisition')</span>
            </a>
        @else
            <a href="{{ action([\App\Http\Controllers\StockTransferController::class, 'index']) }}" class="header-btn">
                <i class="fa fa-arrow-left"></i>
                <span>@lang('All Transfers')</span>
            </a>
        @endif

        <button type="button" class="header-btn header-btn-primary no-print" onclick="$('.st-page-card').printThis();">
            <i class="fa fa-print"></i>
            <span>@lang('messages.print')</span>
        </button>
    </div>
</section>

<!-- Main content -->
<section class="content stock-transfer-page-content">
    <div class="st-page-card">
        @include('stock_transfer.partials.details')
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        if (typeof __currency_convert_recursively === 'function') {
            __currency_convert_recursively($('.st-page-card'));
        }
    });
</script>
@endsection