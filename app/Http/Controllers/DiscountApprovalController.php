<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\DiscountApproval;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DiscountApprovalController extends Controller
{
    protected $commonUtil;
    protected $businessUtil;
    protected $moduleUtil;

    public function __construct(Util $commonUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Cashier submits a discount approval request from POS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestApproval(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $request->validate([
                'location_id' => 'required|integer',
                'discount_type' => 'required|in:fixed,percentage',
                'discount_amount' => 'required|numeric|min:0',
                'original_total' => 'required|numeric|min:0',
                'resulting_total' => 'required|numeric|min:0',
            ]);

            $location_id = $request->input('location_id');
            $discount_type = $request->input('discount_type');
            $discount_amount = $this->commonUtil->num_uf($request->input('discount_amount'));
            $original_total = $this->commonUtil->num_uf($request->input('original_total'));
            $resulting_total = $this->commonUtil->num_uf($request->input('resulting_total'));
            $cart_snapshot = $request->input('cart_snapshot', []);
            $cashier_notes = $request->input('cashier_notes');

            // Create pending approval
            $approval = DiscountApproval::create([
                'business_id' => $business_id,
                'location_id' => $location_id,
                'created_by' => $user_id,
                'status' => 'pending',
                'discount_type' => $discount_type,
                'requested_discount_type' => $discount_type,
                'requested_discount_amount' => $discount_amount,
                'original_total' => $original_total,
                'resulting_total' => $resulting_total,
                'cart_snapshot' => is_string($cart_snapshot) ? json_decode($cart_snapshot, true) : $cart_snapshot,
                'cashier_notes' => $cashier_notes,
                'expires_at' => Carbon::now()->addMinutes(30),
            ]);

            // Log activity
            activity()
                ->performedOn($approval)
                ->causedBy(auth()->user())
                ->withProperties(['requested_amount' => $discount_amount, 'discount_type' => $discount_type, 'original_total' => $original_total])
                ->log('Discount approval requested by cashier');

            return response()->json([
                'success' => true,
                'approval_id' => $approval->id,
                'msg' => __('Discount approval request submitted. Waiting for Admin response...'),
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Cashier polls for approval status on the POS.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        $approval = DiscountApproval::where('business_id', $business_id)
            ->where('created_by', $user_id)
            ->with(['approver'])
            ->find($id);

        if (! $approval) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'msg' => __('Request not found'),
            ]);
        }

        // Check if expired
        if ($approval->status == 'pending' && $approval->expires_at && Carbon::now()->gt($approval->expires_at)) {
            $approval->status = 'expired';
            $approval->save();
        }

        $response = [
            'success' => true,
            'status' => $approval->status,
            'approval_id' => $approval->id,
        ];

        if (in_array($approval->status, ['approved', 'modified'])) {
            $response['approval_token'] = $approval->approval_token;
            $response['approved_discount_type'] = $approval->approved_discount_type;
            $response['approved_discount_amount'] = $approval->approved_discount_amount;
            $response['resulting_total'] = $approval->resulting_total;
            $response['admin_name'] = $approval->approver ? $approval->approver->user_full_name : 'Admin';
            $response['admin_notes'] = $approval->admin_notes;
        } elseif ($approval->status == 'rejected') {
            $response['admin_name'] = $approval->approver ? $approval->approver->user_full_name : 'Admin';
            $response['admin_notes'] = $approval->admin_notes;
        }

        return response()->json($response);
    }

    /**
     * Cashier cancels a pending approval request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelRequest($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        $approval = DiscountApproval::where('business_id', $business_id)
            ->where('created_by', $user_id)
            ->where('status', 'pending')
            ->find($id);

        if ($approval) {
            $approval->status = 'cancelled';
            $approval->save();
        }

        return response()->json([
            'success' => true,
            'msg' => __('Request cancelled'),
        ]);
    }

    /**
     * Admin endpoint: get all pending discount approval requests for the business.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingApprovals()
    {
        if (! $this->canApproveDiscount()) {
            return response()->json(['count' => 0, 'requests' => []]);
        }

        $business_id = request()->session()->get('user.business_id');

        $pending = DiscountApproval::where('business_id', $business_id)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->with(['cashier', 'location'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $pending->map(function ($item) {
            return [
                'id' => $item->id,
                'cashier_name' => $item->cashier ? $item->cashier->user_full_name : 'Cashier',
                'location_name' => $item->location ? $item->location->name : '',
                'discount_type' => $item->requested_discount_type,
                'requested_discount_amount' => $this->commonUtil->num_f($item->requested_discount_amount),
                'original_total' => $this->commonUtil->num_f($item->original_total),
                'resulting_total' => $this->commonUtil->num_f($item->resulting_total),
                'cashier_notes' => $item->cashier_notes,
                'created_at_human' => $item->created_at ? $item->created_at->diffForHumans() : '',
                'created_at_time' => $item->created_at ? $this->commonUtil->format_date($item->created_at->toDateTimeString(), true) : '',
            ];
        });

        return response()->json([
            'count' => $data->count(),
            'requests' => $data,
        ]);
    }

    /**
     * Admin endpoint: get details of a specific request for review in modal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails($id)
    {
        if (! $this->canApproveDiscount()) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized action.'], 403);
        }

        $business_id = request()->session()->get('user.business_id');

        $approval = DiscountApproval::where('business_id', $business_id)
            ->with(['cashier', 'location'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'id' => $approval->id,
            'status' => $approval->status,
            'cashier_name' => $approval->cashier ? $approval->cashier->user_full_name : 'Cashier',
            'location_name' => $approval->location ? $approval->location->name : '',
            'discount_type' => $approval->requested_discount_type,
            'requested_discount_amount' => $approval->requested_discount_amount,
            'requested_discount_amount_formatted' => $this->commonUtil->num_f($approval->requested_discount_amount),
            'original_total' => $approval->original_total,
            'original_total_formatted' => $this->commonUtil->num_f($approval->original_total),
            'resulting_total' => $approval->resulting_total,
            'resulting_total_formatted' => $this->commonUtil->num_f($approval->resulting_total),
            'cart_snapshot' => $approval->cart_snapshot ?: [],
            'cashier_notes' => $approval->cashier_notes,
            'created_at' => $approval->created_at ? $this->commonUtil->format_date($approval->created_at->toDateTimeString(), true) : '',
        ]);
    }

    /**
     * Admin responds to discount approval request (Approve, Modify, or Reject).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitResponse(Request $request, $id)
    {
        if (! $this->canApproveDiscount()) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized action.'], 403);
        }

        $business_id = $request->session()->get('user.business_id');
        $admin_id = $request->session()->get('user.id');

        $request->validate([
            'action' => 'required|in:approve,modify,reject',
            'approved_discount_type' => 'nullable|in:fixed,percentage',
            'approved_discount_amount' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $approval = DiscountApproval::where('business_id', $business_id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $action = $request->input('action');
        $admin_notes = $request->input('admin_notes');

        DB::beginTransaction();
        try {
            if ($action === 'approve') {
                $approval->status = 'approved';
                $approval->approved_discount_type = $approval->requested_discount_type;
                $approval->approved_discount_amount = $approval->requested_discount_amount;
                $approval->approval_token = Str::random(32) . md5(uniqid(rand(), true));
                $approval->approved_by = $admin_id;
                $approval->admin_notes = $admin_notes;
                $approval->approved_at = Carbon::now();
                $approval->expires_at = Carbon::now()->addMinutes(30);
                $approval->save();

                $msg = __('Discount approved successfully.');
            } elseif ($action === 'modify') {
                $mod_type = $request->input('approved_discount_type', $approval->requested_discount_type);
                $mod_amount = $this->commonUtil->num_uf($request->input('approved_discount_amount', $approval->requested_discount_amount));

                // Recalculate resulting total
                $orig = $approval->original_total;
                if ($mod_type === 'percentage') {
                    $discount_val = ($orig * $mod_amount) / 100;
                } else {
                    $discount_val = $mod_amount;
                }
                $new_resulting_total = max(0, $orig - $discount_val);

                $approval->status = 'modified';
                $approval->approved_discount_type = $mod_type;
                $approval->approved_discount_amount = $mod_amount;
                $approval->resulting_total = $new_resulting_total;
                $approval->approval_token = Str::random(32) . md5(uniqid(rand(), true));
                $approval->approved_by = $admin_id;
                $approval->admin_notes = $admin_notes;
                $approval->approved_at = Carbon::now();
                $approval->expires_at = Carbon::now()->addMinutes(30);
                $approval->save();

                $msg = __('Modified discount approved successfully.');
            } else {
                // Reject
                $approval->status = 'rejected';
                $approval->approved_by = $admin_id;
                $approval->admin_notes = $admin_notes;
                $approval->approved_at = Carbon::now();
                $approval->save();

                $msg = __('Discount request rejected.');
            }

            // Log activity audit
            activity()
                ->performedOn($approval)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => $action,
                    'approved_type' => $approval->approved_discount_type,
                    'approved_amount' => $approval->approved_discount_amount,
                    'admin_notes' => $admin_notes,
                ])
                ->log("Discount approval request {$approval->status} by manager");

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $approval->status,
                'msg' => $msg,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Audit log report listing all historical discount approvals.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return redirect()->route('reports.discounts_allowed');
    }

    /**
     * Helper to check if current user has permission to approve discounts.
     */
    private function canApproveDiscount()
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $business_id = request()->session()->get('user.business_id');

        return $user->can('superadmin') ||
            $user->can('admin') ||
            $user->hasRole('Admin#'.$business_id) ||
            $user->can('business_settings.access');
    }
}
