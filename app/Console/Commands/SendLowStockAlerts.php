<?php

namespace App\Console\Commands;

use App\Business;
use App\User;
use App\Utils\ProductUtil;
use App\Mail\LowStockAlertMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:sendLowStockAlerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email alerts for products whose stock is below the alert quantity';

    protected $productUtil;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        parent::__construct();
        $this->productUtil = $productUtil;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting low stock email alerts check...');

        $businesses = Business::get();

        foreach ($businesses as $business) {
            $this->info("Checking stock for business: {$business->name} (ID: {$business->id})");

            // Get all admin users for this business
            $adminRole = 'Admin#' . $business->id;
            $admins = User::role($adminRole)
                ->where('business_id', $business->id)
                ->get();

            $emails = $admins->pluck('email')->filter()->all();

            if (empty($emails)) {
                $this->warn("No admin emails found for business ID: {$business->id}. Skipping.");
                continue;
            }

            // Query low stock items for this business using ProductUtil
            $low_stock_query = $this->productUtil->getProductAlert($business->id);
            
            // Add alert_quantity explicitly since it's not selected by default in ProductUtil
            $low_stock_items = $low_stock_query
                ->addSelect('p.alert_quantity')
                ->get();

            if ($low_stock_items->isEmpty()) {
                $this->info("No low stock items for business: {$business->name}");
                continue;
            }

            $emails_str = implode(', ', $emails);
            $this->info("Found {$low_stock_items->count()} low stock items. Sending email to: {$emails_str}...");

            try {
                Mail::to($emails)->send(new LowStockAlertMail($business, $low_stock_items));
                $this->info("Email sent successfully to: {$emails_str}");
            } catch (\Exception $e) {
                $this->error("Failed to send email to: {$emails_str}. Error: " . $e->getMessage());
                \Log::error("Failed to send low stock email for business {$business->id} to {$emails_str}: " . $e->getMessage());
            }
        }

        $this->info('Finished low stock email alerts check.');
    }
}
