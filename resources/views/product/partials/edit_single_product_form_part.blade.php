@php
    $common_settings = session()->get('business.common_settings');
    $variation = $product_deatails->variations->first();
    $wholesale_price_val = !empty($wholesale_price) ? $wholesale_price : 0;
@endphp

@include('product.partials.simplified_pricing', [
    'purchase_price' => $variation->default_purchase_price ?? 0,
    'selling_price' => $variation->sell_price_inc_tax ?? 0,
    'wholesale_price' => $wholesale_price_val,
    'variation_id' => $variation->id ?? null,
])

<div class="col-sm-4" style="margin-top: 15px;">
    @php 
        $is_image_required = !empty($common_settings['is_product_image_required']) && count($variation->media) == 0;
        $action = !empty($action) ? $action : '';
    @endphp
    @if($action !== 'duplicate')
        @foreach($variation->media as $media)
            <div class="img-thumbnail">
                <span class="badge bg-red delete-media" data-href="{{ action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], ['media_id' => $media->id])}}"><i class="fas fa-times"></i></span>
                {!! $media->thumbnail() !!}
            </div>
        @endforeach
    @endif
    <div class="form-group">
        {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
        {!! Form::file('variation_images[]', ['class' => 'variation_images', 
            'accept' => 'image/*', 'multiple', 'required' => $is_image_required]); !!}
        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
    </div>
</div>
