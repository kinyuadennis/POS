<?php

use App\Unit;
use App\SellingPriceGroup;
use App\Variation;
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
        Schema::create('selling_price_to_unit_of_measures', function (Blueprint $table) {
            $table->id();
            $table->integer('variation_id')->unsigned();
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->integer('unit_of_measure_id')->unsigned();
            $table->foreign('unit_of_measure_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('selling_price_group_id')->unsigned();
            $table->foreign('selling_price_group_id')->references('id')->on('selling_price_groups')->onDelete('cascade');
            $table->decimal('default_price', 10, 2)->nullable()->default(0);
            $table->decimal('minimum_price', 10, 2)->nullable()->default(0);
            $table->decimal('maximum_price', 10, 2)->nullable()->default(0);
            $table->boolean('is_default')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selling_price_to_unit_of_measures');
    }
};
