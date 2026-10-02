<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseLocationService;
use App\Services\Warehouse\WarehouseStockService;
use App\Utils\TransactionUtil;
use Datatables;
use Illuminate\Http\Request;

class WarehouseStockController extends Controller
{
    public function __construct(
        protected WarehouseLocationService $warehouseLocationService,
        protected WarehouseStockService $warehouseStockService,
        protected TransactionUtil $transactionUtil
    ) {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.view_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE
        );
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);

        if ($request->ajax()) {
            $location_id = (int) $request->input('location_id', $default_warehouse_id);

            if (empty($location_id)) {
                return Datatables::of(collect())->make(true);
            }

            $products = $this->warehouseStockService->getAssignedStockQuery($business_id, $location_id);

            return Datatables::of($products)
                ->filter(function ($query) use ($request) {
                    $search = $request->get('search');
                    $keyword = is_array($search) ? ($search['value'] ?? '') : '';
                    if ($keyword !== '') {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('products.name', 'like', '%'.$keyword.'%')
                                ->orWhere('variations.sub_sku', 'like', '%'.$keyword.'%')
                                ->orWhere('c.name', 'like', '%'.$keyword.'%');
                        });
                    }
                }, true)
                ->orderColumn('product', 'products.name $1')
                ->orderColumn('sku', 'variations.sub_sku $1')
                ->orderColumn('category_name', 'c.name $1')
                ->orderColumn('location_name', 'l.name $1')
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0;

                        return '<span class="current_stock" data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'"> '
                            .$this->transactionUtil->num_f($stock, false, null, true).'</span> '.$row->unit;
                    }

                    return '--';
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;
                    if ($row->type == 'variable') {
                        $name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $name;
                })
                ->rawColumns(['stock'])
                ->make(true);
        }

        return redirect()->to(
            action([WarehouseDashboardController::class, 'index']).'#warehouse-stock'
        );
    }
}
