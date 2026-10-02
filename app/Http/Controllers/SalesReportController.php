<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesReportExport;
use App\Exports\SalesReportCsvExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    protected $transactionUtil;
    protected $commonUtil;

    public function __construct(TransactionUtil $transactionUtil, Util $commonUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->commonUtil = $commonUtil;
    }

    /**
     * Show main Sales Analytics & Report dashboard
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('purchase_n_sell_report.view') && !auth()->user()->can('report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $permitted_locations = auth()->user()->permitted_locations();
        $business_locations = BusinessLocation::forDropdown($business_id, true, false, true, $permitted_locations);

        $default_location_id = BusinessLocation::where('business_id', $business_id)
            ->where('name', 'like', '%Vawa%')
            ->value('id');

        $customers = $this->customersForLocation($business_id, $default_location_id);
        $cashiers = $this->cashiersForLocation($business_id, $default_location_id);

        return view('report.sales_analytics', compact('business_locations', 'customers', 'cashiers', 'default_location_id'));
    }

    /**
     * Get JSON analytics data (Summary Metrics + Grouped Sales Table)
     */
    public function getAnalyticsData(Request $request)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('purchase_n_sell_report.view') && !auth()->user()->can('report.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = $request->session()->get('user.business_id');
        $dates = $this->resolveDateRange($request);
        $start_date = $dates['start'];
        $end_date = $dates['end'];
        $group_by = $request->input('group_by', 'daily');

        // Base Sell Query
        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        // Apply filters
        $this->applyFilters($query, $request, $start_date, $end_date);

        // Summary KPI Metrics
        $total_orders = (clone $query)->count();
        $total_sales = (clone $query)->sum(DB::raw('final_total - COALESCE(shipping_charges, 0)'));
        $avg_order_value = $total_orders > 0 ? ($total_sales / $total_orders) : 0;
        $total_header_discount = (clone $query)->sum('discount_amount');
        $total_header_tax = (clone $query)->sum('tax_amount');

        // Line level details for items, discounts, tax
        $line_stats = DB::table('transactions')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        $this->applyFiltersDirect($line_stats, $request, $start_date, $end_date);

        $total_items_sold = $line_stats->sum('transaction_sell_lines.quantity') ?? 0;
        $line_discounts = $line_stats->sum(DB::raw("
            CASE 
                WHEN line_discount_type = 'fixed' THEN line_discount_amount * quantity
                WHEN line_discount_type = 'percentage' THEN (unit_price_before_discount * line_discount_amount / 100) * quantity
                ELSE 0 
            END
        ")) ?? 0;
        $line_taxes = $line_stats->sum('transaction_sell_lines.item_tax') ?? 0;

        $total_discounts = $total_header_discount + $line_discounts;
        $total_tax = $total_header_tax + $line_taxes;

        // Grouped Sales Performance Query
        $select_group_sql = $this->getGroupSql($group_by);

        $grouped_query = DB::table('transactions')
            ->leftJoin('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->select(
                DB::raw($select_group_sql . ' as period_key'),
                DB::raw('MIN(transactions.transaction_date) as min_date'),
                DB::raw('MAX(transactions.transaction_date) as max_date'),
                DB::raw('COUNT(DISTINCT transactions.id) as order_count'),
                DB::raw('COALESCE(SUM(transaction_sell_lines.quantity), 0) as items_sold'),
                DB::raw('(SELECT COALESCE(SUM(t2.final_total - COALESCE(t2.shipping_charges, 0)), 0) FROM transactions t2 WHERE t2.business_id = transactions.business_id AND t2.type = "sell" AND t2.status = "final" ' . $this->getSubqueryWhere($group_by, $request, $start_date, $end_date) . ') as total_sales')
            )
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        $this->applyFiltersDirect($grouped_query, $request, $start_date, $end_date);

        $grouped_query->groupBy(DB::raw('period_key'));

        // Sorting
        $sort_by = $request->input('sort_by', 'date');
        $sort_dir = strtolower($request->input('sort_dir', 'desc')) == 'asc' ? 'asc' : 'desc';

        if ($sort_by === 'sales') {
            $grouped_query->orderBy('total_sales', $sort_dir);
        } elseif ($sort_by === 'orders') {
            $grouped_query->orderBy('order_count', $sort_dir);
        } else {
            $grouped_query->orderBy('period_key', $sort_dir);
        }

        $all_grouped_records = $grouped_query->get();

        // Process period labels and formatted metrics
        $processed_records = [];
        foreach ($all_grouped_records as $rec) {
            $label = $this->formatPeriodLabel($rec->period_key, $group_by, $rec->min_date);
            
            // Format start and end date for drill-down modal range
            $p_dates = $this->getPeriodStartEnd($rec->period_key, $group_by, $rec->min_date, $rec->max_date);

            $processed_records[] = [
                'period_key' => $rec->period_key,
                'period_label' => $label,
                'start_date' => $p_dates['start'],
                'end_date' => $p_dates['end'],
                'order_count' => (int) $rec->order_count,
                'items_sold' => (float) $rec->items_sold,
                'total_sales' => (float) $rec->total_sales,
                'formatted_sales' => $this->transactionUtil->num_f($rec->total_sales, true),
                'formatted_items' => $this->transactionUtil->num_f($rec->items_sold, false, null, true),
            ];
        }

        // Return response
        return response()->json([
            'summary' => [
                'total_sales' => (float) $total_sales,
                'formatted_total_sales' => $this->transactionUtil->num_f($total_sales, true),
                'total_orders' => (int) $total_orders,
                'total_items_sold' => (float) $total_items_sold,
                'formatted_total_items_sold' => $this->transactionUtil->num_f($total_items_sold, false, null, true),
                'avg_order_value' => (float) $avg_order_value,
                'formatted_avg_order_value' => $this->transactionUtil->num_f($avg_order_value, true),
                'total_discounts' => (float) $total_discounts,
                'formatted_total_discounts' => $this->transactionUtil->num_f($total_discounts, true),
                'total_tax' => (float) $total_tax,
                'formatted_total_tax' => $this->transactionUtil->num_f($total_tax, true),
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            'period_records' => $processed_records,
        ]);
    }

    /**
     * Get list of transactions for period drill-down modal
     */
    public function getPeriodTransactions(Request $request)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('purchase_n_sell_report.view') && !auth()->user()->can('report.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = $request->session()->get('user.business_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $search = $request->input('search');

        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->with([
                'contact:id,name,supplier_business_name',
                'sales_person:id,first_name,last_name',
                'payment_lines:id,transaction_id,method,amount'
            ]);

        $this->applyFilters($query, $request, $start_date, $end_date);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.invoice_no', 'like', '%' . $search . '%')
                    ->orWhereHas('contact', function ($cq) use ($search) {
                        $cq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('supplier_business_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $transactions = $query->orderBy('transactions.transaction_date', 'desc')->get();

        $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

        $result = $transactions->map(function ($t) use ($payment_types) {
            $payment_methods = $t->payment_lines->pluck('method')->map(function ($m) use ($payment_types) {
                return $payment_types[$m] ?? ucfirst($m);
            })->unique()->implode(', ');

            if (empty($payment_methods)) {
                $payment_methods = 'N/A';
            }

            $customer_name = $t->contact ? ($t->contact->name ?? $t->contact->supplier_business_name) : 'Walk-In Customer';
            $cashier = $t->sales_person ? trim($t->sales_person->first_name . ' ' . $t->sales_person->last_name) : 'N/A';

            return [
                'id' => $t->id,
                'invoice_no' => $t->invoice_no,
                'transaction_date' => $this->transactionUtil->format_date($t->transaction_date, true),
                'customer_name' => $customer_name,
                'cashier' => $cashier,
                'payment_methods' => $payment_methods,
                'payment_status' => ucfirst($t->payment_status),
                'final_total' => (float) $t->final_total,
                'formatted_total' => $this->transactionUtil->num_f($t->final_total, true),
            ];
        });

        return response()->json([
            'transactions' => $result,
            'count' => $result->count(),
        ]);
    }

    /**
     * Get itemized product details for a single invoice in drill-down modal
     */
    public function getTransactionDetails(Request $request, $id)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('purchase_n_sell_report.view') && !auth()->user()->can('report.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business_id = $request->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->with([
                'sell_lines.product',
                'sell_lines.variations',
                'contact',
                'sales_person'
            ])
            ->firstOrFail();

        $lines = $transaction->sell_lines->map(function ($line) {
            $product_name = $line->product ? $line->product->name : 'N/A';
            $variation_name = ($line->variations && $line->variations->name != 'DUMMY') ? ' - ' . $line->variations->name : '';
            $full_product_name = $product_name . $variation_name;

            $sku = $line->variations ? $line->variations->sub_sku : ($line->product ? $line->product->sku : 'N/A');

            $qty = (float) $line->quantity;
            $unit_price = (float) $line->unit_price_before_discount;
            
            $discount_amount = (float) $line->get_discount_amount();
            $line_total = (float) ($line->unit_price_inc_tax * $line->quantity);

            return [
                'id' => $line->id,
                'product_name' => $full_product_name,
                'sku' => $sku,
                'quantity' => $qty,
                'formatted_quantity' => $this->transactionUtil->num_f($qty, false, null, true),
                'unit_price' => $unit_price,
                'formatted_unit_price' => $this->transactionUtil->num_f($unit_price, true),
                'discount_amount' => $discount_amount,
                'formatted_discount' => $this->transactionUtil->num_f($discount_amount, true),
                'line_total' => $line_total,
                'formatted_line_total' => $this->transactionUtil->num_f($line_total, true),
            ];
        });

        return response()->json([
            'invoice_no' => $transaction->invoice_no,
            'transaction_date' => $this->transactionUtil->format_date($transaction->transaction_date, true),
            'customer_name' => $transaction->contact ? $transaction->contact->name : 'Walk-In Customer',
            'total_amount' => $this->transactionUtil->num_f($transaction->final_total, true),
            'discount_amount' => $this->transactionUtil->num_f($transaction->discount_amount, true),
            'tax_amount' => $this->transactionUtil->num_f($transaction->tax_amount, true),
            'sell_lines' => $lines,
        ]);
    }

    /**
     * Export sales report to PDF, Excel, or CSV
     */
    public function export(Request $request, $format)
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('purchase_n_sell_report.view') && !auth()->user()->can('report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dates = $this->resolveDateRange($request);
        $start_date = $dates['start'];
        $end_date = $dates['end'];
        $group_by = $request->input('group_by', 'daily');

        // Fetch Analytics Data
        $dataResponse = $this->getAnalyticsData($request)->getData(true);
        $summary = $dataResponse['summary'];
        $period_records = $dataResponse['period_records'] ?? [];

        $file_name = 'Period_Performance_' . Carbon::now()->format('Y_m_d_His');

        if ($format === 'pdf') {
            $business = DB::table('business')->where('id', $business_id)->first();
            
            $html = view('report.pdf.sales_analytics_pdf', compact('summary', 'period_records', 'business', 'start_date', 'end_date', 'group_by'))->render();

            $temp_dir = public_path('uploads/temp');
            if (!is_dir($temp_dir) || !is_writable($temp_dir)) {
                $temp_dir = storage_path('app/temp');
            }
            if (!file_exists($temp_dir)) {
                @mkdir($temp_dir, 0777, true);
            }

            $mpdf = new Mpdf([
                'tempDir' => $temp_dir,
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);

            $mpdf->SetTitle('Sales Analytics Report');
            $mpdf->WriteHTML($html);
            return response($mpdf->Output($file_name . '.pdf', 'I'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file_name . '.pdf"',
            ]);
        }

        if ($format === 'csv') {
            return Excel::download(new SalesReportCsvExport($period_records), $file_name . '.csv');
        }

        if ($format === 'xlsx') {
            $date_format = session('business.date_format', 'd/m/Y');
            $meta = [
                'generated_at' => Carbon::now()->format($date_format . ' H:i'),
                'date_range' => Carbon::parse($start_date)->format($date_format) . ' – ' . Carbon::parse($end_date)->format($date_format),
                'grouping' => ucfirst($group_by),
                'location' => $this->exportFilterLocationLabel($request, $business_id),
                'customer' => $this->exportFilterCustomerLabel($request, $business_id),
                'cashier' => $this->exportFilterCashierLabel($request, $business_id),
            ];

            return Excel::download(new SalesReportExport($period_records, $meta), $file_name . '.xlsx');
        }

        return redirect()->back();
    }

    /**
     * Resolve date range using the same start/end dates as the home dashboard picker.
     */
    private function resolveDateRange(Request $request)
    {
        if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
            return [
                'start' => Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString(),
                'end' => Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString(),
            ];
        }

        $preset = $request->input('date_preset', 'today');
        if ($preset === 'this_financial_year') {
            $preset = 'current_financial_year';
        }

        if ($preset === 'current_financial_year') {
            $fy_start = $request->session()->get('financial_year.start');
            $fy_end = $request->session()->get('financial_year.end');
            if (!empty($fy_start) && !empty($fy_end)) {
                return [
                    'start' => Carbon::parse($fy_start)->startOfDay()->toDateTimeString(),
                    'end' => Carbon::parse($fy_end)->endOfDay()->toDateTimeString(),
                ];
            }
        }

        if ($preset === 'last_financial_year') {
            $fy_start = $request->session()->get('financial_year.start');
            $fy_end = $request->session()->get('financial_year.end');
            if (!empty($fy_start) && !empty($fy_end)) {
                return [
                    'start' => Carbon::parse($fy_start)->subYear()->startOfDay()->toDateTimeString(),
                    'end' => Carbon::parse($fy_end)->subYear()->endOfDay()->toDateTimeString(),
                ];
            }
        }

        $range = $this->commonUtil->getDateRange($preset);
        if (!empty($range['start']) && !empty($range['end'])) {
            return [
                'start' => Carbon::parse($range['start'])->startOfDay()->toDateTimeString(),
                'end' => Carbon::parse($range['end'])->endOfDay()->toDateTimeString(),
            ];
        }

        return [
            'start' => Carbon::today()->startOfDay()->toDateTimeString(),
            'end' => Carbon::today()->endOfDay()->toDateTimeString(),
        ];
    }

    private function exportFilterLocationLabel(Request $request, $business_id)
    {
        $location_id = $request->input('location_id');
        if (empty($location_id)) {
            return 'All locations';
        }

        return BusinessLocation::where('business_id', $business_id)->where('id', $location_id)->value('name') ?: 'All locations';
    }

    private function exportFilterCustomerLabel(Request $request, $business_id)
    {
        $customer_id = $request->input('customer_id');
        if (empty($customer_id)) {
            return 'All customers';
        }

        $contact = Contact::where('business_id', $business_id)->find($customer_id);

        return $contact ? ($contact->name ?: $contact->supplier_business_name) : 'All customers';
    }

    private function exportFilterCashierLabel(Request $request, $business_id)
    {
        $created_by = $request->input('created_by');
        if (empty($created_by)) {
            return 'All cashiers';
        }

        $user = User::where('id', $created_by)->first();
        if (! $user) {
            return 'All cashiers';
        }

        return trim($user->first_name . ' ' . $user->last_name) ?: 'All cashiers';
    }

    private function applyFilters($query, Request $request, $start_date, $end_date)
    {
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', Carbon::parse($start_date)->toDateString())
                ->whereDate('transactions.transaction_date', '<=', Carbon::parse($end_date)->toDateString());
        }

        if (!empty($request->input('location_id'))) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        if (!empty($request->input('customer_id'))) {
            $query->where('transactions.contact_id', $request->input('customer_id'));
        }

        if (!empty($request->input('created_by'))) {
            $query->where('transactions.created_by', $request->input('created_by'));
        }
    }

    private function applyFiltersDirect($query, Request $request, $start_date, $end_date)
    {
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', Carbon::parse($start_date)->toDateString())
                ->whereDate('transactions.transaction_date', '<=', Carbon::parse($end_date)->toDateString());
        }

        if (!empty($request->input('location_id'))) {
            $query->where('transactions.location_id', $request->input('location_id'));
        }

        if (!empty($request->input('customer_id'))) {
            $query->where('transactions.contact_id', $request->input('customer_id'));
        }

        if (!empty($request->input('created_by'))) {
            $query->where('transactions.created_by', $request->input('created_by'));
        }
    }

    private function getGroupSql($group_by)
    {
        switch ($group_by) {
            case 'weekly':
                return "DATE_FORMAT(transactions.transaction_date, '%Y-W%u')";
            case 'monthly':
                return "DATE_FORMAT(transactions.transaction_date, '%Y-%m')";
            case 'quarterly':
                return "CONCAT(YEAR(transactions.transaction_date), '-Q', QUARTER(transactions.transaction_date))";
            case 'yearly':
                return "DATE_FORMAT(transactions.transaction_date, '%Y')";
            case 'daily':
            default:
                return "DATE_FORMAT(transactions.transaction_date, '%Y-%m-%d')";
        }
    }

    private function getSubqueryWhere($group_by, Request $request, $start_date, $end_date)
    {
        $where = " AND DATE(t2.transaction_date) >= '" . Carbon::parse($start_date)->toDateString() . "' AND DATE(t2.transaction_date) <= '" . Carbon::parse($end_date)->toDateString() . "'";

        switch ($group_by) {
            case 'weekly':
                $where .= " AND DATE_FORMAT(t2.transaction_date, '%Y-W%u') = period_key";
                break;
            case 'monthly':
                $where .= " AND DATE_FORMAT(t2.transaction_date, '%Y-%m') = period_key";
                break;
            case 'quarterly':
                $where .= " AND CONCAT(YEAR(t2.transaction_date), '-Q', QUARTER(t2.transaction_date)) = period_key";
                break;
            case 'yearly':
                $where .= " AND DATE_FORMAT(t2.transaction_date, '%Y') = period_key";
                break;
            case 'daily':
            default:
                $where .= " AND DATE_FORMAT(t2.transaction_date, '%Y-%m-%d') = period_key";
                break;
        }

        if (!empty($request->input('location_id'))) {
            $where .= " AND t2.location_id = " . intval($request->input('location_id'));
        }
        if (!empty($request->input('customer_id'))) {
            $where .= " AND t2.contact_id = " . intval($request->input('customer_id'));
        }
        if (!empty($request->input('created_by'))) {
            $where .= " AND t2.created_by = " . intval($request->input('created_by'));
        }

        return $where;
    }

    private function formatPeriodLabel($period_key, $group_by, $sample_date)
    {
        if (empty($sample_date)) {
            return $period_key;
        }

        $cDate = Carbon::parse($sample_date);

        switch ($group_by) {
            case 'weekly':
                $start = $cDate->copy()->startOfWeek();
                $end = $cDate->copy()->endOfWeek();
                return $start->format('M d, Y') . ' - ' . $end->format('M d, Y') . ' (' . $period_key . ')';
            case 'monthly':
                return $cDate->format('F Y');
            case 'quarterly':
                return 'Quarter ' . $cDate->quarter . ', ' . $cDate->year;
            case 'yearly':
                return 'Year ' . $cDate->year;
            case 'daily':
            default:
                return $cDate->format('D, M d, Y');
        }
    }

    private function getPeriodStartEnd($period_key, $group_by, $min_date, $max_date)
    {
        $cMin = Carbon::parse($min_date);
        $cMax = Carbon::parse($max_date);

        switch ($group_by) {
            case 'weekly':
                return [
                    'start' => $cMin->copy()->startOfWeek()->startOfDay()->toDateTimeString(),
                    'end' => $cMax->copy()->endOfWeek()->endOfDay()->toDateTimeString(),
                ];
            case 'monthly':
                return [
                    'start' => $cMin->copy()->startOfMonth()->startOfDay()->toDateTimeString(),
                    'end' => $cMax->copy()->endOfMonth()->endOfDay()->toDateTimeString(),
                ];
            case 'quarterly':
                return [
                    'start' => $cMin->copy()->startOfQuarter()->startOfDay()->toDateTimeString(),
                    'end' => $cMax->copy()->endOfQuarter()->endOfDay()->toDateTimeString(),
                ];
            case 'yearly':
                return [
                    'start' => $cMin->copy()->startOfYear()->startOfDay()->toDateTimeString(),
                    'end' => $cMax->copy()->endOfYear()->endOfDay()->toDateTimeString(),
                ];
            case 'daily':
            default:
                return [
                    'start' => $cMin->copy()->startOfDay()->toDateTimeString(),
                    'end' => $cMax->copy()->endOfDay()->toDateTimeString(),
                ];
        }
    }

    /**
     * Discounts Allowed Report: which products were discounted, by whom, and on which receipt.
     */
    public function discountActivity(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view') && ! auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $cashier_name = "TRIM(CONCAT(COALESCE(cashier.surname, ''), ' ', COALESCE(cashier.first_name, ''), ' ', COALESCE(cashier.last_name, '')))";
            $product_name = "TRIM(CONCAT(p.name, IF(v.name IS NULL OR v.name = '' OR v.name = 'DUMMY', '', CONCAT(' - ', v.name))))";

            $item_discounts = DB::table('transaction_sell_lines as tsl')
                ->join('transactions as t', 't.id', '=', 'tsl.transaction_id')
                ->join('products as p', 'p.id', '=', 'tsl.product_id')
                ->leftJoin('variations as v', 'v.id', '=', 'tsl.variation_id')
                ->leftJoin('users as cashier', 'cashier.id', '=', 't.created_by')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->where('tsl.line_discount_amount', '>', 0)
                ->whereNull('tsl.parent_sell_line_id')
                ->select(
                    't.transaction_date as discounted_at',
                    't.invoice_no',
                    DB::raw("{$cashier_name} as cashier_name"),
                    DB::raw("{$product_name} as product_name"),
                    DB::raw("'Item' as discount_on"),
                    DB::raw("IF(tsl.line_discount_type = 'percentage', (tsl.unit_price_before_discount * tsl.quantity * tsl.line_discount_amount / 100), (tsl.line_discount_amount * tsl.quantity)) as discount_amount"),
                    't.created_by',
                    't.location_id'
                );

            $sale_discounts = DB::table('transactions as t')
                ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 't.id')
                ->join('products as p', 'p.id', '=', 'tsl.product_id')
                ->leftJoin('users as cashier', 'cashier.id', '=', 't.created_by')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->where('t.discount_amount', '>', 0)
                ->whereNull('tsl.parent_sell_line_id')
                ->groupBy(
                    't.id',
                    't.transaction_date',
                    't.invoice_no',
                    't.created_by',
                    't.location_id',
                    't.discount_type',
                    't.discount_amount',
                    't.total_before_tax',
                    'cashier.surname',
                    'cashier.first_name',
                    'cashier.last_name'
                )
                ->select(
                    't.transaction_date as discounted_at',
                    't.invoice_no',
                    DB::raw("{$cashier_name} as cashier_name"),
                    DB::raw("GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') as product_name"),
                    DB::raw("'Sale' as discount_on"),
                    DB::raw("IF(t.discount_type = 'percentage', (t.total_before_tax * t.discount_amount / 100), t.discount_amount) as discount_amount"),
                    't.created_by',
                    't.location_id'
                );

            $query = DB::query()->fromSub($item_discounts->unionAll($sale_discounts), 'discount_rows');

            $location_id = $request->location_id;
            if (empty($location_id)) {
                $location_id = BusinessLocation::where('business_id', $business_id)
                    ->where('name', 'like', '%Vawa%')
                    ->value('id');
            }
            if (! empty($location_id)) {
                $query->where('location_id', $location_id);
            }
            if (! empty($request->created_by)) {
                $query->where('created_by', $request->created_by);
            }
            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $query->whereDate('discounted_at', '>=', $request->start_date)
                    ->whereDate('discounted_at', '<=', $request->end_date);
            }

            return DataTables::of($query)
                ->editColumn('discounted_at', function ($row) {
                    return $this->commonUtil->format_date($row->discounted_at, true);
                })
                ->editColumn('invoice_no', function ($row) {
                    return '<span class="tw-font-semibold tw-text-gray-900">'.e($row->invoice_no).'</span>';
                })
                ->editColumn('cashier_name', function ($row) {
                    return trim($row->cashier_name) !== '' ? e(trim($row->cashier_name)) : '—';
                })
                ->editColumn('product_name', function ($row) {
                    $names = array_values(array_filter(array_map('trim', explode(', ', (string) $row->product_name))));

                    if (empty($names)) {
                        return '—';
                    }

                    $lines = array_map(function ($name) {
                        return '<div class="da-product-line">'.e($name).'</div>';
                    }, $names);

                    return '<div class="da-products">'.implode('', $lines).'</div>';
                })
                ->editColumn('discount_on', function ($row) {
                    return $row->discount_on === 'Item' ? 'Item' : 'Entire sale';
                })
                ->editColumn('discount_amount', function ($row) {
                    return "<span class='display_currency' data-currency_symbol='true'>{$row->discount_amount}</span>";
                })
                ->rawColumns(['invoice_no', 'product_name', 'discount_amount'])
                ->make(true);
        }

        $permitted_locations = auth()->user()->permitted_locations();
        $business_locations = BusinessLocation::forDropdown($business_id, false, false, true, $permitted_locations);

        $default_location_id = BusinessLocation::where('business_id', $business_id)
            ->where('name', 'like', '%Vawa%')
            ->value('id');

        if (empty($default_location_id)) {
            $default_location_id = $business_locations->keys()->first();
        }

        $cashiers = $this->cashiersForLocation($business_id, $default_location_id);

        return view('report.discounts_allowed', compact('business_locations', 'cashiers', 'default_location_id'));
    }

    /**
     * Cashiers assigned to (or who have sold at) a location, for the Discounts Allowed filter.
     */
    public function discountActivityCashiers(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view') && ! auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id');

        if (empty($location_id)) {
            $location_id = BusinessLocation::where('business_id', $business_id)
                ->where('name', 'like', '%Vawa%')
                ->value('id');
        }

        return response()->json($this->cashiersForLocation($business_id, $location_id));
    }

    private function cashiersForLocation($business_id, $location_id)
    {
        $query = User::where('users.business_id', $business_id)
            ->user()
            ->where('is_cmmsn_agnt', 0);

        if (! empty($location_id)) {
            $seller_ids = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('location_id', $location_id)
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by');

            $query->where(function ($q) use ($location_id, $seller_ids) {
                $q->whereHas('permissions', function ($p) use ($location_id) {
                    $p->where('permissions.name', 'location.'.$location_id);
                });
                if ($seller_ids->isNotEmpty()) {
                    $q->orWhereIn('users.id', $seller_ids);
                }
            });
        }

        $users = $query
            ->select(
                'users.id',
                DB::raw("TRIM(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, ''))) as full_name")
            )
            ->orderBy('users.first_name')
            ->get()
            ->pluck('full_name', 'id');

        return $users->prepend(__('lang_v1.all'), '');
    }

    /**
     * Customers and cashiers for a location, used by Sales Report filters.
     */
    public function getFilterOptions(Request $request)
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('purchase_n_sell_report.view') && ! auth()->user()->can('report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id');

        return response()->json([
            'customers' => $this->customersForLocation($business_id, $location_id),
            'cashiers' => $this->cashiersForLocation($business_id, $location_id),
        ]);
    }

    private function customersForLocation($business_id, $location_id)
    {
        $query = Contact::where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both'])
            ->active();

        if (auth()->check() && ! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            $query->onlyOwnContact();
        }

        if (! empty($location_id)) {
            $contact_ids = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('location_id', $location_id)
                ->whereNotNull('contact_id')
                ->distinct()
                ->pluck('contact_id');

            $query->whereIn('contacts.id', $contact_ids);
        }

        $customers = $query
            ->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name, ' (', contacts.contact_id, ')')) AS customer")
            )
            ->orderBy('contacts.name')
            ->get()
            ->pluck('customer', 'id');

        return $customers->prepend('All Customers', '');
    }
}
