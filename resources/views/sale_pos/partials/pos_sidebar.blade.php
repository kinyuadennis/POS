<div class="row" id="featured_products_box" style="display: none;">
    @if (!empty($featured_products))
        @include('sale_pos.partials.featured_products')
    @endif
</div>
<div class="row tw-mb-1">
    @if (!empty($categories))
        @php $category_count = count($categories); @endphp
        <div class="col-xs-6 col-sm-6 col-md-6 !tw-px-1" id="product_category_div">
            <div class="tw-dw-drawer tw-dw-drawer-end">
                <input id="my-drawer-4" type="checkbox" class="tw-dw-drawer-toggle">
                <div class="tw-dw-drawer-content">
                    <label for="my-drawer-4" class="pos-filter-tab">
                        <span class="pos-filter-tab__left">
                            <span class="pos-filter-tab__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-category-plus"
                                    viewBox="0 0 24 24" stroke-width="1.5" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 4h6v6h-6zm10 0h6v6h-6zm-10 10h6v6h-6zm10 3h6m-3 -3v6" />
                                </svg>
                            </span>
                            <span class="pos-filter-tab__label">@lang('category.category')</span>
                        </span>
                        <span class="pos-filter-tab__count">{{ $category_count }}</span>
                    </label>
                </div>
                <div class="tw-dw-drawer-side" style="z-index: 4000">
                    <label for="my-drawer-4" aria-label="close sidebar"
                        class="tw-dw-drawer-overlay overlay-category"></label>
                    <div class="pos-drawer-menu tw-dw-menu tw-min-h-full tw-bg-white tw-p-4 md:tw-p-6">
                        <div class="tw-flex tw-items-center tw-mb-4" style="border-bottom:1px solid #e2e8f0;padding-bottom:12px;">
                            <button type="button"
                                class="tw-dw-btn tw-dw-btn-accent category-back tw-bg-transparent tw-border-2"
                                style="display: none">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="tw-w-5 icon icon-tabler icon-tabler-chevron-left" width="44"
                                    height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 6l-6 6l6 6" />
                                </svg>
                            </button>

                            <h3 class="tw-text-center tw-flex-grow mx-auto category_heading tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-inline-block tw-text-transparent tw-bg-clip-text tw-font-bold tw-text-base md:tw-text-2xl"
                                style="margin-bottom: 0px; margin-top:5px;">@lang('category.category')</h3>

                            <button type="button" class="tw-dw-btn tw-dw-btn-error close-side-bar-category">
                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                            </button>

                        </div>
                        <div class="row pos-filter-grid">
                            @foreach ($categories as $category)
                                <div class="col-md-4 col-sm-6 col-xs-6 tw-mb-3 md:tw-mb-4 no-print main-category-div"
                                    data-value="{{ $category['id'] }}" data-name="{{ $category['name'] }}">
                                    <div class="pos-filter-card main-category-select" role="button" tabindex="0"
                                        data-value="{{ $category['id'] }}" data-name="{{ $category['name'] }}">
                                        <span class="pos-filter-card__label">{{ $category['name'] }}</span>
                                        @if (!empty($category['sub_categories']))
                                            <button type="button"
                                                class="pos-filter-card__action main-category-drill"
                                                data-value="{{ $category['id'] }}"
                                                data-name="{{ $category['name'] }}"
                                                title="@lang('pagination.next')">
                                                <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($categories as $category)
                                @if (!empty($category['sub_categories']))
                                    <div class="row {{ $category['id'] }} all-sub-category" style="display: none">
                                        @foreach ($category['sub_categories'] as $sc)
                                            @if ($sc['parent_id'] != 0)
                                                <div class="col-md-4 col-sm-6 col-xs-6 tw-mb-3 md:tw-mb-4 no-print product_category"
                                                    data-value="{{ $sc['id'] }}" data-name="{{ $sc['name'] }}">
                                                    <div class="pos-filter-card product_category-select" role="button"
                                                        tabindex="0" data-value="{{ $sc['id'] }}"
                                                        data-name="{{ $sc['name'] }}">
                                                        <span class="pos-filter-card__label">{{ $sc['name'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($brands))
        @php $brand_count = count($brands); @endphp
        <div class="col-xs-6 col-sm-6 col-md-6 !tw-px-1" id="product_brand_div">
            <div class="tw-dw-drawer tw-dw-drawer-end">
                <input id="my-drawer-brand" type="checkbox" class="tw-dw-drawer-toggle">
                <div class="tw-dw-drawer-content">
                    <label for="my-drawer-brand" class="pos-filter-tab">
                        <span class="pos-filter-tab__left">
                            <span class="pos-filter-tab__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-brand-beats"
                                    viewBox="0 0 24 24" stroke-width="1.5" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12.5 12.5m-3.5 0a3.5 3.5 0 1 0 7 0a3.5 3.5 0 1 0 -7 0" />
                                    <path d="M9 12v-8" />
                                </svg>
                            </span>
                            <span class="pos-filter-tab__label">@lang('brand.brands')</span>
                        </span>
                        <span class="pos-filter-tab__count" id="brand_counter_badge">{{ $brand_count }}</span>
                    </label>
                </div>
                <div class="tw-dw-drawer-side" style="z-index: 4000">
                    <label for="my-drawer-brand" aria-label="close sidebar"
                        class="tw-dw-drawer-overlay overlay-brand"></label>
                    <div class="pos-drawer-menu tw-dw-menu tw-min-h-full tw-bg-white tw-p-4 md:tw-p-6">

                        <div class="tw-flex tw-items-center tw-mb-4" style="border-bottom:1px solid #e2e8f0;padding-bottom:12px;">
                            <h3 class="tw-text-center tw-mx-auto tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-text-transparent tw-bg-clip-text tw-font-bold tw-text-base md:tw-text-2xl"
                                style="margin-bottom: 0px; margin-top:5px;">@lang('brand.brands')
                                <span id="brand_active_filter_badge" style="display:none;font-size:11px;background:#fef3c7;color:#92400e;border-radius:999px;padding:2px 8px;font-weight:600;"></span>
                            </h3>
                            <button type="button" class="tw-dw-btn tw-dw-btn-error close-side-bar-brand">
                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                            </button>
                        </div>

                        <div class="row pos-filter-grid">
                            @foreach ($brands as $key => $brand)
                                <div class="col-md-4 col-sm-6 col-xs-6 tw-mb-3 md:tw-mb-4 no-print product_brand"
                                    data-value="{{ $key }}" data-name="{{ $brand }}">
                                    <div class="pos-filter-card product_brand-select" role="button" tabindex="0"
                                        data-value="{{ $key }}" data-name="{{ $brand }}">
                                        <span class="pos-filter-card__label">{{ $brand }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- used in repair : filter for service/product -->
    <div class="col-md-6 hide" id="product_service_div">
        {!! Form::select(
            'is_enabled_stock',
            ['' => __('messages.all'), 'product' => __('sale.product'), 'service' => __('lang_v1.service')],
            null,
            ['id' => 'is_enabled_stock', 'class' => 'select2', 'name' => null, 'style' => 'width:100% !important'],
        ) !!}
    </div>

    <div class="col-sm-4 @if (empty($featured_products)) hide @endif" id="feature_product_div">
        <button type="button" class="btn btn-primary btn-flat"
            id="show_featured_products">@lang('lang_v1.featured_products')</button>
    </div>
</div>
<div class="row pos-products-list-row">
    <input type="hidden" id="suggestion_page" value="1">
    <div class="col-md-12 pos-products-scroll">
        <div class="eq-height-row" id="product_list_body"></div>
    </div>
    <div class="col-md-12 text-center" id="suggestion_page_loader" style="display: none;">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
    </div>
</div>
