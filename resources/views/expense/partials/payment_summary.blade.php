@php
    $paid_amount = $paid_amount ?? collect($payments ?? [])->sum('amount');
    $due_amount = $transaction->final_total - $paid_amount;
    if ($transaction->type == 'expense_refund') {
        $due_amount = -1 * $due_amount;
    }
    $contact_name = '';
    if (!empty($transaction->contact)) {
        $contact_name = trim(implode(' · ', array_filter([
            $transaction->contact->name,
            $transaction->contact->supplier_business_name
        ])));
    }
@endphp
<div class="expense-pay-grid">
    <div class="expense-pay-card meta">
        <div class="expense-pay-label">@lang('purchase.ref_no')</div>
        <div class="expense-pay-value">#{{ $transaction->ref_no }}</div>
    </div>
    <div class="expense-pay-card meta">
        <div class="expense-pay-label">@lang('messages.date')</div>
        <div class="expense-pay-value">{{ @format_date($transaction->transaction_date) }}</div>
    </div>
    @if(!empty($transaction->location))
        <div class="expense-pay-card meta">
            <div class="expense-pay-label">@lang('business.location')</div>
            <div class="expense-pay-value">{{ $transaction->location->name }}</div>
        </div>
    @endif
    <div class="expense-pay-card meta">
        <div class="expense-pay-label">@lang('sale.payment_status')</div>
        <div class="expense-pay-value">
            <span class="label @payment_status($transaction->payment_status)">{{ __('lang_v1.' . $transaction->payment_status) }}</span>
        </div>
    </div>
    @if($contact_name !== '')
        <div class="expense-pay-card meta">
            <div class="expense-pay-label">@lang('contact.contact')</div>
            <div class="expense-pay-value">{{ $contact_name }}</div>
        </div>
    @endif
</div>
<div class="expense-pay-grid">
    <div class="expense-pay-card total-card">
        <div class="expense-pay-label">@lang('sale.total_amount')</div>
        <div class="expense-pay-value amount"><span class="display_currency" data-currency_symbol="true">{{ $transaction->final_total }}</span></div>
    </div>
    <div class="expense-pay-card total-card">
        <div class="expense-pay-label">@lang('lang_v1.paid_amount')</div>
        <div class="expense-pay-value amount"><span class="display_currency" data-currency_symbol="true">{{ $paid_amount }}</span></div>
    </div>
    <div class="expense-pay-card total-card due-card">
        <div class="expense-pay-label">@lang('purchase.payment_due')</div>
        <div class="expense-pay-value amount due"><span class="display_currency" data-currency_symbol="true">{{ $due_amount }}</span></div>
    </div>
</div>
@if(!empty($transaction->additional_notes))
    <div class="expense-pay-section" style="margin-bottom: 16px; padding: 14px 16px;">
        <div class="expense-pay-label">@lang('expense.expense_note')</div>
        <p class="expense-pay-note">{{ $transaction->additional_notes }}</p>
    </div>
@endif
