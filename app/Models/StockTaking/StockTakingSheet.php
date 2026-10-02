<?php

namespace App\Models\StockTaking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTakingSheet extends Model
{
    protected $guarded = ['id'];

    public function stockTaking()
    {
        return $this->belongsTo(StockTaking::class);
    }
}
