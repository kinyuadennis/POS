<div class="modal fade" id="edit_product_location_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px; margin: 30px auto;">
	    <div class="modal-content" style="border-radius: 16px; border: 1px solid #cbd5e1; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; background-color: #ffffff;">
	    	{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'updateProductLocation']), 'method' => 'post', 'id' => 'edit_product_location_form' ]) !!}
		    	<div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; background-color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
				      <h4 class="modal-title" style="font-weight: 700; color: #0f172a; font-size: 17px; margin: 0; display: flex; align-items: center; gap: 10px;">
                          <div style="width: 36px; height: 36px; border-radius: 10px; background-color: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                              <i class="fa fa-map-marker-alt"></i>
                          </div>
                          <span class="add_to_location_title hide">@lang( 'lang_v1.add_location_to_the_selected_products' )</span>
                          <span class="remove_from_location_title hide">@lang( 'lang_v1.remove_location_from_the_selected_products' )</span>
                      </h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 0.5; font-size: 24px; font-weight: 400; outline: none; transition: all 0.15s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'"><span aria-hidden="true">&times;</span></button>
			    </div>
			    <div class="modal-body" style="padding: 24px; background-color: #fafbfd;">
			    	<div class="form-group" style="margin-bottom: 0;">
		                {!! Form::label('product_location',  __('purchase.business_location') . ':', ['style' => 'font-weight: 600; color: #334155; font-size: 14px; margin-bottom: 8px; display: block;']) !!}
                        <p style="color: #64748b; font-size: 13px; margin-bottom: 14px;">Select the business location(s) to apply for the selected products.</p>
		                {!! Form::select('product_location[]', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%; border-radius: 8px;', 'required', 'multiple', 'id' => 'product_location']); !!}
		                {!! Form::hidden('products', null, ['id' => 'products_to_update_location']) !!}
		                {!! Form::hidden('update_type', null, ['id' => 'update_type']) !!}
		            </div>
			    </div>
			    <div class="modal-footer" style="padding: 16px 24px; background-color: #ffffff; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
		      		<button type="button" class="tw-dw-btn tw-dw-btn-neutral" data-dismiss="modal" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; padding: 9px 20px; font-size: 13px;">@lang( 'messages.close' )</button>
		      		<button type="submit" class="tw-dw-btn tw-dw-btn-primary" id="update_product_location" style="background-color: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; padding: 9px 22px; font-size: 13px; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); display: inline-flex; align-items: center; gap: 6px;">
                          <i class="fa fa-check"></i> @lang( 'messages.save' )
                      </button>
		    	</div>
	    	{!! Form::close() !!}
	    </div>
    </div>
</div>