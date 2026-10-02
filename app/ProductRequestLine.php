<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRequestLine extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'requested_quantity' => 'float',
        'approved_quantity' => 'float',
        'dispatched_quantity' => 'float',
        'received_quantity' => 'float',
        'discrepancy_quantity' => 'float',
    ];

    public function request()
    {
        return $this->belongsTo(ProductRequest::class, 'product_request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}