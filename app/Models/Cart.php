<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'Cart';
    public $timestamps = false;

    protected $fillable = [
        'BuyerID',
        'ProductID',
        'TotalAmount',
        'CartItems',
    ];

    // Relationship with Buyer
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'BuyerID');
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
}
