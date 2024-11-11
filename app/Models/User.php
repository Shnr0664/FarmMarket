<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'User';
    protected $primaryKey = 'UserID';
    public $timestamps = false;

    public function admin()
    {
        return $this->hasOne(Admin::class, 'UserID');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'UserID');
    }

    public function farmer()
    {
        return $this->hasOne(Farmer::class, 'UserID');
    }

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'UserID');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Password',
        'ProfilePic',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Password',
    ];

    /**
     * Hash the password before saving to the database.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['Password'] = Hash::make($value);
    }
}
