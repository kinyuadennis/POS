@php
    $has_actions = auth()->user()->can('warehouse.dispatch')
        || auth()->user()->can('warehouse.adjust_stock')
        || auth()->user()->can('warehouse.receive_stock');
@endphp

@if($has_actions)
    <div class="btn-group" id="warehouse_actions_dropdown_group" style="position: relative;">
        <button type="button"
            id="warehouse_actions_toggle"
            class="tw-dw-btn warehouse-theme-btn tw-text-white tw-border-none"
            aria-haspopup="true"
            aria-expanded="false"
            style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            <i class="fa fa-bolt"></i> @lang('messages.actions') <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right warehouse-actions-menu">
            @can('warehouse.dispatch')
                <li>
                    <a href="{{ action([\App\Http\Controllers\Warehouse\WarehouseDispatchController::class, 'create']) }}">
                        <i class="fa fa-truck"></i> @lang('lang_v1.warehouse_new_dispatch')
                    </a>
                </li>
            @endcan
            @can('warehouse.adjust_stock')
                <li>
                    <a href="{{ action([\App\Http\Controllers\Warehouse\WarehouseAdjustmentController::class, 'create']) }}">
                        <i class="fa fa-sliders-h"></i> @lang('lang_v1.warehouse_new_adjustment')
                    </a>
                </li>
            @endcan
            @can('warehouse.receive_stock')
                @if(auth()->user()->can('warehouse.dispatch') || auth()->user()->can('warehouse.adjust_stock'))
                    <li role="separator" class="divider"></li>
                @endif
                @can('purchase.create')
                    <li>
                        <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}">
                            <i class="fa fa-shopping-cart"></i> @lang('purchase.add_purchase')
                        </a>
                    </li>
                @endcan
                @can('product.opening_stock')
                    <li>
                        <a href="{{ action([\App\Http\Controllers\ImportOpeningStockController::class, 'index']) }}">
                            <i class="fa fa-upload"></i> @lang('lang_v1.import_opening_stock')
                        </a>
                    </li>
                @endcan
                @can('purchase.view')
                    <li>
                        <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'index']) }}">
                            <i class="fa fa-list"></i> @lang('purchase.list_purchase')
                        </a>
                    </li>
                @endcan
            @endcan
        </ul>
    </div>
@endif
