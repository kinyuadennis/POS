<div id="warehouse-stock" class="warehouse-stock-panel">
    <div class="warehouse-search-section">
        <div class="warehouse-search-top-row">
            <div class="warehouse-search-input-wrap">
                <span class="warehouse-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text"
                    id="custom_warehouse_stock_search"
                    placeholder="@lang('lang_v1.warehouse_stock_search_placeholder')"
                    class="form-control warehouse-search-input">
            </div>

            <div class="warehouse-search-actions">
                <button type="button"
                    class="tw-dw-btn warehouse-theme-btn tw-text-white tw-border-none warehouse-filter-btn"
                    data-toggle="collapse"
                    data-target="#warehouseStockFilters"
                    aria-expanded="false"
                    aria-controls="warehouseStockFilters">
                    <i class="fa fa-filter"></i> @lang('report.filters')
                </button>

                @include('warehouse.partials.actions_dropdown')
            </div>
        </div>

        <div class="warehouse-search-bottom-row">
            <label class="warehouse-entries-label">
                @lang('lang_v1.show')
                <select id="custom_warehouse_stock_length" class="form-control warehouse-entries-select">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="-1">@lang('lang_v1.all')</option>
                </select>
                @lang('lang_v1.entries')
            </label>
        </div>
    </div>

    <div class="collapse" id="warehouseStockFilters">
        <div class="warehouse-filters-panel">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('warehouse_location_filter', __('business.business_location') . ':') !!}
                        {!! Form::select('warehouse_location_filter', $warehouse_locations, $default_warehouse_id, [
                            'class' => 'form-control select2',
                            'id' => 'warehouse_location_filter',
                        ]) !!}
                    </div>
                </div>
            </div>
            <p class="text-muted tw-mb-0">@lang('lang_v1.warehouse_assigned_zero_stock_help')</p>
        </div>
    </div>

    @if ($warehouse_locations->isEmpty())
        <div class="alert alert-warning">
            @lang('lang_v1.warehouse_no_locations_help')
        </div>
    @else
        <input type="hidden" id="warehouse_default_location_id" value="{{ $default_warehouse_id }}">
    @endif

    <div class="table-responsive warehouse-table-wrap">
        <table class="table table-bordered table-striped" id="warehouse_stock_table">
            <thead>
                <tr>
                    <th>@lang('sale.product')</th>
                    <th>@lang('product.sku')</th>
                    <th>@lang('product.category')</th>
                    <th>@lang('report.current_stock')</th>
                    <th>@lang('business.business_location')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
