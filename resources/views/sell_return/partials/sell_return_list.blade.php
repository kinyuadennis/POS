<div class="table-responsive sr-table-wrap">
    <table class="table table-bordered table-striped dataTable" id="sell_return_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang('messages.date')</th>
                <th>@lang('lang_v1.return_no')</th>
                <th>@lang('lang_v1.original_receipt')</th>
                <th>@lang('sale.customer_name')</th>
                <th>@lang('sale.location')</th>
                <th>@lang('purchase.payment_status')</th>
                <th>@lang('sale.total_amount')</th>
                <th>@lang('purchase.payment_due')</th>
                <th>@lang('messages.action')</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 5; $i++)
                <tr class="skeleton-row">
                    <td><div class="skeleton-block" style="width: 70%;"></div></td>
                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                    <td><div class="skeleton-block" style="width: 55%;"></div></td>
                    <td><div class="skeleton-block" style="width: 75%;"></div></td>
                    <td><div class="skeleton-block" style="width: 45%;"></div></td>
                    <td><div class="skeleton-block" style="width: 40%;"></div></td>
                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                    <td><div class="skeleton-block" style="width: 50%;"></div></td>
                    <td><div class="skeleton-block" style="width: 60%;"></div></td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr class="bg-gray font-17 text-center footer-total">
                <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                <td class="footer_payment_status_count_sr" id="footer_payment_status_count_sr"></td>
                <td><span class="display_currency footer_sell_return_total" id="footer_sell_return_total" data-currency_symbol ="true"></span></td>
                <td><span class="display_currency footer_total_due_sr" id="footer_total_due_sr" data-currency_symbol ="true"></span></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
