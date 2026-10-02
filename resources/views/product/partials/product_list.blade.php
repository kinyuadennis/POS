@php 
    $colspan = 14;
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<div class="product-table-top-scroll-wrapper" id="product_table_top_scroll">
    <div class="product-table-top-scroll-inner" id="product_table_top_scroll_inner"></div>
</div>
<table class="table ajax_view hide-footer" id="product_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all-row" data-table-id="product_table"></th>
            <th>@lang('messages.action')</th>
            <th>@lang('sale.product')</th>
            <th>@lang('product.wholesale_price')</th>
            <th>@lang('product.retail_price')</th>
            <th>@lang('product.purchase_price')</th>
            <th>@lang('lang_v1.opening_stock')</th>
            <th>@lang('report.current_stock')</th>
            <th>@lang('product.product_type')</th>
            <th>@lang('product.category')</th>
            <th>@lang('product.brand')</th>
            <th>@lang('purchase.business_location') @show_tooltip(__('lang_v1.product_business_location_tooltip'))</th>
            <th>@lang('product.sku')</th>
            <th class="tw-w-full">{{__('lang_v1.product_image')}}</th>
            <th id="cf_1">{{ $custom_labels['product']['custom_field_1'] ?? '' }}</th>
            <th id="cf_2">{{ $custom_labels['product']['custom_field_2'] ?? '' }}</th>
            <th id="cf_3">{{ $custom_labels['product']['custom_field_3'] ?? '' }}</th>
            <th id="cf_4">{{ $custom_labels['product']['custom_field_4'] ?? '' }}</th>
            <th id="cf_5">{{ $custom_labels['product']['custom_field_5'] ?? '' }}</th>
            <th id="cf_6">{{ $custom_labels['product']['custom_field_6'] ?? '' }}</th>
            <th id="cf_7">{{ $custom_labels['product']['custom_field_7'] ?? '' }}</th>
        </tr>
    </thead>
    <tbody id="product_table_body">
        @for ($i = 0; $i < 5; $i++)
            <tr class="skeleton-row">
                <td><div class="skeleton-block" style="width: 16px; height: 16px; border-radius: 4px;"></div></td>
                <td><div class="skeleton-block" style="width: 65px; height: 26px; border-radius: 8px;"></div></td>
                <td>
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <div class="skeleton-block" style="width: 110px; height: 14px;"></div>
                        <div class="skeleton-block" style="width: 50px; height: 10px;"></div>
                    </div>
                </td>
                <td><div class="skeleton-block" style="width: 70px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 70px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 70px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 65px; height: 22px; border-radius: 20px;"></div></td>
                <td><div class="skeleton-block" style="width: 65px; height: 22px; border-radius: 20px;"></div></td>
                <td><div class="skeleton-block" style="width: 55px; height: 22px; border-radius: 20px;"></div></td>
                <td><div class="skeleton-block" style="width: 80px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 75px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 90px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 85px; height: 14px;"></div></td>
                <td><div class="skeleton-block" style="width: 42px; height: 42px; border-radius: 8px;"></div></td>
                @for ($c = 1; $c <= 7; $c++)
                    @if(!empty($custom_labels['product']['custom_field_' . $c]))
                        <td><div class="skeleton-block" style="width: 70px; height: 14px;"></div></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </tbody>
</table>

<!-- Hidden Forms for Bulk Actions (outside table to prevent HTML table parser form stripping) -->
<div style="display: none;">
    @can('product.delete')
        {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massDestroy']), 'method' => 'post', 'id' => 'mass_delete_form' ]) !!}
            {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
            {!! Form::submit(__('lang_v1.delete_selected'), array('id' => 'delete-selected')) !!}
        {!! Form::close() !!}
    @endcan

    @can('product.update')
        @if(config('constants.enable_product_bulk_edit'))
            {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'bulkEdit']), 'method' => 'post', 'id' => 'bulk_edit_form' ]) !!}
                {!! Form::hidden('selected_products', null, ['id' => 'selected_products_for_edit']); !!}
                <button type="submit" id="edit-selected">{{__('lang_v1.bulk_edit')}}</button>
            {!! Form::close() !!}
        @endif
        <button type="button" class="update_product_location" data-type="add">@lang('lang_v1.add_to_location')</button>
        <button type="button" class="update_product_location" data-type="remove">@lang('lang_v1.remove_from_location')</button>
    @endcan

    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massDeactivate']), 'method' => 'post', 'id' => 'mass_deactivate_form' ]) !!}
        {!! Form::hidden('selected_products', null, ['id' => 'selected_products']); !!}
        {!! Form::submit(__('lang_v1.deactivate_selected'), array('id' => 'deactivate-selected')) !!}
    {!! Form::close() !!}

    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massUpdateNotForSelling']), 'method' => 'post', 'id' => 'mass_not_for_selling_form' ]) !!}
        {!! Form::hidden('selected_products', null, ['id' => 'selected_products_nfs']); !!}
        {!! Form::hidden('not_for_selling', 1, ['id' => 'not_for_selling_flag']); !!}
        {!! Form::submit(__('lang_v1.mark_not_for_selling_selected'), array('id' => 'not-for-selling-selected')) !!}
    {!! Form::close() !!}

    @if($is_woocommerce)
        <button type="button" class="toggle_woocomerce_sync">@lang('lang_v1.woocommerce_sync')</button>
    @endif
</div>
