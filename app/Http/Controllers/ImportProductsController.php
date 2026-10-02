<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Exports\ProductsImportTemplateExport;
use App\Product;
use App\PurchaseLine;
use App\Transaction;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductImportExportColumns;
use App\Utils\ProductUtil;
use App\Variation;
use DB;
use Excel;
use Illuminate\Http\Request;

class ImportProductsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = ['success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];

            return view('import_products.index')
                ->with('notification', $output);
        } else {
            return view('import_products.index');
        }
    }

    /**
     * Download the product import template (XLSX — same formats accepted by import: CSV, XLS, XLSX).
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $filename = 'product-import-template-'.\Carbon::now()->format('Y-m-d').'.xlsx';

        return Excel::download(new ProductsImportTemplateExport, $filename);
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $update_existing = $request->boolean('update_existing');
            $updated_count = 0;
            $created_count = 0;

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row and resolve column indices dynamically from headers
                $headers_raw = $parsed_array[0][0] ?? [];
                $imported_data = array_splice($parsed_array[0], 1);

                $col_map = [
                    'name' => ProductImportExportColumns::COL_NAME,
                    'brand' => ProductImportExportColumns::COL_BRAND,
                    'unit' => ProductImportExportColumns::COL_UNIT,
                    'category' => ProductImportExportColumns::COL_CATEGORY,
                    'sku' => ProductImportExportColumns::COL_SKU,
                    'manage_stock' => ProductImportExportColumns::COL_MANAGE_STOCK,
                    'alert_quantity' => ProductImportExportColumns::COL_ALERT_QUANTITY,
                    'purchase_price' => ProductImportExportColumns::COL_PURCHASE_PRICE,
                    'retail_price' => ProductImportExportColumns::COL_RETAIL_PRICE,
                    'wholesale_price' => ProductImportExportColumns::COL_WHOLESALE_PRICE,
                    'opening_stock' => ProductImportExportColumns::COL_OPENING_STOCK,
                    'current_stock' => ProductImportExportColumns::COL_CURRENT_STOCK,
                    'opening_stock_location' => ProductImportExportColumns::COL_OPENING_STOCK_LOCATION,
                    'product_locations' => ProductImportExportColumns::COL_PRODUCT_LOCATIONS,
                ];

                if (! empty($headers_raw) && is_array($headers_raw)) {
                    foreach ($headers_raw as $idx => $h) {
                        $clean_h = preg_replace('/[^a-z0-9]/', '_', strtolower(trim((string) $h)));
                        if (str_contains($clean_h, 'product_name') || $clean_h === 'name') {
                            $col_map['name'] = $idx;
                        } elseif (str_contains($clean_h, 'opening_stock_location')) {
                            $col_map['opening_stock_location'] = $idx;
                        } elseif (str_contains($clean_h, 'current_stock')) {
                            $col_map['current_stock'] = $idx;
                        } elseif (str_contains($clean_h, 'opening_stock') || str_contains($clean_h, 'initial_stock')) {
                            $col_map['opening_stock'] = $idx;
                        } elseif (str_contains($clean_h, 'manage_stock')) {
                            $col_map['manage_stock'] = $idx;
                        } elseif (str_contains($clean_h, 'alert_quantity')) {
                            $col_map['alert_quantity'] = $idx;
                        } elseif (str_contains($clean_h, 'wholesale_price')) {
                            $col_map['wholesale_price'] = $idx;
                        } elseif (str_contains($clean_h, 'retail_price') || str_contains($clean_h, 'selling_price')) {
                            $col_map['retail_price'] = $idx;
                        } elseif (str_contains($clean_h, 'purchase_price') || str_contains($clean_h, 'cost')) {
                            $col_map['purchase_price'] = $idx;
                        } elseif (str_contains($clean_h, 'product_locations') || str_contains($clean_h, 'business_locations')) {
                            $col_map['product_locations'] = $idx;
                        } elseif ($clean_h === 'brand') {
                            $col_map['brand'] = $idx;
                        } elseif ($clean_h === 'unit') {
                            $col_map['unit'] = $idx;
                        } elseif ($clean_h === 'category') {
                            $col_map['category'] = $idx;
                        } elseif ($clean_h === 'sku') {
                            $col_map['sku'] = $idx;
                        }
                    }
                }

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $default_profit_percent = $request->session()->get('business.default_profit_percent');

                $business = \App\Business::find($business_id);
                $sku_prefix = ! empty($business->sku_prefix) ? trim($business->sku_prefix) : '';

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                //Check if subscribed or not, then check for products quota
                if (! $this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                } elseif (! $this->moduleUtil->isQuotaAvailable('products', $business_id, $total_rows)) {
                    return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action([\App\Http\Controllers\ImportProductsController::class, 'index']));
                }

                $business_locations = BusinessLocation::where('business_id', $business_id)->get();
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 1;

                    $max_col = max(array_values($col_map)) + 1;
                    $value = array_pad(array_values($value), max($max_col, ProductImportExportColumns::COLUMN_COUNT), '');

                    if ($this->cellValue($value[$col_map['name']]) === '') {
                        continue;
                    }

                    if (count($value) < ProductImportExportColumns::COLUMN_COUNT) {
                        $is_valid = false;
                        $error_msg = 'Some of the columns are missing. Please, use latest CSV file template.';
                        break;
                    }

                    $product_array = $this->parseSimplifiedImportRow(
                        $value,
                        $row_no,
                        $business_id,
                        $user_id,
                        $sku_prefix,
                        $default_profit_percent,
                        $update_existing,
                        $business_locations,
                        $col_map
                    );

                    if ($product_array === false) {
                        $is_valid = false;
                        $error_msg = $this->last_import_error;
                        break;
                    }

                    if (! empty($product_array['skip'])) {
                        continue;
                    }

                    $formated_data[] = $product_array;
                }

                if (! $is_valid) {
                    throw new \Exception($error_msg);
                }

                if (! empty($formated_data)) {
                    foreach ($formated_data as $index => $product_data) {
                        $variation_data = $product_data['variation'];
                        unset($product_data['variation']);

                        $wholesale_price = $product_data['wholesale_price'] ?? null;
                        unset($product_data['wholesale_price']);

                        $opening_stock = null;
                        if (! empty($product_data['opening_stock_details'])) {
                            $opening_stock = $product_data['opening_stock_details'];
                        }
                        if (isset($product_data['opening_stock_details'])) {
                            unset($product_data['opening_stock_details']);
                        }

                        //Check if we need to update existing
                        $update_existing_flag = ! empty($product_data['update_existing']);
                        $existing_product_id = $product_data['existing_product_id'] ?? null;
                        $sku_to_update = $product_data['sku_to_update'] ?? null;

                        unset($product_data['update_existing'], $product_data['existing_product_id'], $product_data['sku_to_update']);

                        if ($update_existing_flag && ! empty($existing_product_id)) {
                            $product = Product::where('business_id', $business_id)
                                            ->find($existing_product_id);

                            if ($product) {
                                unset($product_data['created_by'], $product_data['business_id'], $product_data['type']);

                                if (empty(trim($product_data['sku'])) || $product_data['sku'] === ' ') {
                                    unset($product_data['sku']);
                                } elseif (! empty($sku_to_update)) {
                                    $product_data['sku'] = $sku_to_update;
                                }

                                if (empty($product_data['image']) && ! empty($product->image)) {
                                    unset($product_data['image']);
                                }

                                $import_product_locations = $product_data['import_product_locations'] ?? null;
                                unset($product_data['import_product_locations']);

                                $product->update($product_data);
                                $product->refresh();

                                if ($product->type == 'single') {
                                    $variation = Variation::where('product_id', $product->id)->first();
                                    if ($variation) {
                                        $variation->update([
                                            'default_purchase_price' => $this->productUtil->num_uf($variation_data['dpp_exc_tax']),
                                            'dpp_inc_tax' => $this->productUtil->num_uf($variation_data['dpp_inc_tax']),
                                            'profit_percent' => $this->productUtil->num_uf($variation_data['profit_percent']),
                                            'default_sell_price' => $this->productUtil->num_uf($variation_data['dsp_exc_tax']),
                                            'sell_price_inc_tax' => $this->productUtil->num_uf($variation_data['dsp_inc_tax']),
                                            'sub_sku' => $product->sku,
                                        ]);

                                        if ($wholesale_price !== null && $wholesale_price !== '') {
                                            $this->productUtil->saveWholesalePrice($variation->id, $business_id, $wholesale_price);
                                        }
                                    }
                                    if (! empty($opening_stock)) {
                                        $this->addOpeningStock($opening_stock, $product, $business_id);
                                    }
                                } elseif ($product->type == 'variable') {
                                    $this->productUtil->updateVariableProductVariations($product->id, [$variation_data], 'with_out_variation');

                                    if ($wholesale_price !== null && $wholesale_price !== '') {
                                        $wp_array = explode('|', (string) $wholesale_price);
                                        $variations = Variation::where('product_id', $product->id)->get();
                                        foreach ($variations as $idx => $v) {
                                            if (isset($wp_array[$idx]) && $wp_array[$idx] !== '') {
                                                $this->productUtil->saveWholesalePrice($v->id, $business_id, $wp_array[$idx]);
                                            }
                                        }
                                    }

                                    if (! empty($variation_data['opening_stock_location']) && $product->enable_stock == 1) {
                                        $this->addOpeningStockForVariable($variation_data, $product, $business_id);
                                    }
                                }

                                //Product locations
                                if (! empty($import_product_locations)) {
                                    $location_ids = $this->resolveProductLocationIds(
                                        $import_product_locations,
                                        $business_locations,
                                        $business_id
                                    );
                                    if (! empty($location_ids)) {
                                        $product->product_locations()->sync($location_ids);
                                    }
                                }

                                $updated_count++;
                                continue;
                            }
                        }

                        $import_product_locations = $product_data['import_product_locations'] ?? null;
                        unset($product_data['import_product_locations']);

                        //Create new product
                        $product = Product::create($product_data);
                        $created_count++;
                        //If auto generate sku generate new sku
                        if ($product->sku == ' ') {
                            $sku = $this->productUtil->generateProductSku($business_id);
                            $product->sku = $sku;
                            $product->save();
                        }

                        //Product locations
                        if (! empty($import_product_locations)) {
                            $location_ids = $this->resolveProductLocationIds(
                                $import_product_locations,
                                $business_locations,
                                $business_id
                            );
                            if (! empty($location_ids)) {
                                $product->product_locations()->sync($location_ids);
                            }
                        }

                        //Create single product variation
                        if ($product->type == 'single') {
                            $this->productUtil->createSingleProductVariation(
                                $product,
                                $product->sku,
                                $variation_data['dpp_exc_tax'],
                                $variation_data['dpp_inc_tax'],
                                $variation_data['profit_percent'],
                                $variation_data['dsp_exc_tax'],
                                $variation_data['dsp_inc_tax']
                            );

                            if ($wholesale_price !== null && $wholesale_price !== '') {
                                $variation = Variation::where('product_id', $product->id)->first();
                                if ($variation) {
                                    $this->productUtil->saveWholesalePrice($variation->id, $business_id, $wholesale_price);
                                }
                            }

                            if (! empty($opening_stock)) {
                                $this->addOpeningStock($opening_stock, $product, $business_id);
                            }
                        } elseif ($product->type == 'variable') {
                            //Create variable product variations and with_out_variation is sku type of variation

                            $this->productUtil->createVariableProductVariations(
                                $product,
                                [$variation_data],
                                "with_out_variation",
                                $business_id
                            );

                            if (! empty($variation_data['opening_stock_location']) && $product->enable_stock == 1) {
                                $this->addOpeningStockForVariable($variation_data, $product, $business_id);
                            }
                        }
                    }
                }
            }

            $import_message = __('product.file_imported_successfully');
            if ($update_existing) {
                $summary = __('lang_v1.products_import_summary', [
                    'updated' => $updated_count,
                    'created' => $created_count,
                ]);
                $import_message = rtrim($import_message, '.') . '. ' . $summary;
            }

            $output = ['success' => 1,
                'msg' => $import_message,
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];

            return redirect('import-products')->with('notification', $output);
        }

        return redirect('import-products')->with('status', $output);
    }

    private function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin)
    {
        $dpp_exc_tax = $this->productUtil->num_uf($dpp_exc_tax);
        $dpp_inc_tax = $this->productUtil->num_uf($dpp_inc_tax);
        $selling_price = $this->productUtil->num_uf($selling_price);
        $tax_amount = $this->productUtil->num_uf($tax_amount);
        $margin = $this->productUtil->num_uf($margin);

        //Calculate purchase prices
        if ($dpp_inc_tax == 0) {
            $dpp_inc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $tax_amount,
                $dpp_exc_tax
            );
        }

        if ($dpp_exc_tax == 0) {
            $dpp_exc_tax = $this->productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
        }

        if ($selling_price != 0) {
            if ($tax_type == 'inclusive') {
                $dsp_inc_tax = $selling_price;
                $dsp_exc_tax = $this->productUtil->calc_percentage_base(
                    $dsp_inc_tax,
                    $tax_amount
                );
            } else {
                $dsp_exc_tax = $selling_price;
                $dsp_inc_tax = $this->productUtil->calc_percentage(
                    $selling_price,
                    $tax_amount,
                    $selling_price
                );
            }
        } else {
            $dsp_exc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $margin,
                $dpp_exc_tax
            );
            $dsp_inc_tax = $this->productUtil->calc_percentage(
                $dsp_exc_tax,
                $tax_amount,
                $dsp_exc_tax
            );
        }

        return [
            'dpp_exc_tax' => $dpp_exc_tax,
            'dpp_inc_tax' => $dpp_inc_tax,
            'dsp_exc_tax' => $dsp_exc_tax,
            'dsp_inc_tax' => $dsp_inc_tax,
        ];
    }

    /**
     * Adds opening stock of a single product
     *
     * @param  array  $opening_stock
     * @param  obj  $product
     * @param  int  $business_id
     * @return void
     */
    private function addOpeningStock($opening_stock, $product, $business_id)
    {
        $user_id = request()->session()->get('user.id');

        $variation = Variation::where('product_id', $product->id)
            ->first();

        $qty = $this->productUtil->num_uf($opening_stock['quantity']);
        $dpp_inc_tax = $this->productUtil->num_uf($variation->dpp_inc_tax);
        $total_before_tax = $qty * $dpp_inc_tax;

        $transaction_date = request()->session()->get('financial_year.start');
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        $transaction = Transaction::where('opening_stock_product_id', $product->id)
            ->where('type', 'opening_stock')
            ->first();

        if (empty($transaction)) {
            //Add opening stock transaction
            $transaction = Transaction::create(
                [
                    'type' => 'opening_stock',
                    'opening_stock_product_id' => $product->id,
                    'status' => 'received',
                    'business_id' => $business_id,
                    'transaction_date' => $transaction_date,
                    'total_before_tax' => $total_before_tax,
                    'location_id' => $opening_stock['location_id'],
                    'final_total' => $total_before_tax,
                    'payment_status' => 'paid',
                    'created_by' => $user_id,
                ]
            );
        } else {
            $transaction->total_before_tax = $total_before_tax;
            $transaction->final_total = $total_before_tax;
            $transaction->save();
        }

        //Get product tax
        $tax_percent = ! empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
        $tax_id = ! empty($product->product_tax->id) ? $product->product_tax->id : null;

        $item_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);

        $purchase_line = \App\PurchaseLine::where('transaction_id', $transaction->id)
            ->where('product_id', $product->id)
            ->where('variation_id', $variation->id)
            ->first();

        $old_qty = ! empty($purchase_line) ? $purchase_line->quantity : 0;

        if (empty($purchase_line)) {
            //Create purchase line
            $transaction->purchase_lines()->create([
                'product_id' => $product->id,
                'variation_id' => $variation->id,
                'quantity' => $opening_stock['quantity'],
                'item_tax' => $item_tax,
                'tax_id' => $tax_id,
                'pp_without_discount' => $variation->default_purchase_price,
                'purchase_price' => $variation->default_purchase_price,
                'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                'exp_date' => ! empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null,
            ]);
        } else {
            $purchase_line->quantity = $opening_stock['quantity'];
            $purchase_line->purchase_price = $variation->default_purchase_price;
            $purchase_line->purchase_price_inc_tax = $variation->dpp_inc_tax;
            $purchase_line->save();
        }

        //Update variation location details
        $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $variation->id, $opening_stock['quantity'], $old_qty);

        //Add product location
        $this->__addProductLocation($product, $opening_stock['location_id']);
    }

    private function __addProductLocation($product, $location_id)
    {
        $count = DB::table('product_locations')->where('product_id', $product->id)
                                            ->where('location_id', $location_id)
                                            ->count();
        if ($count == 0) {
            DB::table('product_locations')->insert(['product_id' => $product->id,
                'location_id' => $location_id, ]);
        }
    }

    private function addOpeningStockForVariable($variations, $product, $business_id)
    {
        $user_id = request()->session()->get('user.id');

        $transaction_date = request()->session()->get('financial_year.start');
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        $total_before_tax = 0;
        $location_id = $variations['opening_stock_location'];
        if (isset($variations['variations'][0]['opening_stock'])) {
            //Add opening stock transaction
            $transaction = Transaction::create(
                [
                    'type' => 'opening_stock',
                    'opening_stock_product_id' => $product->id,
                    'status' => 'received',
                    'business_id' => $business_id,
                    'transaction_date' => $transaction_date,
                    'total_before_tax' => $total_before_tax,
                    'location_id' => $location_id,
                    'final_total' => $total_before_tax,
                    'payment_status' => 'paid',
                    'created_by' => $user_id,
                ]
            );

            //Add product location
            $this->__addProductLocation($product, $location_id);

            foreach ($variations['variations'] as $variation_os) {
                if (! empty($variation_os['opening_stock'])) {
                    $variation = Variation::where('product_id', $product->id)
                                    ->where('name', $variation_os['value'])
                                    ->first();
                    if (! empty($variation)) {
                        $opening_stock = [
                            'quantity' => $variation_os['opening_stock'],
                            'exp_date' => $variation_os['opening_stock_exp_date'],
                        ];

                        $os_qty = $this->productUtil->num_uf($variation_os['opening_stock']);
                        $dpp_inc_tax = $this->productUtil->num_uf($variation->dpp_inc_tax);
                        $total_before_tax = $total_before_tax + ($os_qty * $dpp_inc_tax);
                    }

                    //Get product tax
                    $tax_percent = ! empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
                    $tax_id = ! empty($product->product_tax->id) ? $product->product_tax->id : null;

                    $item_tax = $this->productUtil->calc_percentage($variation->default_purchase_price, $tax_percent);

                    //Create purchase line
                    $transaction->purchase_lines()->create([
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'quantity' => $opening_stock['quantity'],
                        'item_tax' => $item_tax,
                        'tax_id' => $tax_id,
                        'purchase_price' => $variation->default_purchase_price,
                        'purchase_price_inc_tax' => $variation->dpp_inc_tax,
                        'exp_date' => ! empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null,
                    ]);
                    //Update variation location details
                    $this->productUtil->updateProductQuantity($location_id, $product->id, $variation->id, $opening_stock['quantity']);
                }
            }

            $transaction->total_before_tax = $total_before_tax;
            $transaction->final_total = $total_before_tax;
            $transaction->save();
        }
    }

    private function rackDetails($rack_value, $row_value, $position_value, $business_id, $product_id, $row_no)
    {
        if (! empty($rack_value) || ! empty($row_value) || ! empty($position_value)) {
            $locations = BusinessLocation::forDropdown($business_id);
            $loc_count = count($locations);

            $racks = explode('|', $rack_value);
            $rows = explode('|', $row_value);
            $position = explode('|', $position_value);

            if (count($racks) > $loc_count) {
                $error_msg = "Invalid value for RACK in row no. $row_no";
                throw new \Exception($error_msg);
            }

            if (count($rows) > $loc_count) {
                $error_msg = "Invalid value for ROW in row no. $row_no";
                throw new \Exception($error_msg);
            }

            if (count($position) > $loc_count) {
                $error_msg = "Invalid value for POSITION in row no. $row_no";
                throw new \Exception($error_msg);
            }

            $rack_details = [];
            $counter = 0;
            foreach ($locations as $key => $value) {
                $rack_details[$key]['rack'] = isset($racks[$counter]) ? $racks[$counter] : '';
                $rack_details[$key]['row'] = isset($rows[$counter]) ? $rows[$counter] : '';
                $rack_details[$key]['position'] = isset($position[$counter]) ? $position[$counter] : '';
                $counter += 1;
            }

            if (! empty($rack_details)) {
                $this->productUtil->addRackDetails($business_id, $product_id, $rack_details);
            }
        }
    }

    /**
     * Find an existing product to update during import.
     *
     * @param  int  $business_id
     * @param  string  $product_name
     * @param  string  $sku
     * @param  string  $sku_prefix
     * @return array|null
     */
    private function findExistingProductForUpdate($business_id, $product_name, $sku, $sku_prefix)
    {
        $sku = trim($sku);

        if (! empty($sku)) {
            $product = Product::where('business_id', $business_id)
                            ->where('sku', $sku)
                            ->first();
            if ($product) {
                return ['product' => $product, 'sku_to_update' => null];
            }

            $product = Product::where('business_id', $business_id)
                            ->whereHas('variations', function ($query) use ($sku) {
                                $query->where('sub_sku', $sku);
                            })
                            ->first();
            if ($product) {
                return ['product' => $product, 'sku_to_update' => $sku];
            }

            if (! empty($sku_prefix) && stripos($sku, $sku_prefix) === 0) {
                $stripped_sku = substr($sku, strlen($sku_prefix));
                $product = Product::where('business_id', $business_id)
                                ->where('sku', $stripped_sku)
                                ->first();
                if ($product) {
                    return ['product' => $product, 'sku_to_update' => $sku];
                }
            }

            if (! empty($sku_prefix)) {
                $prefixed_sku = $sku_prefix.$sku;
                $product = Product::where('business_id', $business_id)
                                ->where('sku', $prefixed_sku)
                                ->first();
                if ($product) {
                    return ['product' => $product, 'sku_to_update' => $sku];
                }
            }

            $product = Product::where('business_id', $business_id)
                            ->whereRaw('LOWER(sku) = ?', [strtolower($sku)])
                            ->first();
            if ($product) {
                return ['product' => $product, 'sku_to_update' => $sku];
            }
        }

        if (! empty($product_name)) {
            $product = Product::where('business_id', $business_id)
                            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($product_name))])
                            ->orderBy('id', 'asc')
                            ->first();
            if ($product) {
                $sku_to_update = ! empty($sku) ? $sku : null;

                return ['product' => $product, 'sku_to_update' => $sku_to_update];
            }
        }

        return null;
    }

    private $last_import_error = '';

    /**
     * Parse one simplified import row into product data.
     *
     * @return array|false
     */
    private function parseSimplifiedImportRow(
        array $value,
        int $row_no,
        int $business_id,
        int $user_id,
        string $sku_prefix,
        $default_profit_percent,
        bool $update_existing,
        $business_locations = null,
        array $col_map = []
    ) {
        $c = ProductImportExportColumns::class;
        $col_name = $col_map['name'] ?? $c::COL_NAME;
        $col_brand = $col_map['brand'] ?? $c::COL_BRAND;
        $col_unit = $col_map['unit'] ?? $c::COL_UNIT;
        $col_category = $col_map['category'] ?? $c::COL_CATEGORY;
        $col_sku = $col_map['sku'] ?? $c::COL_SKU;
        $col_manage_stock = $col_map['manage_stock'] ?? $c::COL_MANAGE_STOCK;
        $col_alert_qty = $col_map['alert_quantity'] ?? $c::COL_ALERT_QUANTITY;
        $col_purchase = $col_map['purchase_price'] ?? $c::COL_PURCHASE_PRICE;
        $col_retail = $col_map['retail_price'] ?? $c::COL_RETAIL_PRICE;
        $col_wholesale = $col_map['wholesale_price'] ?? $c::COL_WHOLESALE_PRICE;
        $col_op_stock = $col_map['opening_stock'] ?? $c::COL_OPENING_STOCK;
        $col_op_loc = $col_map['opening_stock_location'] ?? $c::COL_OPENING_STOCK_LOCATION;
        $col_prod_locs = $col_map['product_locations'] ?? $c::COL_PRODUCT_LOCATIONS;

        $product_array = [];
        $product_array['business_id'] = $business_id;
        $product_array['created_by'] = $user_id;
        $product_array['image'] = '';
        $product_array['product_description'] = null;
        $product_array['product_custom_field1'] = '';
        $product_array['product_custom_field2'] = '';
        $product_array['product_custom_field3'] = '';
        $product_array['product_custom_field4'] = '';
        $product_array['not_for_selling'] = 0;
        $product_array['enable_sr_no'] = 0;
        $product_array['weight'] = '';
        $product_array['tax_type'] = 'exclusive';
        $product_array['type'] = 'single';
        $product_array['barcode_type'] = 'C128';

        $product_name = $this->cellValue($value[$col_name]);
        if ($product_name === '') {
            $this->last_import_error = "Product name is required in row no. $row_no";

            return false;
        }
        $product_array['name'] = $product_name;

        $enable_stock = $this->cellValue($value[$col_manage_stock]);
        if (in_array($enable_stock, ['0', '1'], true)) {
            $product_array['enable_stock'] = (int) $enable_stock;
        } else {
            $product_array['enable_stock'] = 1;
        }

        $unit_name = $this->cellValue($value[$col_unit]);
        if (empty($unit_name)) {
            $this->last_import_error = "UNIT is required in row no. $row_no";

            return false;
        }
        $unit = Unit::where('business_id', $business_id)
            ->where(function ($query) use ($unit_name) {
                $query->where('short_name', $unit_name)
                    ->orWhere('actual_name', $unit_name);
            })->first();
        if (empty($unit)) {
            $this->last_import_error = "Unit with name $unit_name not found in row no. $row_no. You can add unit from Products > Units";

            return false;
        }
        $product_array['unit_id'] = $unit->id;

        if ($product_array['enable_stock'] == 1) {
            $product_array['alert_quantity'] = $this->cellValue($value[$col_alert_qty]);
        }

        $brand_name = $this->cellValue($value[$col_brand]);
        if (! empty($brand_name)) {
            $brand = Brands::firstOrCreate(
                ['business_id' => $business_id, 'name' => $brand_name],
                ['created_by' => $user_id]
            );
            $product_array['brand_id'] = $brand->id;
        }

        $category_name = $this->cellValue($value[$col_category]);
        if (! empty($category_name)) {
            $category = Category::firstOrCreate(
                ['business_id' => $business_id, 'name' => $category_name, 'category_type' => 'product'],
                ['created_by' => $user_id, 'parent_id' => 0]
            );
            $product_array['category_id'] = $category->id;
        }

        $sku = $this->cellValue($value[$col_sku]);
        $product_array['sku'] = ! empty($sku) ? $sku : ' ';

        if ($update_existing) {
            $existing_match = $this->findExistingProductForUpdate(
                $business_id,
                $product_name,
                $sku,
                $sku_prefix
            );

            if ($existing_match) {
                $product_array['update_existing'] = true;
                $product_array['existing_product_id'] = $existing_match['product']->id;
                if (! empty($existing_match['sku_to_update'])) {
                    $product_array['sku_to_update'] = $existing_match['sku_to_update'];
                }
            }
        } elseif (! empty($sku)) {
            $is_exist = Product::where('sku', $sku)
                ->where('business_id', $business_id)
                ->exists();
            if ($is_exist) {
                $this->last_import_error = "$sku SKU already exist in row no. $row_no";

                return false;
            }
        }

        $purchase_price = $this->cellValue($value[$col_purchase]);
        if ($purchase_price === '') {
            $this->last_import_error = "PURCHASE PRICE is required in row no. $row_no";

            return false;
        }
        $purchase_price = $this->productUtil->num_uf($purchase_price);

        $selling_price_raw = $this->cellValue($value[$col_retail]);
        $selling_price = $selling_price_raw !== '' ? $this->productUtil->num_uf($selling_price_raw) : 0;
        $profit_margin = $this->productUtil->num_uf($default_profit_percent);

        $product_prices = $this->calculateVariationPrices(
            $purchase_price,
            $purchase_price,
            $selling_price,
            0,
            'exclusive',
            $profit_margin
        );

        $product_array['variation'] = [
            'profit_percent' => $profit_margin,
            'dpp_inc_tax' => $product_prices['dpp_inc_tax'],
            'dpp_exc_tax' => $product_prices['dpp_exc_tax'],
            'dsp_inc_tax' => $product_prices['dsp_inc_tax'],
            'dsp_exc_tax' => $product_prices['dsp_exc_tax'],
        ];

        $wholesale_price = $this->cellValue($value[$col_wholesale]);
        $product_array['wholesale_price'] = $wholesale_price !== '' ? $this->productUtil->num_uf($wholesale_price) : null;

        $op_stock_val = $this->cellValue($value[$col_op_stock]);
        if ($op_stock_val !== '') {
            $product_array['enable_stock'] = 1;
            $product_array['opening_stock_details']['quantity'] = $op_stock_val;
            $product_array['opening_stock_details']['exp_date'] = null;

            $location_name = $this->cellValue($value[$col_op_loc]);
            if (! empty($location_name)) {
                $location = $this->findBusinessLocation($location_name, $business_locations, $business_id);
                if (empty($location)) {
                    $this->last_import_error = "No location with name '$location_name' found in row no. $row_no";

                    return false;
                }
                $product_array['opening_stock_details']['location_id'] = $location->id;
            } else {
                $location = (! empty($business_locations) && method_exists($business_locations, 'first'))
                    ? $business_locations->first()
                    : BusinessLocation::where('business_id', $business_id)->first();
                if (empty($location)) {
                    $this->last_import_error = "No business location found for opening stock in row no. $row_no";

                    return false;
                }
                $product_array['opening_stock_details']['location_id'] = $location->id;
            }
        }

        $product_array['import_product_locations'] = $this->cellValue($value[$col_prod_locs]);

        return $product_array;
    }

    /**
     * Normalize a spreadsheet cell value to string.
     */
    private function cellValue($value): string
    {
        if ($value === null) {
            return '';
        }

        $str = trim((string) $value);
        if (str_starts_with($str, '=')) {
            $expr = trim(substr($str, 1));
            if (preg_match('/^[\d\.\s\+\-\*\/\(\)]+$/', $expr)) {
                try {
                    $calc = @eval("return ($expr);");
                    if (is_numeric($calc)) {
                        return (string) $calc;
                    }
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
        }

        return $str;
    }

    /**
     * Resolve a location string to a BusinessLocation model.
     * Matches raw name, location_id code, or combined "Name (location_id)" format.
     */
    private function findBusinessLocation(string $search, $business_locations, $business_id): ?BusinessLocation
    {
        $search = trim($search);
        if ($search === '') {
            return null;
        }

        $search_lower = strtolower($search);

        // 1. Match against loaded collection
        if (! empty($business_locations)) {
            foreach ($business_locations as $loc) {
                $loc_name_lower = strtolower(trim((string) $loc->name));
                $loc_id_lower = strtolower(trim((string) $loc->location_id));
                $combined_lower = strtolower(trim($loc->name . (! empty($loc->location_id) ? " ({$loc->location_id})" : '')));

                if ($search_lower === $loc_name_lower || $search_lower === $loc_id_lower || $search_lower === $combined_lower) {
                    return $loc;
                }
            }

            // If search contains parentheses e.g. "Vawa Scents (The One Mall)", check components
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $search, $matches)) {
                $name_part = strtolower(trim($matches[1]));
                $id_part = strtolower(trim($matches[2]));

                foreach ($business_locations as $loc) {
                    $loc_name_lower = strtolower(trim((string) $loc->name));
                    $loc_id_lower = strtolower(trim((string) $loc->location_id));

                    if ($loc_name_lower === $name_part || $loc_id_lower === $id_part) {
                        return $loc;
                    }
                }
            }
        }

        // 2. Query database directly if not matched in collection
        $location = BusinessLocation::where('business_id', $business_id)
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($search)])
                    ->orWhereRaw('LOWER(TRIM(location_id)) = ?', [strtolower($search)])
                    ->orWhereRaw("LOWER(TRIM(CONCAT(name, ' (', location_id, ')'))) = ?", [strtolower($search)]);
            })
            ->first();

        if ($location) {
            return $location;
        }

        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $search, $matches)) {
            $name_part = strtolower(trim($matches[1]));
            $id_part = strtolower(trim($matches[2]));

            return BusinessLocation::where('business_id', $business_id)
                ->where(function ($query) use ($name_part, $id_part) {
                    $query->whereRaw('LOWER(TRIM(name)) = ?', [$name_part])
                        ->orWhereRaw('LOWER(TRIM(location_id)) = ?', [$id_part]);
                })
                ->first();
        }

        return null;
    }

    /**
     * Resolve comma-separated location names to location IDs.
     */
    private function resolveProductLocationIds(string $locations_string, $business_locations, $business_id): array
    {
        $location_ids = [];
        $locations_array = explode(',', $locations_string);

        foreach ($locations_array as $business_location) {
            $business_location = trim($business_location);
            if ($business_location === '') {
                continue;
            }

            $loc = $this->findBusinessLocation($business_location, $business_locations, $business_id);
            if ($loc) {
                $location_ids[] = $loc->id;
            }
        }

        return array_values(array_unique($location_ids));
    }
}
