<div class="modal-dialog" role="document">
  <div class="modal-content">

@php
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
    /* Modal header dynamic theme styling */
    .modal-header {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }
    .modal-header .modal-title,
    .modal-header .close {
        color: #ffffff !important;
        opacity: 1 !important;
    }

    /* Submit button dynamic theme styling */
    #submit_spg_modal_btn {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
    }
    #submit_spg_modal_btn:hover {
        background-color: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
    }
</style>

    {!! Form::open(['url' => action([\App\Http\Controllers\SellingPriceGroupController::class, 'update'], [$spg->id]), 'method' => 'put', 'id' => 'selling_price_group_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_selling_price_group' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
          {!! Form::text('name', $spg->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
          {!! Form::textarea('description', $spg->description, ['class' => 'form-control','placeholder' => __( 'lang_v1.description' ), 'rows' => 3]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="submit_spg_modal_btn">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->