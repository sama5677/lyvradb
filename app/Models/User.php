<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'full_name',              
        'email',
        'password',
        'gender',
        'phone',
        'age',
        'height',
        'weight',
        'goal',
        'focus_area',
        'activity_level',
        'workout_frequency',
        'injuries',
        'meals_per_day',
        'eating_pattern',
        'water_intake',
        'snacks',
        'is_email_verified',
        'onboarding_completed',
        'profile_completed',
        'otp',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'otp',
        'otp_expires_at',
    ];

    protected $casts = [
        'is_email_verified'    => 'boolean',
        'onboarding_completed' => 'boolean',
        'profile_completed'    => 'boolean',
        'age'                  => 'integer',
        'workout_frequency'    => 'integer',
        'meals_per_day'        => 'integer',
        'water_intake'         => 'integer',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['email' => $this->email];
    }

    public function calculateBMI()
    {
        if (!$this->weight || !$this->height) return null;

        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }
}