<?php

namespace App\Console\Commands;

use App\Business;
use App\Product;
use App\Variation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateProductSkus extends Command
{
    protected $signature = 'products:regenerate-skus {--dry-run : Preview without updating} {--only-missing : Only generate SKUs for products that do not have one}';

    protected $description = 'Reassign sequential SKUs to products and sync single-product variation sub_skus';

    public function handle()
    {
        $dry_run = $this->option('dry-run');
        $only_missing = $this->option('only-missing');
        $business = Business::first();

        if (empty($business)) {
            $this->error('No business found.');

            return 1;
        }

        $prefix = $business->sku_prefix ?? '';
        $query = Product::where('business_id', $business->id)->orderBy('id');

        if ($only_missing) {
            $query->where(function ($q) {
                $q->whereNull('sku')
                  ->orWhere('sku', '')
                  ->orWhere('sku', ' ');
            });
        }

        $products = $query->get();

        $this->info('Products to process: '.$products->count());
        $this->info('SKU prefix: '.($prefix ?: '(none)'));
        if ($only_missing) {
            $this->info('Mode: Only generating for products missing SKUs (preserving manufacturer/custom SKUs)');
        } else {
            $this->warn('Mode: Full re-sequencing (all product SKUs will be renumbered)');
        }

        // Determine starting sequence
        $start_seq = 1;
        if ($only_missing) {
            $existing_skus = Product::where('business_id', $business->id)
                ->whereNotNull('sku')
                ->where('sku', '!=', '')
                ->where('sku', '!=', ' ')
                ->pluck('sku');

            $max_number = 0;
            foreach ($existing_skus as $sku) {
                if (! empty($prefix) && stripos($sku, $prefix) === 0) {
                    $suffix = substr($sku, strlen($prefix));
                    if (is_numeric($suffix)) {
                        $max_number = max($max_number, (int) $suffix);
                    }
                } elseif (is_numeric($sku)) {
                    $max_number = max($max_number, (int) $sku);
                }
            }
            $start_seq = $max_number + 1;
        }

        if ($dry_run) {
            $seq = $start_seq;
            foreach ($products->take(5) as $product) {
                $new_sku = $prefix.str_pad($seq, 4, '0', STR_PAD_LEFT);
                $this->line("{$product->id} | {$product->sku} -> {$new_sku} | {$product->name}");
                $seq++;
            }
            $this->warn('Dry run only. No SKUs were updated.');

            return 0;
        }

        DB::beginTransaction();

        try {
            $seq = $start_seq;
            foreach ($products as $product) {
                $new_sku = $prefix.str_pad($seq, 4, '0', STR_PAD_LEFT);
                $product->sku = $new_sku;
                $product->save();

                if ($product->type === 'single') {
                    Variation::where('product_id', $product->id)->update(['sub_sku' => $new_sku]);
                }

                $seq++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed: '.$e->getMessage());

            return 1;
        }

        $empty = Product::where('business_id', $business->id)
                    ->where(function ($query) {
                        $query->whereNull('sku')
                            ->orWhere('sku', '')
                            ->orWhere('sku', ' ');
                    })
                    ->count();

        $this->info('SKUs processed successfully.');
        $this->info('Products with empty SKU remaining: '.$empty);
        $this->info('Next auto SKU will be: '.$prefix.str_pad($seq, 4, '0', STR_PAD_LEFT));

        return 0;
    }
}
