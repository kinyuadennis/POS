<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\ProductRequest;
use App\ProductRequestLine;
use App\Services\ProductRequestService;
use App\Services\Warehouse\WarehouseLocationService;
use App\Utils\ProductUtil;
use App\VariationLocationDetails;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRequestController extends Controller
{
    public function __construct(
        protected ProductRequestService $productRequestService,
        protected WarehouseLocationService $warehouseLocationService,
        protected ProductUtil $productUtil
    ) {
        $this->middleware(function ($request, $next) {
            $enabled_modules = $request->session()->get('business.enabled_modules', []);
            if (!in_array('product_requests', $enabled_modules)) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of product requests.
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('product_request.view') && ! auth()->user()->can('product_request.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $permitted_locations = auth()->user()->permitted_locations();

        if ($request->ajax()) {
            $query = ProductRequest::where('product_requests.business_id', $business_id)
                ->join('business_locations as bl_req', 'bl_req.id', '=', 'product_requests.requesting_location_id')
                ->join('business_locations as bl_src', 'bl_src.id', '=', 'product_requests.source_location_id')
                ->join('users as u', 'u.id', '=', 'product_requests.created_by')
                ->leftJoin('transactions as t', 't.id', '=', 'product_requests.stock_transfer_id')
                ->select([
                    'product_requests.id',
                    'product_requests.request_no',
                    'product_requests.status',
                    'product_requests.priority',
                    'product_requests.required_date',
                    'product_requests.created_at',
                    'bl_req.name as requesting_location_name',
                    'bl_src.name as source_location_name',
                    'product_requests.requesting_location_id',
                    'product_requests.source_location_id',
                    DB::raw("CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as requested_by"),
                    't.ref_no as transfer_ref_no',
                    DB::raw('(SELECT COUNT(*) FROM product_request_lines WHERE product_request_lines.product_request_id = product_requests.id) as total_items'),
                    DB::raw('(SELECT SUM(requested_quantity) FROM product_request_lines WHERE product_request_lines.product_request_id = product_requests.id) as total_units'),
                ]);

            // Location scoping
            if ($permitted_locations !== 'all' && ! auth()->user()->can('product_request.view_all')) {
                $query->whereIn('product_requests.requesting_location_id', $permitted_locations);
            }

            // Filters
            if (! empty($request->input('status')) && $request->input('status') !== 'all') {
                $query->where('product_requests.status', $request->input('status'));
            }

            if (! empty($request->input('location_id'))) {
                $query->where('product_requests.requesting_location_id', $request->input('location_id'));
            }

            if (! empty($request->input('priority'))) {
                $query->where('product_requests.priority', $request->input('priority'));
            }

            if (! empty($request->input('start_date')) && ! empty($request->input('end_date'))) {
                $query->whereDate('product_requests.created_at', '>=', $request->input('start_date'))
                      ->whereDate('product_requests.created_at', '<=', $request->input('end_date'));
            }

            return Datatables::of($query)
                ->editColumn('request_no', function ($row) {
                    return '<a href="' . action([self::class, 'show'], [$row->id]) . '" style="font-weight: 700; color: #0284c7;">' . e($row->request_no) . '</a>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, H:i') : '--';
                })
                ->editColumn('required_date', function ($row) {
                    return $row->required_date ? $row->required_date->format('d M Y') : '--';
                })
                ->editColumn('status', function ($row) {
                    $badges = [
                        'draft' => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'label' => 'Draft'],
                        'pending' => ['bg' => '#fef3c7', 'color' => '#d97706', 'border' => '#fde68a', 'label' => 'Pending Review'],
                        'approved' => ['bg' => '#e0f2fe', 'color' => '#0284c7', 'border' => '#bae6fd', 'label' => 'Approved'],
                        'partially_approved' => ['bg' => '#ede9fe', 'color' => '#7c3aed', 'border' => '#ddd6fe', 'label' => 'Partially Approved'],
                        'rejected' => ['bg' => '#fee2e2', 'color' => '#dc2626', 'border' => '#fecaca', 'label' => 'Rejected'],
                        'changes_requested' => ['bg' => '#ffedd5', 'color' => '#ea580c', 'border' => '#fed7aa', 'label' => 'Changes Requested'],
                        'in_transit' => ['bg' => '#f3e8ff', 'color' => '#9333ea', 'border' => '#e9d5ff', 'label' => 'In Transit'],
                        'received' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'border' => '#bbf7d0', 'label' => 'Received / Complete'],
                        'cancelled' => ['bg' => '#f1f5f9', 'color' => '#94a3b8', 'border' => '#e2e8f0', 'label' => 'Cancelled'],
                    ];
                    $b = $badges[$row->status] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'label' => $row->status];

                    return '<span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600;background:' . $b['bg'] . ';color:' . $b['color'] . ';border:1px solid ' . $b['border'] . ';">' . e($b['label']) . '</span>';
                })
                ->editColumn('priority', function ($row) {
                    $colors = [
                        'low' => ['bg' => '#f1f5f9', 'color' => '#64748b'],
                        'medium' => ['bg' => '#f0f9ff', 'color' => '#0369a1'],
                        'high' => ['bg' => '#fff7ed', 'color' => '#c2410c'],
                        'urgent' => ['bg' => '#fef2f2', 'color' => '#b91c1c'],
                    ];
                    $c = $colors[$row->priority] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];

                    return '<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;background:' . $c['bg'] . ';color:' . $c['color'] . ';">' . e($row->priority) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div style="display:inline-flex;gap:4px;align-items:center;">';

                    // View
                    $html .= '<a href="' . action([self::class, 'show'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info" title="View Details" data-toggle="tooltip"><i class="fa fa-eye"></i></a>';

                    // Edit (Draft or Changes Requested)
                    if (in_array($row->status, ['draft', 'changes_requested']) && auth()->user()->can('product_request.edit')) {
                        $html .= '<a href="' . action([self::class, 'edit'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-warning" title="Edit Request" data-toggle="tooltip"><i class="glyphicon glyphicon-edit"></i></a>';
                    }

                    // Receive (In Transit)
                    if ($row->status === 'in_transit' && auth()->user()->can('product_request.receive')) {
                        $html .= '<a href="' . action([self::class, 'show'], [$row->id]) . '#receive" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-success" style="font-weight:600;font-size:11px;" title="Confirm Receipt" data-toggle="tooltip"><i class="fa fa-check-double"></i> Receive</a>';
                    }

                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['request_no', 'status', 'priority', 'action'])
                ->make(true);
        }

        // Stats calculation
        $baseStatsQuery = ProductRequest::where('business_id', $business_id);
        if ($permitted_locations !== 'all' && ! auth()->user()->can('product_request.view_all')) {
            $baseStatsQuery->whereIn('requesting_location_id', $permitted_locations);
        }

        $stats = [
            'pending' => (clone $baseStatsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseStatsQuery)->whereIn('status', ['approved', 'partially_approved'])->count(),
            'in_transit' => (clone $baseStatsQuery)->where('status', 'in_transit')->count(),
            'received' => (clone $baseStatsQuery)->where('status', 'received')->count(),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id);
        $statuses = [
            'all' => 'All Statuses',
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'partially_approved' => 'Partially Approved',
            'in_transit' => 'In Transit',
            'received' => 'Received / Complete',
            'rejected' => 'Rejected',
            'draft' => 'Draft',
        ];

        return view('product_request.index', compact('stats', 'business_locations', 'statuses'));
    }

    /**
     * Show form to create a new product request.
     */
    public function create(Request $request)
    {
        if (! auth()->user()->can('product_request.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $permitted_locations = auth()->user()->permitted_locations($business_id);

        // Only retail shops (exclude warehouses from destination shops)
        $shopsQuery = BusinessLocation::where('business_id', $business_id)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->where('location_type', '!=', 'warehouse')
                  ->orWhereNull('location_type');
            });

        // Limit requesting location to user's permitted locations if not 'all'
        if ($permitted_locations !== 'all') {
            if (empty($permitted_locations)) {
                $shopsQuery->whereRaw('1 = 0');
            } else {
                $shopsQuery->whereIn('id', $permitted_locations);
            }
        }

        $business_locations = $shopsQuery->pluck('name', 'id');

        $default_warehouse_id = $this->warehouseLocationService->getDefaultWarehouseId($business_id);
        // Source locations are central warehouses; do NOT filter by cashier's permitted retail shops
        $source_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE,
            false,
            false
        );

        if ($source_locations->isEmpty()) {
            $source_locations = BusinessLocation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->select(
                    \DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
                    'id'
                )->pluck('name', 'id');
        }

        if (empty($default_warehouse_id) || ! isset($source_locations[$default_warehouse_id])) {
            $default_warehouse_id = $source_locations->keys()->first();
        }

        // If user has only one assigned shop, preselect it. Otherwise null so it shows "Choose Business Location"
        $default_requesting_location_id = $business_locations->count() === 1
            ? $business_locations->keys()->first()
            : null;

        return view('product_request.create', compact(
            'business_locations',
            'source_locations',
            'default_warehouse_id',
            'default_requesting_location_id'
        ));
    }

    /**
     * Store newly created product request.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('product_request.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'requesting_location_id' => 'required|integer',
            'source_location_id' => 'required|integer',
            'products' => 'required|array|min:1',
            'products.*.variation_id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        // Location authorization check
        $permitted_locations = auth()->user()->permitted_locations($business_id);
        if ($permitted_locations !== 'all') {
            if (empty($permitted_locations) || ! in_array($request->input('requesting_location_id'), $permitted_locations)) {
                abort(403, 'You are not authorized to create requests for this location.');
            }
        }

        $destinationLocation = BusinessLocation::where('business_id', $business_id)->find($request->input('requesting_location_id'));
        if (! $destinationLocation || ! $destinationLocation->is_active || $destinationLocation->location_type === 'warehouse' || $destinationLocation->id == $request->input('source_location_id')) {
            return redirect()->back()->withInput()->with('status', [
                'success' => 0,
                'msg' => 'Invalid destination location selected. Requisitions must be directed to an active retail shop.'
            ]);
        }

        try {
            $productRequest = $this->productRequestService->createRequest($request->all(), $business_id, $user_id);

            $msg = $productRequest->status === 'pending'
                ? "Product Request #{$productRequest->request_no} submitted for review successfully!"
                : "Product Request #{$productRequest->request_no} saved as draft.";

            return redirect()->action([self::class, 'show'], [$productRequest->id])
                ->with('status', ['success' => 1, 'msg' => $msg]);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified product request details with review/fulfill actions.
     */
    public function show($id, Request $request)
    {
        if (! auth()->user()->can('product_request.view') && ! auth()->user()->can('product_request.view_all')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $productRequest = ProductRequest::where('business_id', $business_id)
            ->with([
                'lines.product',
                'lines.variation',
                'requestingLocation',
                'sourceLocation',
                'creator',
                'approver',
                'dispatcher',
                'receiver',
                'stockTransfer',
                'activities.user',
            ])
            ->findOrFail($id);

        // Security check
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations !== 'all' && ! auth()->user()->can('product_request.view_all')) {
            if (! in_array($productRequest->requesting_location_id, $permitted_locations) &&
                ! in_array($productRequest->source_location_id, $permitted_locations)) {
                abort(403, 'Unauthorized access to this request.');
            }
        }

        // Fetch live stock for each item at Source vs Requesting location
        $variation_ids = $productRequest->lines->pluck('variation_id')->toArray();
        $sourceStock = VariationLocationDetails::where('location_id', $productRequest->source_location_id)
            ->whereIn('variation_id', $variation_ids)
            ->pluck('qty_available', 'variation_id')
            ->toArray();

        $shopStock = VariationLocationDetails::where('location_id', $productRequest->requesting_location_id)
            ->whereIn('variation_id', $variation_ids)
            ->pluck('qty_available', 'variation_id')
            ->toArray();

        return view('product_request.show', compact('productRequest', 'sourceStock', 'shopStock'));
    }

    /**
     * Edit draft or changes_requested request.
     */
    public function edit($id, Request $request)
    {
        if (! auth()->user()->can('product_request.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $productRequest = ProductRequest::where('business_id', $business_id)
            ->with(['lines.product', 'lines.variation'])
            ->findOrFail($id);

        if (! in_array($productRequest->status, ['draft', 'changes_requested'])) {
            return redirect()->action([self::class, 'show'], [$id])
                ->with('status', ['success' => 0, 'msg' => 'Only draft or revision requests can be edited.']);
        }

        $permitted_locations = auth()->user()->permitted_locations($business_id);
        $shopsQuery = BusinessLocation::where('business_id', $business_id)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->where('location_type', '!=', 'warehouse')
                  ->orWhereNull('location_type');
            });

        if ($permitted_locations !== 'all') {
            if (empty($permitted_locations)) {
                $shopsQuery->where('id', $productRequest->requesting_location_id);
            } else {
                $shopsQuery->where(function ($q) use ($permitted_locations, $productRequest) {
                    $q->whereIn('id', $permitted_locations)
                      ->orWhere('id', $productRequest->requesting_location_id);
                });
            }
        }

        $business_locations = $shopsQuery->select(
            \DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
            'id'
        )->pluck('name', 'id');

        $source_locations = $this->warehouseLocationService->forDropdownByType(
            $business_id,
            WarehouseLocationService::TYPE_WAREHOUSE,
            false,
            false
        );
        if ($source_locations->isEmpty()) {
            $source_locations = BusinessLocation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->select(
                    \DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
                    'id'
                )->pluck('name', 'id');
        }

        if (! isset($source_locations[$productRequest->source_location_id]) && $productRequest->sourceLocation) {
            $source_locations->put(
                $productRequest->source_location_id,
                $productRequest->sourceLocation->name . ($productRequest->sourceLocation->location_id ? " ({$productRequest->sourceLocation->location_id})" : '')
            );
        }

        return view('product_request.edit', compact('productRequest', 'business_locations', 'source_locations'));
    }

    /**
     * Update request.
     */
    public function update($id, Request $request)
    {
        if (! auth()->user()->can('product_request.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        if (! in_array($productRequest->status, ['draft', 'changes_requested'])) {
            return redirect()->action([self::class, 'show'], [$id])
                ->with('status', ['success' => 0, 'msg' => 'Only draft or revision requests can be edited.']);
        }

        try {
            $this->productRequestService->updateRequest($productRequest, $request->all(), $user_id);

            return redirect()->action([self::class, 'show'], [$productRequest->id])
                ->with('status', ['success' => 1, 'msg' => 'Request updated successfully!']);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Approve or partially approve a request.
     */
    public function approve($id, Request $request)
    {
        if (! auth()->user()->can('product_request.approve')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        try {
            $this->productRequestService->approveRequest($productRequest, $request->input('lines', []), $user_id, $request->input('notes'));

            return redirect()->action([self::class, 'show'], [$id])
                ->with('status', ['success' => 1, 'msg' => "Request #{$productRequest->request_no} has been approved."]);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Reject a request with mandatory reason.
     */
    public function reject($id, Request $request)
    {
        if (! auth()->user()->can('product_request.reject')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['rejection_reason' => 'required|string|min:3']);

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        try {
            $this->productRequestService->rejectRequest($productRequest, $request->input('rejection_reason'), $user_id);

            return redirect()->action([self::class, 'show'], [$id])
                ->with('status', ['success' => 1, 'msg' => "Request #{$productRequest->request_no} has been rejected."]);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Request changes from cashier.
     */
    public function requestChangesAction($id, Request $request)
    {
        if (! auth()->user()->can('product_request.approve')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['notes' => 'required|string|min:3']);

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        try {
            $this->productRequestService->requestChanges($productRequest, $request->input('notes'), $user_id);

            return redirect()->action([self::class, 'show'], [$id])
                ->with('status', ['success' => 1, 'msg' => "Returned request #{$productRequest->request_no} to cashier for revisions."]);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Fulfill & Dispatch stock transfer.
     */
    public function dispatchStock($id, Request $request)
    {
        if (! auth()->user()->can('product_request.fulfill')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        try {
            $res = $this->productRequestService->dispatchRequest($productRequest, $request->all(), $business_id, $user_id);

            return redirect()->action([self::class, 'show'], [$id])->with('status', $res);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Confirm physical receipt with discrepancy notes.
     */
    public function receiveStock($id, Request $request)
    {
        if (! auth()->user()->can('product_request.receive')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $productRequest = ProductRequest::where('business_id', $business_id)->findOrFail($id);

        try {
            $res = $this->productRequestService->receiveRequest($productRequest, $request->all(), $business_id, $user_id);

            return redirect()->action([self::class, 'show'], [$id])->with('status', $res);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['success' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * AJAX search products with dual stock comparison.
     */
    public function searchProducts(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $keyword = $request->input('term', '');
        $source_id = (int) $request->input('source_location_id');
        $requesting_id = (int) $request->input('requesting_location_id');

        if (empty($source_id) || empty($requesting_id) || strlen($keyword) < 1) {
            return response()->json([]);
        }

        $results = $this->productRequestService->searchProductsForRequest($keyword, $source_id, $requesting_id, $business_id);

        return response()->json($results);
    }
}