@php
    $is_edit = !empty($is_edit);
    $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];
    $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
@endphp
<div class="row payment_row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control', 'readonly', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label("amount" , __('sale.amount') . ':*') !!}
            <div class="input-group expense-pay-amount-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                @if($is_edit)
                    {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount')]); !!}
                @else
                    {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount'), 'data-rule-max-value' => $payment_line->amount, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="{{ json_encode($enable_cash_denomination_for_payment_methods) }}">

    @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens')
        <div class="clearfix"></div>
        <div class="col-md-12 cash_denomination_div @if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
            <hr>
            <strong>@lang('lang_v1.cash_denominations')</strong>
            @if(!empty($pos_settings['cash_denominations']))
                <table class="table table-slim">
                    <thead>
                        <tr>
                            <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                            <th width="20%">&nbsp;</th>
                            <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                            <th width="20%">&nbsp;</th>
                            <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $denom_total = 0; @endphp
                        @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
                            @php
                                $count = 0;
                                $sub_total = 0;
                                if ($is_edit && !empty($payment_line->denominations)) {
                                    foreach ($payment_line->denominations as $d) {
                                        if ($d->amount == $dnm) {
                                            $count = $d->total_count;
                                            $sub_total = $d->total_count * $d->amount;
                                            $denom_total += $sub_total;
                                        }
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-right">{{ $dnm }}</td>
                                <td class="text-center">X</td>
                                <td>{!! Form::number("denominations[$dnm]", $is_edit ? $count : null, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;']); !!}</td>
                                <td class="text-center">=</td>
                                <td class="text-left">
                                    <span class="denomination_subtotal">{{ $is_edit ? @num_format($sub_total) : 0 }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-center">@lang('sale.total')</th>
                            <td>
                                <span class="denomination_total">{{ $is_edit ? @num_format($denom_total) : 0 }}</span>
                                <input type="hidden" class="denomination_total_amount" value="{{ $is_edit ? $denom_total : 0 }}">
                                <input type="hidden" class="is_strict" value="{{ $pos_settings['cash_denomination_strict_check'] ?? '' }}">
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
            @else
                <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
            @endif
        </div>
        <div class="clearfix"></div>
    @endif

    @if(!empty($accounts))
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-money-bill-alt"></i>
                    </span>
                    {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
                </div>
            </div>
        </div>
    @endif
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <p class="help-block">
                @if($is_edit)
                    @lang('lang_v1.previous_file_will_be_replaced')
                @endif
                @includeIf('components.document_help_text')
            </p>
        </div>
    </div>
    <div class="clearfix"></div>
    @include('transaction_payment.payment_type_details')
    <div class="col-md-12">
        <div class="form-group" style="margin-bottom: 0;">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
            <p class="help-block">@lang('expense.payment_note_help')</p>
        </div>
    </div>
</div>
