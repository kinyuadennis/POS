@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

@include('product.partials.simplified_pricing', [
    'purchase_price' => $default,
    'selling_price' => $default,
    'wholesale_price' => $default,
])

@if(empty($quick_add))
  <div class="col-sm-4" style="margin-top: 15px;">
      <div class="form-group">
        {!! Form::label('variation_images', __('lang_v1.product_image') . ':') !!}
        {!! Form::file('variation_images[]', ['class' => 'variation_images', 
            'accept' => 'image/*', 'multiple']); !!}
        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
      </div>
  </div>
@endif
