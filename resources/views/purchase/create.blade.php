@extends('layouts.app')
@section('title', __('purchase.add_purchase'))

@section('content')

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$theme_color = session('business.theme_color', 'blue');
	$color_map = [
		'blue' => '#3c8dbc',
		'purple' => '#605ca8',
		'green' => '#00a65a',
		'red' => '#dd4b39',
		'yellow' => '#f39c12',
		'orange' => '#ff851b',
		'sky' => '#00c0ef',
		'primary' => '#3c8dbc'
	];
	$primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';
@endphp

<style>
	.content-header {
		background-color: #ffffff !important;
		padding: 16px 24px !important;
		border-bottom: 1px solid #cbd5e160 !important;
		box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
	}

	.purchase-create-content {
		background-color: {{ $primary_hex }}0d !important;
		padding: 24px !important;
		min-height: calc(100vh - 100px);
		overflow: visible;
	}

	.form-card {
		background-color: #ffffff !important;
		border: 1px solid #e2e8f0 !important;
		border-radius: 12px !important;
		padding: 24px !important;
		margin-bottom: 24px !important;
		box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
	}
	.form-card-title {
		font-size: 18px !important;
		font-weight: 700 !important;
		color: #1e293b !important;
		margin-top: 0 !important;
		margin-bottom: 20px !important;
		padding-bottom: 12px !important;
		border-bottom: 1px solid #f1f5f9 !important;
		display: flex !important;
		align-items: center !important;
		gap: 10px !important;
	}
	.form-card-title i {
		color: {{ $primary_hex }} !important;
		font-size: 18px !important;
	}

	.purchase-create-content label {
		font-size: 13px !important;
		font-weight: 600 !important;
		color: #475569 !important;
		margin-bottom: 6px !important;
	}
	.purchase-create-content .form-control {
		border: 1px solid #cbd5e1 !important;
		border-radius: 8px !important;
		height: 40px !important;
		box-shadow: none !important;
		color: #1e293b !important;
		font-size: 14px !important;
	}
	.purchase-create-content .form-control:focus {
		border-color: {{ $primary_hex }} !important;
		box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
		outline: none !important;
	}
	.purchase-create-content textarea.form-control {
		height: auto !important;
	}
	.purchase-create-content input[type="file"].form-control,
	.purchase-create-content #upload_document {
		height: auto !important;
		padding: 8px !important;
	}

	.purchase-create-content .input-group-addon {
		border: 1px solid #cbd5e1 !important;
		border-right: 0 !important;
		border-radius: 8px 0 0 8px !important;
		background-color: #f8fafc !important;
		color: #64748b !important;
	}
	.purchase-create-content .input-group .form-control {
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
	}

	.purchase-create-content .select2-container--default .select2-selection--single {
		border: 1px solid #cbd5e1 !important;
		border-radius: 8px !important;
		height: 40px !important;
	}
	.purchase-create-content .select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 38px !important;
		color: #1e293b !important;
	}
	.purchase-create-content .select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 38px !important;
	}

	.product-search-wrapper {
		position: relative;
		max-width: 720px;
		margin: 0 auto 16px auto;
	}
	.product-search-wrapper .input-group-addon {
		background-color: #ffffff !important;
		border-color: #cbd5e1 !important;
		color: #94a3b8 !important;
	}

	.purchase-table-wrap {
		border: 1px solid #e2e8f0;
		border-radius: 8px;
		overflow: auto;
		background-color: #ffffff;
	}
	#purchase_entry_table thead th {
		background-color: #f8fafc !important;
		color: #475569 !important;
		font-weight: 700 !important;
		font-size: 12px !important;
		border-bottom: 2px solid #e2e8f0 !important;
		vertical-align: middle !important;
	}
	#add_purchase_form .purchase-col-discount,
	#add_purchase_form .purchase-col-cost-before-tax,
	#add_purchase_form .purchase-col-profit {
		display: none !important;
	}

	.btn-purchase-primary {
		background-color: {{ $primary_hex }} !important;
		border: 1px solid {{ $primary_hex }} !important;
		color: #ffffff !important;
		border-radius: 8px !important;
		font-weight: 600 !important;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
		transition: all 0.2s ease-in-out !important;
	}
	.btn-purchase-primary:hover,
	.btn-purchase-primary:focus {
		opacity: 0.9 !important;
		transform: translateY(-1px) !important;
		color: #ffffff !important;
	}
	#submit_purchase_form {
		background-color: {{ $primary_hex }} !important;
		border: 1px solid {{ $primary_hex }} !important;
		color: #ffffff !important;
		border-radius: 8px !important;
		font-weight: 600 !important;
		font-size: 15px !important;
		height: 42px !important;
		padding: 0 32px !important;
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
	}
	#submit_purchase_form:hover {
		opacity: 0.9 !important;
		transform: translateY(-1px) !important;
		color: #ffffff !important;
	}

	@media (max-width: 767px) {
		.content-header {
			padding: 12px 16px !important;
		}
		.purchase-create-content {
			padding: 14px 12px !important;
		}
		.form-card {
			padding: 16px 14px !important;
			margin-bottom: 16px !important;
		}
	}
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center">
	<div>
		<h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">
			@lang('purchase.add_purchase')
			<i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
		</h1>
		<p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Record a new supplier purchase</p>
	</div>
</section>

<section class="content purchase-create-content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action([\App\Http\Controllers\PurchaseController::class, 'store']), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	<div class="form-card">
		<h3 class="form-card-title">
			<i class="fa fa-file-text-o"></i>
			@lang('purchase.purchases')
		</h3>
		<div class="row">
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
				<strong>
					@lang('business.address'):
				</strong>
				<div id="supplier_address_div"></div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
					@show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
					{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
				</div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-3 @endif">
				<div class="form-group">
					{!! Form::label('transaction_date', __('purchase.purchase_date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-3 @if(!empty($default_purchase_status)) hide @endif">
				<div class="form-group">
					{!! Form::label('status', __('purchase.purchase_status') . ':*') !!} @show_tooltip(__('tooltip.order_status'))
					{!! Form::select('status', $orderStatuses, $default_purchase_status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
				</div>
			</div>			
			@php
				$default_location = $default_location_id ?? null;
				if (empty($default_location) && count($business_locations) == 1) {
					$default_location = current(array_keys($business_locations->toArray()));
				}
				$search_disable = empty($default_location);
			@endphp
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('location_id', __('purchase.business_location').':*') !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
				</div>
			</div>

			<!-- Currency Exchange Rate -->
			<div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
					{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
					@show_tooltip(__('tooltip.currency_exchange_factor'))
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
					</div>
					<span class="help-block text-danger">
						@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
					</span>
				</div>
			</div>

		</div>
		<div class="row">
			@php
		    $custom_field_1_label = !empty($custom_labels['purchase']['custom_field_1']) ? $custom_labels['purchase']['custom_field_1'] : '';

		    $is_custom_field_1_required = !empty($custom_labels['purchase']['is_custom_field_1_required']) && $custom_labels['purchase']['is_custom_field_1_required'] == 1 ? true : false;

		    $custom_field_2_label = !empty($custom_labels['purchase']['custom_field_2']) ? $custom_labels['purchase']['custom_field_2'] : '';

		    $is_custom_field_2_required = !empty($custom_labels['purchase']['is_custom_field_2_required']) && $custom_labels['purchase']['is_custom_field_2_required'] == 1 ? true : false;

		    $custom_field_3_label = !empty($custom_labels['purchase']['custom_field_3']) ? $custom_labels['purchase']['custom_field_3'] : '';

		    $is_custom_field_3_required = !empty($custom_labels['purchase']['is_custom_field_3_required']) && $custom_labels['purchase']['is_custom_field_3_required'] == 1 ? true : false;

		    $custom_field_4_label = !empty($custom_labels['purchase']['custom_field_4']) ? $custom_labels['purchase']['custom_field_4'] : '';

		    $is_custom_field_4_required = !empty($custom_labels['purchase']['is_custom_field_4_required']) && $custom_labels['purchase']['is_custom_field_4_required'] == 1 ? true : false;
		@endphp
		@if(!empty($custom_field_1_label))
			@php
				$label_1 = $custom_field_1_label . ':';
				if($is_custom_field_1_required) {
					$label_1 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_1', $label_1 ) !!}
		            {!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_2_label))
			@php
				$label_2 = $custom_field_2_label . ':';
				if($is_custom_field_2_required) {
					$label_2 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_2', $label_2 ) !!}
		            {!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_3_label))
			@php
				$label_3 = $custom_field_3_label . ':';
				if($is_custom_field_3_required) {
					$label_3 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_3', $label_3 ) !!}
		            {!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
		        </div>
		    </div>
		@endif
		@if(!empty($custom_field_4_label))
			@php
				$label_4 = $custom_field_4_label . ':';
				if($is_custom_field_4_required) {
					$label_4 .= '*';
				}
			@endphp

			<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('custom_field_4', $label_4 ) !!}
		            {!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
		        </div>
		    </div>
		@endif
		</div>
		@if(!empty($common_settings['enable_purchase_order']))
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('purchase_order_ids', __('lang_v1.purchase_order').':') !!}
					{!! Form::select('purchase_order_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'purchase_order_ids']); !!}
				</div>
			</div>
		</div>
		@endif
	</div>

	<div class="form-card">
		<h3 class="form-card-title">
			<i class="fa fa-cubes"></i>
			@lang('sale.products')
		</h3>
		<div class="row">
			<div class="col-sm-2 text-center">
				<button type="button" class="btn btn-purchase-primary btn-sm" data-toggle="modal" data-target="#import_purchase_products_modal">@lang('product.import_products')</button>
			</div>
			<div class="col-sm-8">
				<div class="form-group product-search-wrapper" style="max-width: none; margin: 0;">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" 
            	data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
				</div>
			</div>
		</div>
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
		<div class="row">
			<div class="col-sm-12">
				<div class="table-responsive purchase-table-wrap">
					<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
						<thead>
							<tr>
								<th>#</th>
								<th>@lang( 'product.product_name' )</th>
								<th>@lang( 'purchase.purchase_quantity' )</th>
								<th>@lang( 'purchase.unit_cost' )</th>
								<th class="purchase-col-discount">@lang( 'lang_v1.discount_percent' )</th>
								<th class="purchase-col-cost-before-tax">@lang( 'purchase.unit_cost_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
								<th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
								<th>@lang( 'purchase.line_total' )</th>
								<th class="purchase-col-profit @if(!session('business.enable_editing_product_from_purchase')) hide @endif">
									@lang( 'lang_v1.profit_margin' )
								</th>
								<th>
									@lang( 'purchase.unit_selling_price' )
								</th>
								@if(session('business.enable_lot_number'))
									<th>
										@lang('lang_v1.lot_number')
									</th>
								@endif
								@if(session('business.enable_product_expiry'))
									<th>
										@lang('product.mfg_date') / @lang('product.exp_date')
									</th>
								@endif
								<th><i class="fa fa-trash" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<hr/>
				<div class="pull-right col-md-5">
					<table class="pull-right col-md-12">
						<tr>
							<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
							</td>
						</tr>
						<tr class="hide">
							<th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_st_before_tax" class="display_currency"></span>
								<input type="hidden" id="st_before_tax_input" value=0>
							</td>
						</tr>
						<tr>
							<th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_subtotal" class="display_currency"></span>
								<!-- This is total before purchase tax-->
								<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
							</td>
						</tr>
					</table>
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	</div>

	<div class="hide">
		{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control', 'id' => 'discount_type']); !!}
		{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'id' => 'discount_amount']); !!}
		<select name="tax_id" id="tax_id">
			<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
		</select>
		{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
		<span id="discount_calculated_amount" class="display_currency">0</span>
		<span id="tax_calculated_amount" class="display_currency">0</span>
	</div>
	<div class="form-card">
		<h3 class="form-card-title">
			<i class="fa fa-truck"></i>
			@lang('purchase.shipping_details')
		</h3>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
			{!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
			{!! Form::text('shipping_details', null, ['class' => 'form-control']); !!}
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				{!! Form::label('additional_notes',__('purchase.additional_notes')) !!}
				{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				{!! Form::label('shipping_charges','(+) ' . __( 'purchase.additional_shipping_charges' ) . ':') !!}
				{!! Form::text('shipping_charges', 0, ['class' => 'form-control input_number', 'required']); !!}
			</div>
		</div>
	</div>
	<div class="row">
			@php
			    $shipping_custom_label_1 = !empty($custom_labels['purchase_shipping']['custom_field_1']) ? $custom_labels['purchase_shipping']['custom_field_1'] : '';

			    $is_shipping_custom_field_1_required = !empty($custom_labels['purchase_shipping']['is_custom_field_1_required']) && $custom_labels['purchase_shipping']['is_custom_field_1_required'] == 1 ? true : false;

			    $shipping_custom_label_2 = !empty($custom_labels['purchase_shipping']['custom_field_2']) ? $custom_labels['purchase_shipping']['custom_field_2'] : '';

			    $is_shipping_custom_field_2_required = !empty($custom_labels['purchase_shipping']['is_custom_field_2_required']) && $custom_labels['purchase_shipping']['is_custom_field_2_required'] == 1 ? true : false;

			    $shipping_custom_label_3 = !empty($custom_labels['purchase_shipping']['custom_field_3']) ? $custom_labels['purchase_shipping']['custom_field_3'] : '';
			    
			    $is_shipping_custom_field_3_required = !empty($custom_labels['purchase_shipping']['is_custom_field_3_required']) && $custom_labels['purchase_shipping']['is_custom_field_3_required'] == 1 ? true : false;

			    $shipping_custom_label_4 = !empty($custom_labels['purchase_shipping']['custom_field_4']) ? $custom_labels['purchase_shipping']['custom_field_4'] : '';
			    
			    $is_shipping_custom_field_4_required = !empty($custom_labels['purchase_shipping']['is_custom_field_4_required']) && $custom_labels['purchase_shipping']['is_custom_field_4_required'] == 1 ? true : false;

			    $shipping_custom_label_5 = !empty($custom_labels['purchase_shipping']['custom_field_5']) ? $custom_labels['purchase_shipping']['custom_field_5'] : '';
			    
			    $is_shipping_custom_field_5_required = !empty($custom_labels['purchase_shipping']['is_custom_field_5_required']) && $custom_labels['purchase_shipping']['is_custom_field_5_required'] == 1 ? true : false;
			@endphp

			@if(!empty($shipping_custom_label_1))
				@php
					$label_1 = $shipping_custom_label_1 . ':';
					if($is_shipping_custom_field_1_required) {
						$label_1 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
			            {!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_2))
				@php
					$label_2 = $shipping_custom_label_2 . ':';
					if($is_shipping_custom_field_2_required) {
						$label_2 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
			            {!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_3))
				@php
					$label_3 = $shipping_custom_label_3 . ':';
					if($is_shipping_custom_field_3_required) {
						$label_3 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
			            {!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_4))
				@php
					$label_4 = $shipping_custom_label_4 . ':';
					if($is_shipping_custom_field_4_required) {
						$label_4 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
			            {!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
			        </div>
			    </div>
			@endif
			@if(!empty($shipping_custom_label_5))
				@php
					$label_5 = $shipping_custom_label_5 . ':';
					if($is_shipping_custom_field_5_required) {
						$label_5 .= '*';
					}
				@endphp

				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
			            {!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
			        </div>
			    </div>
			@endif
		</div>
		<div class="row">
			<div class="col-md-12 text-center">
				<button type="button" class="btn btn-purchase-primary btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
			</div>
			<div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>@lang('lang_v1.additional_expense_name')</th>
							<th>@lang('sale.amount')</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control', 'id' => 'additional_expense_key_1']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_1', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control', 'id' => 'additional_expense_key_2']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_2', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control', 'id' => 'additional_expense_key_3']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_3', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control', 'id' => 'additional_expense_key_4']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_4', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-right">
				{!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
						<b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
			</div>
		</div>
	</div>
	<div class="form-card">
		<h3 class="form-card-title">
			<i class="fa fa-money"></i>
			@lang('purchase.add_payment')
		</h3>
		<div class="box-body payment_row" style="padding: 0;">
			<div class="row">
				<div class="col-md-12">
					<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text">0</span>
					{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
				</div>
			</div>
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true, 'show_denomination' => true])
			<hr>
			<div class="row">
				<div class="col-sm-12">
					<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span id="payment_due">0.00</span></div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12 text-center">
					<button type="button" id="submit_purchase_form">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div>

{!! Form::close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>

@include('purchase.partials.import_purchase_products_modal')
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

			if($('.payment_types_dropdown').length){
				$('.payment_types_dropdown').change();
			}
			set_payment_type_dropdown();
			$('select#location_id').change(function() {
				set_payment_type_dropdown();
			});
    	});
    	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
		    var default_accounts = $('select#location_id').length ? 
		                $('select#location_id')
		                .find(':selected')
		                .data('default_payment_accounts') : [];
		    var payment_types_dropdown = $('.payment_types_dropdown');
		    var payment_type = payment_types_dropdown.val();
		    var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
		    if (payment_type && payment_type != 'advance') {
		        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
		            default_accounts[payment_type]['account'] : '';
		        if (account_dropdown.length && default_accounts) {
		            account_dropdown.val(default_account);
		            account_dropdown.change();
		        }
		    }

		    if (payment_type == 'advance') {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', true);
		            account_dropdown.closest('.form-group').addClass('hide');
		        }
		    } else {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', false); 
		            account_dropdown.closest('.form-group').removeClass('hide');
		        }    
		    }
		});

		function set_payment_type_dropdown() {
			var payment_settings = $('#location_id').find(':selected').data('default_payment_accounts');
			payment_settings = payment_settings ? payment_settings : [];
			enabled_payment_types = [];
			for (var key in payment_settings) {
				if (payment_settings[key] && payment_settings[key]['is_enabled']) {
					enabled_payment_types.push(key);
				}
			}
			if (enabled_payment_types.length) {
				$(".payment_types_dropdown > option").each(function() {
					//skip if advance
					if ($(this).val() && $(this).val() != 'advance') {
						if (enabled_payment_types.indexOf($(this).val()) != -1) {
							$(this).removeClass('hide');
						} else {
							$(this).addClass('hide');
						}
					}
				});
			}
		}
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
