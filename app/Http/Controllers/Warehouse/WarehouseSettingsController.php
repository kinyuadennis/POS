<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseLocationService;
use Illuminate\Http\Request;

class WarehouseSettingsController extends Controller
{
    public function __construct(protected WarehouseLocationService $warehouseLocationService)
    {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE
        );
        $settings = $this->warehouseLocationService->getWarehouseSettings($business_id);
        $default_warehouse_id = $settings['default_warehouse_location_id'] ?? null;

        return view('warehouse.settings.index', compact(
            'warehouse_locations',
            'default_warehouse_id'
        ));
    }

    public function update(Request $request)
    {
        if (! auth()->user()->can('warehouse.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $default_id = $request->input('default_warehouse_location_id');

            if ($default_id && ! $this->warehouseLocationService->isWarehouseLocation((int) $default_id, $business_id)) {
                return [
                    'success' => false,
                    'msg' => __('lang_v1.warehouse_invalid_default_location'),
                ];
            }

            $this->warehouseLocationService->saveWarehouseSettings($business_id, [
                'default_warehouse_location_id' => $default_id ?: null,
            ]);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.warehouse_settings_updated'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}
