@extends('layouts.app')
@section('title', __('barcode.print_labels'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('barcode.print_labels') @show_tooltip(__('tooltip.print_label'))</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'preview_setting_form', 'onsubmit' => 'return false']) !!}
	@component('components.widget', ['class' => 'box-primary', 'title' => __('product.add_product_for_labels')])
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_label', 'placeholder' => __('lang_v1.enter_product_name_to_print_labels'), 'autofocus']); !!}
					</div>
				</div>
			</div>
		</div>

		{{-- Bulk Add Toolbar --}}
		<div class="row" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 10px; margin-bottom: 20px;">
			<div class="col-sm-12">
				<h4 style="margin-top: 0; margin-bottom: 12px; font-weight: 600; color: #1e293b;">
					<i class="fa fa-bolt text-warning"></i> Batch / Bulk Add Products
				</h4>
			</div>
			<div class="col-sm-3">
				<div class="form-group" style="margin-bottom: 10px;">
					{!! Form::label('bulk_category_id', __('category.category') . ':') !!}
					{!! Form::select('bulk_category_id', $categories ?? ['all' => __('lang_v1.all_category')], 'all', ['class' => 'form-control select2', 'id' => 'bulk_category_id', 'style' => 'width: 100%;']); !!}
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group" style="margin-bottom: 10px;">
					{!! Form::label('bulk_brand_id', __('brand.brands') . ':') !!}
					{!! Form::select('bulk_brand_id', $brands ?? ['all' => __('lang_v1.all_brands')], 'all', ['class' => 'form-control select2', 'id' => 'bulk_brand_id', 'style' => 'width: 100%;']); !!}
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group" style="margin-bottom: 10px;">
					{!! Form::label('bulk_qty_type', __('barcode.no_of_labels') . ' Mode:') !!}
					{!! Form::select('bulk_qty_type', ['1' => '1 Label per product', 'stock' => 'Current stock quantity'], '1', ['class' => 'form-control', 'id' => 'bulk_qty_type']); !!}
				</div>
			</div>
			<div class="col-sm-3" id="bulk_location_div" style="display: none;">
				<div class="form-group" style="margin-bottom: 10px;">
					{!! Form::label('bulk_location_id', __('business.business_locations') . ':') !!}
					{!! Form::select('bulk_location_id', $business_locations ?? [], null, ['class' => 'form-control', 'id' => 'bulk_location_id', 'placeholder' => __('lang_v1.select_location')]); !!}
				</div>
			</div>
			<div class="col-sm-12" style="margin-top: 10px;">
				<button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-primary" id="btn_bulk_add_products">
					<i class="fa fa-plus-circle"></i> Add Filtered Products
				</button>
				<button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-success" id="btn_add_all_catalog">
					<i class="fa fa-layer-group"></i> Add All Products
				</button>
				<button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-outline tw-dw-btn-error pull-right" id="btn_clear_label_table">
					<i class="fa fa-trash"></i> Clear List
				</button>
				<span id="bulk_add_spinner" style="display: none; margin-left: 10px;" class="text-primary">
					<i class="fa fa-spinner fa-spin"></i> Loading products...
				</span>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
					<span style="font-weight: 600; color: #475569;">
						Products in Print Queue: <span class="badge bg-primary" id="label_item_count">0</span>
					</span>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed" id="product_table">
						<thead>
							<tr>
								<th>@lang( 'barcode.products' )</th>
								<th style="width: 140px;">@lang( 'barcode.no_of_labels' )</th>
								@if(request()->session()->get('business.enable_lot_number') == 1)
									<th>@lang( 'lang_v1.lot_number' )</th>
								@endif
								@if(request()->session()->get('business.enable_product_expiry') == 1)
									<th>@lang( 'product.exp_date' )</th>
								@endif
								<th>@lang('lang_v1.packing_date')</th>
								<th>@lang('lang_v1.selling_price_group')</th>
								<th style="width: 50px; text-align: center;"><i class="fa fa-trash"></i></th>
							</tr>
						</thead>
						<tbody>
							@include('labels.partials.show_table_rows', ['index' => 0])
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-primary', 'title' => __( 'barcode.info_in_labels' )])
		<div class="row">
			<div class="col-md-12">
				<table class="table table-bordered">
					<tr>
						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[name]" value="1"> <b>@lang( 'barcode.print_name' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[name_size]" 
									value="12">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[variations]" value="1"> <b>@lang( 'barcode.print_variations' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[variations_size]" 
									value="11">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[price]" value="1" id="is_show_price"> <b>@lang( 'barcode.print_price' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[price_size]" 
									value="12">
							</div>

						</td>

						<td>
							
							<div class="" id="price_type_div">
								<div class="form-group">
									{!! Form::label('print[price_type]', @trans( 'barcode.show_price' ) . ':') !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-info"></i>
										</span>
										{!! Form::select('print[price_type]', ['inclusive' => __('product.inc_of_tax'), 'exclusive' => __('product.exc_of_tax')], 'inclusive', ['class' => 'form-control']); !!}
									</div>
								</div>
							</div>

						</td>
					</tr>

					<tr>
						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[business_name]" value="1"> <b>@lang( 'barcode.print_business_name' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[business_name_size]" 
									value="13">
							</div>
						</td>

						<td>
							<div class="checkbox">
							    <label>
							    	<input type="checkbox" checked name="print[packing_date]" value="1"> <b>@lang( 'lang_v1.print_packing_date' )</b>
							    </label>
							</div>

							<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
								<input type="text" class="form-control" 
									name="print[packing_date_size]" 
									value="10">
							</div>
						</td>

						<td>
							@if(request()->session()->get('business.enable_lot_number') == 1)
							
								<div class="checkbox">
								    <label>
								    	<input type="checkbox" checked name="print[lot_number]" value="1"> <b>@lang( 'lang_v1.print_lot_number' )</b>
								    </label>
								</div>

								<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
									<input type="text" class="form-control" 
										name="print[lot_number_size]" 
										value="10">
								</div>
							@endif
						</td>

						<td>
							@if(request()->session()->get('business.enable_product_expiry') == 1)
								<div class="checkbox">
								    <label>
								    	<input type="checkbox" checked name="print[exp_date]" value="1"> <b>@lang( 'lang_v1.print_exp_date' )</b>
								    </label>
								</div>

								<div class="input-group">
      							<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
									<input type="text" class="form-control" 
										name="print[exp_date_size]" 
										value="10">
								</div>
							@endif
						</td>						
					</tr>
					<tr>
						
						@php
							$c = 0;
							$custom_labels = json_decode(session('business.custom_labels'), true);
        					$product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
							 $product_cf_details = !empty($custom_labels['product_cf_details']) ? $custom_labels['product_cf_details'] : [];
						@endphp
						@foreach($product_custom_fields as $index => $cf)
							@if(!empty($cf))
								@php
									$field_name = 'product_custom_field' . $loop->iteration;
									$cf_type = !empty($product_cf_details[$loop->iteration]['type']) ? $product_cf_details[$loop->iteration]['type'] : 'text';
									$dropdown = !empty($product_cf_details[$loop->iteration]['dropdown_options']) ? explode(PHP_EOL, $product_cf_details[$loop->iteration]['dropdown_options']) : [];
									$c++;
								@endphp
								<td>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="print[{{ $field_name }}]" value="1"> <b>{{ $cf }}</b>
										</label>
									</div>

									<div class="input-group">
									<div class="input-group-addon"><b>@lang( 'lang_v1.size' )</b></div>
										<input type="text" class="form-control" 
											name="print[{{ $field_name }}_size]" 
											value="10">
									</div>
								</td>
								@if ($c % 4 == 0)
									</tr>
								@endif
							@endif
						@endforeach
					</tr>
				</table>
			</div>

			

			

			<div class="col-sm-12">
				<hr/>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('price_type', @trans( 'barcode.barcode_setting' ) . ':') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-cog"></i>
						</span>
						{!! Form::select('barcode_setting', $barcode_settings, !empty($default) ? $default->id : null, ['class' => 'form-control']); !!}
					</div>
				</div>
			</div>

			<div class="clearfix"></div>
			
			<div class="col-sm-12 text-center">
				<button type="button" id="labels_preview" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang( 'barcode.preview' )</button>
			</div>
		</div>
	@endcomponent
	{!! Form::close() !!}

	<div class="col-sm-8 hide display_label_div">
		<h3 class="box-title">@lang( 'barcode.preview' )</h3>
		<button type="button" class="col-sm-offset-2 btn btn-success btn-block" id="print_label">Print</button>
	</div>
	<div class="clearfix"></div>
</section>

<!-- Preview section-->
<div id="preview_box">
</div>

@stop
@section('javascript')
	<script src="{{ asset('js/labels.js?v=' . $asset_v) }}"></script>
@endsection
