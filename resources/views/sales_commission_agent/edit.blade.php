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
    #submit_agent_modal_btn {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
    }
    #submit_agent_modal_btn:hover {
        background-color: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
    }

    /* Style select border based on theme */
    select#surname {
        border: 1px solid {{ $primary_hex }} !important;
    }
    select#surname:focus {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
        outline: 0 !important;
    }
</style>

    {!! Form::open(['url' => action([\App\Http\Controllers\SalesCommissionAgentController::class, 'update'], [$user->id]), 'method' => 'PUT', 'id' => 'sale_commission_agent_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_sales_commission_agent' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
            {!! Form::select('surname', ['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Miss' => 'Miss'], $user->surname, ['class' => 'form-control', 'id' => 'surname', 'placeholder' => __( 'messages.please_select' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ':') !!}
            {!! Form::text('email', $user->email, ['class' => 'form-control', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('contact_no', __( 'lang_v1.contact_no' ) . ':') !!}
            {!! Form::text('contact_no', $user->contact_no, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.contact_no' ) ]); !!}
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          {!! Form::label('address', __( 'business.address' ) . ':') !!}
            {!! Form::textarea('address', $user->address, ['class' => 'form-control', 'placeholder' => __( 'business.address'), 'rows' => 3 ]); !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!}
            {!! Form::text('cmmsn_percent', @num_format($user->cmmsn_percent), ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ), 'required' ]); !!}
        </div>
      </div>
      
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="submit_agent_modal_btn">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->