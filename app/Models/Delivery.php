<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'delivery_location',
        'delivery_ship_date',
        'delivery_status',
        'delivery_finish_date',
    ];

    protected $casts = [
        'delivery_ship_date' => 'date',
        'delivery_finish_date' => 'date',
        'delivery_status' => 'string',
    ];

    // Inverse of One-to-One relationship
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
