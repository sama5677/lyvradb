<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Workout;
use App\Models\Weight;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData()
    {
        $user = Auth::user();

        // Weekly progress (mocked for now)
        $weeklyProgress = [
            ['day' => 'Sat', 'completed' => true],
            ['day' => 'Sun', 'completed' => false],
            ['day' => 'Mon', 'completed' => true],
        ];

        // Daily progress (mocked for now)
        $dailyProgress = [
            'progress_percentage' => 80,
            'calories_burned' => 2000,
            'steps' => 10000,
        ];

        // Today's workout (mocked for now)
        $todayWorkout = [
            'title' => 'Full Body Workout',
            'image' => 'https://example.com/workout.jpg',
            'duration' => '45 mins',
        ];

        return [
            'user' => [
                'fullName' => $user->full_name,
            ],
            'weekly_progress' => $weeklyProgress,
            'daily_progress' => $dailyProgress,
            'today_workout' => $todayWorkout,
        ];
    }
}
