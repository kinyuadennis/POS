<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure product_locations has no duplicates before applying primary key
        $duplicates = DB::table('product_locations')
            ->select('product_id', 'location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('product_id', 'location_id')
            ->having('count', '>', 1)
            ->exists();

        if ($duplicates) {
            DB::statement('CREATE TEMPORARY TABLE temp_pl AS SELECT DISTINCT product_id, location_id FROM product_locations');
            DB::table('product_locations')->truncate();
            DB::statement('INSERT INTO product_locations (product_id, location_id) SELECT product_id, location_id FROM temp_pl');
            DB::statement('DROP TEMPORARY TABLE temp_pl');
        }

        // Add composite primary key to product_locations
        Schema::table('product_locations', function (Blueprint $table) {
            $table->primary(['product_id', 'location_id']);
        });

        // 2. Ensure variation_location_details has no duplicates before applying unique constraint
        $vldDuplicates = DB::table('variation_location_details')
            ->select('variation_id', 'location_id', DB::raw('COUNT(*) as count'))
            ->groupBy('variation_id', 'location_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($vldDuplicates as $dup) {
            $rows = DB::table('variation_location_details')
                ->where('variation_id', $dup->variation_id)
                ->where('location_id', $dup->location_id)
                ->orderBy('id', 'asc')
                ->get();

            $keepId = $rows->first()->id;
            $totalQty = $rows->sum('qty_available');

            // Consolidate total stock onto canonical row
            DB::table('variation_location_details')
                ->where('id', $keepId)
                ->update(['qty_available' => $totalQty]);

            // Delete surplus duplicate rows
            $deleteIds = $rows->slice(1)->pluck('id');
            DB::table('variation_location_details')
                ->whereIn('id', $deleteIds)
                ->delete();
        }

        // Add unique constraint to variation_location_details for (variation_id, location_id)
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->unique(['variation_id', 'location_id'], 'vld_variation_location_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->dropUnique('vld_variation_location_unique');
        });

        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropPrimary();
        });
    }
};