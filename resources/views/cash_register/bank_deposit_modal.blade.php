<div class="modal-dialog" role="document">
  <div class="modal-content" style="border-radius: 8px;">
    {!! Form::open(['url' => action([\App\Http\Controllers\CashRegisterController::class, 'postBankDeposit']), 'method' => 'post', 'id' => 'bank_deposit_form' ]) !!}

    <div class="modal-header" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; border-top-left-radius: 8px; border-top-right-radius: 8px;">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title" style="font-size: 16px; font-weight: 700; color: #1e293b;">
        <i class="fa fa-university text-primary" style="margin-right: 6px;"></i> Cash Drop / Bank Deposit
      </h3>
    </div>

    <div class="modal-body" style="padding: 20px;">
      <div class="form-group">
        {!! Form::label('amount', 'Amount Deposited to Bank:*', ['style' => 'font-weight: 700; color: #0f172a;']) !!}
        {!! Form::text('amount', null, ['class' => 'form-control input_number', 'required', 'placeholder' => '0.00', 'style' => 'height: 42px; font-size: 16px; font-weight: 700; border-radius: 6px;']); !!}
      </div>

      <div class="form-group">
        {!! Form::label('note', 'Reference / Deposit Slip Note:', ['style' => 'font-weight: 600; color: #334155;']) !!}
        {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => 'Enter bank reference, slip number, or notes...', 'rows' => 3, 'style' => 'border-radius: 6px;']); !!}
      </div>
    </div>

    <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.cancel')</button>
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">Submit Deposit</button>
    </div>

    {!! Form::close() !!}
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#bank_deposit_form').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            method: 'POST',
            url: form.attr('action'),
            dataType: 'json',
            data: form.serialize(),
            success: function(result) {
                if (result.success) {
                    form.closest('.modal').modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(result.msg);
                    } else {
                        alert(result.msg);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(result.msg);
                    } else {
                        alert(result.msg);
                    }
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Something went wrong. Please try again.');
                }
            }
        });
    });
});
</script>
