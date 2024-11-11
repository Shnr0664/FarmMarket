<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'Admin';
    protected $primaryKey = 'AdminID';
    public $timestamps = false;

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
