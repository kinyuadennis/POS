{{-- Business Name --}}
@if(!empty($print['business_name']))
    <div class="label-business-name" style="font-size: {{ $print['business_name_size'] }}px;">
        {{ $business_name }}
    </div>
@endif

{{-- Product Name --}}
@if(!empty($print['name']))
    <div class="label-product-name" style="font-size: {{ $print['name_size'] }}px;">
        {{ $page_product->product_actual_name }}
        @if(!empty($print['lot_number']) && !empty($page_product->lot_number))
            <span style="font-size: {{ $print['lot_number_size'] ?? 10 }}px; font-weight: normal;">
                ({{ $page_product->lot_number }})
            </span>
        @endif
    </div>
@endif

{{-- Variation --}}
@if(!empty($print['variations']) && $page_product->is_dummy != 1)
    <div class="label-variation" style="font-size: {{ $print['variations_size'] }}px;">
        {{ $page_product->product_variation_name }}: <b>{{ $page_product->variation_name }}</b>
    </div>
@endif

{{-- Product Custom Fields --}}
@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    $product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
@endphp
@foreach($product_custom_fields as $index => $cf)
    @php
        $field_name = 'product_custom_field' . $loop->iteration;
    @endphp
    @if(!empty($cf) && !empty($page_product->$field_name) && !empty($print[$field_name]))
        <div class="label-custom-field" style="font-size: {{ $print[$field_name . '_size'] ?? 10 }}px;">
            <b>{{ $cf }}:</b> {{ $page_product->$field_name }}
        </div>
    @endif
@endforeach

{{-- Price --}}
@if(!empty($print['price']))
    <div class="label-price" style="font-size: {{ $print['price_size'] }}px;">
        @lang('lang_v1.price'): <b>{{ session('currency')['symbol'] ?? '' }}
        @if(($print['price_type'] ?? '') == 'inclusive')
            {{ @num_format($page_product->sell_price_inc_tax) }}
        @else
            {{ @num_format($page_product->default_sell_price) }}
        @endif</b>
    </div>
@endif

{{-- Exp Date --}}
@if(!empty($print['exp_date']) && !empty($page_product->exp_date))
    <div class="label-date" style="font-size: {{ $print['exp_date_size'] ?? 10 }}px;">
        <b>@lang('product.exp_date'):</b> {{ $page_product->exp_date }}
    </div>
@endif

{{-- Packing Date --}}
@if(!empty($print['packing_date']) && !empty($page_product->packing_date))
    <div class="label-date" style="font-size: {{ $print['packing_date_size'] ?? 10 }}px;">
        <b>@lang('lang_v1.packing_date'):</b> {{ $page_product->packing_date }}
    </div>
@endif

{{-- Barcode --}}
@php
    $barcode_type = !empty($page_product->barcode_type) ? $page_product->barcode_type : 'C128';
    $is_continuous_label = !empty($is_continuous) || !empty($barcode_details->is_continuous);
    $barcode_img_height = $is_continuous_label ? 56 : max(18, min(36, round($barcode_details->height * 28)));
    $barcode_bar_width = $is_continuous_label ? 2.4 : 1.5;
@endphp
<img class="label-barcode-img" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($page_product->sub_sku, $barcode_type, $barcode_bar_width, $barcode_img_height, [0, 0, 0], false) }}" alt="{{ $page_product->sub_sku }}">

{{-- Sub SKU --}}
<div class="label-sku" style="font-size: {{ $is_continuous_label ? 14 : max(8, min(11, round($barcode_details->height * 9))) }}px;">
    {{ $page_product->sub_sku }}
</div>
