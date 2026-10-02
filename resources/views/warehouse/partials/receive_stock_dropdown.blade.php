@if(auth()->user()->can('warehouse.receive_stock'))
    <div class="btn-group">
        <button type="button"
            class="tw-dw-btn tw-dw-btn-info tw-text-white dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <i class="fa fa-download"></i> @lang('lang_v1.warehouse_receive_stock') <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
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
        </ul>
    </div>
@endif
