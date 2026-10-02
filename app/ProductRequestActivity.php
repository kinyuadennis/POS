<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRequestActivity extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(ProductRequest::class, 'product_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log(ProductRequest $request, string $action, ?string $from_status = null, ?string $to_status = null, ?string $notes = null, ?int $user_id = null): self
    {
        return self::create([
            'product_request_id' => $request->id,
            'user_id' => $user_id ?? auth()->id() ?? 1,
            'action' => $action,
            'from_status' => $from_status,
            'to_status' => $to_status,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}