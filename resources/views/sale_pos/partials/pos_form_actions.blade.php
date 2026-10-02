@php
    $is_mobile = isMobile();
@endphp

<style>
/* POS Checkout Actions Toolbar & Buttons */
.pos-payment-footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    z-index: 1000 !important;
    pointer-events: none !important;
}

.pos-payment-footer .pos-form-actions {
    pointer-events: auto !important;
    padding: 6px 12px !important;
    background: #ffffff !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.1), 0 2px 6px -1px rgba(0, 0, 0, 0.05) !important;
    border: 1px solid #e2e8f0 !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

.pos-actions-bar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
    width: 100% !important;
}

.pos-actions-left {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
    -webkit-overflow-scrolling: touch !important;
    padding: 2px 0 !important;
}

.pos-actions-left::-webkit-scrollbar {
    display: none !important;
}

.pos-actions-right {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    flex-shrink: 0 !important;
}

.pos-actions-divider {
    width: 1px !important;
    height: 22px !important;
    background-color: #e2e8f0 !important;
    margin: 0 3px !important;
    flex-shrink: 0 !important;
}

/* Base Action Button */
.pos-action-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 38px !important;
    min-height: 38px !important;
    max-height: 38px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    font-family: inherit !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    user-select: none !important;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-sizing: border-box !important;
    outline: none !important;
    flex-shrink: 0 !important;
    text-decoration: none !important;
}

.pos-action-btn:focus-visible {
    outline: 2px solid #3b82f6 !important;
    outline-offset: 1px !important;
}

.pos-action-btn .pos-btn-icon {
    font-size: 13px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
}

.pos-action-btn .pos-btn-text {
    display: inline !important;
    white-space: nowrap !important;
    line-height: 1 !important;
}

.pos-action-btn .pos-btn-img {
    height: 16px !important;
    width: auto !important;
    max-width: 48px !important;
    object-fit: contain !important;
    display: inline-block !important;
    vertical-align: middle !important;
    flex-shrink: 0 !important;
}

/* 1. Multiple Pay Button (Primary dark navy checkout) */
.pos-btn-multipay {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.25) !important;
}

.pos-btn-multipay:hover {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
    border-color: #1e293b !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(15, 23, 42, 0.3) !important;
}

.pos-btn-multipay:active {
    transform: translateY(0) !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.2) !important;
}

/* 2. Cash Button (Primary emerald checkout) */
.pos-btn-cash {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
    color: #ffffff !important;
    border-color: #059669 !important;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.3) !important;
}

.pos-btn-cash:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%) !important;
    border-color: #047857 !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(5, 150, 105, 0.35) !important;
}

.pos-btn-cash:active {
    transform: translateY(0) !important;
    box-shadow: 0 1px 2px rgba(5, 150, 105, 0.2) !important;
}

/* 3. I&M Bank Button (Brand paybill) */
.pos-btn-imbank {
    background: #f8fafc !important;
    color: #1e293b !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

.pos-btn-imbank:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08) !important;
}

.pos-btn-imbank:active {
    background: #e2e8f0 !important;
    transform: translateY(0) !important;
}

/* 4. Till Button (M-Pesa / Mobile Till) */
.pos-btn-till {
    background: #f8fafc !important;
    color: #166534 !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

.pos-btn-till .pos-icon-till {
    color: #00A651 !important;
    font-size: 14px !important;
}

.pos-btn-till:hover {
    background: #f0fdf4 !important;
    border-color: #86efac !important;
    color: #14532d !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 3px 6px rgba(22, 101, 52, 0.12) !important;
}

.pos-btn-till:active {
    background: #dcfce7 !important;
    transform: translateY(0) !important;
}

/* Secondary Actions Shared Styling */
.pos-btn-secondary {
    background: #ffffff !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
}

.pos-btn-secondary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.06) !important;
}

.pos-btn-secondary:active {
    transform: translateY(0) !important;
}

/* 5. Draft Button */
.pos-btn-draft .pos-icon-draft {
    color: #0284c7 !important;
}

.pos-btn-draft:hover {
    background: #f0f9ff !important;
    border-color: #bae6fd !important;
    color: #0369a1 !important;
}

.pos-btn-draft:active {
    background: #e0f2fe !important;
}

/* 6. Quotation Button */
.pos-btn-quotation .pos-icon-quotation {
    color: #d97706 !important;
}

.pos-btn-quotation:hover {
    background: #fffbeb !important;
    border-color: #fde68a !important;
    color: #b45309 !important;
}

.pos-btn-quotation:active {
    background: #fef3c7 !important;
}

/* 7. Suspend Button */
.pos-btn-suspend .pos-icon-suspend {
    color: #e11d48 !important;
}

.pos-btn-suspend:hover {
    background: #fff1f2 !important;
    border-color: #fecdd3 !important;
    color: #be123c !important;
}

.pos-btn-suspend:active {
    background: #ffe4e6 !important;
}

/* 8. Credit Sale Button */
.pos-btn-creditsale .pos-icon-creditsale {
    color: #7c3aed !important;
}

.pos-btn-creditsale:hover {
    background: #f5f3ff !important;
    border-color: #ddd6fe !important;
    color: #6d28d9 !important;
}

.pos-btn-creditsale:active {
    background: #ede9fe !important;
}

/* 9. Cancel / Delete Button */
.pos-btn-cancel {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border-color: #fecaca !important;
    box-shadow: 0 1px 2px rgba(185, 28, 28, 0.04) !important;
}

.pos-btn-cancel:hover {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(220, 38, 38, 0.25) !important;
}

.pos-btn-cancel:active {
    background: #b91c1c !important;
    border-color: #b91c1c !important;
    color: #ffffff !important;
    transform: translateY(0) !important;
}

/* 10. Recent Transactions Button */
.pos-btn-recent {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
    color: #ffffff !important;
    border-color: #4f46e5 !important;
    box-shadow: 0 1px 3px rgba(79, 70, 229, 0.25) !important;
}

.pos-btn-recent:hover {
    background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
    border-color: #4338ca !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.35) !important;
}

.pos-btn-recent:active {
    transform: translateY(0) !important;
}

/* Disabled & Inactive States */
.pos-action-btn:disabled,
.pos-action-btn.disabled,
.pos-action-btn[disabled] {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    transform: none !important;
    box-shadow: none !important;
    filter: grayscale(40%) !important;
}

/* Responsive Rules for iPad / Tablet / Mobile */
@media (max-width: 1024px) {
    .pos-action-btn {
        height: 36px !important;
        min-height: 36px !important;
        max-height: 36px !important;
        padding: 0 9px !important;
        font-size: 11.5px !important;
        gap: 5px !important;
    }
    
    .pos-action-btn .pos-btn-icon {
        font-size: 12px !important;
    }

    .pos-action-btn .pos-btn-img {
        height: 14px !important;
        max-width: 42px !important;
    }

    .pos-payment-footer .pos-form-actions {
        padding: 5px 8px !important;
    }
}

@media (max-width: 767px) {
    .pos-actions-bar {
        gap: 6px !important;
    }
    
    .pos-action-btn {
        height: 35px !important;
        min-height: 35px !important;
        max-height: 35px !important;
        padding: 0 8px !important;
        font-size: 11px !important;
    }
    
    .pos-actions-divider {
        height: 18px !important;
        margin: 0 1px !important;
    }
}
</style>

<div class="pos-payment-footer pos-checkout-actions" id="pos_checkout_actions">
    <div class="pos-form-actions">
        <div class="pos-actions-bar">
            <div class="pos-actions-left pos-actions-scroll-wrapper">
                @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="pos-action-btn pos-btn-multipay no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif"
                        id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')">
                        <i class="fas fa-money-check-alt pos-btn-icon" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('lang_v1.checkout_multi_pay')</span>
                    </button>
                @endif

                @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="pos-action-btn pos-btn-cash no-print pos-express-finalize @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                        <i class="fas fa-money-bill-alt pos-btn-icon" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('lang_v1.express_checkout_cash')</span>
                    </button>

                    <button type="button"
                        class="pos-action-btn pos-btn-imbank no-print pos-express-finalize @if (!array_key_exists('paybill', $payment_types)) hide @endif"
                        data-pay_method="paybill" title="I&M Bank"
                        aria-label="I&M Bank">
                        <img src="{{ asset('img/payment-methods/imbank-logo.svg') }}" alt="I&M Bank" class="pos-btn-img">
                        <span class="pos-btn-text">I&M Bank</span>
                    </button>

                    <button type="button"
                        class="pos-action-btn pos-btn-till no-print pos-express-finalize @if (!array_key_exists('till', $payment_types)) hide @endif"
                        data-pay_method="till" title="Till"
                        aria-label="Till">
                        <i class="fas fa-mobile-alt pos-btn-icon pos-icon-till" aria-hidden="true"></i>
                        <span class="pos-btn-text">Till</span>
                    </button>
                @endif

                <div class="pos-actions-divider" aria-hidden="true"></div>

                @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="pos-action-btn pos-btn-secondary pos-btn-draft @if ($pos_settings['disable_draft'] != 0) hide @endif"
                        id="pos-draft" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-edit pos-btn-icon pos-icon-draft" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('sale.draft')</span>
                    </button>
                @endif

                @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button"
                        class="pos-action-btn pos-btn-secondary pos-btn-quotation"
                        id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-file-invoice pos-btn-icon pos-icon-quotation" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('lang_v1.quotation')</span>
                    </button>
                @endif

                @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_suspend']))
                        <button type="button"
                            class="pos-action-btn pos-btn-secondary pos-btn-suspend no-print pos-express-finalize"
                            data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')"
                            @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-pause pos-btn-icon pos-icon-suspend" aria-hidden="true"></i>
                            <span class="pos-btn-text">@lang('lang_v1.suspend')</span>
                        </button>
                    @endif
                @endif

                @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_credit_sale_button']))
                        <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">
                        <button type="button"
                            class="pos-action-btn pos-btn-secondary pos-btn-creditsale no-print pos-express-finalize"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')"
                            @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-check pos-btn-icon pos-icon-creditsale" aria-hidden="true"></i>
                            <span class="pos-btn-text">@lang('lang_v1.credit_sale')</span>
                        </button>
                    @endif
                @endif

                <div class="pos-actions-divider" aria-hidden="true"></div>

                @if (empty($edit))
                    <button type="button"
                        class="pos-action-btn pos-btn-cancel"
                        id="pos-cancel">
                        <i class="fas fa-window-close pos-btn-icon" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('sale.cancel')</span>
                    </button>
                @else
                    <button type="button"
                        class="pos-action-btn pos-btn-cancel hide"
                        id="pos-delete" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-trash-alt pos-btn-icon" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('messages.delete')</span>
                    </button>
                @endif
            </div>

            <div class="pos-actions-right">
                @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                    <button type="button"
                        class="pos-action-btn pos-btn-recent"
                        data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions">
                        <i class="fas fa-clock pos-btn-icon" aria-hidden="true"></i>
                        <span class="pos-btn-text">@lang('lang_v1.recent_transactions')</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@if (isset($transaction))
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $transaction->discount_amount,
        'discount_type' => $transaction->discount_type,
        'rp_redeemed' => $transaction->rp_redeemed,
        'rp_redeemed_amount' => $transaction->rp_redeemed_amount,
        'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
    ])
@else
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $business_details->default_sales_discount,
        'discount_type' => 'fixed',
        'rp_redeemed' => 0,
        'rp_redeemed_amount' => 0,
        'max_available' => 0,
    ])
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
    @include('sale_pos.partials.edit_order_tax_modal', [
        'selected_tax' => $business_details->default_sales_tax,
    ])
@endif

@include('sale_pos.partials.edit_shipping_modal')
