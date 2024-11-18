<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'farm_id',
        'product_name',
        'product_quantity',
        'product_category',
        'product_desc',
        'product_price',
        'product_img',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    // Inverse of One-to-Many relationship
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }
}
