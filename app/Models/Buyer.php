<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory;
    protected $table = 'Buyer';
    protected $primaryKey = 'BuyerID';
    public $timestamps = false;

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    // One-to-Many relationship with Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'BuyerID');
    }
}

