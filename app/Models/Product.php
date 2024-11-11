<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'Product';
    protected $primaryKey = 'ProductID';
    public $timestamps = false;

    protected $fillable = [
        'FarmID',
        'ProductName',
        'ProductQuantity',
        'ProductCategory',
        'ProductDesc',
        'ProductPrice',
        'ProductImg',
    ];

    // Inverse of One-to-Many relationship
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'FarmID');
    }

    // Many-to-Many relationship with Buyer through Cart
    public function buyers()
    {
        return $this->belongsToMany(Buyer::class, 'Cart', 'ProductID', 'BuyerID')
            ->withPivot('TotalAmount', 'CartItems');
    }
}
