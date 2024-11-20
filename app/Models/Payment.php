<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_date',
        'total_amount',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Inverse of One-to-One relationship
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
