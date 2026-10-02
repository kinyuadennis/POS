@include('expense.partials.payment_modal_theme')
<div class="modal-dialog modal-lg expense-pay-dialog" role="document">
    <div class="modal-content expense-pay-modal">
        <div class="modal-header expense-pay-header expense-pay-header-add">
            <div>
                <h4 class="modal-title expense-pay-title no-print">@lang('purchase.view_payments')</h4>
                <p class="expense-pay-subtitle no-print">@lang('expense.view_payments_subtitle') · @lang('purchase.ref_no') #{{ $transaction->ref_no }}</p>
                <h4 class="modal-title visible-print-block">@lang('purchase.ref_no'): {{ $transaction->ref_no }}</h4>
            </div>
            <button type="button" class="close expense-pay-close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body expense-pay-body">
            @include('expense.partials.payment_summary')

            @php
                $can_manage_expense_payment = auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense');
            @endphp

            <div class="expense-pay-section">
                <div class="expense-pay-section-head">
                    <h5 class="expense-pay-section-title">@lang('sale.payments')</h5>
                    @if($transaction->payment_status != 'paid' && $can_manage_expense_payment)
                        <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'addPayment'], [$transaction->id]) }}" class="expense-pay-btn-primary add_payment_modal no-print" style="height: 36px; padding: 0 14px; font-size: 13px;">
                            <i class="fa fa-plus" aria-hidden="true"></i> @lang('purchase.add_payment')
                        </a>
                    @endif
                </div>
                <div class="expense-pay-table-wrap">
                    <table class="table expense-pay-table">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.amount')</th>
                                <th>@lang('purchase.payment_method')</th>
                                <th>@lang('purchase.payment_note')</th>
                                @if($accounts_enabled)
                                    <th>@lang('lang_v1.payment_account')</th>
                                @endif
                                <th class="no-print">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td>{{ @format_datetime($payment->paid_on) }}</td>
                                    <td>{{ $payment->payment_ref_no }}</td>
                                    <td class="amount-cell"><span class="display_currency" data-currency_symbol="true">{{ $payment->amount }}</span></td>
                                    <td>{{ $payment_types[$payment->method] ?? '' }}</td>
                                    <td>@if(!empty($payment->gateway)){{ $payment->gateway }} - @endif {{ $payment->note }}</td>
                                    @if($accounts_enabled)
                                        <td>{{ $payment->payment_account->name ?? '' }}</td>
                                    @endif
                                    <td class="no-print">
                                        <div class="expense-pay-actions">
                                            @if($can_manage_expense_payment && $payment->method != 'advance')
                                                <button type="button" class="expense-pay-btn-xs edit_payment" title="@lang('messages.edit')"
                                                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'edit'], [$payment->id]) }}">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                            @endif
                                            @if($can_manage_expense_payment)
                                                <button type="button" class="expense-pay-btn-xs danger delete_payment" title="@lang('messages.delete')"
                                                    data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'destroy'], [$payment->id]) }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="expense-pay-btn-xs theme view_payment" title="@lang('messages.view')"
                                                data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'viewPayment'], [$payment->id]) }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                            @if(!empty($payment->document_path))
                                                <a href="{{ $payment->document_path }}" class="expense-pay-btn-xs" download="{{ $payment->document_name }}" title="@lang('purchase.download_document')">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                @if(isFileImage($payment->document_name))
                                                    <button type="button" data-href="{{ $payment->document_path }}" class="expense-pay-btn-xs view_uploaded_document" title="@lang('lang_v1.view_document')">
                                                        <i class="fa fa-picture-o"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $accounts_enabled ? 7 : 6 }}" class="expense-pay-empty">@lang('purchase.no_records_found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer expense-pay-footer">
            <button type="button" class="expense-pay-btn-secondary no-print" data-dismiss="modal">@lang('messages.close')</button>
            <button type="button" class="expense-pay-btn-primary no-print" aria-label="Print" onclick="$(this).closest('div.modal').printThis();">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>
        </div>
    </div>
</div>
