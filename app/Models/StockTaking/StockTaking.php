<?php

namespace App\Models\StockTaking;

use App\BusinessLocation;
use App\User;
use Illuminate\Database\Eloquent\Model;

class StockTaking extends Model
{
    protected $guarded = ['id'];

    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    public function sheets()
    {
        return $this->hasMany(StockTakingSheet::class);
    }

    public function counters()
    {
        return $this->hasMany(StockTakingCounter::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
