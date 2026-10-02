<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseLocationService;
use App\Services\Warehouse\WarehouseStockService;
use App\Transaction;
use App\VariationLocationDetails;
use Illuminate\Http\Request;

class WarehouseDashboardController extends Controller
{
    public function __construct(
        protected WarehouseLocationService $warehouseLocationService,
        protected WarehouseStockService $warehouseStockService
    ) {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $warehouse_ids = $this->warehouseLocationService->getWarehouseLocationIds($business_id);
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);

        $stats = [
            'warehouse_count' => count($warehouse_ids),
            'product_count' => 0,
            'total_units' => 0,
            'pending_dispatches' => 0,
            'in_transit_dispatches' => 0,
        ];

        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE
        );

        if (empty($default_warehouse_id) && $warehouse_locations->isNotEmpty()) {
            $default_warehouse_id = $warehouse_locations->keys()->first();
        }

        if (! empty($warehouse_ids)) {
            $count_location_ids = $default_warehouse_id ? [$default_warehouse_id] : $warehouse_ids;
            $stats['product_count'] = $this->warehouseStockService->countAssignedProducts($count_location_ids);

            $units_location_ids = $default_warehouse_id ? [$default_warehouse_id] : $warehouse_ids;
            $stats['total_units'] = (float) VariationLocationDetails::join('product_locations as pl', function ($join) {
                    $join->on('pl.product_id', '=', 'variation_location_details.product_id')
                         ->on('pl.location_id', '=', 'variation_location_details.location_id');
                })
                ->whereIn('variation_location_details.location_id', $units_location_ids)
                ->sum('variation_location_details.qty_available');

            $stats['pending_dispatches'] = Transaction::where('business_id', $business_id)
                ->where('type', 'sell_transfer')
                ->whereIn('location_id', $warehouse_ids)
                ->where('status', 'pending')
                ->count();

            $stats['in_transit_dispatches'] = Transaction::where('business_id', $business_id)
                ->where('type', 'sell_transfer')
                ->whereIn('location_id', $warehouse_ids)
                ->where('status', 'in_transit')
                ->count();
        }

        return view('warehouse.dashboard', compact(
            'stats',
            'default_warehouse_id',
            'warehouse_ids',
            'warehouse_locations'
        ));
    }
}
