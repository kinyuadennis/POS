<?php

namespace App\Services\Warehouse;

use App\Business;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use Illuminate\Support\Collection;

class WarehouseLocationService
{
    public const TYPE_WAREHOUSE = 'warehouse';

    public const TYPE_SHOP = 'shop';

    public function __construct(protected ModuleUtil $moduleUtil)
    {
    }

    public function isModuleEnabled(?int $business_id = null): bool
    {
        return $this->moduleUtil->isModuleEnabled('warehouse', $business_id);
    }

    public function getWarehouseLocationIds(int $business_id): array
    {
        return BusinessLocation::where('business_id', $business_id)
            ->where('location_type', self::TYPE_WAREHOUSE)
            ->Active()
            ->pluck('id')
            ->toArray();
    }

    public function getShopLocationIds(int $business_id): array
    {
        return BusinessLocation::where('business_id', $business_id)
            ->where(function ($query) {
                $query->where('location_type', self::TYPE_SHOP)
                    ->orWhereNull('location_type');
            })
            ->Active()
            ->pluck('id')
            ->toArray();
    }

    public function forDropdownByType(int $business_id, string $type, bool $show_all = false, bool $filter_permitted = true): Collection
    {
        $query = BusinessLocation::where('business_id', $business_id)->Active();

        if ($type === self::TYPE_WAREHOUSE) {
            $query->where('location_type', self::TYPE_WAREHOUSE);
        } else {
            $query->where(function ($q) {
                $q->where('location_type', self::TYPE_SHOP)
                    ->orWhereNull('location_type');
            });
        }

        if ($filter_permitted && auth()->check()) {
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('id', $permitted_locations);
            }
        }

        $locations = $query->select(
            \DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
            'id'
        )->pluck('name', 'id');

        if ($show_all) {
            $locations->prepend(__('report.all_locations'), '');
        }

        return $locations;
    }

    public function isWarehouseLocation(?int $location_id, ?int $business_id = null): bool
    {
        if (empty($location_id)) {
            return false;
        }

        $query = BusinessLocation::where('id', $location_id);

        if ($business_id) {
            $query->where('business_id', $business_id);
        }

        $location = $query->first();

        return $location && $location->location_type === self::TYPE_WAREHOUSE;
    }

    public function getDefaultWarehouseId(int $business_id): ?int
    {
        $settings = $this->getWarehouseSettings($business_id);
        $default_id = $settings['default_warehouse_location_id'] ?? null;

        if ($default_id && $this->isWarehouseLocation((int) $default_id, $business_id)) {
            return (int) $default_id;
        }

        $ids = $this->getWarehouseLocationIds($business_id);

        return ! empty($ids) ? (int) $ids[0] : null;
    }

    public function isDefaultWarehouse(int $business_id, int $location_id): bool
    {
        $settings = $this->getWarehouseSettings($business_id);

        return (int) ($settings['default_warehouse_location_id'] ?? 0) === $location_id;
    }

    public function syncDefaultWarehouseForLocation(
        int $business_id,
        int $location_id,
        string $location_type,
        bool $is_default_warehouse
    ): void {
        if (! $this->isModuleEnabled($business_id)) {
            return;
        }

        $current_default = $this->getWarehouseSettings($business_id)['default_warehouse_location_id'] ?? null;
        $is_current_default = (int) $current_default === $location_id;

        if ($location_type !== self::TYPE_WAREHOUSE) {
            if ($is_current_default) {
                $this->assignFallbackDefaultWarehouse($business_id, $location_id);
            }

            return;
        }

        if ($is_default_warehouse) {
            $this->saveWarehouseSettings($business_id, [
                'default_warehouse_location_id' => $location_id,
            ]);

            return;
        }

        if ($is_current_default) {
            $this->assignFallbackDefaultWarehouse($business_id, $location_id);
        }
    }

    protected function assignFallbackDefaultWarehouse(int $business_id, int $exclude_id): void
    {
        $ids = array_values(array_filter(
            $this->getWarehouseLocationIds($business_id),
            fn ($id) => (int) $id !== $exclude_id
        ));

        $this->saveWarehouseSettings($business_id, [
            'default_warehouse_location_id' => ! empty($ids) ? (int) $ids[0] : null,
        ]);
    }

    public function getWarehouseSettings(int $business_id): array
    {
        $business = Business::find($business_id);

        if (empty($business)) {
            return [];
        }

        $common_settings = $business->common_settings ?? [];

        return $common_settings['warehouse_settings'] ?? [];
    }

    public function saveWarehouseSettings(int $business_id, array $settings): void
    {
        $business = Business::findOrFail($business_id);
        $common_settings = $business->common_settings ?? [];
        $common_settings['warehouse_settings'] = array_merge(
            $common_settings['warehouse_settings'] ?? [],
            $settings
        );
        $business->common_settings = $common_settings;
        $business->save();
    }
}
