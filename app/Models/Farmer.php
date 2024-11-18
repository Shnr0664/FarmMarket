<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    /** @use HasFactory<\Database\Factories\FarmerFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
    ];

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // One-to-Many relationship with Farm
    public function farms()
    {
        return $this->hasMany(Farm::class, 'farmer_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Farm::class, 'farmer_id', 'farm_id');
    }
}
