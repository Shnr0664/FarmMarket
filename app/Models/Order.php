<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'buyer_id',
        'order_date',
        'total_amount',
        'order_status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'order_status' => 'string',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class, 'order_id');
    }

    // Inverse of One-to-Many relationship
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    // One-to-One relationship with Payment and Delivery
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id');
    }
}
