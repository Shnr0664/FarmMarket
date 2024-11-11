<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'Order';
    protected $primaryKey = 'OrderID';
    public $timestamps = false;

    // Inverse of One-to-Many relationship
    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'BuyerID');
    }

    // One-to-One relationship with Payment and Delivery
    public function payment()
    {
        return $this->hasOne(Payment::class, 'OrderID');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'OrderID');
    }
}
