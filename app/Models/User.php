<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public $timestamps = false; // Disable if timestamps are not in the table


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'email',
        'password',
        'profile_pic',
        'email_verification_code',
        'email_verification_expires_at',
        'email_verified_at',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_expires_at' => 'datetime', // Automatically cast to a DateTime instance
    ];

    public function generateVerificationCode()
    {
        $this->email_verification_code = rand(100000, 999999);
        $this->email_verification_expires_at = now()->addMinutes(10); // Code valid for 10 minutes
        $this->save();
    }
    public function clearVerificationCode()
    {
        $this->email_verification_code = null;
        $this->email_verification_expires_at = null;
        $this->save();
    }
    public function isVerificationCodeValid($code)
    {
        return $this->email_verification_code === $code &&
            $this->email_verification_expires_at &&
            $this->email_verification_expires_at->isFuture();
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'user_id');
    }

    public function farmer()
    {
        return $this->hasOne(Farmer::class, 'user_id');
    }

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Buyer::class, 'user_id', 'buyer_id');
    }

    public function isAdmin()
    {
        return Admin::where('user_id', $this->id)->exists();
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }





}
