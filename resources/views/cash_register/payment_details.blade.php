@php
    $opening_balance = $register_details->cash_in_hand ?? 0;
    $cash_sales = $register_details->total_cash ?? 0;
    $cash_refunds = $register_details->total_cash_refund ?? 0;
    $cash_expenses = $register_details->total_cash_expense ?? 0;
    $total_bank_deposit = $register_details->total_bank_deposit ?? 0;
    $expected_cash_in_drawer = $opening_balance + $cash_sales - $cash_refunds - $cash_expenses - $total_bank_deposit;
@endphp

<div class="row mini_print" style="margin-bottom: 10px;">
  <!-- Cash Drawer Summary -->
  <div class="col-sm-6 col-xs-12" style="margin-bottom: 15px;">
    <div class="panel panel-default" style="border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 0; height: 100%;">
      <div class="panel-heading" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 15px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <h4 class="panel-title" style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa fa-cash-register text-success" style="margin-right: 6px;"></i> Cash Drawer Summary
        </h4>
      </div>
      <div class="panel-body" style="padding: 0;">
        @if(!empty($is_close_register))
          <div style="background-color: #f8fafc; padding: 14px 15px; border-bottom: 1px solid #e2e8f0;">
            <div class="form-group" style="margin-bottom: 10px;">
              <label for="closing_amount" style="font-weight: 700; color: #0f172a; font-size: 13px; margin-bottom: 4px; display: block;">
                Physical Cash Counted in Till (Total Cash):*
              </label>
              {!! Form::text('closing_amount', @num_format($expected_cash_in_drawer), [
                  'class' => 'form-control input_number',
                  'id' => 'closing_amount',
                  'required',
                  'style' => 'height: 42px; font-size: 16px; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; color: #0f172a; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);',
                  'placeholder' => __('cash_register.total_cash'),
                  'data-expected' => $expected_cash_in_drawer
              ]); !!}
              <p class="help-block" style="margin-top: 4px; margin-bottom: 0; font-size: 11.5px; color: #64748b;">
                Enter the total physical cash currently counted in the cash drawer.
              </p>
            </div>



            <!-- Live Variance Display Badge -->
            <div id="cash_variance_wrapper" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; background: #ffffff; border: 1px solid #cbd5e1; margin-top: 8px;">
              <span style="font-size: 12px; font-weight: 600; color: #475569;">Cash Discrepancy / Variance:</span>
              <span id="cash_variance_badge" style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background-color: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                Balanced (0.00)
              </span>
            </div>
          </div>
        @else
          @php
            $status = $register_details->status ?? 'open';
            $closing_cash = ($status === 'close' && isset($register_details->closing_amount)) ? (float)$register_details->closing_amount : null;
            $variance = !is_null($closing_cash) ? ($closing_cash - $expected_cash_in_drawer) : 0;
          @endphp
          <div style="background-color: #f8fafc; padding: 14px 15px; border-bottom: 1px solid #e2e8f0;">
            <div style="margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
              <span style="font-weight: 700; color: #0f172a; font-size: 13px;">
                Physical Cash Counted (Total Cash):
              </span>
              <span style="font-size: 16px; font-weight: 700; color: #0f172a; padding: 4px 12px; background: #ffffff; border-radius: 6px; border: 1px solid #cbd5e1;">
                @if(!is_null($closing_cash))
                  <span class="display_currency" data-currency_symbol="true">{{ $closing_cash }}</span>
                @else
                  <span class="text-muted" style="font-size: 13px; font-weight: 500;">Register Open</span>
                @endif
              </span>
            </div>

            <!-- Variance Display Badge -->
            <div id="cash_variance_wrapper" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; background: #ffffff; border: 1px solid #cbd5e1; margin-top: 6px;">
              <span style="font-size: 12px; font-weight: 600; color: #475569;">Cash Discrepancy / Variance:</span>
              @if(is_null($closing_cash))
                <span style="font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 9999px; background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                  N/A (Register Open)
                </span>
              @elseif(abs($variance) < 0.01)
                <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background-color: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                  <i class="fa fa-check-circle"></i> Balanced (0.00)
                </span>
              @elseif($variance > 0)
                <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background-color: #eff6ff; color: #1d4ed8; border: 1px solid #93c5fd;">
                  <i class="fa fa-arrow-up"></i> Over / Surplus (+ <span class="display_currency" data-currency_symbol="true">{{ abs($variance) }}</span>)
                </span>
              @else
                <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; background-color: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5;">
                  <i class="fa fa-arrow-down"></i> Shortage (- <span class="display_currency" data-currency_symbol="true">{{ abs($variance) }}</span>)
                </span>
              @endif
            </div>
          </div>
        @endif
        <table class="table" style="margin-bottom: 0;">
          <tbody>
            <tr>
              <td style="padding: 10px 15px; border-top: none; color: #475569; font-weight: 500;">
                @lang('cash_register.cash_in_hand')
              </td>
              <td style="padding: 10px 15px; border-top: none; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $opening_balance }}</span>
              </td>
            </tr>
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <span class="text-success" style="font-weight: 700; margin-right: 4px;">(+)</span> Cash Received (Sales)
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #16a34a;">
                <span class="display_currency" data-currency_symbol="true">{{ $cash_sales }}</span>
              </td>
            </tr>
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <span class="@if($cash_refunds > 0) text-danger @else text-muted @endif" style="font-weight: 700; margin-right: 4px;">(-)</span> Cash Refunds
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: @if($cash_refunds > 0) #dc2626 @else #64748b @endif;">
                <span class="display_currency" data-currency_symbol="true">{{ $cash_refunds }}</span>
              </td>
            </tr>
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <span class="@if($cash_expenses > 0) text-danger @else text-muted @endif" style="font-weight: 700; margin-right: 4px;">(-)</span> Cash Expenses
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: @if($cash_expenses > 0) #dc2626 @else #64748b @endif;">
                <span class="display_currency" data-currency_symbol="true">{{ $cash_expenses }}</span>
              </td>
            </tr>
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <span class="@if($total_bank_deposit > 0) text-danger @else text-muted @endif" style="font-weight: 700; margin-right: 4px;">(-)</span> Cash Deposited to Bank
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: @if($total_bank_deposit > 0) #dc2626 @else #64748b @endif;">
                <span class="display_currency" data-currency_symbol="true">{{ $total_bank_deposit }}</span>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr style="background-color: #f1f5f9; border-top: 2px solid #cbd5e1;">
              <th style="padding: 12px 15px; font-weight: 700; color: #0f172a; font-size: 13px;">
                Expected Cash in Drawer
              </th>
              <th style="padding: 12px 15px; text-align: right; font-weight: 700; color: #0f172a; font-size: 14px;">
                <span class="display_currency" data-currency_symbol="true">{{ $expected_cash_in_drawer }}</span>
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <!-- Sales Summary -->
  <div class="col-sm-6 col-xs-12" style="margin-bottom: 15px;">
    <div class="panel panel-default" style="border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 0; height: 100%;">
      <div class="panel-heading" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 10px 15px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <h4 class="panel-title" style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa fa-receipt text-primary" style="margin-right: 6px;"></i> Sales Summary
        </h4>
      </div>
      <div class="panel-body" style="padding: 0;">

        <table class="table" style="margin-bottom: 0;">
          <tbody>
            <tr>
              <td style="padding: 10px 15px; border-top: none; color: #475569; font-weight: 500;">
                <i class="fa fa-money-bill-wave text-muted" style="width: 16px; margin-right: 6px;"></i> @lang('cash_register.cash_payment')
              </td>
              <td style="padding: 10px 15px; border-top: none; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_cash }}</span>
              </td>
            </tr>
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <i class="fa fa-credit-card text-muted" style="width: 16px; margin-right: 6px;"></i> @lang('cash_register.card_payment')
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_card }}</span>
              </td>
            </tr>
            @if(array_key_exists('paybill', $payment_types))
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <i class="fa fa-mobile-alt text-muted" style="width: 16px; margin-right: 6px;"></i> {{ $payment_types['paybill'] }}
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_paybill ?? 0 }}</span>
              </td>
            </tr>
            @endif
            @if(array_key_exists('till', $payment_types))
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <i class="fa fa-store text-muted" style="width: 16px; margin-right: 6px;"></i> {{ $payment_types['till'] }}
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_till ?? 0 }}</span>
              </td>
            </tr>
            @endif
            @if(array_key_exists('mpesa', $payment_types))
            <tr>
              <td style="padding: 10px 15px; color: #475569; font-weight: 500;">
                <i class="fa fa-phone text-muted" style="width: 16px; margin-right: 6px;"></i> {{ $payment_types['mpesa'] }}
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #1e293b;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_mpesa ?? 0 }}</span>
              </td>
            </tr>
            @endif
            @if($register_details->total_refund > 0)
            <tr>
              <td style="padding: 10px 15px; color: #dc2626; font-weight: 500;">
                <i class="fa fa-undo text-danger" style="width: 16px; margin-right: 6px;"></i> @lang('cash_register.total_refund')
              </td>
              <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: #dc2626;">
                -<span class="display_currency" data-currency_symbol="true">{{ $register_details->total_refund }}</span>
              </td>
            </tr>
            @endif
          </tbody>
          <tfoot>
            <tr style="background-color: #f1f5f9; border-top: 2px solid #cbd5e1;">
              <th style="padding: 12px 15px; font-weight: 700; color: #0f172a; font-size: 13px;">
                @lang('cash_register.total_sales')
              </th>
              <th style="padding: 12px 15px; text-align: right; font-weight: 700; color: #0f172a; font-size: 14px;">
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_sale }}</span>
              </th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

@include('cash_register.register_product_details')
