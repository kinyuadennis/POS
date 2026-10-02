<?php

namespace App\Models\StockTaking;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTakingCounter extends Model
{
    protected $guarded = ['id'];

    public function stockTaking()
    {
        return $this->belongsTo(StockTaking::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
