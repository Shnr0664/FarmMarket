<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'Delivery';
    protected $primaryKey = 'DeliveryID';
    public $timestamps = false;

    // Inverse of One-to-One relationship
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }
}

