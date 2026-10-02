<?php

namespace App\Services;

use App\Business;
use App\BusinessLocation;
use App\Events\StockTransferCreatedOrModified;
use App\Product;
use App\ProductRequest;
use App\ProductRequestActivity;
use App\ProductRequestLine;
use App\PurchaseLine;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;

class ProductRequestService
{
    public function __construct(
        protected ProductUtil $productUtil,
        protected TransactionUtil $transactionUtil
    ) {
    }

    /**
     * Create a new product request (draft or pending).
     */
    public function createRequest(array $data, int $business_id, int $user_id): ProductRequest
    {
        return DB::transaction(function () use ($data, $business_id, $user_id) {
            $is_submit = ! empty($data['submit_request']);
            $status = $is_submit ? 'pending' : 'draft';

            $request_no = ProductRequest::generateRequestNo($business_id);

            $request = ProductRequest::create([
                'business_id' => $business_id,
                'request_no' => $request_no,
                'requesting_location_id' => $data['requesting_location_id'],
                'source_location_id' => $data['source_location_id'],
                'created_by' => $user_id,
                'status' => $status,
                'priority' => $data['priority'] ?? 'medium',
                'required_date' => ! empty($data['required_date']) ? $this->productUtil->uf_date($data['required_date']) : null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['products'] as $item) {
                if (empty($item['variation_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                $variation = Variation::findOrFail($item['variation_id']);

                ProductRequestLine::create([
                    'product_request_id' => $request->id,
                    'product_id' => $variation->product_id,
                    'variation_id' => $variation->id,
                    'requested_quantity' => $this->productUtil->num_uf($item['quantity']),
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $action = $is_submit ? 'submitted' : 'created';
            ProductRequestActivity::log($request, $action, null, $status, $is_submit ? 'Submitted for review' : 'Saved as draft', $user_id);

            return $request;
        });
    }

    /**
     * Update an existing draft/changes_requested request.
     */
    public function updateRequest(ProductRequest $request, array $data, int $user_id): ProductRequest
    {
        return DB::transaction(function () use ($request, $data, $user_id) {
            $is_submit = ! empty($data['submit_request']);
            $old_status = $request->status;
            $new_status = $is_submit ? 'pending' : 'draft';

            $request->update([
                'priority' => $data['priority'] ?? $request->priority,
                'required_date' => ! empty($data['required_date']) ? $this->productUtil->uf_date($data['required_date']) : null,
                'notes' => $data['notes'] ?? $request->notes,
                'status' => $new_status,
            ]);

            // Re-create lines
            $request->lines()->delete();

            foreach ($data['products'] as $item) {
                if (empty($item['variation_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                $variation = Variation::findOrFail($item['variation_id']);

                ProductRequestLine::create([
                    'product_request_id' => $request->id,
                    'product_id' => $variation->product_id,
                    'variation_id' => $variation->id,
                    'requested_quantity' => $this->productUtil->num_uf($item['quantity']),
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $action = $is_submit ? 'submitted' : 'updated';
            ProductRequestActivity::log($request, $action, $old_status, $new_status, $is_submit ? 'Re-submitted for review' : 'Updated draft', $user_id);

            return $request;
        });
    }

    /**
     * Approve or partially approve a request.
     */
    public function approveRequest(ProductRequest $request, array $lines_data, int $user_id, ?string $notes = null): ProductRequest
    {
        return DB::transaction(function () use ($request, $lines_data, $user_id, $notes) {
            $old_status = $request->status;
            $all_full = true;
            $any_approved = false;

            foreach ($lines_data as $line_id => $values) {
                $line = ProductRequestLine::where('product_request_id', $request->id)->findOrFail($line_id);
                $approved_qty = max(0, (float) $this->productUtil->num_uf($values['approved_quantity'] ?? 0));

                $line->update([
                    'approved_quantity' => $approved_qty,
                ]);

                if ($approved_qty > 0) {
                    $any_approved = true;
                }

                if ($approved_qty < $line->requested_quantity) {
                    $all_full = false;
                }
            }

            if (! $any_approved) {
                throw new \Exception('At least one item must have an approved quantity greater than 0. Otherwise, use Reject.');
            }

            $new_status = $all_full ? 'approved' : 'partially_approved';

            $request->update([
                'status' => $new_status,
                'approved_by' => $user_id,
                'approved_at' => now(),
            ]);

            $action = $all_full ? 'approved' : 'partially_approved';
            ProductRequestActivity::log($request, $action, $old_status, $new_status, $notes, $user_id);

            return $request;
        });
    }

    /**
     * Reject a request with mandatory reason.
     */
    public function rejectRequest(ProductRequest $request, string $reason, int $user_id): ProductRequest
    {
        return DB::transaction(function () use ($request, $reason, $user_id) {
            $old_status = $request->status;

            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            ProductRequestActivity::log($request, 'rejected', $old_status, 'rejected', "Reason: $reason", $user_id);

            return $request;
        });
    }

    /**
     * Request modifications/changes from cashier.
     */
    public function requestChanges(ProductRequest $request, string $notes, int $user_id): ProductRequest
    {
        return DB::transaction(function () use ($request, $notes, $user_id) {
            $old_status = $request->status;

            $request->update([
                'status' => 'changes_requested',
                'notes' => $notes,
            ]);

            ProductRequestActivity::log($request, 'changes_requested', $old_status, 'changes_requested', $notes, $user_id);

            return $request;
        });
    }

    /**
     * Fulfill & Dispatch stock from Main Store to Requesting Location.
     * Deducts stock from Main Store immediately. Stock is placed In Transit.
     */
    public function dispatchRequest(ProductRequest $request, array $dispatch_data, int $business_id, int $user_id): array
    {
        return DB::transaction(function () use ($request, $dispatch_data, $business_id, $user_id) {
            $lines = $request->lines()->with(['product', 'variation'])->get();

            // 1. Check live stock in Main Store
            foreach ($lines as $line) {
                $qty_to_send = isset($dispatch_data['quantities'][$line->id])
                    ? (float) $this->productUtil->num_uf($dispatch_data['quantities'][$line->id])
                    : (float) ($line->approved_quantity ?? $line->requested_quantity);

                if ($qty_to_send <= 0) {
                    continue;
                }

                $available = VariationLocationDetails::where('variation_id', $line->variation_id)
                    ->where('location_id', $request->source_location_id)
                    ->value('qty_available') ?? 0;

                if ($available < $qty_to_send) {
                    $prodName = $line->product->name . ' (' . ($line->variation->name ?? 'Default') . ')';
                    throw new \Exception("Insufficient stock in Main Store for '$prodName'. Available: $available, Needed: $qty_to_send");
                }
            }

            // 2. Create Stock Transfer
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer', $business_id);
            $ref_no = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count, $business_id);

            $sell_transfer_data = [
                'business_id' => $business_id,
                'location_id' => $request->source_location_id,
                'type' => 'sell_transfer',
                'status' => 'in_transit',
                'payment_status' => 'paid',
                'created_by' => $user_id,
                'transaction_date' => now(),
                'total_before_tax' => 0,
                'final_total' => 0,
                'ref_no' => $ref_no,
                'additional_notes' => "Fulfilled from Product Request #{$request->request_no}. Notes: " . ($dispatch_data['notes'] ?? ''),
            ];

            $sell_transfer = Transaction::create($sell_transfer_data);

            $purchase_transfer_data = [
                'business_id' => $business_id,
                'location_id' => $request->requesting_location_id,
                'type' => 'purchase_transfer',
                'status' => 'in_transit',
                'payment_status' => 'paid',
                'created_by' => $user_id,
                'transaction_date' => now(),
                'total_before_tax' => 0,
                'final_total' => 0,
                'ref_no' => $ref_no,
                'transfer_parent_id' => $sell_transfer->id,
            ];

            $purchase_transfer = Transaction::create($purchase_transfer_data);

            $sell_lines = [];
            $purchase_lines = [];

            foreach ($lines as $line) {
                $qty_to_send = isset($dispatch_data['quantities'][$line->id])
                    ? (float) $this->productUtil->num_uf($dispatch_data['quantities'][$line->id])
                    : (float) ($line->approved_quantity ?? $line->requested_quantity);

                if ($qty_to_send <= 0) {
                    continue;
                }

                $line->update(['dispatched_quantity' => $qty_to_send]);

                $unit_price = (float) $line->variation->default_purchase_price;

                $sell_line_arr = [
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'quantity' => $qty_to_send,
                    'unit_price' => $unit_price,
                    'unit_price_inc_tax' => $unit_price,
                    'item_tax' => 0,
                    'tax_id' => null,
                ];

                $purchase_line_arr = [
                    'product_id' => $line->product_id,
                    'variation_id' => $line->variation_id,
                    'quantity' => $qty_to_send,
                    'purchase_price' => $unit_price,
                    'purchase_price_inc_tax' => $unit_price,
                    'item_tax' => 0,
                    'tax_id' => null,
                ];

                $sell_lines[] = $sell_line_arr;
                $purchase_lines[] = $purchase_line_arr;

                // Deduct stock from Main Store (Source) immediately
                if ($line->product->enable_stock) {
                    $this->productUtil->decreaseProductQuantity(
                        $line->product_id,
                        $line->variation_id,
                        $request->source_location_id,
                        $qty_to_send
                    );
                }
            }

            if (! empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $request->source_location_id, false, null, [], false);
            }

            if (! empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }

            $old_status = $request->status;
            $request->update([
                'status' => 'in_transit',
                'stock_transfer_id' => $sell_transfer->id,
                'dispatched_by' => $user_id,
                'dispatched_at' => now(),
            ]);

            ProductRequestActivity::log($request, 'dispatched', $old_status, 'in_transit', "Dispatched Transfer #$ref_no", $user_id);
            event(new StockTransferCreatedOrModified($sell_transfer, 'added'));

            return [
                'success' => 1,
                'msg' => "Stock dispatched successfully! Transfer #$ref_no is now in transit.",
                'transfer_id' => $sell_transfer->id,
            ];
        });
    }

    /**
     * Confirm receipt at destination shop with discrepancy tracking.
     * Stock enters destination shop inventory now.
     */
    public function receiveRequest(ProductRequest $request, array $receipt_data, int $business_id, int $user_id): array
    {
        return DB::transaction(function () use ($request, $receipt_data, $business_id, $user_id) {
            if ($request->status !== 'in_transit') {
                throw new \Exception('Only requests currently In Transit can be marked as received.');
            }

            $sell_transfer = Transaction::where('id', $request->stock_transfer_id)->first();
            $purchase_transfer = Transaction::where('transfer_parent_id', $request->stock_transfer_id)->first();

            $lines = $request->lines()->with(['product', 'variation'])->get();
            $has_discrepancy = false;
            $discrepancy_summary = [];

            foreach ($lines as $line) {
                $received_qty = isset($receipt_data['received'][$line->id])
                    ? (float) $this->productUtil->num_uf($receipt_data['received'][$line->id])
                    : (float) $line->dispatched_quantity;

                $dispatched_qty = (float) $line->dispatched_quantity;
                $diff = $dispatched_qty - $received_qty;
                $reason = $receipt_data['reason'][$line->id] ?? null;

                if ($diff != 0) {
                    $has_discrepancy = true;
                    $discrepancy_summary[] = "{$line->product->name}: Sent {$dispatched_qty}, Received {$received_qty} (Diff: -{$diff})" . ($reason ? " Reason: $reason" : '');
                }

                $line->update([
                    'received_quantity' => $received_qty,
                    'discrepancy_quantity' => $diff,
                    'discrepancy_reason' => $reason,
                ]);

                // Increase stock at destination location by actual received quantity
                if ($line->product->enable_stock && $received_qty > 0) {
                    $this->productUtil->updateProductQuantity(
                        $request->requesting_location_id,
                        $line->product_id,
                        $line->variation_id,
                        $received_qty,
                        0,
                        null,
                        false
                    );
                }
            }

            // Finalize transfer status
            if (! empty($sell_transfer)) {
                $sell_transfer->status = 'final';
                $sell_transfer->save();
            }

            if (! empty($purchase_transfer)) {
                $purchase_transfer->status = 'received';
                $purchase_transfer->save();

                $business = [
                    'id' => $business_id,
                    'accounting_method' => session('business.accounting_method') ?? (Business::where('id', $business_id)->value('accounting_method') ?? 'fifo'),
                    'location_id' => $request->source_location_id,
                ];
                try {
                    $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');
                } catch (\Exception $e) {
                    \Log::warning('Stock transfer COGS mapping notice: ' . $e->getMessage());
                }
            }

            $old_status = $request->status;
            $request->update([
                'status' => 'received',
                'received_by' => $user_id,
                'received_at' => now(),
            ]);

            $notes = $has_discrepancy
                ? 'Received with discrepancy: ' . implode('; ', $discrepancy_summary)
                : 'Received in full. Stock added to shop inventory.';

            ProductRequestActivity::log($request, 'received', $old_status, 'received', $notes, $user_id);

            return [
                'success' => 1,
                'msg' => 'Stock receipt confirmed! Inventory has been updated at your shop.',
            ];
        });
    }

    /**
     * Search products with dual stock (Current Shop vs Main Store).
     */
    public function searchProductsForRequest(string $keyword, int $source_location_id, int $requesting_location_id, int $business_id): array
    {
        $products = Product::where('products.business_id', $business_id)
            ->where('products.type', '!=', 'modifier')
            ->where('products.is_inactive', 0)
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->leftJoin('units as u', 'products.unit_id', '=', 'u.id')
            ->leftJoin('variation_location_details as vld_source', function ($join) use ($source_location_id) {
                $join->on('vld_source.variation_id', '=', 'v.id')
                    ->where('vld_source.location_id', '=', $source_location_id);
            })
            ->leftJoin('variation_location_details as vld_shop', function ($join) use ($requesting_location_id) {
                $join->on('vld_shop.variation_id', '=', 'v.id')
                    ->where('vld_shop.location_id', '=', $requesting_location_id);
            })
            ->where(function ($q) use ($keyword) {
                $q->where('products.name', 'like', "%{$keyword}%")
                  ->orWhere('products.sku', 'like', "%{$keyword}%")
                  ->orWhere('v.sub_sku', 'like', "%{$keyword}%");
            })
            ->select([
                'products.id as product_id',
                'products.name as product_name',
                'products.type as product_type',
                'products.enable_stock',
                'v.id as variation_id',
                'v.name as variation_name',
                'v.sub_sku',
                'u.short_name as unit',
                DB::raw('COALESCE(vld_source.qty_available, 0) as source_stock'),
                DB::raw('COALESCE(vld_shop.qty_available, 0) as shop_stock'),
            ])
            ->take(20)
            ->get();

        // Check outstanding requests for these products
        $variation_ids = $products->pluck('variation_id')->toArray();
        $outstanding = ProductRequestLine::join('product_requests as pr', 'pr.id', '=', 'product_request_lines.product_request_id')
            ->where('pr.requesting_location_id', $requesting_location_id)
            ->whereIn('pr.status', ['pending', 'approved', 'in_transit'])
            ->whereIn('product_request_lines.variation_id', $variation_ids)
            ->select([
                'product_request_lines.variation_id',
                'pr.request_no',
                'pr.status',
                'product_request_lines.requested_quantity',
            ])
            ->get()
            ->keyBy('variation_id');

        return $products->map(function ($p) use ($outstanding) {
            $has_open = isset($outstanding[$p->variation_id]);
            $var_name = ($p->variation_name && $p->variation_name !== 'DUMMY') ? $p->variation_name : null;
            $full_name = $p->product_name . ($var_name ? " - {$var_name}" : '');

            return [
                'product_id' => $p->product_id,
                'variation_id' => $p->variation_id,
                'product_name' => $p->product_name,
                'variation_name' => $var_name,
                'name' => $full_name,
                'sub_sku' => $p->sub_sku,
                'unit' => $p->unit,
                'enable_stock' => (bool) $p->enable_stock,
                'source_stock' => (float) $p->source_stock,
                'shop_stock' => (float) $p->shop_stock,
                'has_outstanding' => $has_open,
                'outstanding_info' => $has_open ? "Already requested on {$outstanding[$p->variation_id]->request_no} ({$outstanding[$p->variation_id]->status})" : null,
            ];
        })->toArray();
    }
}