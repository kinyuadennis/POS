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
        // Set all existing sheet barcodes to non-default
        \App\Barcode::where('is_continuous', 0)->update(['is_default' => 0]);

        // Fix existing continuous roll if present
        $continuousRoll = \App\Barcode::where('id', 6)->orWhere('name', 'like', '%Continuous Rolls - 31.75mm%')->first();
        if ($continuousRoll) {
            $continuousRoll->update([
                'paper_width' => 1.25,
                'paper_height' => 1.0,
                'top_margin' => 0,
                'left_margin' => 0,
                'row_distance' => 0,
                'col_distance' => 0,
                'is_continuous' => 1,
                'stickers_in_one_row' => 1,
            ]);
        }

        // Standard Continuous Roll Presets
        $presets = [
            [
                'name' => 'Continuous Rolls - 50mm x 25mm (2" x 1")',
                'description' => 'Label Size: 50mm x 25mm (2" x 1"), 1 per row',
                'width' => 2.0,
                'height' => 1.0,
                'paper_width' => 2.0,
                'paper_height' => 1.0,
                'top_margin' => 0.0,
                'left_margin' => 0.0,
                'row_distance' => 0.0,
                'col_distance' => 0.0,
                'stickers_in_one_row' => 1,
                'is_default' => 1,
                'is_continuous' => 1,
                'stickers_in_one_sheet' => 1,
                'business_id' => null,
            ],
            [
                'name' => 'Continuous Rolls - 50mm x 30mm',
                'description' => 'Label Size: 50mm x 30mm, 1 per row',
                'width' => 2.0,
                'height' => 1.2,
                'paper_width' => 2.0,
                'paper_height' => 1.2,
                'top_margin' => 0.0,
                'left_margin' => 0.0,
                'row_distance' => 0.0,
                'col_distance' => 0.0,
                'stickers_in_one_row' => 1,
                'is_default' => 0,
                'is_continuous' => 1,
                'stickers_in_one_sheet' => 1,
                'business_id' => null,
            ],
            [
                'name' => 'Continuous Rolls - 40mm x 30mm',
                'description' => 'Label Size: 40mm x 30mm, 1 per row',
                'width' => 1.575,
                'height' => 1.18,
                'paper_width' => 1.575,
                'paper_height' => 1.18,
                'top_margin' => 0.0,
                'left_margin' => 0.0,
                'row_distance' => 0.0,
                'col_distance' => 0.0,
                'stickers_in_one_row' => 1,
                'is_default' => 0,
                'is_continuous' => 1,
                'stickers_in_one_sheet' => 1,
                'business_id' => null,
            ],
            [
                'name' => 'Continuous Rolls - 38mm x 25mm (1.5" x 1")',
                'description' => 'Label Size: 38mm x 25mm (1.5" x 1"), 1 per row',
                'width' => 1.5,
                'height' => 1.0,
                'paper_width' => 1.5,
                'paper_height' => 1.0,
                'top_margin' => 0.0,
                'left_margin' => 0.0,
                'row_distance' => 0.0,
                'col_distance' => 0.0,
                'stickers_in_one_row' => 1,
                'is_default' => 0,
                'is_continuous' => 1,
                'stickers_in_one_sheet' => 1,
                'business_id' => null,
            ],
        ];

        foreach ($presets as $preset) {
            $exists = \App\Barcode::where('name', $preset['name'])->first();
            if (! $exists) {
                \App\Barcode::create($preset);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Barcode::where('name', 'like', 'Continuous Rolls - 50mm%')
            ->orWhere('name', 'like', 'Continuous Rolls - 40mm%')
            ->orWhere('name', 'like', 'Continuous Rolls - 38mm%')
            ->delete();
    }
};
