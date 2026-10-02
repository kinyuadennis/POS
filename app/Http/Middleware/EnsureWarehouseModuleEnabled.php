<?php

namespace App\Http\Middleware;

use App\Utils\ModuleUtil;
use Closure;
use Illuminate\Http\Request;

class EnsureWarehouseModuleEnabled
{
    public function __construct(protected ModuleUtil $moduleUtil)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $business_id = $request->session()->get('user.business_id');

        if (! $this->moduleUtil->isModuleEnabled('warehouse', $business_id)) {
            abort(403, __('lang_v1.warehouse_module_not_enabled'));
        }

        return $next($request);
    }
}
