<?php

namespace Database\Seeders;

use App\Contact;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\Variation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inserts extra final sales for report testing. Does not update or delete existing rows.
 */
class TestSalesReportDataSeeder extends Seeder
{
    public function run()
    {
        $existing = Transaction::where('invoice_no', 'like', 'TEST-SR-%')->count();
        if ($existing > 0) {
            $this->command?->warn("Skipped: {$existing} TEST-SR sales already exist.");

            return;
        }

        $business_id = 1;
        $walk_in_id = Contact::where('business_id', $business_id)->where('is_default', 1)->value('id') ?: 1;

        $customers = $this->ensureTestCustomers($business_id);
        $catalog = $this->sellableCatalog($business_id);
        if ($catalog->count() < 8) {
            throw new \RuntimeException('Not enough priced products to seed test sales.');
        }

        $sales = $this->saleBlueprints($walk_in_id, $customers, $catalog);

        DB::transaction(function () use ($sales, $business_id) {
            $created = 0;
            $pay_seq = 1;

            foreach ($sales as $sale) {
                $lines = $sale['lines'];
                $total = 0;
                foreach ($lines as $line) {
                    $total += $line['unit_price_inc_tax'] * $line['quantity'];
                }

                $tx = Transaction::create([
                    'business_id' => $business_id,
                    'location_id' => $sale['location_id'],
                    'type' => 'sell',
                    'status' => 'final',
                    'payment_status' => 'paid',
                    'contact_id' => $sale['contact_id'],
                    'invoice_no' => $sale['invoice_no'],
                    'ref_no' => '',
                    'transaction_date' => $sale['transaction_date'],
                    'total_before_tax' => $total,
                    'tax_amount' => 0,
                    'discount_type' => 'percentage',
                    'discount_amount' => 0,
                    'shipping_charges' => 0,
                    'round_off_amount' => 0,
                    'final_total' => $total,
                    'created_by' => $sale['created_by'],
                    'is_direct_sale' => 0,
                    'is_suspend' => 0,
                    'exchange_rate' => 1,
                    'additional_notes' => 'Test sale for sales reports (TEST-SR).',
                ]);

                foreach ($lines as $line) {
                    TransactionSellLine::create([
                        'transaction_id' => $tx->id,
                        'product_id' => $line['product_id'],
                        'variation_id' => $line['variation_id'],
                        'quantity' => $line['quantity'],
                        'secondary_unit_quantity' => 0,
                        'quantity_returned' => 0,
                        'unit_price_before_discount' => $line['unit_price_inc_tax'],
                        'unit_price' => $line['unit_price_inc_tax'],
                        'line_discount_type' => 'fixed',
                        'line_discount_amount' => 0,
                        'unit_price_inc_tax' => $line['unit_price_inc_tax'],
                        'item_tax' => 0,
                        'tax_id' => null,
                        'sell_line_note' => '',
                        'children_type' => '',
                    ]);
                }

                TransactionPayment::create([
                    'transaction_id' => $tx->id,
                    'business_id' => $business_id,
                    'is_return' => 0,
                    'amount' => $total,
                    'method' => $sale['method'],
                    'paid_on' => $sale['transaction_date'],
                    'created_by' => $sale['created_by'],
                    'payment_for' => $sale['contact_id'],
                    'payment_ref_no' => 'TEST-SP-'.str_pad((string) $pay_seq, 4, '0', STR_PAD_LEFT),
                    'note' => 'Test payment for sales reports',
                ]);

                $pay_seq++;
                $created++;
            }

            $this->command?->info("Created {$created} test sales (TEST-SR-*). Existing data was not changed.");
        });
    }

    private function ensureTestCustomers(int $business_id): array
    {
        $defs = [
            ['name' => 'Amina Wanjiku', 'mobile' => '0711001001', 'contact_id' => 'TEST-C-001'],
            ['name' => 'David Otieno', 'mobile' => '0722002002', 'contact_id' => 'TEST-C-002'],
            ['name' => 'Fatuma Hassan', 'mobile' => '0733003003', 'contact_id' => 'TEST-C-003'],
        ];

        $ids = [];
        foreach ($defs as $def) {
            $existing = Contact::where('business_id', $business_id)->where('contact_id', $def['contact_id'])->first();
            if ($existing) {
                $ids[] = $existing->id;
                continue;
            }

            $contact = Contact::create([
                'business_id' => $business_id,
                'type' => 'customer',
                'contact_type' => 'individual',
                'name' => $def['name'],
                'first_name' => explode(' ', $def['name'])[0],
                'last_name' => explode(' ', $def['name'])[1] ?? '',
                'mobile' => $def['mobile'],
                'contact_id' => $def['contact_id'],
                'contact_status' => 'active',
                'created_by' => 1,
                'is_default' => 0,
                'balance' => 0,
            ]);
            $ids[] = $contact->id;
        }

        return $ids;
    }

    private function sellableCatalog(int $business_id)
    {
        $preferred_names = [
            '212 MEN 100ml PERFUME',
            '9PM BLACK {AFNAN} 100ml PERFUME',
            '9PM ELIXIR {AFNAN} 100ml PERFUME',
            '9PM REBEL {AFNAN} 100ml PERFUME',
            '9PM 30ml - PERFUME OIL',
            '9PM 50ml - PERFUME OIL',
            'AMEER AL OUDH 100ml PERFUME',
            'AL WISAM {By Rasasi} 100ml PERFUME',
            'A THOUSAND WISHES 30ml - PERFUME OIL',
            'ACTIVE WOMAN 50ml - PERFUME OIL',
            'AIR FRESHENERS 300MLS',
            '50ML BOTTLE CLEAR PERFUME Filled',
            '30ml Refill Bottle',
            '50ml Refill Bottle',
            '9AM 9PM SET {30MlsX4} 30ml PERFUME',
        ];

        $rows = Variation::query()
            ->join('products as p', 'p.id', '=', 'variations.product_id')
            ->where('p.business_id', $business_id)
            ->where('p.is_inactive', 0)
            ->where('p.not_for_selling', 0)
            ->whereNull('variations.deleted_at')
            ->where('p.type', '!=', 'modifier')
            ->where('variations.sell_price_inc_tax', '>', 0)
            ->select(
                'p.id as product_id',
                'p.name',
                'variations.id as variation_id',
                'variations.sell_price_inc_tax'
            )
            ->get();

        $picked = $rows->filter(function ($row) use ($preferred_names) {
            return in_array($row->name, $preferred_names, true);
        })->values();

        if ($picked->count() < 8) {
            $picked = $rows->sortByDesc('sell_price_inc_tax')->take(15)->values();
        }

        return $picked->values();
    }

    private function saleBlueprints(int $walk_in, array $customers, $catalog): array
    {
        $p = function (int $i) use ($catalog) {
            return $catalog[$i % $catalog->count()];
        };

        $line = function (int $i, $qty) use ($p) {
            $item = $p($i);

            return [
                'product_id' => $item->product_id,
                'variation_id' => $item->variation_id,
                'quantity' => $qty,
                'unit_price_inc_tax' => (float) $item->sell_price_inc_tax,
            ];
        };

        $n = 1;
        $inv = function () use (&$n) {
            $no = 'TEST-SR-'.str_pad((string) $n, 4, '0', STR_PAD_LEFT);
            $n++;

            return $no;
        };

        $c1 = $customers[0];
        $c2 = $customers[1];
        $c3 = $customers[2];

        return [
            // Today — Vawa, mixed cashiers
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-26 09:15:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(0, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-26 11:40:00', 'location_id' => 1, 'contact_id' => $c1, 'created_by' => 3, 'method' => 'paybill', 'lines' => [$line(1, 1), $line(11, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-26 15:05:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'till', 'lines' => [$line(4, 2), $line(12, 2)]],

            // Yesterday
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-25 10:22:00', 'location_id' => 1, 'contact_id' => $c2, 'created_by' => 1, 'method' => 'cash', 'lines' => [$line(2, 1), $line(5, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-25 16:48:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 3, 'method' => 'paybill', 'lines' => [$line(6, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-25 18:12:00', 'location_id' => 1, 'contact_id' => $c3, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(10, 3), $line(0, 1)]],

            // This week (Mon 24 Aug)
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-24 09:05:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'till', 'lines' => [$line(7, 1), $line(8, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-24 14:30:00', 'location_id' => 2, 'contact_id' => $c1, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(3, 2)]],

            // Last week
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-22 12:10:00', 'location_id' => 1, 'contact_id' => $c2, 'created_by' => 1, 'method' => 'paybill', 'lines' => [$line(9, 1), $line(13, 1), $line(11, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-20 11:00:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(1, 1), $line(4, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-18 17:25:00', 'location_id' => 1, 'contact_id' => $c3, 'created_by' => 3, 'method' => 'till', 'lines' => [$line(14, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-17 10:40:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(0, 2), $line(12, 2)]],

            // Earlier this month
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-12 13:15:00', 'location_id' => 1, 'contact_id' => $c1, 'created_by' => 1, 'method' => 'paybill', 'lines' => [$line(6, 1), $line(2, 1), $line(10, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-08 09:50:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(5, 3)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-03 16:05:00', 'location_id' => 1, 'contact_id' => $c2, 'created_by' => 4, 'method' => 'till', 'lines' => [$line(3, 1), $line(8, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-08-01 11:20:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 1, 'method' => 'cash', 'lines' => [$line(7, 1)]],

            // Last month (July)
            ['invoice_no' => $inv(), 'transaction_date' => '2026-07-28 14:45:00', 'location_id' => 2, 'contact_id' => $c3, 'created_by' => 3, 'method' => 'paybill', 'lines' => [$line(0, 1), $line(1, 1), $line(11, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-07-21 10:10:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(9, 2), $line(13, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-07-15 15:30:00', 'location_id' => 1, 'contact_id' => $c1, 'created_by' => 1, 'method' => 'till', 'lines' => [$line(14, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-07-09 12:00:00', 'location_id' => 2, 'contact_id' => $c2, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(4, 4), $line(12, 4)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-07-02 09:35:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'paybill', 'lines' => [$line(2, 1)]],

            // June — this quarter
            ['invoice_no' => $inv(), 'transaction_date' => '2026-06-24 11:55:00', 'location_id' => 1, 'contact_id' => $c3, 'created_by' => 1, 'method' => 'cash', 'lines' => [$line(6, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-06-11 16:40:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 3, 'method' => 'till', 'lines' => [$line(1, 1), $line(5, 1), $line(10, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-06-03 13:05:00', 'location_id' => 1, 'contact_id' => $c1, 'created_by' => 4, 'method' => 'paybill', 'lines' => [$line(8, 3)]],

            // Last quarter (Apr–Jun already has June; add Apr/May)
            ['invoice_no' => $inv(), 'transaction_date' => '2026-05-19 10:25:00', 'location_id' => 2, 'contact_id' => $c2, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(3, 1), $line(7, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-05-06 14:50:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 1, 'method' => 'paybill', 'lines' => [$line(0, 3)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-04-22 09:15:00', 'location_id' => 1, 'contact_id' => $c3, 'created_by' => 4, 'method' => 'till', 'lines' => [$line(14, 1), $line(11, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-04-08 17:10:00', 'location_id' => 2, 'contact_id' => $walk_in, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(9, 1), $line(4, 1), $line(12, 1)]],

            // Earlier this year
            ['invoice_no' => $inv(), 'transaction_date' => '2026-03-18 12:40:00', 'location_id' => 1, 'contact_id' => $c1, 'created_by' => 1, 'method' => 'paybill', 'lines' => [$line(2, 2), $line(6, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-02-14 15:20:00', 'location_id' => 2, 'contact_id' => $c2, 'created_by' => 4, 'method' => 'cash', 'lines' => [$line(1, 1), $line(8, 1)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-01-20 11:05:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 3, 'method' => 'till', 'lines' => [$line(0, 1), $line(10, 5)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2026-01-08 16:30:00', 'location_id' => 2, 'contact_id' => $c3, 'created_by' => 1, 'method' => 'cash', 'lines' => [$line(3, 1)]],

            // Last year
            ['invoice_no' => $inv(), 'transaction_date' => '2025-12-18 13:45:00', 'location_id' => 1, 'contact_id' => $walk_in, 'created_by' => 4, 'method' => 'paybill', 'lines' => [$line(7, 2), $line(5, 2)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2025-12-05 10:00:00', 'location_id' => 2, 'contact_id' => $c1, 'created_by' => 3, 'method' => 'cash', 'lines' => [$line(14, 1), $line(13, 3)]],
            ['invoice_no' => $inv(), 'transaction_date' => '2025-11-11 14:15:00', 'location_id' => 1, 'contact_id' => $c2, 'created_by' => 1, 'method' => 'till', 'lines' => [$line(6, 1), $line(1, 2)]],
        ];
    }
}
