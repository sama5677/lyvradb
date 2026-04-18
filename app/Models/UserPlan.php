<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    protected $fillable = [
        'user_id',
        'workout_split',
        'breakfast',
        'lunch',
        'dinner',
        'snack',
        'coach_motivation',
        'full_response',
    ];

    protected $casts = [
        'breakfast' => 'array',
        'lunch' => 'array',
        'dinner' => 'array',
        'snack' => 'array',
        'full_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
