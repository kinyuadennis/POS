<!-- Transaction-Level Selling Price Selection Modal -->
<div class="modal fade" id="price_selection_modal" tabindex="-1" role="dialog" aria-labelledby="priceSelectionModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content tw-rounded-2xl tw-shadow-2xl tw-border-0 tw-overflow-hidden">
            <!-- Modal Header with extra padding and pure white close button -->
            <div class="tw-text-white tw-px-8 tw-py-6 tw-flex tw-items-center tw-justify-between" style="background-color: #414aac; background: linear-gradient(135deg, #414aac 0%, #303784 100%); padding: 22px 28px !important;">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-white/20 tw-flex tw-items-center tw-justify-center tw-backdrop-blur-sm tw-shrink-0">
                        <i class="fas fa-tags tw-text-yellow-300 tw-text-lg"></i>
                    </div>
                    <div>
                        <h4 class="modal-title tw-font-bold tw-text-lg tw-text-white tw-m-0" style="color: #ffffff !important;">Select Pricing Mode</h4>
                        <p class="tw-text-xs tw-m-0 tw-mt-0.5" style="color: rgba(255, 255, 255, 0.85) !important;">Choose pricing mode for this sale transaction</p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff !important; opacity: 1 !important; text-shadow: none !important; font-size: 28px; line-height: 1; padding: 0; margin: 0; background: transparent; border: none; cursor: pointer;">
                    <span aria-hidden="true" style="color: #ffffff !important; font-weight: 300;">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body tw-p-6 tw-bg-slate-50">
                <p class="tw-text-gray-600 tw-text-sm tw-mb-5 tw-leading-relaxed">
                    Please select the selling price type to apply to <strong>all products</strong> in this transaction:
                </p>

                <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                    <!-- Retail Option Card (styles applied via .active-price-card only when picked) -->
                    <label class="price-type-card tw-cursor-pointer tw-border-2 tw-border-gray-200 tw-bg-white tw-rounded-xl tw-p-5 tw-flex tw-flex-col tw-justify-between tw-shadow-sm hover:tw-shadow-md tw-transition-all active-price-card" id="card_retail_mode">
                        <div>
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-text-lg" style="background-color: rgba(65, 74, 172, 0.12); color: #414aac;">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <input type="radio" name="modal_price_type" value="retail" checked class="tw-form-radio tw-h-5 tw-w-5 tw-cursor-pointer" style="accent-color: #414aac;">
                            </div>
                            <span class="tw-font-bold tw-text-base tw-text-gray-900 tw-block">Retail Pricing</span>
                            <span class="tw-text-xs tw-text-gray-500 tw-mt-1 tw-block tw-leading-snug">Standard retail selling prices for all items in this sale</span>
                        </div>
                        <div class="tw-mt-4 tw-pt-3 tw-border-t tw-border-gray-200 tw-flex tw-items-center tw-font-semibold tw-text-xs" style="color: #414aac;">
                            <i class="fas fa-check-circle tw-mr-1.5"></i> Standard Mode
                        </div>
                    </label>

                    <!-- Wholesale Option Card (styles applied via .active-price-card only when picked) -->
                    <label class="price-type-card tw-cursor-pointer tw-border-2 tw-border-gray-200 tw-bg-white tw-rounded-xl tw-p-5 tw-flex tw-flex-col tw-justify-between tw-shadow-sm hover:tw-shadow-md tw-transition-all" id="card_wholesale_mode">
                        <div>
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-text-lg" style="background-color: rgba(65, 74, 172, 0.12); color: #414aac;">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <input type="radio" name="modal_price_type" value="wholesale" class="tw-form-radio tw-h-5 tw-w-5 tw-cursor-pointer" style="accent-color: #414aac;">
                            </div>
                            <span class="tw-font-bold tw-text-base tw-text-gray-900 tw-block">Wholesale Pricing</span>
                            <span class="tw-text-xs tw-text-gray-500 tw-mt-1 tw-block tw-leading-snug">Wholesale selling group rates for all items in this sale</span>
                        </div>
                        <div class="tw-mt-4 tw-pt-3 tw-border-t tw-border-gray-200 tw-flex tw-items-center tw-font-semibold tw-text-xs" style="color: #414aac;">
                            <i class="fas fa-tags tw-mr-1.5"></i> Bulk Wholesale
                        </div>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer tw-bg-gray-100 tw-px-6 tw-py-4 tw-flex tw-items-center tw-justify-between tw-border-t tw-border-gray-200">
                <button type="button" class="btn btn-default tw-rounded-lg tw-px-4 tw-py-2" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary tw-text-white tw-font-bold tw-rounded-lg tw-px-6 tw-py-2 tw-border-0 tw-shadow-md" id="btn_confirm_price_selection" style="background-color: #414aac !important; border-color: #414aac !important;">
                    Proceed
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.price-type-card {
    border-color: #e5e7eb !important;
    background-color: #ffffff !important;
    transition: all 0.2s ease-in-out;
}
.price-type-card.active-price-card {
    border-color: #414aac !important;
    background-color: rgba(65, 74, 172, 0.05) !important;
    box-shadow: 0 4px 14px -2px rgba(65, 74, 172, 0.25) !important;
}
.price-type-card:hover {
    border-color: #cbd5e1 !important;
}
.price-type-card.active-price-card:hover {
    border-color: #414aac !important;
}
#btn_confirm_price_selection:hover {
    background-color: #303784 !important;
    border-color: #303784 !important;
}
</style>
