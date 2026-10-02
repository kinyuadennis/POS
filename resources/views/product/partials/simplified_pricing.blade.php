@php
    $purchase_price_val = $purchase_price ?? null;
    $selling_price_val = $selling_price ?? null;
    $wholesale_price_val = $wholesale_price ?? null;
@endphp

<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('purchase_price', __('product.purchase_price') . ':*') !!}
            {!! Form::text('purchase_price', !is_null($purchase_price_val) ? @num_format($purchase_price_val) : null, [
                'class' => 'form-control input_number',
                'id' => 'purchase_price',
                'placeholder' => __('product.purchase_price'),
                'required',
            ]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('selling_price', __('product.retail_price') . ':*') !!}
            {!! Form::text('selling_price', !is_null($selling_price_val) ? @num_format($selling_price_val) : null, [
                'class' => 'form-control input_number',
                'id' => 'selling_price',
                'placeholder' => __('product.retail_price'),
                'required',
            ]); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('wholesale_price', __('product.wholesale_price') . ':*') !!}
            {!! Form::text('wholesale_price', !is_null($wholesale_price_val) ? @num_format($wholesale_price_val) : null, [
                'class' => 'form-control input_number',
                'id' => 'wholesale_price',
                'placeholder' => __('product.wholesale_price'),
                'required',
            ]); !!}
        </div>
    </div>
</div>

@if(!empty($variation_id))
    {!! Form::hidden('single_variation_id', $variation_id) !!}
@endif

{{-- Hidden fields for backward compatibility with variation storage --}}
{!! Form::hidden('single_dpp', null, ['id' => 'single_dpp']) !!}
{!! Form::hidden('single_dpp_inc_tax', null, ['id' => 'single_dpp_inc_tax']) !!}
{!! Form::hidden('single_dsp', null, ['id' => 'single_dsp']) !!}
{!! Form::hidden('single_dsp_inc_tax', null, ['id' => 'single_dsp_inc_tax']) !!}
{!! Form::hidden('profit_percent', 0, ['id' => 'profit_percent']) !!}
