<div class="product-table-top-scroll-wrapper" id="stock_report_table_top_scroll">
    <div class="product-table-top-scroll-inner" id="stock_report_table_top_scroll_inner"></div>
</div>
<table class="table" id="stock_report_table">
    <thead>
        <tr>
            <th>@lang('messages.action')</th>
            <th>SKU</th>
            <th>@lang('business.product')</th>
            <th>@lang('lang_v1.variation')</th>
            <th>@lang('product.category')</th>
            <th>@lang('sale.location')</th>
            <th>@lang('purchase.unit_selling_price')</th>
            <th>@lang('report.current_stock')</th>
            @can('view_product_stock_value')
            <th class="stock_price">@lang('lang_v1.total_stock_price') <br><small>(@lang('lang_v1.by_purchase_price'))</small></th>
            <th>@lang('lang_v1.total_stock_price') <br><small>(@lang('lang_v1.by_sale_price'))</small></th>
            <th>@lang('lang_v1.potential_profit')</th>
            @endcan
            <th>@lang('report.total_unit_sold')</th>
            <th>@lang('lang_v1.total_unit_transfered')</th>
            <th>@lang('lang_v1.total_unit_adjusted')</th>
            @if($show_manufacturing_data)
                <th class="current_stock_mfg">@lang('manufacturing::lang.current_stock_mfg') @show_tooltip(__('manufacturing::lang.mfg_stock_tooltip'))</th>
            @endif
    </thead>
    <tbody id="stock_report_table_body">
        @for ($i = 0; $i < 5; $i++)
            <tr class="skeleton-row">
                <td><div class="skeleton-block" style="width: 65px; height: 26px; border-radius: 8px;"></div></td>
                <td><div class="skeleton-block" style="width: 80px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 120px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 90px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 90px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 85px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 70px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 65px; height: 22px; border-radius: 20px;"></div></td>
                @can('view_product_stock_value')
                    <td><div class="skeleton-block" style="width: 75px; height: 14px;"></div></td>
                    <td><div class="skeleton-block" style="width: 75px; height: 14px;"></div></td>
                    <td><div class="skeleton-block" style="width: 75px; height: 14px;"></div></td>
                @endcan
                <td><div class="skeleton-block" style="width: 60px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 60px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 60px; height: 14px;"></div></td>
                @if($show_manufacturing_data)
                    <td><div class="skeleton-block" style="width: 65px; height: 14px;"></div></td>
                @endif
            </tr>
        @endfor
    </tbody>
    <tfoot>
        <tr class="bg-gray font-17 text-center footer-total">
            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
            <td class="footer_total_stock"></td>
            @can('view_product_stock_value')
            <td class="footer_total_stock_price"></td>
            <td class="footer_stock_value_by_sale_price"></td>
            <td class="footer_potential_profit"></td>
            @endcan
            <td class="footer_total_sold"></td>
            <td class="footer_total_transfered"></td>
            <td class="footer_total_adjusted"></td>
            @if($show_manufacturing_data)
                <td class="footer_total_mfg_stock"></td>
            @endif
        </tr>
    </tfoot>
</table>