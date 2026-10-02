<div class="tab-pane" id="psr_by_cat_tab">
    <div class="psr-table-responsive">
        <table class="table table-bordered table-striped" 
        id="product_sell_report_by_category" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('category.category')</th>
                    <th>@lang('report.current_stock')</th>
                    <th>@lang('report.total_unit_sold')</th>
                    <th>@lang('sale.total')</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr class="skeleton-row">
                        <td><div class="skeleton-block" style="width: 60%;"></div></td>
                        <td><div class="skeleton-block" style="width: 50%;"></div></td>
                        <td><div class="skeleton-block" style="width: 55%;"></div></td>
                        <td><div class="skeleton-block" style="width: 65%;"></div></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td><strong>@lang('sale.total'):</strong></td>
                    <td id="footer_psr_by_cat_total_stock"></td>
                    <td id="footer_psr_by_cat_total_sold"></td>
                    <td><span class="display_currency" id="footer_psr_by_cat_total_sell" data-currency_symbol ="true"></span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
