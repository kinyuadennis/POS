@extends('layouts.app')
@section('title', __('Product Request') . ' #' . $productRequest->request_no)

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

    .detail-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    .kpi-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 18px 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }
    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .detail-card-title {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 18px !important;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .btn-action-primary {
        background: {{ $primary_hex }} !important;
        color: #ffffff !important;
        height: 40px !important;
        padding: 0 20px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
    }
    .btn-action-primary:hover, .btn-action-primary:focus {
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    .btn-action-pill {
        border-radius: 20px !important;
    }

    .btn-action-default {
        background: #ffffff !important;
        color: #475569 !important;
        height: 40px !important;
        padding: 0 16px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
    }
    .btn-action-default:hover {
        background: #f8fafc !important;
        color: #1e293b !important;
    }

    #request_items_table th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        padding: 12px 14px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    #request_items_table td {
        padding: 12px 14px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }

    /* Modern Timeline */
    .modern-timeline {
        position: relative;
        padding: 10px 0;
        list-style: none;
    }
    .modern-timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-node {
        position: relative;
        margin-bottom: 24px;
        padding-left: 54px;
    }
    .timeline-node-icon {
        position: absolute;
        left: 6px;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #fff;
        box-shadow: 0 0 0 4px #ffffff;
    }
    .timeline-node-content {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .timeline-node-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    .modal-header {
        padding: 16px 20px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    /* Enhanced Top Header & Actions */
    .content-header.request-header {
        background-color: #ffffff !important;
        padding: 18px 24px !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
        position: relative !important;
        min-height: 76px !important;
        display: flex !important;
        align-items: center !important;
    }
    .content-header.request-header:before,
    .content-header.request-header:after {
        display: none !important;
    }

    .request-header-inner {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 20px !important;
        width: 100% !important;
    }

    .request-header-title-col {
        display: flex !important;
        flex-direction: column !important;
        gap: 5px !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
    }

    .request-title-row {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
    }

    .request-page-title {
        font-size: 22px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 !important;
        line-height: 1.25 !important;
        letter-spacing: -0.02em !important;
    }
    .request-page-title .request-no {
        color: {{ $primary_hex }} !important;
        font-weight: 800 !important;
    }

    .request-status-badge-wrap {
        display: inline-flex !important;
        align-items: center !important;
    }

    .request-status-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 5px 12px !important;
        border-radius: 20px !important;
        border: 1px solid transparent !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase !important;
        line-height: 1 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
    }

    .status-dot {
        width: 7px !important;
        height: 7px !important;
        border-radius: 50% !important;
        display: inline-block !important;
    }

    .request-subtitle {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        line-height: 1.4 !important;
    }

    .req-meta-item strong {
        color: #1e293b !important;
        font-weight: 600 !important;
    }

    .req-meta-divider {
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }

    .req-meta-sep {
        color: #cbd5e1 !important;
        margin: 0 2px !important;
    }

    .req-transfer-chip {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 12px !important;
        color: #475569 !important;
    }
    .req-transfer-chip strong {
        color: #0f172a !important;
    }

    /* Actions container */
    .request-header-actions-col {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        flex-wrap: nowrap !important;
    }

    /* Unified action button styles */
    .header-btn {
        height: 40px !important;
        min-height: 40px !important;
        padding: 0 16px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        font-family: inherit !important;
        line-height: 1 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
        vertical-align: middle !important;
        margin: 0 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.18s ease-in-out !important;
    }
    .header-btn i {
        font-size: 13px !important;
        line-height: 1 !important;
    }

    /* Secondary outline button (All Requests, View Stock Transfer) */
    .header-btn-secondary {
        background: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .header-btn-secondary:hover, .header-btn-secondary:focus {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.08) !important;
        text-decoration: none !important;
    }
    .header-btn-secondary.header-btn-blue {
        color: #0284c7 !important;
        border-color: #bae6fd !important;
    }
    .header-btn-secondary.header-btn-blue:hover {
        background: #f0f9ff !important;
        border-color: #0284c7 !important;
        color: #0369a1 !important;
    }

    /* Primary theme action button (Dispatch) */
    .header-btn-primary {
        background: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border: 1px solid {{ $primary_hex }} !important;
        box-shadow: 0 2px 4px -1px rgba(60, 141, 188, 0.25) !important;
    }
    .header-btn-primary:hover, .header-btn-primary:focus {
        opacity: 0.95 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(60, 141, 188, 0.35) !important;
        text-decoration: none !important;
    }

    /* Success action button (Confirm Physical Receipt) */
    .header-btn-success {
        background: #16a34a !important;
        color: #ffffff !important;
        border: 1px solid #15803d !important;
        box-shadow: 0 2px 4px -1px rgba(22, 163, 74, 0.25) !important;
    }
    .header-btn-success:hover, .header-btn-success:focus {
        background: #15803d !important;
        border-color: #166534 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.35) !important;
        text-decoration: none !important;
    }

    /* Warning action button (Review) */
    .header-btn-warning {
        background: #d97706 !important;
        color: #ffffff !important;
        border: 1px solid #b45309 !important;
        box-shadow: 0 2px 4px -1px rgba(217, 119, 6, 0.25) !important;
    }
    .header-btn-warning:hover, .header-btn-warning:focus {
        background: #b45309 !important;
        border-color: #92400e !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.35) !important;
        text-decoration: none !important;
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .content-header.request-header {
            padding: 16px 20px !important;
            height: auto !important;
            min-height: auto !important;
        }
        .request-header-inner {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 16px !important;
        }
        .request-header-actions-col {
            width: 100% !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
        }
    }

    @media (max-width: 640px) {
        .content-header.request-header {
            padding: 14px 16px !important;
        }
        .request-page-title {
            font-size: 19px !important;
        }
        .request-header-actions-col {
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .request-header-actions-col .header-btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

<!-- Top Header with Actions -->
<section class="content-header request-header no-print">
    <div class="request-header-inner">
        <!-- Left: Title, Request No, Status Badge, and Subtitle -->
        <div class="request-header-title-col">
            <div class="request-title-row">
                <h1 class="request-page-title">
                    <span>@lang('Product Request')</span>
                    <span class="request-no">#{{ $productRequest->request_no }}</span>
                </h1>

                @php
                    $statusConfig = [
                        'draft' => ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1', 'icon' => 'fa-file-text-o', 'dot' => '#94a3b8', 'label' => 'Draft'],
                        'pending' => ['bg' => '#fef3c7', 'text' => '#b45309', 'border' => '#fde68a', 'icon' => 'fa-clock-o', 'dot' => '#f59e0b', 'label' => 'Pending Review'],
                        'approved' => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'border' => '#bae6fd', 'icon' => 'fa-check', 'dot' => '#0284c7', 'label' => 'Approved'],
                        'partially_approved' => ['bg' => '#e0e7ff', 'text' => '#4338ca', 'border' => '#c7d2fe', 'icon' => 'fa-check', 'dot' => '#6366f1', 'label' => 'Partially Approved'],
                        'changes_requested' => ['bg' => '#ffedd5', 'text' => '#c2410c', 'border' => '#fed7aa', 'icon' => 'fa-exclamation-triangle', 'dot' => '#f97316', 'label' => 'Changes Requested'],
                        'in_transit' => ['bg' => '#f3e8ff', 'text' => '#7e22ce', 'border' => '#d8b4fe', 'icon' => 'fa-truck', 'dot' => '#9333ea', 'label' => 'In Transit'],
                        'received' => ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#bbf7d0', 'icon' => 'fa-check-circle', 'dot' => '#16a34a', 'label' => 'Received'],
                        'rejected' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'border' => '#fca5a5', 'icon' => 'fa-times', 'dot' => '#ef4444', 'label' => 'Rejected'],
                    ];
                    $sc = $statusConfig[$productRequest->status] ?? [
                        'bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1', 'icon' => 'fa-info-circle', 'dot' => '#94a3b8', 'label' => ucfirst($productRequest->status)
                    ];
                @endphp
                <div class="request-status-badge-wrap">
                    <span class="request-status-badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border-color: {{ $sc['border'] }};">
                        <span class="status-dot" style="background: {{ $sc['dot'] }};"></span>
                        <i class="fa {{ $sc['icon'] }}" style="font-size: 11px;"></i>
                        <span>{{ $sc['label'] }}</span>
                    </span>
                </div>
            </div>

            <p class="request-subtitle">
                <span class="req-meta-item">
                    <i class="fa fa-home text-muted" style="font-size: 11px;"></i>
                    @lang('Requisition from') <strong>{{ $productRequest->requestingLocation->name ?? 'Shop' }}</strong>
                </span>
                <span class="req-meta-divider">&rarr;</span>
                <span class="req-meta-item">
                    <i class="fa fa-building text-muted" style="font-size: 11px;"></i>
                    <strong>{{ $productRequest->sourceLocation->name ?? 'Main Store' }}</strong>
                </span>
                @if($productRequest->stock_transfer_id)
                    <span class="req-meta-sep">•</span>
                    <a href="javascript:void(0)" 
                       class="req-transfer-chip btn-modal" 
                       data-href="{{ action([\App\Http\Controllers\StockTransferController::class, 'show'], [$productRequest->stock_transfer_id]) }}" 
                       data-container=".view_modal" 
                       title="@lang('View Associated Stock Transfer')"
                       style="text-decoration: none; cursor: pointer;">
                        <i class="fa fa-exchange text-muted" style="font-size: 10px;"></i>
                        @lang('Transfer'): <strong>#{{ $productRequest->stockTransfer->ref_no ?? $productRequest->stock_transfer_id }}</strong>
                    </a>
                @endif
            </p>
        </div>

        <!-- Right: Action Buttons in Clean Horizontal Layout on Desktop -->
        <div class="request-header-actions-col">
            <!-- 1. All Requests (Navigation) -->
            <a href="{{ action([\App\Http\Controllers\ProductRequestController::class, 'index']) }}" 
               class="header-btn header-btn-secondary" 
               title="@lang('Back to Requisitions')">
                <i class="fa fa-arrow-left"></i>
                <span>@lang('All Requests')</span>
            </a>

            <!-- 2. View Stock Transfer (Linked Reference) -->
            @if($productRequest->stock_transfer_id)
                <button type="button" 
                   class="header-btn header-btn-secondary btn-modal" 
                   data-href="{{ action([\App\Http\Controllers\StockTransferController::class, 'show'], [$productRequest->stock_transfer_id]) }}" 
                   data-container=".view_modal"
                   title="@lang('View Associated Stock Transfer')">
                    <i class="fa fa-eye"></i>
                    <span>@lang('View Stock Transfer')</span>
                </button>
            @endif

            <!-- 3. Edit Request (if draft or changes_requested) -->
            @if(in_array($productRequest->status, ['draft', 'changes_requested']) && auth()->user()->can('product_request.edit'))
                <a href="{{ action([\App\Http\Controllers\ProductRequestController::class, 'edit'], [$productRequest->id]) }}" 
                   class="header-btn header-btn-secondary header-btn-blue">
                    <i class="fa fa-edit"></i>
                    <span>@lang('Edit Request')</span>
                </a>
            @endif

            <!-- 4. Submit for Review (if draft) -->
            @if($productRequest->status == 'draft' && auth()->user()->can('product_request.create'))
                {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'update'], [$productRequest->id]), 'method' => 'put', 'style' => 'display:inline-flex; margin: 0;']) !!}
                    <input type="hidden" name="submit_request" value="1">
                    <button type="submit" class="header-btn header-btn-success">
                        <i class="fa fa-paper-plane"></i>
                        <span>@lang('Submit for Review')</span>
                    </button>
                {!! Form::close() !!}
            @endif

            <!-- 5. Review Request (if pending) -->
            @if($productRequest->status == 'pending' && auth()->user()->can('product_request.approve'))
                <button type="button" 
                        class="header-btn header-btn-warning" 
                        data-toggle="modal" 
                        data-target="#review_modal">
                    <i class="fa fa-check-square"></i>
                    <span>@lang('Review Request')</span>
                </button>
            @endif

            <!-- 6. Dispatch Stock Transfer (if approved / partially_approved) -->
            @if(in_array($productRequest->status, ['approved', 'partially_approved']) && auth()->user()->can('product_request.fulfill'))
                <button type="button" 
                        class="header-btn header-btn-primary" 
                        data-toggle="modal" 
                        data-target="#dispatch_modal">
                    <i class="fa fa-truck"></i>
                    <span>@lang('Dispatch Stock Transfer')</span>
                </button>
            @endif

            <!-- 7. Confirm Physical Receipt (if in_transit) -->
            @if($productRequest->status == 'in_transit' && auth()->user()->can('product_request.receive'))
                <button type="button" 
                        class="header-btn header-btn-success" 
                        data-toggle="modal" 
                        data-target="#receive_modal">
                    <i class="fa fa-check-circle"></i>
                    <span>@lang('Confirm Physical Receipt')</span>
                </button>
            @endif
        </div>
    </div>
</section>

<!-- Content -->
<section class="content product-request-content no-print">
    <!-- 4 KPI Overview Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa fa-store"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.3;">{{ $productRequest->requestingLocation->name ?? 'N/A' }}</div>
                    <div style="font-size: 12px; font-weight: 500; color: #64748b;">@lang('Destination Shop')</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #f3e8ff; color: #9333ea;">
                    <i class="fa fa-warehouse"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.3;">{{ $productRequest->sourceLocation->name ?? 'N/A' }}</div>
                    <div style="font-size: 12px; font-weight: 500; color: #64748b;">@lang('Supplying Main Store')</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                    <i class="fa fa-user"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.3;">{{ $productRequest->creator->user_full_name ?? 'N/A' }}</div>
                    <div style="font-size: 12px; font-weight: 500; color: #64748b;">{{ @format_datetime($productRequest->created_at) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrap" style="background: #fef3c7; color: #d97706;">
                    <i class="fa fa-flag"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.3; text-transform: capitalize;">{{ $productRequest->priority }} Priority</div>
                    <div style="font-size: 12px; font-weight: 500; color: #64748b;">
                        @if($productRequest->required_date)
                            @lang('Needed by'): {{ @format_date($productRequest->required_date) }}
                        @else
                            @lang('Standard schedule')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes if any -->
    @if(!empty($productRequest->notes) || !empty($productRequest->rejection_reason))
        <div class="detail-card" style="padding: 18px 20px;">
            <div class="row">
                @if(!empty($productRequest->notes))
                    <div class="col-md-6">
                        <div style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 6px;">
                            <i class="fa fa-commenting text-primary"></i> @lang('Requester Notes / Justification'):
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; color: #334155; font-size: 13px;">
                            {{ $productRequest->notes }}
                        </div>
                    </div>
                @endif

                @if(!empty($productRequest->rejection_reason))
                    <div class="col-md-6">
                        <div style="font-weight: 600; color: #dc2626; font-size: 13px; margin-bottom: 6px;">
                            <i class="fa fa-times-circle"></i> @lang('Rejection Reason'):
                        </div>
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; color: #b91c1c; font-size: 13px;">
                            {{ $productRequest->rejection_reason }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Items Table Card -->
    <div class="detail-card">
        <h3 class="detail-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $primary_hex }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
            @lang('Requisition Items & Quantities')
        </h3>

        <div class="table-responsive pr-table-wrap">
            <table class="table table-bordered table-striped" id="request_items_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 25%;">@lang('Product Name')</th>
                        <th style="width: 12%;">@lang('SKU')</th>
                        <th style="width: 10%;" class="text-center">@lang('Shop Stock')</th>
                        <th style="width: 10%;" class="text-center">@lang('Main Store Stock')</th>
                        <th style="width: 10%;" class="text-center">@lang('Requested Qty')</th>
                        <th style="width: 9%;" class="text-center">@lang('Approved Qty')</th>
                        <th style="width: 9%;" class="text-center">@lang('Dispatched Qty')</th>
                        <th style="width: 9%;" class="text-center">@lang('Received Qty')</th>
                        <th style="width: 8%;" class="text-center">@lang('Discrepancy')</th>
                        <th style="width: 13%;">@lang('Line Notes')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_req = 0;
                        $total_app = 0;
                        $total_disp = 0;
                        $total_rec = 0;
                    @endphp
                    @foreach($productRequest->lines as $index => $line)
                        @php
                            $product = $line->product;
                            $variation = $line->variation;

                            $itemShopStock = $shopStock[$line->variation_id] ?? 0;
                            $itemSourceStock = $sourceStock[$line->variation_id] ?? 0;

                            $reqQty = (float)$line->requested_quantity;
                            $appQty = $line->approved_quantity !== null ? (float)$line->approved_quantity : null;
                            $dispQty = $line->dispatched_quantity !== null ? (float)$line->dispatched_quantity : null;
                            $recQty = $line->received_quantity !== null ? (float)$line->received_quantity : null;

                            $total_req += $reqQty;
                            if ($appQty !== null) $total_app += $appQty;
                            if ($dispQty !== null) $total_disp += $dispQty;
                            if ($recQty !== null) $total_rec += $recQty;

                            $discrepancy = ($dispQty !== null && $recQty !== null) ? ($recQty - $dispQty) : 0;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $product->name ?? '' }}</div>
                                @if($variation && $variation->name != 'DUMMY')
                                    <div style="color: #64748b; font-size: 12px;">({{ $variation->name }})</div>
                                @endif
                            </td>
                            <td><code>{{ $variation->sub_sku ?? '' }}</code></td>
                            <td class="text-center">
                                <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: #475569;">
                                    {{ number_format($itemShopStock, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: {{ $itemSourceStock > 0 ? '#dcfce7' : '#fee2e2' }}; color: {{ $itemSourceStock > 0 ? '#16a34a' : '#dc2626' }};">
                                    {{ number_format($itemSourceStock, 2) }}
                                </span>
                            </td>
                            <td class="text-center" style="font-weight: 700; color: #1e293b;">{{ number_format($reqQty, 2) }}</td>
                            <td class="text-center">
                                @if($appQty !== null)
                                    <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; background: {{ $appQty < $reqQty ? '#fef3c7' : '#e0f2fe' }}; color: {{ $appQty < $reqQty ? '#d97706' : '#0284c7' }};">
                                        {{ number_format($appQty, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($dispQty !== null)
                                    <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #f3e8ff; color: #9333ea;">
                                        {{ number_format($dispQty, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($recQty !== null)
                                    <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #16a34a;">
                                        {{ number_format($recQty, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($recQty !== null && $dispQty !== null)
                                    @if($discrepancy == 0)
                                        <span style="color: #16a34a; font-weight: 600;"><i class="fa fa-check"></i> 0</span>
                                    @elseif($discrepancy < 0)
                                        <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #fee2e2; color: #dc2626;" title="Shortage of {{ abs($discrepancy) }}">
                                            {{ number_format($discrepancy, 2) }}
                                        </span>
                                    @else
                                        <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: #dcfce7; color: #16a34a;" title="Surplus of {{ $discrepancy }}">
                                            +{{ number_format($discrepancy, 2) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($line->notes))
                                    <div style="font-size: 12px; color: #475569;">{{ $line->notes }}</div>
                                @endif
                                @if(!empty($line->discrepancy_reason))
                                    <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-top: 2px;">
                                        <i class="fa fa-exclamation-circle"></i> {{ $line->discrepancy_reason }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td colspan="5" class="text-right" style="padding: 14px; color: #1e293b;">@lang('Totals'):</td>
                        <td class="text-center" style="padding: 14px; font-weight: 700;">{{ number_format($total_req, 2) }}</td>
                        <td class="text-center" style="padding: 14px; font-weight: 700;">{{ !in_array($productRequest->status, ['draft', 'pending']) ? number_format($total_app, 2) : '-' }}</td>
                        <td class="text-center" style="padding: 14px; font-weight: 700;">{{ $total_disp > 0 ? number_format($total_disp, 2) : '-' }}</td>
                        <td class="text-center" style="padding: 14px; font-weight: 700;">{{ $total_rec > 0 ? number_format($total_rec, 2) : '-' }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Timeline & Stock Transfer Links -->
    <div class="row">
        <div class="col-md-8">
            <div class="detail-card">
                <h3 class="detail-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $primary_hex }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /></svg>
                    @lang('Audit Trail & Request History')
                </h3>

                <ul class="modern-timeline">
                    @forelse($productRequest->activities as $act)
                        @php
                            $colors = [
                                'created' => '#0284c7',
                                'submitted' => '#3b82f6',
                                'approved' => '#16a34a',
                                'partially_approved' => '#d97706',
                                'dispatched' => '#9333ea',
                                'received' => '#16a34a',
                                'rejected' => '#dc2626',
                                'changes_requested' => '#ea580c',
                            ];
                            $c = $colors[$act->action] ?? '#64748b';
                        @endphp
                        <li class="timeline-node">
                            <div class="timeline-node-icon" style="background: {{ $c }};">
                                <i class="fa fa-check" style="font-size: 11px;"></i>
                            </div>
                            <div class="timeline-node-content">
                                <div class="timeline-node-header">
                                    <strong style="color: #1e293b; font-size: 14px;">
                                        {{ $act->user->user_full_name ?? 'System' }}
                                        <span style="font-weight: 500; color: #64748b; font-size: 13px;">({{ ucfirst(str_replace('_', ' ', $act->action)) }})</span>
                                    </strong>
                                    <span style="font-size: 12px; color: #94a3b8;"><i class="fa fa-clock"></i> {{ @format_datetime($act->created_at) }}</span>
                                </div>
                                @if(!empty($act->notes))
                                    <div style="font-size: 13px; color: #475569; margin-top: 6px; line-height: 1.5;">
                                        {{ $act->notes }}
                                    </div>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">@lang('No activity recorded yet.')</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            @if($productRequest->stockTransfer)
                <div class="detail-card">
                    <h3 class="detail-card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $primary_hex }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /></svg>
                        @lang('Linked Stock Transfer')
                    </h3>

                    <div style="font-size: 13px; color: #475569; display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                        <div><strong>@lang('Reference No'):</strong> <code>{{ $productRequest->stockTransfer->ref_no }}</code></div>
                        <div><strong>@lang('Transfer Date'):</strong> {{ @format_datetime($productRequest->stockTransfer->transaction_date) }}</div>
                        <div><strong>@lang('Status'):</strong> <span class="label label-info">{{ ucfirst($productRequest->stockTransfer->status) }}</span></div>
                    </div>

                    <button type="button" 
                        class="btn-action-primary btn-modal" 
                        style="width: 100%; justify-content: center;"
                        data-href="{{ action([\App\Http\Controllers\StockTransferController::class, 'show'], [$productRequest->stockTransfer->id]) }}"
                        data-container=".view_modal">
                        <i class="fa fa-eye"></i> @lang('View Stock Transfer')
                    </button>
                </div>
            @else
                <div class="detail-card" style="text-align: center; padding: 32px 20px;">
                    <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px;">
                        <i class="fa fa-truck-loading"></i>
                    </div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 15px;">@lang('Transfer Pending Dispatch')</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 6px; line-height: 1.5;">
                        @lang('When the Main Store dispatches this request, an official stock transfer will be automatically linked here.')
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Review Modal (Approve / Reject / Changes) -->
@if($productRequest->status == 'pending' && auth()->user()->can('product_request.approve'))
<div class="modal fade" id="review_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $primary_hex }}; color: #ffffff;">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-white" style="font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-check-square"></i> @lang('Review Product Request') #{{ $productRequest->request_no }}
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="font-size: 13px; color: #475569; margin-bottom: 18px;">
                    @lang('Review requested quantities below. You can approve full amounts or adjust quantities if warehouse stock is limited.')
                </p>

                {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'approve'], [$productRequest->id]), 'method' => 'post', 'id' => 'form_approve_request']) !!}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="background: #f8fafc; font-size: 12px; text-transform: uppercase;">
                                    <th>@lang('Product')</th>
                                    <th class="text-center">@lang('Main Store Stock')</th>
                                    <th class="text-center">@lang('Requested Qty')</th>
                                    <th class="text-center" style="width: 25%;">@lang('Approved Qty') *</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productRequest->lines as $line)
                                    @php $mainStock = $sourceStock[$line->variation_id] ?? 0; @endphp
                                    <tr>
                                        <td>
                                            <strong style="color: #1e293b;">{{ $line->product->name ?? '' }}</strong>
                                            <br><small class="text-muted">SKU: {{ $line->variation->sub_sku ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; background: {{ $mainStock >= $line->requested_quantity ? '#dcfce7' : '#fee2e2' }}; color: {{ $mainStock >= $line->requested_quantity ? '#16a34a' : '#dc2626' }};">
                                                {{ number_format($mainStock, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center" style="font-weight: 700;">{{ number_format($line->requested_quantity, 2) }}</td>
                                        <td class="text-center">
                                            <input type="number" 
                                                   name="lines[{{ $line->id }}][approved_quantity]" 
                                                   class="form-control text-center" 
                                                   value="{{ (float)$line->requested_quantity }}" 
                                                   min="0" 
                                                   step="any" 
                                                   style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 700;"
                                                   required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group tw-mt-3">
                        <label style="font-size: 13px; font-weight: 500; color: #475569;">@lang('Approval Note'):</label>
                        {!! Form::textarea('notes', null, [
                            'class' => 'form-control',
                            'rows' => 2,
                            'placeholder' => __('Optional note for the destination shop...'),
                            'style' => 'border-radius: 8px; border: 1px solid #cbd5e1;'
                        ]) !!}
                    </div>

                    <div class="text-right tw-mt-4">
                        <button type="submit" class="btn-action-primary btn-action-pill" style="background: #16a34a !important;">
                            <i class="fa fa-check"></i> @lang('Approve Request')
                        </button>
                    </div>
                {!! Form::close() !!}

                <hr style="margin: 24px 0; border-color: #e2e8f0;">

                <div class="row">
                    <div class="col-sm-6">
                        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 16px;">
                            <div style="font-weight: 700; color: #92400e; font-size: 14px; margin-bottom: 8px;">
                                <i class="fa fa-undo"></i> @lang('Need Cashier to Modify?')
                            </div>
                            {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'requestChangesAction'], [$productRequest->id]), 'method' => 'post']) !!}
                                <div class="form-group">
                                    <input type="text" name="notes" class="form-control" placeholder="Reason / requested changes..." style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm btn-block" style="border-radius: 6px; font-weight: 600;">
                                    @lang('Return to Cashier for Revisions')
                                </button>
                            {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px;">
                            <div style="font-weight: 700; color: #b91c1c; font-size: 14px; margin-bottom: 8px;">
                                <i class="fa fa-times-circle"></i> @lang('Reject Requisition')
                            </div>
                            {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'reject'], [$productRequest->id]), 'method' => 'post']) !!}
                                <div class="form-group">
                                    <input type="text" name="rejection_reason" class="form-control" placeholder="Reason for rejection..." style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm btn-block" style="border-radius: 6px; font-weight: 600;">
                                    @lang('Reject Request')
                                </button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dispatch Modal -->
@if(in_array($productRequest->status, ['approved', 'partially_approved']) && auth()->user()->can('product_request.fulfill'))
<div class="modal fade" id="dispatch_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'dispatchStock'], [$productRequest->id]), 'method' => 'post']) !!}
            <div class="modal-header" style="background: {{ $primary_hex }}; color: #ffffff;">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-white" style="font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-truck"></i> @lang('Dispatch Stock Transfer to') {{ $productRequest->requestingLocation->name ?? 'Shop' }}
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; color: #1e40af; font-size: 13px; margin-bottom: 18px;">
                    <i class="fa fa-info-circle"></i> @lang('Dispatching will deduct items immediately from Main Store stock and place the transfer In Transit. The receiving shop will confirm arrival.')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="background: #f8fafc; font-size: 12px; text-transform: uppercase;">
                                <th>@lang('Product')</th>
                                <th class="text-center">@lang('Approved Qty')</th>
                                <th class="text-center" style="width: 25%;">@lang('Quantity to Dispatch') *</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productRequest->lines as $line)
                                @php $toSend = (float)($line->approved_quantity ?? $line->requested_quantity); @endphp
                                <tr>
                                    <td>
                                        <strong style="color: #1e293b;">{{ $line->product->name ?? '' }}</strong>
                                        <br><small class="text-muted">SKU: {{ $line->variation->sub_sku ?? '' }}</small>
                                    </td>
                                    <td class="text-center" style="font-weight: 700;">{{ number_format($toSend, 2) }}</td>
                                    <td class="text-center">
                                        <input type="number" 
                                               name="quantities[{{ $line->id }}]" 
                                               class="form-control text-center" 
                                               value="{{ $toSend }}" 
                                               min="0" 
                                               step="any" 
                                               style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 700;"
                                               required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group tw-mt-3">
                    <label style="font-size: 13px; font-weight: 500; color: #475569;">@lang('Dispatch / Courier Notes'):</label>
                    {!! Form::textarea('notes', null, [
                        'class' => 'form-control',
                        'rows' => 2,
                        'placeholder' => __('e.g. Sent with driver John, 2 sealed packages...'),
                        'style' => 'border-radius: 8px; border: 1px solid #cbd5e1;'
                    ]) !!}
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; height: 38px;">@lang('Cancel')</button>
                <button type="submit" class="btn-action-primary btn-action-pill">
                    <i class="fa fa-truck"></i> @lang('Confirm & Dispatch Transfer')
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endif

<!-- Receive Modal -->
@if($productRequest->status == 'in_transit' && auth()->user()->can('product_request.receive'))
<div class="modal fade" id="receive_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action([\App\Http\Controllers\ProductRequestController::class, 'receiveStock'], [$productRequest->id]), 'method' => 'post']) !!}
            <div class="modal-header" style="background: #16a34a; color: #ffffff;">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-white" style="font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-check-circle"></i> @lang('Confirm Physical Receipt of Products')
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px; color: #92400e; font-size: 13px; margin-bottom: 18px;">
                    <i class="fa fa-exclamation-triangle"></i> @lang('Please count each item carefully before confirming. Only the actual received units will be credited into your shop stock.')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="background: #f8fafc; font-size: 12px; text-transform: uppercase;">
                                <th>@lang('Product')</th>
                                <th class="text-center">@lang('Dispatched Qty')</th>
                                <th class="text-center" style="width: 25%;">@lang('Actual Received Qty') *</th>
                                <th style="width: 35%;">@lang('Discrepancy Reason (if any)')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productRequest->lines as $line)
                                @php $dispatched = (float)($line->dispatched_quantity ?? $line->approved_quantity); @endphp
                                <tr>
                                    <td>
                                        <strong style="color: #1e293b;">{{ $line->product->name ?? '' }}</strong>
                                        <br><small class="text-muted">SKU: {{ $line->variation->sub_sku ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span style="display:inline-block; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #f3e8ff; color: #9333ea;">
                                            {{ number_format($dispatched, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" 
                                               name="received[{{ $line->id }}]" 
                                               class="form-control text-center" 
                                               value="{{ $dispatched }}" 
                                               min="0" 
                                               step="any" 
                                               style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 700;"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="reason[{{ $line->id }}]" 
                                               class="form-control" 
                                               placeholder="e.g. 1 bottle broken in transit"
                                               style="height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group tw-mt-3">
                    <label style="font-size: 13px; font-weight: 500; color: #475569;">@lang('Receiving Notes'):</label>
                    {!! Form::textarea('notes', null, [
                        'class' => 'form-control',
                        'rows' => 2,
                        'placeholder' => __('Condition on delivery, remarks...'),
                        'style' => 'border-radius: 8px; border: 1px solid #cbd5e1;'
                    ]) !!}
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; height: 38px;">@lang('Cancel')</button>
                <button type="submit" class="btn-action-primary btn-action-pill" style="background: #16a34a !important;">
                    <i class="fa fa-check-circle"></i> @lang('Confirm & Update Shop Stock')
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endif
@endsection