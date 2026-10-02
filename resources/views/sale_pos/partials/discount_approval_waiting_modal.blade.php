<!-- Cashier Discount Approval Waiting Modal -->
<div class="modal fade" id="posDiscountWaitingModal" tabindex="-1" role="dialog" aria-labelledby="posDiscountWaitingModalLabel" data-backdrop="static" data-keyboard="false" style="z-index: 1070;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-body text-center" style="padding: 36px 28px;">
                <!-- Animated Waiting Icon -->
                <div class="tw-relative tw-inline-flex tw-items-center tw-justify-center tw-mb-4">
                    <span class="tw-animate-ping tw-absolute tw-inline-flex tw-h-16 tw-w-16 tw-rounded-full tw-bg-amber-400 tw-opacity-75"></span>
                    <span class="tw-relative tw-inline-flex tw-rounded-full tw-h-16 tw-w-16 tw-bg-amber-500 tw-text-white tw-items-center tw-justify-center tw-shadow-lg">
                        <i class="fas fa-hourglass-half fa-2x tw-animate-pulse"></i>
                    </span>
                </div>

                <h3 class="tw-text-xl tw-font-bold tw-text-gray-900 tw-mb-1" id="waiting_modal_title">
                    @lang('Waiting for Admin Approval')
                </h3>
                <p class="tw-text-sm tw-text-gray-500 tw-mb-5" id="waiting_modal_subtitle">
                    @lang('A notification has been dispatched to managers for review.')
                </p>

                <!-- Requested Details Card -->
                <div class="tw-bg-gray-50 tw-border tw-border-gray-200 tw-rounded-xl tw-p-4 tw-mb-5 text-left">
                    <div class="tw-flex tw-justify-between tw-py-1 tw-border-b tw-border-gray-200">
                        <span class="tw-text-xs tw-text-gray-500 tw-font-medium">@lang('Original Total'):</span>
                        <span class="tw-text-xs tw-font-bold tw-text-gray-800" id="waiting_original_total">0.00</span>
                    </div>
                    <div class="tw-flex tw-justify-between tw-py-1 tw-border-b tw-border-gray-200">
                        <span class="tw-text-xs tw-text-rose-600 tw-font-medium">@lang('Requested Discount'):</span>
                        <span class="tw-text-xs tw-font-bold tw-text-rose-700" id="waiting_requested_discount">0.00</span>
                    </div>
                    <div class="tw-flex tw-justify-between tw-py-1">
                        <span class="tw-text-xs tw-text-emerald-600 tw-font-bold">@lang('Resulting Total'):</span>
                        <span class="tw-text-xs tw-font-extrabold tw-text-emerald-700" id="waiting_resulting_total">0.00</span>
                    </div>
                </div>

                <div class="tw-flex tw-items-center tw-justify-center tw-gap-2 tw-text-xs tw-text-amber-600 tw-font-semibold tw-mb-6">
                    <i class="fas fa-sync fa-spin"></i>
                    <span>@lang('Checking status in real-time...')</span>
                </div>

                <button type="button" class="btn btn-default btn-block tw-font-semibold" id="btnCancelDiscountApproval">
                    <i class="fas fa-times-circle tw-mr-1"></i> @lang('Cancel Approval Request')
                </button>
            </div>
        </div>
    </div>
</div>
