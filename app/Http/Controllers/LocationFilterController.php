<?php

namespace App\Http\Controllers;

use App\Utils\LocationFilterUtil;
use Illuminate\Http\Request;

class LocationFilterController extends Controller
{
    /**
     * Customers, suppliers, contacts and users for the selected location.
     */
    public function options(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id');

        $users = LocationFilterUtil::usersForLocation($business_id, $location_id);

        return response()->json([
            'customers' => LocationFilterUtil::customersForLocation($business_id, $location_id),
            'suppliers' => LocationFilterUtil::suppliersForLocation($business_id, $location_id),
            'contacts' => LocationFilterUtil::contactsForLocation($business_id, $location_id),
            'users' => $users,
            'cashiers' => $users,
        ]);
    }
}
