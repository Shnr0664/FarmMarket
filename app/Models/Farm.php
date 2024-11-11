<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;
    protected $table = 'Farm';
    protected $primaryKey = 'FarmID';
    public $timestamps = false;

    // Inverse of One-to-Many relationship
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'FarmerID');
    }

    // One-to-Many relationship with Product
    public function products()
    {
        return $this->hasMany(Product::class, 'FarmID');
    }
}
