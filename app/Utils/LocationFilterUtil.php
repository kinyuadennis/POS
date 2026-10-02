<?php

namespace App\Utils;

use App\BusinessLocation;
use App\Contact;
use App\User;
use Illuminate\Support\Facades\DB;

class LocationFilterUtil
{
    /**
     * Preferred filter location: exact "Vawa Scents", then a name containing it, then the first real location.
     *
     * @param  iterable<int|string, string>|null  $locations
     * @return int|string|null
     */
    public static function preferredIdFromList($locations)
    {
        if (empty($locations)) {
            return null;
        }

        $locations = collect($locations);
        $exact = null;
        $contains = null;
        $first = null;

        foreach ($locations as $id => $name) {
            if ($id === '' || $id === null) {
                continue;
            }

            if ($first === null) {
                $first = $id;
            }

            $label = is_string($name) ? $name : (string) $name;
            if ($label === 'Vawa Scents' || preg_match('/^Vawa Scents(\s*\(|$)/i', $label)) {
                $exact = $id;
                break;
            }

            if ($contains === null && stripos($label, 'Vawa Scents') !== false) {
                $contains = $id;
            }
        }

        return $exact ?? $contains ?? $first;
    }

    /**
     * Preferred location id for a business, limited to permitted locations.
     *
     * @param  array<int>|string|null  $permitted_locations
     * @return int|string|null
     */
    public static function preferredId($business_id, $permitted_locations = null)
    {
        $query = BusinessLocation::where('business_id', $business_id)->Active();

        if ($permitted_locations !== null && $permitted_locations !== 'all') {
            $query->whereIn('id', (array) $permitted_locations);
        } elseif (auth()->check()) {
            $permitted = auth()->user()->permitted_locations();
            if ($permitted != 'all') {
                $query->whereIn('id', $permitted);
            }
        }

        $locations = $query->pluck('name', 'id');

        return self::preferredIdFromList($locations);
    }

    public static function customersForLocation($business_id, $location_id)
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
                ->whereIn('type', ['sell', 'sell_return'])
                ->where('location_id', $location_id)
                ->whereNotNull('contact_id')
                ->distinct()
                ->pluck('contact_id');

            $query->whereIn('contacts.id', $contact_ids);
        }

        return $query
            ->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name, ' (', contacts.contact_id, ')')) AS customer")
            )
            ->orderBy('contacts.name')
            ->get()
            ->pluck('customer', 'id')
            ->prepend(__('lang_v1.all'), '');
    }

    public static function suppliersForLocation($business_id, $location_id)
    {
        $query = Contact::where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['supplier', 'both'])
            ->active();

        if (auth()->check() && ! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            $query->onlyOwnContact();
        }

        if (! empty($location_id)) {
            $contact_ids = DB::table('transactions')
                ->where('business_id', $business_id)
                ->whereIn('type', ['purchase', 'purchase_return'])
                ->where('location_id', $location_id)
                ->whereNotNull('contact_id')
                ->distinct()
                ->pluck('contact_id');

            $query->whereIn('contacts.id', $contact_ids);
        }

        return $query
            ->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name, ' (', contacts.contact_id, ')')) AS supplier")
            )
            ->orderBy('contacts.name')
            ->get()
            ->pluck('supplier', 'id')
            ->prepend(__('lang_v1.all'), '');
    }

    public static function contactsForLocation($business_id, $location_id)
    {
        $query = Contact::where('contacts.business_id', $business_id)->active();

        if (! empty($location_id)) {
            $contact_ids = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('location_id', $location_id)
                ->whereNotNull('contact_id')
                ->distinct()
                ->pluck('contact_id');

            $query->whereIn('contacts.id', $contact_ids);
        }

        return $query
            ->select(
                'contacts.id',
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name, ' (', contacts.contact_id, ')')) AS contact")
            )
            ->orderBy('contacts.name')
            ->get()
            ->pluck('contact', 'id')
            ->prepend(__('lang_v1.all'), '');
    }

    /**
     * Users assigned to a location or who created transactions there.
     *
     * @param  array<int, string>  $transaction_types
     */
    public static function usersForLocation($business_id, $location_id, array $transaction_types = ['sell', 'sell_return', 'expense', 'purchase'])
    {
        $query = User::where('users.business_id', $business_id)
            ->user()
            ->where('is_cmmsn_agnt', 0);

        if (! empty($location_id)) {
            $user_ids = DB::table('transactions')
                ->where('business_id', $business_id)
                ->whereIn('type', $transaction_types)
                ->where('location_id', $location_id)
                ->whereNotNull('created_by')
                ->distinct()
                ->pluck('created_by');

            $query->where(function ($q) use ($location_id, $user_ids) {
                $q->whereHas('permissions', function ($p) use ($location_id) {
                    $p->where('permissions.name', 'location.'.$location_id);
                });
                if ($user_ids->isNotEmpty()) {
                    $q->orWhereIn('users.id', $user_ids);
                }
            });
        }

        return $query
            ->select(
                'users.id',
                DB::raw("TRIM(CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, ''))) as full_name")
            )
            ->orderBy('users.first_name')
            ->get()
            ->pluck('full_name', 'id')
            ->prepend(__('lang_v1.all'), '');
    }
}
