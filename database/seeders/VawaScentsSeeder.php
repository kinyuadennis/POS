<?php

namespace Database\Seeders;

use App\User;
use App\Business;
use App\Unit;
use App\Product;
use App\Brands;
use App\Category;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\TransactionSellLine;
use App\Variation;
use App\VariationLocationDetails;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VawaScentsSeeder extends Seeder
{
    protected $businessUtil;
    protected $productUtil;

    public function __construct(BusinessUtil $businessUtil, ProductUtil $productUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->productUtil = $productUtil;
    }

    public function run()
    {
        DB::beginTransaction();

        // 1. Create Admins
        // Admin 1 (Main Owner)
        $admin1 = User::create([
            'surname' => 'Mr',
            'first_name' => 'Admin',
            'last_name' => 'Vawa',
            'username' => 'vawa_admin',
            'email' => 'admin@vawascents.com',
            'password' => bcrypt('password'),
            'language' => 'en',
        ]);

        // 2. Create the Vawa Scents Business
        $business_details = [
            'name' => 'Vawa Scents',
            'currency_id' => 133, // KES (Kenyan Shilling)
            'start_date' => '2026-01-01',
            'time_zone' => 'Africa/Nairobi',
            'fy_start_month' => 1,
            'accounting_method' => 'fifo',
            'sell_price_tax' => 'includes',
            'owner_id' => $admin1->id,
            'tax_number_1' => 'P012345678X',
            'tax_label_1' => 'PIN',
            'default_profit_percent' => 25.00,
        ];
        $business = $this->businessUtil->createNewBusiness($business_details);

        // Update main admin user's business association
        $admin1->business_id = $business->id;
        $admin1->save();

        // Seed default resources (roles, scheme, layout, default customer)
        $this->businessUtil->newBusinessDefaultResources($business->id, $admin1->id);

        // Create Admin 2 (Co-Admin)
        $admin2 = User::create([
            'surname' => 'Mr',
            'first_name' => 'Co-Admin',
            'last_name' => 'Vawa',
            'username' => 'vawa_admin_2',
            'email' => 'coadmin@vawascents.com',
            'password' => bcrypt('password'),
            'language' => 'en',
            'business_id' => $business->id,
        ]);
        $admin2->assignRole('Admin#' . $business->id);

        // 3. Create Cashiers
        // Cashier 1: Nairobi CBD Cashier
        $cashier1 = User::create([
            'surname' => 'Ms',
            'first_name' => 'Mercy',
            'last_name' => 'Nairobi',
            'username' => 'cashier_nairobi',
            'email' => 'nairobi_cashier@vawascents.com',
            'password' => bcrypt('password'),
            'language' => 'en',
            'business_id' => $business->id,
        ]);
        $cashier1->assignRole('Cashier#' . $business->id);

        // Cashier 2: Juwaka Scents Mombasa Cashier
        $cashier2 = User::create([
            'surname' => 'Mr',
            'first_name' => 'Joseph',
            'last_name' => 'Mombasa',
            'username' => 'cashier_mombasa',
            'email' => 'mombasa_cashier@vawascents.com',
            'password' => bcrypt('password'),
            'language' => 'en',
            'business_id' => $business->id,
        ]);
        $cashier2->assignRole('Cashier#' . $business->id);

        // Cashier 3: Seasonal/Float Cashier
        $cashier3 = User::create([
            'surname' => 'Ms',
            'first_name' => 'Sarah',
            'last_name' => 'Seasonal',
            'username' => 'cashier_seasonal',
            'email' => 'seasonal_cashier@vawascents.com',
            'password' => bcrypt('password'),
            'language' => 'en',
            'business_id' => $business->id,
        ]);
        $cashier3->assignRole('Cashier#' . $business->id);

        // 4. Create Locations
        // Shop 1: Vawa Scents Nairobi CBD
        $location_details1 = [
            'name' => 'Vawa Scents Nairobi CBD',
            'country' => 'Kenya',
            'state' => 'Nairobi',
            'city' => 'Nairobi',
            'zip_code' => '00100',
            'landmark' => 'Kimathi Street',
            'is_active' => 1,
        ];
        $location1 = $this->businessUtil->addLocation($business->id, $location_details1);
        Permission::firstOrCreate(['name' => 'location.' . $location1->id]);

        // Shop 2: Juwaka Scents Mombasa
        $location_details2 = [
            'name' => 'Juwaka Scents Mombasa',
            'country' => 'Kenya',
            'state' => 'Mombasa',
            'city' => 'Mombasa',
            'zip_code' => '80100',
            'landmark' => 'Moi Avenue',
            'is_active' => 1,
        ];
        $location2 = $this->businessUtil->addLocation($business->id, $location_details2);
        Permission::firstOrCreate(['name' => 'location.' . $location2->id]);

        // 5. Create Unit of Measure (Bottles)
        $unit = Unit::create([
            'business_id' => $business->id,
            'actual_name' => 'Bottles',
            'short_name' => 'Btl',
            'allow_decimal' => 0,
            'created_by' => $admin1->id,
        ]);

        // 6. Create Brands (Perfume Houses)
        $brands_data = ['Chanel', 'Christian Dior', 'Creed', 'Tom Ford', 'Vawa Scents Signature'];
        $brands = [];
        foreach ($brands_data as $brand_name) {
            $brands[$brand_name] = Brands::create([
                'business_id' => $business->id,
                'name' => $brand_name,
                'created_by' => $admin1->id,
            ]);
        }

        // 7. Create Fragrance Categories
        $categories_data = [
            ['name' => 'Oud & Woody', 'short_code' => 'OW'],
            ['name' => 'Floral & Sweet', 'short_code' => 'FS'],
            ['name' => 'Citrus & Fresh', 'short_code' => 'CF'],
            ['name' => 'Amber & Spicy', 'short_code' => 'AS'],
        ];
        $categories = [];
        foreach ($categories_data as $cat_data) {
            $categories[$cat_data['name']] = Category::create([
                'business_id' => $business->id,
                'name' => $cat_data['name'],
                'short_code' => $cat_data['short_code'],
                'category_type' => 'product',
                'created_by' => $admin1->id,
            ]);
        }

        // 8. Create Kenyan Suppliers
        $suppliers = [
            'Nairobi' => Contact::create([
                'business_id' => $business->id,
                'type' => 'supplier',
                'name' => 'Aroma Oils Kenya Ltd',
                'mobile' => '+254711223344',
                'created_by' => $admin1->id,
                'contact_id' => 'SUP-001',
            ]),
            'Packaging' => Contact::create([
                'business_id' => $business->id,
                'type' => 'supplier',
                'name' => 'Kariokor Glass Bottles Suppliers',
                'mobile' => '+254722334455',
                'created_by' => $admin1->id,
                'contact_id' => 'SUP-002',
            ]),
            'Mombasa' => Contact::create([
                'business_id' => $business->id,
                'type' => 'supplier',
                'name' => 'Mombasa Wholesale Fragrances',
                'mobile' => '+254733445566',
                'created_by' => $admin1->id,
                'contact_id' => 'SUP-003',
            ]),
        ];

        // 9. Define 20 Perfume Products
        $perfumes = [
            ['name' => 'Oud Royal', 'sku' => 'OUD-01', 'brand' => 'Tom Ford', 'category' => 'Oud & Woody', 'purchase' => 1500.00, 'sell' => 2500.00, 'alert' => 5],
            ['name' => 'Rose Velvet', 'sku' => 'ROS-01', 'brand' => 'Christian Dior', 'category' => 'Floral & Sweet', 'purchase' => 1000.00, 'sell' => 1800.00, 'alert' => 4],
            ['name' => 'Citrus Breeze', 'sku' => 'CIT-01', 'brand' => 'Creed', 'category' => 'Citrus & Fresh', 'purchase' => 800.00, 'sell' => 1200.00, 'alert' => 10], // low stock test
            ['name' => 'Vanilla Lace', 'sku' => 'VAN-01', 'brand' => 'Vawa Scents Signature', 'category' => 'Floral & Sweet', 'purchase' => 600.00, 'sell' => 1000.00, 'alert' => 5],
            ['name' => 'Midnight Amber', 'sku' => 'AMB-01', 'brand' => 'Christian Dior', 'category' => 'Amber & Spicy', 'purchase' => 1800.00, 'sell' => 3000.00, 'alert' => 6],
            ['name' => 'Blue Ocean Sport', 'sku' => 'BLU-01', 'brand' => 'Creed', 'category' => 'Citrus & Fresh', 'purchase' => 1200.00, 'sell' => 2000.00, 'alert' => 5],
            ['name' => 'Sandalwood Classic', 'sku' => 'SAN-01', 'brand' => 'Tom Ford', 'category' => 'Oud & Woody', 'purchase' => 1400.00, 'sell' => 2200.00, 'alert' => 4],
            ['name' => 'Jasmine Blossom', 'sku' => 'JAS-01', 'brand' => 'Chanel', 'category' => 'Floral & Sweet', 'purchase' => 1100.00, 'sell' => 1800.00, 'alert' => 5],
            ['name' => 'Spicy Tobacco', 'sku' => 'TAB-01', 'brand' => 'Tom Ford', 'category' => 'Amber & Spicy', 'purchase' => 1600.00, 'sell' => 2700.00, 'alert' => 4],
            ['name' => 'Coconut Dream Mist', 'sku' => 'COC-01', 'brand' => 'Vawa Scents Signature', 'category' => 'Floral & Sweet', 'purchase' => 500.00, 'sell' => 900.00, 'alert' => 8],
            ['name' => 'Golden Patchouli', 'sku' => 'PAT-01', 'brand' => 'Creed', 'category' => 'Oud & Woody', 'purchase' => 1500.00, 'sell' => 2400.00, 'alert' => 5],
            ['name' => 'Pure Musk Scented Oil', 'sku' => 'MUS-01', 'brand' => 'Vawa Scents Signature', 'category' => 'Oud & Woody', 'purchase' => 900.00, 'sell' => 1500.00, 'alert' => 6],
            ['name' => 'Black Orchid Premium', 'sku' => 'ORC-01', 'brand' => 'Tom Ford', 'category' => 'Floral & Sweet', 'purchase' => 2000.00, 'sell' => 3500.00, 'alert' => 5],
            ['name' => 'Fresh Lemon Cologne', 'sku' => 'LEM-01', 'brand' => 'Chanel', 'category' => 'Citrus & Fresh', 'purchase' => 700.00, 'sell' => 1100.00, 'alert' => 10], // low stock test
            ['name' => 'Wild Lavender', 'sku' => 'LAV-01', 'brand' => 'Creed', 'category' => 'Floral & Sweet', 'purchase' => 1300.00, 'sell' => 2100.00, 'alert' => 5],
            ['name' => 'Sweet Pomegranate EDP', 'sku' => 'POM-01', 'brand' => 'Vawa Scents Signature', 'category' => 'Floral & Sweet', 'purchase' => 800.00, 'sell' => 1400.00, 'alert' => 5],
            ['name' => 'Leather Luxury', 'sku' => 'LEA-01', 'brand' => 'Tom Ford', 'category' => 'Oud & Woody', 'purchase' => 2200.00, 'sell' => 4000.00, 'alert' => 4],
            ['name' => 'Royal Iris', 'sku' => 'IRI-01', 'brand' => 'Christian Dior', 'category' => 'Floral & Sweet', 'purchase' => 1900.00, 'sell' => 3200.00, 'alert' => 5],
            ['name' => 'Imperial Vetiver', 'sku' => 'VET-01', 'brand' => 'Chanel', 'category' => 'Oud & Woody', 'purchase' => 1400.00, 'sell' => 2300.00, 'alert' => 5],
            ['name' => 'Warm Saffron', 'sku' => 'SAF-01', 'brand' => 'Amber & Spicy', 'category' => 'Amber & Spicy', 'purchase' => 1700.00, 'sell' => 2800.00, 'alert' => 5],
        ];

        $variations_list = [];

        foreach ($perfumes as $p_data) {
            $brand_id = isset($brands[$p_data['brand']]) ? $brands[$p_data['brand']]->id : null;
            $category_id = isset($categories[$p_data['category']]) ? $categories[$p_data['category']]->id : null;

            $product = Product::create([
                'name' => $p_data['name'],
                'business_id' => $business->id,
                'type' => 'single',
                'unit_id' => $unit->id,
                'brand_id' => $brand_id,
                'category_id' => $category_id,
                'tax_type' => 'exclude',
                'enable_stock' => 1,
                'alert_quantity' => $p_data['alert'],
                'sku' => $p_data['sku'],
                'created_by' => $admin1->id,
            ]);

            // Map product to both locations
            DB::table('product_locations')->insert([
                ['product_id' => $product->id, 'location_id' => $location1->id],
                ['product_id' => $product->id, 'location_id' => $location2->id],
            ]);

            // Create variation
            $this->productUtil->createSingleProductVariation(
                $product,
                $product->sku,
                $p_data['purchase'],
                $p_data['purchase'],
                25.00,
                $p_data['sell'],
                $p_data['sell']
            );

            // Fetch created variation
            $variation = Variation::where('product_id', $product->id)->first();
            $variations_list[$p_data['sku']] = $variation;

            // Seed Stocks
            // Nairobi (Location 1) stocks:
            $nairobi_qty = 40;
            if ($p_data['sku'] == 'CIT-01' || $p_data['sku'] == 'LEM-01') {
                $nairobi_qty = 2; // Trigger stock alerts for these two in Nairobi
            }
            VariationLocationDetails::create([
                'product_id' => $product->id,
                'product_variation_id' => $variation->product_variation_id,
                'variation_id' => $variation->id,
                'location_id' => $location1->id,
                'qty_available' => $nairobi_qty,
            ]);

            // Mombasa (Location 2 - Juwaka Scents) stocks:
            $mombasa_qty = 35;
            if ($p_data['sku'] == 'CIT-01' || $p_data['sku'] == 'AMB-01') {
                $mombasa_qty = 1; // Trigger stock alerts for these two in Mombasa
            }
            VariationLocationDetails::create([
                'product_id' => $product->id,
                'product_variation_id' => $variation->product_variation_id,
                'variation_id' => $variation->id,
                'location_id' => $location2->id,
                'qty_available' => $mombasa_qty,
            ]);
        }

        // 10. Seed Default Customer
        $customer = Contact::where('business_id', $business->id)->where('is_default', 1)->first();

        // 11. Seed Transactions (Sales, Purchases, and Expenses for BOTH locations)

        // --- NAIROBI STORE TRANSACTIONS ---
        // Purchase (Nairobi)
        $purchase_nairobi = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location1->id,
            'type' => 'purchase',
            'status' => 'received',
            'payment_status' => 'due',
            'contact_id' => $suppliers['Nairobi']->id,
            'transaction_date' => Carbon::now()->subDays(6)->toDateTimeString(),
            'total_before_tax' => 20000.00,
            'final_total' => 20000.00,
            'ref_no' => 'PUR-NBO-001',
            'created_by' => $admin1->id,
        ]);

        // Sales (Nairobi)
        // Sale 1: Paid sale
        $sale_nbo_1 = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location1->id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'paid',
            'contact_id' => $customer->id,
            'transaction_date' => Carbon::now()->subDays(4)->toDateTimeString(),
            'total_before_tax' => 5000.00,
            'final_total' => 5000.00,
            'invoice_no' => 'INV-NBO-001',
            'created_by' => $admin1->id,
        ]);
        TransactionSellLine::create([
            'transaction_id' => $sale_nbo_1->id,
            'product_id' => $variations_list['OUD-01']->product_id,
            'variation_id' => $variations_list['OUD-01']->id,
            'quantity' => 2,
            'unit_price' => 2500.00,
            'unit_price_before_discount' => 2500.00,
            'unit_price_inc_tax' => 2500.00,
            'item_tax' => 0.00,
        ]);

        // Sale 2: Due sale (trigger sales dues alert)
        $sale_nbo_2 = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location1->id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'due',
            'contact_id' => $customer->id,
            'transaction_date' => Carbon::now()->subDays(2)->toDateTimeString(),
            'total_before_tax' => 1800.00,
            'final_total' => 1800.00,
            'invoice_no' => 'INV-NBO-002',
            'created_by' => $admin1->id,
        ]);
        TransactionSellLine::create([
            'transaction_id' => $sale_nbo_2->id,
            'product_id' => $variations_list['ROS-01']->product_id,
            'variation_id' => $variations_list['ROS-01']->id,
            'quantity' => 1,
            'unit_price' => 1800.00,
            'unit_price_before_discount' => 1800.00,
            'unit_price_inc_tax' => 1800.00,
            'item_tax' => 0.00,
        ]);

        // Expense (Nairobi)
        Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location1->id,
            'type' => 'expense',
            'status' => 'final',
            'payment_status' => 'paid',
            'transaction_date' => Carbon::now()->subDays(1)->toDateTimeString(),
            'total_before_tax' => 3500.00,
            'final_total' => 3500.00,
            'ref_no' => 'EXP-NBO-001',
            'created_by' => $admin1->id,
        ]);


        // --- MOMBASA (JUWAKA SCENTS) STORE TRANSACTIONS ---
        // Purchase (Mombasa)
        $purchase_mombasa = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location2->id,
            'type' => 'purchase',
            'status' => 'received',
            'payment_status' => 'due',
            'contact_id' => $suppliers['Mombasa']->id,
            'transaction_date' => Carbon::now()->subDays(5)->toDateTimeString(),
            'total_before_tax' => 12000.00,
            'final_total' => 12000.00,
            'ref_no' => 'PUR-MSA-001',
            'created_by' => $admin1->id,
        ]);

        // Sales (Mombasa)
        // Sale 1: Paid sale
        $sale_msa_1 = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location2->id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'paid',
            'contact_id' => $customer->id,
            'transaction_date' => Carbon::now()->subDays(3)->toDateTimeString(),
            'total_before_tax' => 3600.00,
            'final_total' => 3600.00,
            'invoice_no' => 'INV-MSA-001',
            'created_by' => $admin1->id,
        ]);
        TransactionSellLine::create([
            'transaction_id' => $sale_msa_1->id,
            'product_id' => $variations_list['ROS-01']->product_id,
            'variation_id' => $variations_list['ROS-01']->id,
            'quantity' => 2,
            'unit_price' => 1800.00,
            'unit_price_before_discount' => 1800.00,
            'unit_price_inc_tax' => 1800.00,
            'item_tax' => 0.00,
        ]);

        // Sale 2: Due sale
        $sale_msa_2 = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location2->id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'due',
            'contact_id' => $customer->id,
            'transaction_date' => Carbon::now()->toDateTimeString(),
            'total_before_tax' => 3000.00,
            'final_total' => 3000.00,
            'invoice_no' => 'INV-MSA-002',
            'created_by' => $admin1->id,
        ]);
        TransactionSellLine::create([
            'transaction_id' => $sale_msa_2->id,
            'product_id' => $variations_list['AMB-01']->product_id,
            'variation_id' => $variations_list['AMB-01']->id,
            'quantity' => 1,
            'unit_price' => 3000.00,
            'unit_price_before_discount' => 3000.00,
            'unit_price_inc_tax' => 3000.00,
            'item_tax' => 0.00,
        ]);

        // Expense (Mombasa)
        Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location2->id,
            'type' => 'expense',
            'status' => 'final',
            'payment_status' => 'paid',
            'transaction_date' => Carbon::now()->subDays(2)->toDateTimeString(),
            'total_before_tax' => 2500.00,
            'final_total' => 2500.00,
            'ref_no' => 'EXP-MSA-001',
            'created_by' => $admin1->id,
        ]);

        DB::commit();
        echo "Successfully seeded expanded Vawa Scents & Juwaka Scents POS data!\n";
        echo "Admins: vawa_admin, vawa_admin_2 (password: password)\n";
        echo "Cashiers: cashier_nairobi, cashier_mombasa, cashier_seasonal (password: password)\n";
    }
}
