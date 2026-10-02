<!-- Admin Discount Approval Modal -->
<div class="modal fade" id="adminDiscountApprovalModal" tabindex="-1" role="dialog" aria-labelledby="adminDiscountApprovalModalLabel" data-backdrop="static" data-keyboard="false" style="z-index: 1060;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 18px 24px;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.85; font-size: 24px; font-weight: 300;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tw-flex tw-items-center tw-gap-3">
                    <span class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full tw-bg-amber-500/20 tw-text-amber-400 tw-ring-1 tw-ring-amber-500/30">
                        <i class="fas fa-percentage fa-lg"></i>
                    </span>
                    <div>
                        <h4 class="modal-title tw-font-bold tw-text-lg tw-text-white" id="adminDiscountApprovalModalLabel" style="margin: 0;">
                            @lang('Discount Approval Request')
                        </h4>
                        <p class="tw-text-xs tw-text-slate-300" style="margin: 0;" id="approval_request_meta">
                            <!-- Populated dynamically -->
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 24px; max-height: calc(100vh - 210px); overflow-y: auto;">
                <input type="hidden" id="current_approval_id" value="">

                <!-- Cashier Note Alert if present -->
                <div id="approval_cashier_note_wrapper" class="tw-hidden tw-mb-4 tw-p-3.5 tw-bg-amber-50 tw-border tw-border-amber-200 tw-rounded-lg tw-flex tw-items-start tw-gap-3">
                    <i class="fas fa-comment-dots tw-text-amber-500 tw-mt-0.5"></i>
                    <div>
                        <strong class="tw-text-xs tw-font-bold tw-text-amber-800 tw-uppercase tw-tracking-wider">@lang('Cashier Reason / Note'):</strong>
                        <p id="approval_cashier_note" class="tw-text-sm tw-text-amber-900 tw-mt-0.5 tw-mb-0"></p>
                    </div>
                </div>

                <!-- Cart Items Snapshot -->
                <div class="tw-mb-5">
                    <h5 class="tw-text-sm tw-font-bold tw-text-gray-800 tw-mb-2 tw-flex tw-items-center tw-gap-2">
                        <i class="fas fa-shopping-cart tw-text-primary-600"></i>
                        @lang('Cart Items Snapshot')
                    </h5>
                    <div class="table-responsive tw-border tw-border-gray-200 tw-rounded-lg">
                        <table class="table table-condensed table-striped" id="approval_cart_items_table" style="margin-bottom: 0; font-size: 13px;">
                            <thead class="tw-bg-gray-50">
                                <tr>
                                    <th class="tw-font-semibold tw-text-gray-600">#</th>
                                    <th class="tw-font-semibold tw-text-gray-600">@lang('Product / Item')</th>
                                    <th class="text-center tw-font-semibold tw-text-gray-600">@lang('Qty')</th>
                                    <th class="text-right tw-font-semibold tw-text-gray-600">@lang('Unit Price')</th>
                                    <th class="text-right tw-font-semibold tw-text-gray-600">@lang('Line Total')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financial Comparison Grid -->
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mb-5">
                    <!-- Original Total -->
                    <div class="tw-p-4 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl text-center">
                        <span class="tw-block tw-text-xs tw-font-semibold tw-text-slate-500 tw-uppercase tw-tracking-wider">
                            @lang('Original Total')
                        </span>
                        <span class="tw-block tw-text-xl tw-font-bold tw-text-slate-800 tw-mt-1" id="approval_original_total">
                            0.00
                        </span>
                    </div>

                    <!-- Requested Discount -->
                    <div class="tw-p-4 tw-bg-rose-50 tw-border tw-border-rose-200 tw-rounded-xl text-center">
                        <span class="tw-block tw-text-xs tw-font-semibold tw-text-rose-600 tw-uppercase tw-tracking-wider">
                            @lang('Requested Discount')
                        </span>
                        <span class="tw-block tw-text-xl tw-font-bold tw-text-rose-700 tw-mt-1" id="approval_requested_discount">
                            0.00
                        </span>
                    </div>

                    <!-- Resulting Total -->
                    <div class="tw-p-4 tw-bg-emerald-50 tw-border tw-border-emerald-200 tw-rounded-xl text-center">
                        <span class="tw-block tw-text-xs tw-font-semibold tw-text-emerald-600 tw-uppercase tw-tracking-wider">
                            @lang('Resulting Total')
                        </span>
                        <span class="tw-block tw-text-xl tw-font-bold tw-text-emerald-700 tw-mt-1" id="approval_resulting_total">
                            0.00
                        </span>
                    </div>
                </div>

                <!-- Admin Action Configuration: Approve as Requested vs Modify -->
                <div class="tw-border tw-border-gray-200 tw-rounded-xl tw-p-4 tw-bg-white">
                    <label class="tw-block tw-text-xs tw-font-bold tw-text-gray-700 tw-uppercase tw-tracking-wider tw-mb-2">
                        @lang('Decision Mode')
                    </label>

                    <div class="tw-flex tw-gap-6 tw-mb-4">
                        <label class="tw-inline-flex tw-items-center tw-cursor-pointer tw-text-sm tw-font-medium tw-text-gray-800">
                            <input type="radio" name="admin_approval_action_type" value="approve" checked class="tw-mr-2" onchange="toggleApprovalMode('approve')">
                            @lang('Approve Requested Discount')
                        </label>
                        <label class="tw-inline-flex tw-items-center tw-cursor-pointer tw-text-sm tw-font-medium tw-text-gray-800">
                            <input type="radio" name="admin_approval_action_type" value="modify" class="tw-mr-2" onchange="toggleApprovalMode('modify')">
                            @lang('Modify Discount Amount & Approve')
                        </label>
                    </div>

                    <!-- Modify Inputs (Hidden by default) -->
                    <div id="approval_modify_fields_wrapper" class="tw-hidden tw-p-3.5 tw-bg-blue-50 tw-border tw-border-blue-200 tw-rounded-lg tw-mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="tw-text-xs tw-font-semibold tw-text-blue-900">@lang('Approved Discount Type'):</label>
                                <select id="admin_mod_discount_type" class="form-control input-sm select2" style="width: 100%;" onchange="recalculateAdminResultingTotal()">
                                    <option value="percentage">@lang('lang_v1.percentage') (%)</option>
                                    <option value="fixed">@lang('lang_v1.fixed')</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="tw-text-xs tw-font-semibold tw-text-blue-900">@lang('Approved Discount Value'):</label>
                                <input type="number" step="0.01" min="0" id="admin_mod_discount_amount" class="form-control input-sm" placeholder="e.g. 10" oninput="recalculateAdminResultingTotal()">
                            </div>
                            <div class="col-md-12 tw-mt-2">
                                <span class="tw-text-xs tw-text-blue-800">
                                    <strong>@lang('New Resulting Total'):</strong> <span id="admin_mod_resulting_total_preview" class="tw-font-bold">0.00</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Remark / Notes -->
                    <div>
                        <label for="admin_approval_notes" class="tw-block tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-1">
                            @lang('Admin Note / Remarks') <span class="tw-text-gray-400">(@lang('Optional, sent to cashier'))</span>:
                        </label>
                        <input type="text" id="admin_approval_notes" class="form-control input-sm" placeholder="@lang('e.g. Approved 10% max for this wholesale customer')">
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer tw-bg-gray-50 tw-flex tw-items-center tw-justify-between" style="padding: 16px 24px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times tw-mr-1"></i> @lang('Dismiss')
                </button>

                <div class="tw-flex tw-items-center tw-gap-2">
                    <button type="button" id="btnAdminRejectDiscount" class="btn btn-danger" onclick="submitAdminDiscountResponse('reject')">
                        <i class="fas fa-ban tw-mr-1"></i> @lang('Reject')
                    </button>
                    <button type="button" id="btnAdminApproveDiscount" class="btn btn-success" onclick="submitAdminDiscountResponse('approve')">
                        <i class="fas fa-check-circle tw-mr-1"></i> @lang('Approve Discount')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
