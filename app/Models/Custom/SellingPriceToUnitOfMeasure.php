<?php

namespace App\Models\Custom;

use App\Product;
use App\SellingPriceGroup;
use App\Unit;
use App\Variation;
use Illuminate\Database\Eloquent\Model;

class SellingPriceToUnitOfMeasure extends Model
{
    protected $guarded = ['id'];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, Variation::class, 'id', 'id', 'variation_id', 'product_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sellingPriceGroup()
    {
        return $this->belongsTo(SellingPriceGroup::class);
    }
}
