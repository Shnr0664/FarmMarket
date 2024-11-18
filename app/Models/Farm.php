<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'farmer_id',
        'farm_name',
        'farm_size',
        'crops_types',
    ];

    protected $casts = [
        'farm_size' => 'decimal:2',
        'crops_types' => 'array', // Assuming crops_types might be stored as JSON.
    ];

    // Inverse of One-to-Many relationship
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    // One-to-Many relationship with Product
    public function products()
    {
        return $this->hasMany(Product::class, 'farm_id');
    }
}
