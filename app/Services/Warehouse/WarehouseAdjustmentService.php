<?php

namespace App\Services\Warehouse;

use App\Events\StockAdjustmentCreatedOrModified;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseAdjustmentService
{
    public function __construct(
        protected ProductUtil $productUtil,
        protected TransactionUtil $transactionUtil,
        protected WarehouseLocationService $warehouseLocationService
    ) {
    }

    public function createAdjustment(Request $request, int $business_id, int $user_id): array
    {
        $location_id = (int) $request->input('location_id');

        if (! $this->warehouseLocationService->isWarehouseLocation($location_id, $business_id)) {
            return [
                'success' => 0,
                'msg' => __('lang_v1.warehouse_adjustment_invalid_location'),
            ];
        }

        try {
            DB::beginTransaction();

            $input_data = $request->only([
                'location_id', 'transaction_date', 'adjustment_type', 'additional_notes',
                'total_amount_recovered', 'final_total', 'ref_no',
            ]);

            $input_data['type'] = 'stock_adjustment';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_amount_recovered'] = $this->productUtil->num_uf($input_data['total_amount_recovered']);

            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
            }

            $products = $request->input('products');

            if (empty($products)) {
                DB::rollBack();

                return [
                    'success' => 0,
                    'msg' => __('lang_v1.warehouse_adjustment_no_products'),
                ];
            }

            $product_data = [];

            foreach ($products as $product) {
                $adjustment_line = [
                    'product_id' => $product['product_id'],
                    'variation_id' => $product['variation_id'],
                    'quantity' => $this->productUtil->num_uf($product['quantity']),
                    'unit_price' => $this->productUtil->num_uf($product['unit_price']),
                ];
                if (! empty($product['lot_no_line_id'])) {
                    $adjustment_line['lot_no_line_id'] = $product['lot_no_line_id'];
                }
                $product_data[] = $adjustment_line;

                $this->productUtil->decreaseProductQuantity(
                    $product['product_id'],
                    $product['variation_id'],
                    $input_data['location_id'],
                    $this->productUtil->num_uf($product['quantity'])
                );
            }

            $stock_adjustment = Transaction::create($input_data);
            $stock_adjustment->stock_adjustment_lines()->createMany($product_data);

            $business = [
                'id' => $business_id,
                'accounting_method' => $request->session()->get('business.accounting_method'),
                'location_id' => $input_data['location_id'],
            ];
            $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');

            event(new StockAdjustmentCreatedOrModified($stock_adjustment, 'added'));
            $this->transactionUtil->activityLog($stock_adjustment, 'added', null, [], false);

            DB::commit();

            return [
                'success' => 1,
                'msg' => __('lang_v1.warehouse_adjustment_added_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $msg = __('messages.something_went_wrong');
            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            return [
                'success' => 0,
                'msg' => $msg,
            ];
        }
    }
}
