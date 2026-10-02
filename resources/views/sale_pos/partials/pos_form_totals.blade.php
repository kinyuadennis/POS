<div class="row pos_form_totals @if(empty($edit)) hide tw-hidden @endif" id="pos_cart_summary" @if(empty($edit)) style="display: none;" @endif>
	<div class="col-md-12">
		<div class="pos-summary-grid">
			<div class="pos-summary-cell pos-summary-items">
				<span class="pos-summary-label">@lang('sale.item')</span>
				<span class="total_quantity pos-summary-value">0</span>
			</div>

			<div class="pos-summary-cell pos-summary-subtotal">
				<span class="pos-summary-label">@lang('sale.subtotal')</span>
				<span class="price_total pos-summary-value">0</span>
			</div>

			<div class="pos-summary-cell pos-summary-discount-cell @if(!Gate::check('disable_discount') || auth()->user()->can('superadmin') || auth()->user()->can('admin')) @else hide @endif">
				<span class="pos-summary-label">
					@if($is_discount_enabled)
						<span
							@if($edit_discount)
								id="pos-edit-discount"
								class="pos-summary-clickable"
								data-toggle="modal"
								data-target="#posEditDiscountModal"
								role="button"
								title="@lang('sale.edit_discount')"
							@endif
						>@lang('sale.discount') (-)</span>
						@show_tooltip(__('tooltip.sale_discount'))
					@endif
					@if($is_rp_enabled)
						{{ session('business.rp_name') }}
					@endif
				</span>
				@if($is_discount_enabled)
					<span
						class="pos-summary-value pos-summary-discount @if($edit_discount) pos-summary-clickable @endif"
						id="total_discount"
						@if($edit_discount)
							data-toggle="modal"
							data-target="#posEditDiscountModal"
							role="button"
							title="@lang('sale.edit_discount')"
						@endif
					>0</span>
				@endif
				<input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'fixed'}}@else{{$transaction->discount_type}}@endif" data-default="fixed">
				<input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">
				<input type="hidden" name="discount_approval_token" id="discount_approval_token" value="">
				<input type="hidden" name="discount_approval_id" id="discount_approval_id" value="">
				<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="@if(empty($edit)){{'0'}}@else{{$transaction->rp_redeemed}}@endif">
				<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="@if(empty($edit)){{'0'}}@else {{$transaction->rp_redeemed_amount}} @endif">
			</div>

			<div class="hide">
				<span id="order_tax">
					@if(empty($edit))
						0
					@else
						{{$transaction->tax_amount}}
					@endif
				</span>
				<input type="hidden" name="tax_rate_id" id="tax_rate_id" value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" data-default="{{$business_details->default_sales_tax}}">
				<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format($transaction->tax?->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
			</div>

			<div class="pos-summary-cell pos-summary-total-payable">
				<input type="hidden" name="final_total" id="final_total_input" value="0.00">
				<span class="pos-summary-label pos-summary-payable-label">@lang('sale.total_payable')</span>
				<span id="total_payable" class="number pos-summary-payable-value">0.00</span>
			</div>

			<div class="pos-summary-cell pos-summary-shipping-cell">
				<span class="pos-summary-label">
					<span
						class="pos-summary-clickable"
						data-toggle="modal"
						data-target="#posShippingModal"
						role="button"
						title="Parcel"
					>Parcel(+)</span>
					@show_tooltip(__('tooltip.shipping'))
				</span>
				<span
					class="pos-summary-value pos-summary-clickable"
					id="shipping_charges_amount"
					data-toggle="modal"
					data-target="#posShippingModal"
					role="button"
					title="Parcel"
				>0</span>
				@if(in_array('types_of_service', $enabled_modules))
					<div class="pos-summary-extra-line">
						<span class="pos-summary-label">@lang('lang_v1.packing_charge')(+):</span>
						<i class="fas fa-edit cursor-pointer service_modal_btn"></i>
						<span class="pos-summary-value" id="packing_charge_text">0</span>
					</div>
				@endif
				@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
					<div class="pos-summary-extra-line">
						<span class="pos-summary-label" id="round_off">@lang('lang_v1.round_off'):</span>
						<span class="pos-summary-value" id="round_off_text">0</span>
						<input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
					</div>
				@endif
				<input type="hidden" name="shipping_details" id="shipping_details" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_details}}@endif" data-default="">
				<input type="hidden" name="shipping_address" id="shipping_address" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_address}}@endif">
				<input type="hidden" name="shipping_status" id="shipping_status" value="@if(empty($edit)){{''}}@else{{$transaction->shipping_status}}@endif">
				<input type="hidden" name="delivered_to" id="delivered_to" value="@if(empty($edit)){{''}}@else{{$transaction->delivered_to}}@endif">
				<input type="hidden" name="delivery_person" id="delivery_person" value="@if(empty($edit)){{''}}@else{{$transaction->delivery_person}}@endif">
				<input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">
			</div>
		</div>
	</div>
</div>
