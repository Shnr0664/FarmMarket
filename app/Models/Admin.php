<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
    ];

    // Inverse of One-to-One relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
