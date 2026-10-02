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
    #update_unit_modal_btn {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
    }
    #update_unit_modal_btn:hover {
        background-color: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
    }
</style>

    {!! Form::open(['url' => action([\App\Http\Controllers\UnitController::class, 'update'], [$unit->id]), 'method' => 'PUT', 'id' => 'unit_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'unit.edit_unit' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}
            {!! Form::text('actual_name', $unit->actual_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}
            {!! Form::text('short_name', $unit->short_name, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}
            {!! Form::select('allow_decimal', ['1' => __('messages.yes'), '0' => __('messages.no')], $unit->allow_decimal, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                     {!! Form::checkbox('define_base_unit', 1, !empty($unit->base_unit_id),[ 'class' => 'toggler', 'data-toggle_id' => 'base_unit_div' ]); !!} @lang( 'lang_v1.add_as_multiple_of_base_unit' )
                  </label> @show_tooltip(__('lang_v1.multi_unit_help'))
                </div>
            </div>
          </div>
        <div class="form-group col-sm-12 @if(empty($unit->base_unit_id)) hide @endif" id="base_unit_div">
          <table class="table">
            <tr>
              <th style="vertical-align: middle;">1 <span id="unit_name">{{$unit->actual_name}}</span></th>
              <th style="vertical-align: middle;">=</th>
              <td style="vertical-align: middle;">
                {!! Form::text('base_unit_multiplier', !empty($unit->base_unit_multiplier) ? @number_format($unit->base_unit_multiplier) : null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>
              <td style="vertical-align: middle;">
                {!! Form::select('base_unit_id', $units, $unit->base_unit_id, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}
              </td>
            </tr>
            <tr><td colspan="4" style="padding-top: 0;">
            <p class="help-block">*@lang('lang_v1.edit_multi_unit_help_text')</p></td></tr>
          </table>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="update_unit_modal_btn">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->