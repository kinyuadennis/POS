<?php

namespace App\Http\Controllers;

use App\Barcode;
use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    /**
     * Constructor
     *
     * @param  TransactionUtil  $TransactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display labels
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $purchase_id = $request->get('purchase_id', false);
        $product_id = $request->get('product_id', false);

        //Get products for the business
        $products = [];
        $price_groups = [];
        if ($purchase_id) {
            $products = $this->transactionUtil->getPurchaseProducts($business_id, $purchase_id);
            $products = $this->excludeNotForSellingLabelProducts($products);
        } elseif ($product_id) {
            $products = $this->productUtil->getDetailsFromProduct($business_id, $product_id);
            $products = $this->excludeNotForSellingLabelProducts($products);
        }

        //get price groups
        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                ->active()
                                ->pluck('name', 'id');

        $barcode_settings = Barcode::where('business_id', $business_id)
                                ->orWhereNull('business_id')
                                ->orderBy('is_continuous', 'desc')
                                ->orderBy('is_default', 'desc')
                                ->select(DB::raw('CONCAT(name, ", ", COALESCE(description, "")) as name, id, is_default, is_continuous'))
                                ->get();
        $default = $barcode_settings->where('is_default', 1)->first()
                   ?? $barcode_settings->where('is_continuous', 1)->first()
                   ?? $barcode_settings->first();
        $barcode_settings = $barcode_settings->pluck('name', 'id');

        $categories = ['all' => __('lang_v1.all_category')] + Category::forDropdown($business_id, 'product')->toArray();
        $brands = ['all' => __('lang_v1.all_brands')] + Brands::forDropdown($business_id)->toArray();
        $business_locations = BusinessLocation::forDropdown($business_id)->toArray();

        return view('labels.show')
            ->with(compact('products', 'barcode_settings', 'default', 'price_groups', 'categories', 'brands', 'business_locations'));
    }

    /**
     * Returns html rows for bulk products by category, brand, or all
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addBulkProducts(Request $request)
    {
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $category_id = $request->input('category_id');
            $brand_id = $request->input('brand_id');
            $qty_type = $request->input('qty_type', '1');
            $location_id = $request->input('location_id');
            $index = (int) $request->input('row_count', 0);

            $query = Product::leftJoin('variations as v', 'products.id', '=', 'v.product_id')
                ->whereNull('v.deleted_at')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier')
                ->where('products.is_inactive', 0)
                ->where('products.not_for_selling', 0);

            if (! empty($category_id) && $category_id !== 'all') {
                $query->where(function ($q) use ($category_id) {
                    $q->where('products.category_id', $category_id)
                      ->orWhere('products.sub_category_id', $category_id);
                });
            }

            if (! empty($brand_id) && $brand_id !== 'all') {
                $query->where('products.brand_id', $brand_id);
            }

            if ($qty_type === 'stock' && ! empty($location_id)) {
                $query->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                    $join->on('v.id', '=', 'vld.variation_id')
                         ->where('vld.location_id', '=', $location_id);
                });
                $query->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'v.id as variation_id',
                    'v.name as variation_name',
                    DB::raw('GREATEST(COALESCE(FLOOR(vld.qty_available), 1), 1) as quantity')
                );
            } else {
                $query->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'v.id as variation_id',
                    'v.name as variation_name',
                    DB::raw('1 as quantity')
                );
            }

            $existing_variation_ids = $request->input('existing_variation_ids', []);
            if (! empty($existing_variation_ids) && is_array($existing_variation_ids)) {
                $query->whereNotIn('v.id', $existing_variation_ids);
            }

            $products = $query->orderBy('products.name', 'asc')->get();

            $price_groups = SellingPriceGroup::where('business_id', $business_id)
                ->active()
                ->pluck('name', 'id');

            $html = view('labels.partials.show_table_rows')
                ->with(compact('products', 'index', 'price_groups'))
                ->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $products->count(),
            ]);
        }
    }

    /**
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = $request->session()->get('user.business_id');

            if (! empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($business_id, $product_id, $variation_id);
                $products = $this->excludeNotForSellingLabelProducts($products);

                $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                            ->active()
                                            ->pluck('name', 'id');

                return view('labels.partials.show_table_rows')
                        ->with(compact('products', 'index', 'price_groups'));
            }
        }
    }

    /**
     * Returns the html for labels preview
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        try {
            $data = $this->prepareLabelPreviewData($request);
            if ($data === null) {
                return redirect()->back();
            }

            $request->session()->put('label_print_job', $request->except('_token'));

            $output = view('labels.partials.preview_2')->with($data)->render();

            return response($output);
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response(__('lang_v1.barcode_label_error').': '.$e->getMessage(), 500);
        }
    }

    /**
     * Stream a PDF whose page size matches the configured label dimensions.
     * This is the reliable print path for continuous-roll printers.
     *
     * @return \Illuminate\Http\Response
     */
    public function printPdf(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        try {
            $stored = $request->session()->get('label_print_job');
            if (empty($stored) || empty($stored['products'])) {
                return response('Label print job expired. Please preview the labels again.', 410);
            }

            $request->merge($stored);
            $data = $this->prepareLabelPreviewData($request);
            if ($data === null) {
                return response('No products found for label printing.', 422);
            }

            $width_pt = $data['page_width_mm'] * 72 / 25.4;
            $height_pt = $data['page_height_mm'] * 72 / 25.4;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('labels.partials.preview_pdf', $data)
                ->setPaper([0, 0, $width_pt, $height_pt], 'portrait')
                ->setOption('dpi', 96)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false);

            $output = $this->embedPdfPrintPreferences($pdf->output());
            $filename = 'labels-'.$data['page_width_mm'].'x'.$data['page_height_mm'].'mm.pdf';

            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response(__('lang_v1.barcode_label_error').': '.$e->getMessage(), 500);
        }
    }

    /**
     * Build view data for HTML preview and exact-size PDF output.
     *
     * @return array<string, mixed>|null
     */
    protected function prepareLabelPreviewData(Request $request)
    {
        $products = $request->get('products');
        $print = $request->get('print');
        $barcode_setting = $request->get('barcode_setting');
        $business_id = $request->session()->get('user.business_id');

        if (empty($products)) {
            return null;
        }

        $barcode_details = Barcode::find($barcode_setting);
        if (empty($barcode_details)) {
            $barcode_details = Barcode::where('business_id', $business_id)->orWhereNull('business_id')->first();
        }

        $this->applyLabelPaperDimensions($barcode_details);

        $margin_top = (float) $barcode_details->top_margin;
        $margin_left = (float) $barcode_details->left_margin;
        $paper_width = (float) $barcode_details->paper_width;
        $paper_height = (float) $barcode_details->paper_height;
        $label_width = (float) $barcode_details->width;
        $label_height = (float) $barcode_details->height;

        $page_width_mm = $this->inchesToMm($paper_width);
        $page_height_mm = $this->inchesToMm($paper_height);
        $label_width_mm = $this->inchesToMm($label_width);
        $label_height_mm = $this->inchesToMm($label_height);
        $margin_top_mm = $this->inchesToMm($margin_top);
        $margin_left_mm = $this->inchesToMm($margin_left);

        $business_name = $request->session()->get('business.name');

        $product_details_page_wise = [];
        $total_qty = 0;

        $all_var_ids = array_unique(array_filter(array_column($products, 'variation_id')));
        $variations_data = Variation::whereIn('variations.id', $all_var_ids)
            ->leftJoin('products as p', 'p.id', '=', 'variations.product_id')
            ->leftJoin('units', 'p.unit_id', '=', 'units.id')
            ->leftJoin('units as u', 'p.secondary_unit_id', '=', 'u.id')
            ->leftJoin('brands', 'p.brand_id', '=', 'brands.id')
            ->leftJoin('product_variations as pv', 'pv.id', '=', 'variations.product_variation_id')
            ->where('p.business_id', $business_id)
            ->where('p.not_for_selling', 0)
            ->select(
                'p.id as product_id',
                'p.name as product_actual_name',
                'p.type as product_type',
                'p.enable_stock',
                'p.tax as tax_id',
                'p.product_custom_field1',
                'p.product_custom_field2',
                'p.product_custom_field3',
                'p.product_custom_field4',
                'pv.name as product_variation_name',
                'pv.is_dummy as is_dummy',
                'variations.name as variation_name',
                'variations.sub_sku',
                'p.barcode_type',
                'variations.default_sell_price',
                'variations.sell_price_inc_tax',
                'variations.id as variation_id'
            )
            ->get()
            ->keyBy('variation_id');

        foreach ($products as $value) {
            $var_id = $value['variation_id'];
            if (! isset($variations_data[$var_id])) {
                continue;
            }
            $details = clone $variations_data[$var_id];

            if (! empty($value['exp_date'])) {
                $details->exp_date = $value['exp_date'];
            }
            if (! empty($value['packing_date'])) {
                $details->packing_date = $value['packing_date'];
            }
            if (! empty($value['lot_number'])) {
                $details->lot_number = $value['lot_number'];
            }

            if (! empty($value['price_group_id'])) {
                $tax_id = ($print['price_type'] ?? '') == 'inclusive' ? null : $details->tax_id;
                $group_prices = $this->productUtil->getVariationGroupPrice($value['variation_id'], $value['price_group_id'], $tax_id);
                $details->sell_price_inc_tax = $group_prices['price_inc_tax'];
                $details->default_sell_price = $group_prices['price_exc_tax'];
            }

            $qty = (int) ($value['quantity'] ?? 1);
            for ($i = 0; $i < $qty; $i++) {
                $sheet_capacity = max((int) $barcode_details->stickers_in_one_sheet, 1);
                $page = intdiv($total_qty, $sheet_capacity);

                if ($total_qty % $sheet_capacity == 0) {
                    $product_details_page_wise[$page] = [];
                }

                $product_details_page_wise[$page][] = $details;
                $total_qty++;
            }
        }

        $factor = (($barcode_details->width / ($barcode_details->height ?: 1))) / ($barcode_details->is_continuous ? 2 : 4);

        return compact(
            'print',
            'product_details_page_wise',
            'business_name',
            'barcode_details',
            'margin_top',
            'margin_left',
            'paper_width',
            'paper_height',
            'factor',
            'page_width_mm',
            'page_height_mm',
            'label_width_mm',
            'label_height_mm',
            'margin_top_mm',
            'margin_left_mm'
        );
    }

    /**
     * Force continuous rolls to a page size equal to the physical label.
     */
    protected function applyLabelPaperDimensions(Barcode $barcode_details)
    {
        $label_width = max((float) $barcode_details->width, 0.1);
        $label_height = max((float) $barcode_details->height, 0.1);
        $stickers_in_row = max((int) $barcode_details->stickers_in_one_row, 1);

        $barcode_details->width = $label_width;
        $barcode_details->height = $label_height;
        $barcode_details->stickers_in_one_row = $stickers_in_row;

        if ($barcode_details->is_continuous) {
            $barcode_details->stickers_in_one_sheet = $stickers_in_row;
            $barcode_details->paper_height = $label_height;
            $barcode_details->top_margin = 0;
            $barcode_details->left_margin = 0;

            if ($stickers_in_row === 1) {
                $barcode_details->paper_width = $label_width;
                $barcode_details->col_distance = 0;
                $barcode_details->row_distance = 0;
            } else {
                $paper_width = (float) $barcode_details->paper_width;
                $barcode_details->paper_width = $paper_width > 0 ? $paper_width : ($label_width * $stickers_in_row);
            }

            return;
        }

        if ($stickers_in_row === 1) {
            $barcode_details->col_distance = 0;
            $barcode_details->row_distance = 0;
        }

        $barcode_details->paper_width = max((float) $barcode_details->paper_width, $label_width);
        $barcode_details->paper_height = max((float) $barcode_details->paper_height, $label_height);
        $barcode_details->top_margin = max((float) $barcode_details->top_margin, 0);
        $barcode_details->left_margin = max((float) $barcode_details->left_margin, 0);
        $barcode_details->stickers_in_one_sheet = max((int) $barcode_details->stickers_in_one_sheet, $stickers_in_row);
    }

    /**
     * Drop products marked not for selling from a label product collection.
     */
    protected function excludeNotForSellingLabelProducts($products)
    {
        if (empty($products) || (is_countable($products) && count($products) === 0)) {
            return $products;
        }

        $product_ids = collect($products)->pluck('product_id')->filter()->unique()->values();
        if ($product_ids->isEmpty()) {
            return $products;
        }

        $sellable_ids = Product::whereIn('id', $product_ids)
            ->where('not_for_selling', 0)
            ->pluck('id');

        return collect($products)->whereIn('product_id', $sellable_ids)->values();
    }

    /**
     * Convert inches to millimetres for printer page boxes.
     */
    protected function inchesToMm($inches)
    {
        return round(max((float) $inches, 0) * 25.4, 2);
    }

    /**
     * Ask the PDF viewer/printer to use this file's page size instead of A4/Letter.
     */
    protected function embedPdfPrintPreferences($pdf)
    {
        if (strpos($pdf, '/ViewerPreferences') === false) {
            $pdf = preg_replace(
                '/\/Type\s*\/Catalog/',
                '/Type /Catalog /ViewerPreferences << /PrintScaling /None /PickTrayByPDFSize true >>',
                $pdf,
                1
            );
        }

        return $pdf;
    }
}
