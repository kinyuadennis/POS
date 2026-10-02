<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\Warehouse\WarehouseDispatchService;
use App\Services\Warehouse\WarehouseLocationService;
use App\Transaction;
use App\Utils\ModuleUtil;
use Datatables;
use Illuminate\Http\Request;

class WarehouseDispatchController extends Controller
{
    protected $status_colors = [
        'in_transit' => 'bg-yellow',
        'completed' => 'bg-green',
        'pending' => 'bg-red',
    ];

    public function __construct(
        protected WarehouseLocationService $warehouseLocationService,
        protected WarehouseDispatchService $dispatchService,
        protected ModuleUtil $moduleUtil
    ) {
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.dispatch')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $warehouse_ids = $this->warehouseLocationService->getWarehouseLocationIds($business_id);

            $dispatches = Transaction::join(
                'business_locations AS l1',
                'transactions.location_id',
                '=',
                'l1.id'
            )
                ->join('transactions as t2', 't2.transfer_parent_id', '=', 'transactions.id')
                ->join(
                    'business_locations AS l2',
                    't2.location_id',
                    '=',
                    'l2.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell_transfer')
                ->whereIn('transactions.location_id', $warehouse_ids)
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'l1.name as location_from',
                    'l2.name as location_to',
                    'transactions.final_total',
                    'transactions.shipping_charges',
                    'transactions.additional_notes',
                    'transactions.id as DT_RowId',
                    'transactions.status'
                );

            $statuses = $this->dispatchStatuses();

            return Datatables::of($dispatches)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('final_total', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">'.$row->final_total.'</span>';
                })
                ->editColumn('shipping_charges', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">'.$row->shipping_charges.'</span>';
                })
                ->editColumn('status', function ($row) use ($statuses) {
                    $status_key = $row->status == 'final' ? 'completed' : $row->status;
                    $status_color = $this->status_colors[$status_key] ?? 'bg-gray';

                    return '<span class="label '.$status_color.'">'.($statuses[$status_key] ?? $status_key).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<button type="button" title="'.__('stock_adjustment.view_details').'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent btn-modal" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\StockTransferController::class, 'show'], [$row->id]).'"><i class="fa fa-eye" aria-hidden="true"></i> '.__('messages.view').'</button>';

                    $html .= ' <a href="#" class="print-invoice tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info" data-href="'.action([\App\Http\Controllers\StockTransferController::class, 'printInvoice'], [$row->id]).'"><i class="fa fa-print" aria-hidden="true"></i> '.__('messages.print').'</a>';

                    return $html;
                })
                ->rawColumns(['final_total', 'shipping_charges', 'status', 'action'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return action([\App\Http\Controllers\StockTransferController::class, 'show'], [$row->id]);
                    },
                ])
                ->make(true);
        }

        $statuses = $this->dispatchStatuses();

        return view('warehouse.dispatch.index', compact('statuses'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('warehouse.dispatch')) {
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
        $shop_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_SHOP
        );
        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);
        $statuses = $this->dispatchStatuses();

        return view('warehouse.dispatch.create', compact(
            'warehouse_locations',
            'shop_locations',
            'default_warehouse_id',
            'statuses'
        ));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('warehouse.dispatch')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        if (! $this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([self::class, 'index']));
        }

        if (! $this->warehouseLocationService->isWarehouseLocation($request->input('location_id'), $business_id)) {
            return redirect()->back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.warehouse_dispatch_invalid_source'),
            ]);
        }

        $shop_ids = $this->warehouseLocationService->getShopLocationIds($business_id);
        if (! in_array((int) $request->input('transfer_location_id'), $shop_ids)) {
            return redirect()->back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.warehouse_dispatch_invalid_destination'),
            ]);
        }

        $output = $this->dispatchService->createDispatch($request, $business_id, $user_id);

        return redirect()->action([self::class, 'index'])->with('status', $output);
    }

    private function dispatchStatuses(): array
    {
        return [
            'pending' => __('lang_v1.pending'),
            'in_transit' => __('lang_v1.in_transit'),
            'completed' => __('restaurant.completed'),
        ];
    }
}
