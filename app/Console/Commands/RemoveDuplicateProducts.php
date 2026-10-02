<?php

namespace App\Console\Commands;

use App\Product;
use App\ProductRack;
use App\Transaction;
use App\VariationLocationDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateProducts extends Command
{
    protected $signature = 'products:remove-duplicates {--dry-run : Preview without deleting} {--force : Delete without confirmation}';

    protected $description = 'Remove duplicate products, keeping the oldest entry per product name';

    public function handle()
    {
        $dry_run = $this->option('dry-run');

        $groups = DB::select(
            'SELECT LOWER(TRIM(name)) as norm_name, GROUP_CONCAT(id ORDER BY id) as ids
             FROM products
             GROUP BY LOWER(TRIM(name))
             HAVING COUNT(*) > 1'
        );

        $to_delete = [];
        foreach ($groups as $group) {
            $ids = array_map('intval', explode(',', $group->ids));
            $keep_id = min($ids);

            foreach ($ids as $id) {
                if ($id !== $keep_id) {
                    $to_delete[] = $id;
                }
            }
        }

        if (empty($to_delete)) {
            $this->info('No duplicate products found.');

            return 0;
        }

        $with_sales = DB::table('transaction_sell_lines')
            ->whereIn('product_id', $to_delete)
            ->distinct()
            ->pluck('product_id')
            ->all();

        $delete_ids = array_values(array_diff($to_delete, $with_sales));

        $this->info('Duplicate groups: '.count($groups));
        $this->info('Duplicate products to remove: '.count($delete_ids));
        $this->info('Skipped (have sales): '.count($with_sales));

        if ($dry_run) {
            $this->warn('Dry run only. No products were deleted.');

            return 0;
        }

        if (! $this->option('force') && ! $this->confirm('Delete '.count($delete_ids).' duplicate products?')) {
            $this->info('Cancelled.');

            return 0;
        }

        $deleted = 0;
        $failed = 0;

        foreach (array_chunk($delete_ids, 50) as $chunk) {
            DB::beginTransaction();

            try {
                foreach ($chunk as $product_id) {
                    $product = Product::find($product_id);

                    if (empty($product)) {
                        continue;
                    }

                    $opening_stock_transactions = Transaction::where('opening_stock_product_id', $product_id)
                        ->where('type', 'opening_stock')
                        ->get();

                    foreach ($opening_stock_transactions as $transaction) {
                        $transaction->delete();
                    }

                    DB::table('purchase_lines')->where('product_id', $product_id)->delete();
                    VariationLocationDetails::where('product_id', $product_id)->delete();
                    DB::table('product_locations')->where('product_id', $product_id)->delete();
                    ProductRack::where('product_id', $product_id)->delete();

                    $product->delete();
                    $deleted++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $failed += count($chunk);
                $this->error('Batch failed: '.$e->getMessage());
            }
        }

        $this->info("Deleted: {$deleted}");
        if ($failed > 0) {
            $this->error("Failed batches (products): {$failed}");
        }

        $remaining = DB::select(
            'SELECT COUNT(*) as cnt FROM (
                SELECT LOWER(TRIM(name)) as norm_name
                FROM products
                GROUP BY LOWER(TRIM(name))
                HAVING COUNT(*) > 1
            ) as d'
        );

        $this->info('Remaining duplicate name groups: '.($remaining[0]->cnt ?? 0));
        $this->info('Total products now: '.Product::count());

        return 0;
    }
}
