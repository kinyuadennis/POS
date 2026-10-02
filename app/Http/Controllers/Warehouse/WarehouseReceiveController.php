<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseLocationService;
use Illuminate\Http\Request;

class WarehouseReceiveController extends Controller
{
    public function __construct(protected WarehouseLocationService $warehouseLocationService)
    {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.receive_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE
        );
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);

        return view('warehouse.receive.index', compact('warehouse_locations', 'default_warehouse_id'));
    }
}
