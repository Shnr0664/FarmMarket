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

    protected $fillable = [
        'UserID',
        'IsApproved'
    ];

    protected $casts = [
        'IsApproved' => 'boolean'
    ];

    public function scopeApproved($query)
    {
        return $query->where('IsApproved', true);
    }

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