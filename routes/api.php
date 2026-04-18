<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ExerciseController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/chatbot', [ChatBotController::class, 'chat']);

// ================= Exercise/Workout Plans =================
Route::get('/categories', [ExerciseController::class, 'getCategories']);
Route::get('/categories/{plan_name}', [ExerciseController::class, 'getCategory']);
Route::get('/categories/{plan_name}/exercises', [ExerciseController::class, 'getAllExercises']);
Route::get('/categories/{plan_name}/exercises/{id}', [ExerciseController::class, 'getExercise']);

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {

    // ================= Profile =================
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);

    // ================= Onboarding =================
    Route::post('/onboarding/general', [OnboardingController::class, 'general']);
    Route::post('/onboarding/fitness', [OnboardingController::class, 'fitness']);
    Route::post('/onboarding/nutrition', [OnboardingController::class, 'nutrition']);
    Route::post('/onboarding/save-answer', [OnboardingController::class, 'saveAnswer']);



    // ================= Plan =================
    Route::post('/generate-plan', [PlanController::class, 'generatePlan']);
    Route::get('/my-plan', [PlanController::class, 'myPlan']);



});

