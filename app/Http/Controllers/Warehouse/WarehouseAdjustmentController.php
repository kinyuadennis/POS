<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseAdjustmentService;
use App\Services\Warehouse\WarehouseLocationService;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Datatables;
use DB;
use Illuminate\Http\Request;

class WarehouseAdjustmentController extends Controller
{
    public function __construct(
        protected WarehouseLocationService $warehouseLocationService,
        protected WarehouseAdjustmentService $adjustmentService,
        protected ModuleUtil $moduleUtil,
        protected TransactionUtil $transactionUtil
    ) {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.adjust_stock')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $warehouse_ids = $this->warehouseLocationService->getWarehouseLocationIds($business_id);

            if (empty($warehouse_ids)) {
                return Datatables::of(collect())->make(true);
            }

            $stock_adjustments = Transaction::join(
                'business_locations AS BL',
                'transactions.location_id',
                '=',
                'BL.id'
            )
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'stock_adjustment')
                ->whereIn('transactions.location_id', $warehouse_ids)
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'ref_no',
                    'BL.name as location_name',
                    'adjustment_type',
                    'final_total',
                    'total_amount_recovered',
                    'additional_notes',
                    'transactions.id as DT_RowId',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                );

            $location_id = $request->get('location_id');
            if (! empty($location_id)) {
                $stock_adjustments->where('transactions.location_id', $location_id);
            }

            $hide = ! auth()->user()->can('purchase.delete') ? 'hide' : '';

            return Datatables::of($stock_adjustments)
                ->addColumn('action', function ($row) use ($hide) {
                    $html = '<button type="button" data-href="'.action([\App\Http\Controllers\StockAdjustmentController::class, 'show'], [$row->id]).'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i> '.__('messages.view').'</button>';
                    $html .= ' &nbsp; <button type="button" data-href="'.action([\App\Http\Controllers\StockAdjustmentController::class, 'destroy'], [$row->id]).'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-error delete_warehouse_stock_adjustment '.$hide.'"><i class="fa fa-trash" aria-hidden="true"></i> '.__('messages.delete').'</button>';

                    return $html;
                })
                ->editColumn('final_total', function ($row) {
                    if (auth()->user()->can('view_purchase_price')) {
                        return $this->transactionUtil->num_f($row->final_total, true);
                    }

                    return '<span>-</span>';
                })
                ->editColumn('total_amount_recovered', function ($row) {
                    if (auth()->user()->can('view_purchase_price')) {
                        return $this->transactionUtil->num_f($row->total_amount_recovered, true);
                    }

                    return '<span>-</span>';
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('adjustment_type', function ($row) {
                    return __('stock_adjustment.'.$row->adjustment_type);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return action([\App\Http\Controllers\StockAdjustmentController::class, 'show'], [$row->id]);
                    },
                ])
                ->rawColumns(['final_total', 'action', 'total_amount_recovered'])
                ->make(true);
        }

        $business_id = $request->session()->get('user.business_id');
        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE,
            true
        );
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);

        return view('warehouse.adjustment.index', compact('warehouse_locations', 'default_warehouse_id'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('warehouse.adjust_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([self::class, 'index']));
        }

        $warehouse_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE
        );
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);

        if (empty($default_warehouse_id) && $warehouse_locations->isNotEmpty()) {
            $default_warehouse_id = $warehouse_locations->keys()->first();
        }

        return view('warehouse.adjustment.create', compact('warehouse_locations', 'default_warehouse_id'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('warehouse.adjust_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([self::class, 'index']));
        }

        $output = $this->adjustmentService->createAdjustment($request, $business_id, $user_id);

        return redirect()->action([self::class, 'index'])->with('status', $output);
    }
}
