<br>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					@can('view_purchase_price')
						<th>@lang('product.purchase_price')</th>
					@endcan
					@can('access_default_selling_price')
				        <th>@lang('product.retail_price')</th>
				        <th>@lang('product.wholesale_price')</th>
				    @endcan
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					@can('view_purchase_price')
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->default_purchase_price }}</span>
					</td>
					@endcan
					@can('access_default_selling_price')
						<td>
							<span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
						</td>
						<td>
							<span class="display_currency" data-currency_symbol="true">{{ $wholesale_prices[$variation->id] ?? 0 }}</span>
						</td>
					@endcan
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>
