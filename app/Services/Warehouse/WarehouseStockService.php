<?php

namespace App\Services\Warehouse;

use App\Product;
use DB;
use Illuminate\Database\Eloquent\Builder;

class WarehouseStockService
{
    /**
     * Products assigned to a location, including zero-stock items.
     */
    public function getAssignedStockQuery(int $business_id, int $location_id): Builder
    {
        $query = Product::query()
            ->join('variations', 'variations.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->join('product_locations as pl', 'pl.product_id', '=', 'products.id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->join('business_locations as l', 'l.id', '=', 'pl.location_id')
            ->leftJoin('categories as c', 'products.category_id', '=', 'c.id')
            ->where('products.business_id', $business_id)
            ->whereIn('products.type', ['single', 'variable'])
            ->where('pl.location_id', $location_id)
            ->whereNull('variations.deleted_at');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all' && ! in_array($location_id, $permitted_locations)) {
            $query->whereRaw('1 = 0');
        }

        return $query->select(
            'variations.sub_sku as sku',
            'products.name as product',
            'products.type',
            'products.enable_stock',
            'products.id as product_id',
            'units.short_name as unit',
            'pv.name as product_variation',
            'variations.name as variation_name',
            'l.name as location_name',
            'l.id as location_id',
            'variations.id as variation_id',
            'c.name as category_name',
            DB::raw('COALESCE(vld.qty_available, 0) as stock')
        )->groupBy('variations.id');
    }

    public function countAssignedProducts(array $warehouse_location_ids): int
    {
        if (empty($warehouse_location_ids)) {
            return 0;
        }

        return DB::table('product_locations')
            ->join('products', 'products.id', '=', 'product_locations.product_id')
            ->whereIn('product_locations.location_id', $warehouse_location_ids)
            ->where('products.is_inactive', 0)
            ->whereIn('products.type', ['single', 'variable'])
            ->distinct()
            ->count('product_locations.product_id');
    }
}
