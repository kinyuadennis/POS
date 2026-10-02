<?php

namespace App\Exports;

use App\Exports\Concerns\ProductImportExportHeaderStyles;
use App\Product;
use App\Transaction;
use App\Utils\ProductImportExportColumns;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromArray, ShouldAutoSize, WithStyles
{
    use ProductImportExportHeaderStyles;

    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $business_id = request()->session()->get('user.business_id');
        $productUtil = app(ProductUtil::class);
        $req = $this->request ?? request();

        $query = Product::where('business_id', $business_id)
                    ->with([
                        'brand',
                        'unit',
                        'category',
                        'sub_category',
                        'product_variations',
                        'product_variations.variations',
                        'product_locations',
                        'variations',
                    ])
                    ->select('products.*');

        if ($req->boolean('hide_not_for_sale') || $req->get('hide_not_for_sale') == '1' || $req->get('hide_not_for_sale') == 'true') {
            $query->where('products.not_for_selling', 0);
        }

        if ($req->has('category_id') && !empty($req->get('category_id'))) {
            $query->where('products.category_id', $req->get('category_id'));
        }
        if ($req->has('brand_id') && !empty($req->get('brand_id'))) {
            $query->where('products.brand_id', $req->get('brand_id'));
        }
        if ($req->has('unit_id') && !empty($req->get('unit_id'))) {
            $query->where('products.unit_id', $req->get('unit_id'));
        }
        if ($req->has('tax_id') && !empty($req->get('tax_id'))) {
            $query->where('products.tax', $req->get('tax_id'));
        }
        if ($req->has('type') && !empty($req->get('type'))) {
            $query->where('products.type', $req->get('type'));
        }
        if ($req->has('active_state') && !empty($req->get('active_state'))) {
            if ($req->get('active_state') == 'active') {
                $query->where('products.is_inactive', 0);
            } elseif ($req->get('active_state') == 'inactive') {
                $query->where('products.is_inactive', 1);
            }
        }
        if ($req->has('location_id') && !empty($req->get('location_id')) && $req->get('location_id') != 'none') {
            $location_id = $req->get('location_id');
            $query->whereHas('product_locations', function ($q) use ($location_id) {
                $q->where('product_locations.location_id', '=', $location_id);
            });
        }

        $products = $query->get();

        $product_ids = $products->pluck('id');

        $current_stock_by_product = VariationLocationDetails::whereIn('product_id', $product_ids)
            ->selectRaw('product_id, SUM(qty_available) as qty')
            ->groupBy('product_id')
            ->pluck('qty', 'product_id');

        $opening_by_product = Transaction::where('business_id', $business_id)
            ->where('type', 'opening_stock')
            ->whereIn('opening_stock_product_id', $product_ids)
            ->with(['purchase_lines', 'location'])
            ->get()
            ->groupBy('opening_stock_product_id');

        $hidden_cols = [];
        if ($req->has('hidden_cols') && !empty($req->get('hidden_cols'))) {
            $hidden_cols = array_filter(array_map('trim', explode(',', $req->get('hidden_cols'))));
        }

        $all_columns = [
            'name' => [
                'header' => 'NAME',
                'keys' => ['name', '2'],
                'val' => fn($product, $data) => $product->name,
            ],
            'brand' => [
                'header' => 'BRAND',
                'keys' => ['brand', '10'],
                'val' => fn($product, $data) => $product->brand->name ?? '',
            ],
            'unit' => [
                'header' => 'UNIT',
                'keys' => ['unit'],
                'val' => fn($product, $data) => $product->unit->short_name ?? '',
            ],
            'category' => [
                'header' => 'CATEGORY',
                'keys' => ['category', '9'],
                'val' => fn($product, $data) => $product->category->name ?? '',
            ],
            'sku' => [
                'header' => 'SKU',
                'keys' => ['sku', '12'],
                'val' => fn($product, $data) => $product->sku,
            ],
            'retail_price' => [
                'header' => 'RETAIL PRICE',
                'keys' => ['retail_price', 'selling_price', '4'],
                'val' => fn($product, $data) => $data['selling_price'],
            ],
            'wholesale_price' => [
                'header' => 'WHOLESALE PRICE',
                'keys' => ['wholesale_price', '3'],
                'val' => fn($product, $data) => $data['wholesale_price'],
            ],
            'opening_stock' => [
                'header' => 'OPENING STOCK',
                'keys' => ['opening_stock', '6'],
                'val' => fn($product, $data) => $data['opening_stock'],
            ],
            'current_stock' => [
                'header' => 'CURRENT STOCK',
                'keys' => ['current_stock', '7'],
                'val' => fn($product, $data) => $data['current_stock'],
            ],
            'opening_stock_location' => [
                'header' => 'OPENING STOCK LOCATION',
                'keys' => ['opening_stock_location', 'opening_stock', '6'],
                'val' => fn($product, $data) => $data['opening_stock_location'],
            ],
            'product_locations' => [
                'header' => 'PRODUCT LOCATIONS',
                'keys' => ['product_locations', 'locations', '11'],
                'val' => fn($product, $data) => $data['locations'],
            ],
            'manage_stock' => [
                'header' => 'MANAGE STOCK (1=yes 0=No)',
                'keys' => ['manage_stock'],
                'val' => fn($product, $data) => $product->enable_stock,
            ],
            'alert_quantity' => [
                'header' => 'ALERT QUANTITY',
                'keys' => ['alert_quantity'],
                'val' => fn($product, $data) => $product->alert_quantity,
            ],
            'purchase_price' => [
                'header' => 'PURCHASE PRICE',
                'keys' => ['purchase_price', '5'],
                'val' => fn($product, $data) => $data['purchase_price'],
            ],
        ];

        $active_columns = array_filter($all_columns, function ($col) use ($hidden_cols) {
            return empty(array_intersect($col['keys'], $hidden_cols));
        });

        $products_array = [array_values(array_map(fn($col) => $col['header'], $active_columns))];

        foreach ($products as $product) {
            $product_variation = $product->product_variations->first();
            $variation = $product_variation?->variations->first();

            $purchase_price = $variation?->default_purchase_price ?? $variation?->dpp_inc_tax ?? '';
            $selling_price = $variation?->default_sell_price ?? $variation?->sell_price_inc_tax ?? '';
            $raw_wholesale_price = $variation ? $productUtil->getWholesalePrice($variation->id, $business_id) : 0;
            $wholesale_price = ! empty($raw_wholesale_price) ? $raw_wholesale_price : '';

            if ($product->type == 'variable' && $product_variation) {
                $purchase_prices = $product_variation->variations->pluck('default_purchase_price')->filter(function ($price) {
                    return $price !== null && $price !== '';
                });
                $selling_prices = $product_variation->variations->pluck('default_sell_price')->filter(function ($price) {
                    return $price !== null && $price !== '';
                });
                $wholesale_prices = $product_variation->variations->map(function ($v) use ($productUtil, $business_id) {
                    $wp = $productUtil->getWholesalePrice($v->id, $business_id);

                    return ! empty($wp) ? $wp : null;
                })->filter(function ($price) {
                    return $price !== null && $price !== '';
                });

                if ($purchase_prices->isNotEmpty()) {
                    $purchase_price = $purchase_prices->implode('|');
                }
                if ($selling_prices->isNotEmpty()) {
                    $selling_price = $selling_prices->implode('|');
                }
                if ($wholesale_prices->isNotEmpty()) {
                    $wholesale_price = $wholesale_prices->implode('|');
                }
            }

            [$opening_stock, $opening_stock_location] = $this->getOpeningStockExportValues(
                $opening_by_product->get($product->id) ?? $opening_by_product->get((string) $product->id),
                $product
            );

            $current_stock = '';
            if ((int) $product->enable_stock === 1) {
                $current_stock = (float) ($current_stock_by_product->get($product->id)
                    ?? $current_stock_by_product->get((string) $product->id)
                    ?? 0);
            }

            $locations = implode(',', $product->product_locations->pluck('name')->toArray());

            $data_context = [
                'selling_price' => $selling_price,
                'wholesale_price' => $wholesale_price,
                'opening_stock' => $opening_stock,
                'current_stock' => $current_stock,
                'opening_stock_location' => $opening_stock_location,
                'locations' => $locations,
                'purchase_price' => $purchase_price,
            ];

            $row_data = [];
            foreach ($active_columns as $col) {
                $row_data[] = ($col['val'])($product, $data_context);
            }

            $products_array[] = $row_data;
        }

        return $products_array;
    }

    /**
     * Original opening-stock lot quantity and location (not remaining or current stock).
     *
     * @return array{0: string|float, 1: string}
     */
    private function getOpeningStockExportValues($opening_transactions, Product $product): array
    {
        if (empty($opening_transactions) || $opening_transactions->isEmpty()) {
            return ['', ''];
        }

        $locations_by_id = $product->product_locations->keyBy('id');

        foreach ($opening_transactions as $transaction) {
            $qty = (float) $transaction->purchase_lines->sum('quantity');
            if ($qty <= 0) {
                continue;
            }

            $location_name = $transaction->location->name
                ?? $locations_by_id->get($transaction->location_id)?->name
                ?? '';

            return [$qty, $location_name];
        }

        return ['', ''];
    }

    public function styles(Worksheet $sheet)
    {
        return $this->productImportExportHeaderStyles();
    }
}
