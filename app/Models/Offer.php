<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'buyer_id',
        'farmer_id',
        'product_id',
        'offered_price',
        'status',
        'counter_offer_price',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}