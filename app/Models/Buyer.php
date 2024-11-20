<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    /** @use HasFactory<\Database\Factories\BuyerFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'delivery_preference',
        'buyer_address',
    ];

    protected $casts = [
        'buyer_address' => 'string',
        'delivery_preference' => 'string',
    ];

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // One-to-Many relationship with Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }
}
