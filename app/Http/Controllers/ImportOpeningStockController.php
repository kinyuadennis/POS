<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Exports\OpeningStockTemplateExport;
use App\Product;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Variation;
use DB;
use Excel;
use Illuminate\Http\Request;

class ImportOpeningStockController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('product.opening_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        $date_formats = Business::date_formats();
        $date_format = session('business.date_format');
        $date_format = isset($date_formats[$date_format]) ? $date_formats[$date_format] : $date_format;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $notification = ['success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $selected_location_id = request()->get('location_id');

            return view('import_opening_stock.index')
                ->with(compact('notification', 'date_format', 'business_locations', 'selected_location_id'));
        } else {
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $selected_location_id = request()->get('location_id');

            return view('import_opening_stock.index')
                ->with(compact('date_format', 'business_locations', 'selected_location_id'));
        }
    }

    /**
     * Download opening stock template pre-filled with products for a location.
     */
    public function downloadTemplate(Request $request)
    {
        if (! auth()->user()->can('product.opening_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = (int) $request->input('location_id');

        $location = BusinessLocation::where('business_id', $business_id)
            ->where('id', $location_id)
            ->firstOrFail();

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all' && ! in_array($location_id, $permitted_locations)) {
            abort(403, 'Unauthorized action.');
        }

        $variations = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
            ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->where('p.business_id', $business_id)
            ->where('pl.location_id', $location_id)
            ->where('p.enable_stock', 1)
            ->whereIn('p.type', ['single', 'variable'])
            ->whereNull('variations.deleted_at')
            ->orderBy('p.name')
            ->select(
                'p.name as product_name',
                'p.type',
                'pv.name as product_variation',
                'variations.name as variation_name',
                'variations.sub_sku',
                'variations.default_purchase_price',
                'variations.dpp_inc_tax',
                DB::raw('COALESCE(vld.qty_available, 0) as current_stock'),
                DB::raw('(SELECT COALESCE(SUM(osl.quantity), 0) FROM purchase_lines as osl
                    INNER JOIN transactions as ost ON ost.id = osl.transaction_id
                    WHERE ost.type = "opening_stock"
                    AND ost.status = "received"
                    AND ost.business_id = '.$business_id.'
                    AND ost.location_id = '.$location_id.'
                    AND osl.variation_id = variations.id) as opening_stock')
            )
            ->get();

        $rows = [];
        foreach ($variations as $variation) {
            $display_name = $variation->product_name;
            if ($variation->type == 'variable') {
                $display_name .= ' - '.$variation->product_variation.' - '.$variation->variation_name;
            }

            $unit_cost = $variation->default_purchase_price;
            if ($unit_cost === null || $unit_cost === '') {
                $unit_cost = $variation->dpp_inc_tax ?? 0;
            }

            $opening_qty_str = ((float) $variation->opening_stock > 0)
                ? (string) $this->productUtil->num_f($variation->opening_stock, false)
                : '';

            $current_qty_str = ($variation->current_stock !== null && $variation->current_stock !== '')
                ? (string) $this->productUtil->num_f($variation->current_stock, false)
                : '';

            $rows[] = [
                $display_name,
                $variation->sub_sku,
                $location->name,
                $opening_qty_str,
                $current_qty_str,
                $unit_cost,
            ];
        }

        $filename = 'opening_stock_'.$location->location_id.'_'.date('Y-m-d').'.xlsx';

        return Excel::download(new OpeningStockTemplateExport($rows), $filename);
    }

    /**
     * Map opening stock import columns from the header row.
     */
    private function mapOpeningStockColumns(array $header_row): array
    {
        $defaults = [
            'product_name' => 0,
            'sku' => 1,
            'location' => 2,
            'quantity' => 3,
            'current_stock' => 4,
            'unit_cost' => 5,
        ];

        if (empty($header_row)) {
            return $defaults;
        }

        $map = [];
        foreach ($header_row as $index => $header) {
            $h = strtoupper(trim((string) $header));
            if (str_contains($h, 'PRODUCT') && str_contains($h, 'NAME')) {
                $map['product_name'] = $index;
            } elseif (str_contains($h, 'SKU')) {
                $map['sku'] = $index;
            } elseif (str_contains($h, 'LOCATION')) {
                $map['location'] = $index;
            } elseif (str_contains($h, 'CURRENT') && str_contains($h, 'STOCK')) {
                $map['current_stock'] = $index;
            } elseif ((str_contains($h, 'OPENING') && str_contains($h, 'STOCK')) || str_contains($h, 'QUANTITY')) {
                $map['quantity'] = $index;
            } elseif (str_contains($h, 'PURCHASE') || str_contains($h, 'UNIT COST') || str_contains($h, 'COST') || str_contains($h, 'PRICE')) {
                $map['unit_cost'] = $index;
            }
        }

        return array_merge($defaults, $map);
    }

    private function openingStockCell(array $row, int $index): string
    {
        if (! array_key_exists($index, $row)) {
            return '';
        }

        $value = $row[$index];
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    /**
     * Store imported opening stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('product.opening_stock')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum time limit
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '-1');

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');
                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_key_exists(0, $parsed_array) ? $parsed_array[0] : [];

                $header_row = array_shift($imported_data);
                $columns = $this->mapOpeningStockColumns($header_row ?? []);

                $business_id = $request->session()->get('user.business_id');

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();

                $rows_imported = 0;
                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 2;

                    //Check for empty row
                    if (array_filter($value) == []) {
                        continue;
                    }

                    $sku = $this->openingStockCell($value, $columns['sku']);
                    $quantity = $this->openingStockCell($value, $columns['quantity']);

                    if ($sku !== '') {
                        $product_info = Variation::where('sub_sku', $sku)
                                ->join('products AS P', 'variations.product_id', '=', 'P.id')
                                ->leftjoin('tax_rates AS TR', 'P.tax', 'TR.id')
                                ->where('P.business_id', $business_id)
                                ->select(['P.id', 'P.name as product_name', 'variations.id as variation_id',
                                    'P.enable_stock', 'TR.amount as tax_percent',
                                    'TR.id as tax_id', 'variations.default_purchase_price', 'variations.dpp_inc_tax', ])
                                ->first();
                        if (empty($product_info)) {
                            $is_valid = false;
                            $error_msg = "Product with sku $sku not found in row no. $row_no";
                            break;
                        } elseif ($product_info->enable_stock == 0) {
                            $is_valid = false;
                            $error_msg = "Manage Stock not enabled for the product with $sku in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "PRODUCT SKU is required in row no. $row_no";
                        break;
                    }

                    // Amend Product Name if modified in Excel
                    $product_name_input = $this->openingStockCell($value, $columns['product_name']);
                    if ($product_name_input !== '' && $product_name_input !== $product_info->product_name) {
                        $existing_product = \App\Product::find($product_info->id);
                        if ($existing_product) {
                            $existing_product->name = $product_name_input;
                            $existing_product->save();
                        }
                    }

                    //Get location details.
                    $location_name = $this->openingStockCell($value, $columns['location']);
                    if ($location_name !== '') {
                        $location = BusinessLocation::where('name', $location_name)
                                            ->where('business_id', $business_id)
                                            ->first();
                        if (empty($location)) {
                            $is_valid = false;
                            $error_msg = "Location with name '$location_name' not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $location = BusinessLocation::where('business_id', $business_id)->first();
                    }

                    $unit_cost_raw = $this->openingStockCell($value, $columns['unit_cost']);
                    if ($unit_cost_raw === '') {
                        if ($product_info->default_purchase_price !== null && $product_info->default_purchase_price !== '') {
                            $unit_cost_raw = (string) $product_info->default_purchase_price;
                        } elseif ($product_info->dpp_inc_tax !== null && $product_info->dpp_inc_tax !== '') {
                            $unit_cost_raw = (string) $product_info->dpp_inc_tax;
                        } else {
                            $unit_cost_raw = '0';
                        }
                    }

                    $unit_cost_before_tax = $this->productUtil->num_uf($unit_cost_raw);
                    $parsed_quantity = $this->productUtil->num_uf($quantity);

                    if (! is_numeric($unit_cost_before_tax)) {
                        $is_valid = false;
                        $error_msg = "Invalid PURCHASE PRICE '$unit_cost_raw' in row no. $row_no";
                        break;
                    }

                    if (! is_numeric($parsed_quantity)) {
                        $is_valid = false;
                        $error_msg = "Invalid QUANTITY '$quantity' in row no. $row_no";
                        break;
                    }

                    $opening_stock = [
                        'quantity' => $parsed_quantity,
                        'location_id' => $location->id,
                    ];

                    //Check for tra, location_id, opening_stock_product_id, type=opening stock.
                    $os_transaction = Transaction::where('business_id', $business_id)
                            ->where('location_id', $location->id)
                            ->where('type', 'opening_stock')
                            ->where('opening_stock_product_id', $product_info->id)
                            ->first();

                    $this->addOpeningStock($opening_stock, $product_info, $business_id, $unit_cost_before_tax, $os_transaction);
                    $rows_imported++;
                }
            }

            if (! $is_valid) {
                throw new \Exception($error_msg);
            }

            if ($rows_imported === 0) {
                throw new \Exception(__('lang_v1.opening_stock_no_rows_imported'));
            }

            $output = ['success' => 1,
                'msg' => __('product.file_imported_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => 'Message:'.$e->getMessage(),
            ];

            return redirect('import-opening-stock')->with('notification', $output);
        }

        return redirect('import-opening-stock')->with('status', $output);
    }

    /**
     * Adds or amends opening stock of a single product
     *
     * @param  array  $opening_stock
     * @param  obj  $product
     * @param  int  $business_id
     * @return void
     */
    private function addOpeningStock($opening_stock, $product, $business_id, $unit_cost_before_tax, $transaction = null)
    {
        $user_id = request()->session()->get('user.id');

        $transaction_date = request()->session()->get('financial_year.start');
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        //Get product tax
        $tax_percent = ! empty($product->tax_percent) ? $product->tax_percent : 0;
        $tax_id = ! empty($product->tax_id) ? $product->tax_id : null;

        $item_tax = $this->productUtil->calc_percentage($unit_cost_before_tax, $tax_percent);

        $new_qty = $this->productUtil->num_uf($opening_stock['quantity']);
        $unit_cost_uf = $this->productUtil->num_uf($unit_cost_before_tax);
        $item_tax_uf = $this->productUtil->num_uf($item_tax);

        $total_before_trans_tax = $new_qty * ($unit_cost_uf + $item_tax_uf);

        if (empty($transaction)) {
            $transaction = new Transaction();
            $transaction->type = 'opening_stock';
            $transaction->status = 'received';
            $transaction->opening_stock_product_id = $product->id;
            $transaction->business_id = $business_id;
            $transaction->transaction_date = $transaction_date;
            $transaction->location_id = $opening_stock['location_id'];
            $transaction->payment_status = 'paid';
            $transaction->created_by = $user_id;
            $transaction->total_before_tax = $total_before_trans_tax;
            $transaction->final_total = $total_before_trans_tax;
            $transaction->save();

            $transaction->purchase_lines()->create([
                'product_id' => $product->id,
                'variation_id' => $product->variation_id,
                'quantity' => $new_qty,
                'pp_without_discount' => $unit_cost_before_tax,
                'item_tax' => $item_tax,
                'tax_id' => $tax_id,
                'purchase_price' => $unit_cost_before_tax,
                'purchase_price_inc_tax' => $unit_cost_before_tax + $item_tax,
            ]);

            $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $product->variation_id, $new_qty, 0, null, false);
        } else {
            // Existing opening stock transaction found - Amend/Overwrite
            $existing_line = \App\PurchaseLine::where('transaction_id', $transaction->id)
                ->where('variation_id', $product->variation_id)
                ->first();

            if (! empty($existing_line)) {
                $old_qty = $existing_line->quantity;

                $existing_line->quantity = $new_qty;
                $existing_line->pp_without_discount = $unit_cost_before_tax;
                $existing_line->item_tax = $item_tax;
                $existing_line->tax_id = $tax_id;
                $existing_line->purchase_price = $unit_cost_before_tax;
                $existing_line->purchase_price_inc_tax = $unit_cost_before_tax + $item_tax;
                $existing_line->save();

                // Update location stock balance using delta (new_qty - old_qty)
                $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $product->variation_id, $new_qty, $old_qty, null, false);
            } else {
                $transaction->purchase_lines()->create([
                    'product_id' => $product->id,
                    'variation_id' => $product->variation_id,
                    'quantity' => $new_qty,
                    'pp_without_discount' => $unit_cost_before_tax,
                    'item_tax' => $item_tax,
                    'tax_id' => $tax_id,
                    'purchase_price' => $unit_cost_before_tax,
                    'purchase_price_inc_tax' => $unit_cost_before_tax + $item_tax,
                ]);

                $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $product->variation_id, $new_qty, 0, null, false);
            }

            // Recalculate transaction totals
            $new_transaction_total = 0;
            $all_lines = \App\PurchaseLine::where('transaction_id', $transaction->id)->get();
            foreach ($all_lines as $line) {
                $new_transaction_total += ($line->quantity * $line->purchase_price_inc_tax);
            }
            $transaction->total_before_tax = $new_transaction_total;
            $transaction->final_total = $new_transaction_total;
            $transaction->save();
        }

        // Synchronize Master Variation Purchase Price & Margin
        $this->productUtil->updateProductFromPurchase([
            'variation_id' => $product->variation_id,
            'pp_without_discount' => $unit_cost_before_tax,
        ]);
    }
}
