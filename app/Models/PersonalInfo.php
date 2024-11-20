<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    /** @use HasFactory<\Database\Factories\PersonalInfoFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone_number',
        'user_address',
    ];

    protected $casts = [
        'phone_number' => 'string',
        'user_address' => 'string',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
