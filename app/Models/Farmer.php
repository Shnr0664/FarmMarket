<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;
    protected $table = 'Farmer';
    protected $primaryKey = 'FarmerID';
    public $timestamps = false;

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    // One-to-Many relationship with Farm
    public function farms()
    {
        return $this->hasMany(Farm::class, 'FarmerID');
    }
}
