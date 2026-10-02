@include('expense.partials.payment_modal_theme')
<div class="modal-dialog modal-lg expense-pay-dialog" role="document">
    <div class="modal-content expense-pay-modal">
        {!! Form::open(['url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'store']), 'method' => 'post', 'id' => 'transaction_payment_add_form', 'files' => true ]) !!}
        {!! Form::hidden('transaction_id', $transaction->id); !!}
        @if(!empty($transaction->location))
            {!! Form::hidden('default_payment_accounts', $transaction->location->default_payment_accounts, ['id' => 'default_payment_accounts']); !!}
        @endif
        @if(!empty($transaction->contact))
            {!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
        @endif

        <div class="modal-header expense-pay-header expense-pay-header-add">
            <div>
                <h4 class="modal-title expense-pay-title">@lang('purchase.add_payment')</h4>
                <p class="expense-pay-subtitle">@lang('expense.add_payment_subtitle') · @lang('purchase.ref_no') #{{ $transaction->ref_no }}</p>
            </div>
            <button type="button" class="close expense-pay-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body expense-pay-body">
            @include('expense.partials.payment_summary', ['paid_amount' => $paid_amount ?? ($transaction->final_total - $payment_line->amount)])

            <div class="expense-pay-section">
                <div class="expense-pay-section-head">
                    <h5 class="expense-pay-section-title">@lang('expense.payment_details')</h5>
                </div>
                @include('expense.partials.payment_form_fields', ['is_edit' => false])
            </div>
        </div>

        <div class="modal-footer expense-pay-footer">
            <button type="button" class="expense-pay-btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
            <button type="submit" class="expense-pay-btn-primary">@lang('messages.save')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
<script>
    (function () {
        var $modal = $('.payment_modal');
        $modal.find('select.select2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                width: '100%',
                dropdownParent: $modal
            });
        });
    })();
</script>
