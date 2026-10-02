<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('stock_adjustment_lines', 'adjustment_type')) {
            Schema::table('stock_adjustment_lines', function (Blueprint $table) {
                $table->string('adjustment_type', 20)->default('sub')->after('quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('stock_adjustment_lines', 'adjustment_type')) {
            Schema::table('stock_adjustment_lines', function (Blueprint $table) {
                $table->dropColumn('adjustment_type');
            });
        }
    }
};
