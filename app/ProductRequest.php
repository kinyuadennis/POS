<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(ProductRequestLine::class, 'product_request_id');
    }

    public function activities()
    {
        return $this->hasMany(ProductRequestActivity::class, 'product_request_id')->orderBy('id', 'asc');
    }

    public function requestingLocation()
    {
        return $this->belongsTo(BusinessLocation::class, 'requesting_location_id');
    }

    public function sourceLocation()
    {
        return $this->belongsTo(BusinessLocation::class, 'source_location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function stockTransfer()
    {
        return $this->belongsTo(Transaction::class, 'stock_transfer_id');
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public static function generateRequestNo(int $business_id): string
    {
        $year = date('Y');
        $count = self::where('business_id', $business_id)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('PR-%s-%04d', $year, $count);
    }
}