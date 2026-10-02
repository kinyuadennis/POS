<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action([\App\Http\Controllers\CashRegisterController::class, 'postCloseRegister']), 'method' => 'post' ]) !!}

    {!! Form::hidden('user_id', $register_details->user_id); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">@lang( 'cash_register.current_register' ) ( {{ \Carbon::createFromFormat('Y-m-d H:i:s', $register_details->open_time)->format('jS M, Y h:i A') }} - {{ \Carbon::now()->format('jS M, Y h:i A') }})</h3>
    </div>

    <div class="modal-body">
        @include('cash_register.payment_details', ['is_close_register' => true])
        <hr>
      <div class="row">
        <div class="col-sm-12 col-xs-12">
          <div class="form-group">
            {!! Form::label('closing_note', __( 'cash_register.closing_note' ) . ':') !!}
              {!! Form::textarea('closing_note', null, ['class' => 'form-control', 'placeholder' => __( 'cash_register.closing_note' ), 'rows' => 3 ]); !!}
          </div>
        </div>
      </div> 

      <div class="row">
        <div class="col-xs-12 col-sm-6">
          <b>@lang('report.user'):</b> {{ $register_details->user_name}}<br>
          <b>@lang('business.email'):</b> {{ $register_details->email}}<br>
          <b>@lang('business.business_location'):</b> {{ $register_details->location_name}}<br>
        </div>
        @if(!empty($register_details->closing_note))
          <div class="col-xs-12 col-sm-6">
            <strong>@lang('cash_register.closing_note'):</strong><br>
            {{$register_details->closing_note}}
          </div>
        @endif
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.cancel' )</button>
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'cash_register.close_register' )</button>
    </div>
    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
$(document).ready(function() {
    function calculateCashVariance() {
        var $input = $('#closing_amount');
        if (!$input.length) return;

        var expected = parseFloat($input.attr('data-expected')) || 0;

        var counted = 0;
        if (typeof __read_number === 'function') {
            counted = __read_number($input);
        } else {
            var val = $input.val() || '0';
            counted = parseFloat(val.replace(/[^0-9.-]+/g, '')) || 0;
        }

        if (isNaN(counted)) counted = 0;

        var diff = counted - expected;
        var $badge = $('#cash_variance_badge');
        if (!$badge.length) return;

        var formattedDiff = Math.abs(diff).toFixed(2);
        if (typeof __currency_trans_from_en === 'function') {
            formattedDiff = __currency_trans_from_en(Math.abs(diff));
        }

        if (Math.abs(diff) < 0.01) {
            $badge.css({
                'background-color': '#dcfce7',
                'color': '#15803d',
                'border-color': '#86efac'
            }).html('<i class="fa fa-check-circle"></i> Balanced (0.00)');
        } else if (diff > 0) {
            $badge.css({
                'background-color': '#eff6ff',
                'color': '#1d4ed8',
                'border-color': '#93c5fd'
            }).html('<i class="fa fa-arrow-up"></i> Over / Surplus (+ ' + formattedDiff + ')');
        } else {
            $badge.css({
                'background-color': '#fef2f2',
                'color': '#b91c1c',
                'border-color': '#fca5a5'
            }).html('<i class="fa fa-arrow-down"></i> Shortage (- ' + formattedDiff + ')');
        }
    }

    $(document).on('keyup input change', '#closing_amount', function() {
        calculateCashVariance();
    });



    calculateCashVariance();
});
</script>
