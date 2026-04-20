<?php

namespace App\Http\Controllers;

use App\Models\UserPlan;
use App\Services\AiPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OnboardingController extends Controller
{
    // ================= General Onboarding =================
    public function general(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $validated = $request->validate([
                'gender' => 'required|in:male,female,other',
                'age'    => 'required|integer|min:10|max:100',
                'height' => 'required|numeric|min:100|max:250',
                'weight' => 'required|numeric|min:30|max:300',
            ]);

            Log::info('Onboarding General - Incoming data', [
                'user_id' => $user->id,
                'data'    => $validated
            ]);

            $user->update([
                'gender' => $validated['gender'],
                'age'    => $validated['age'],
                'height' => $validated['height'],
                'weight' => $validated['weight'],
            ]);

            $user->update(['onboarding_completed' => true]);

            Log::info('Onboarding General completed successfully', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'General onboarding completed successfully',
                'user'    => [
                    'id'      => $user->id,
                    'fullName'=> $user->full_name,
                    'gender'  => $user->gender,
                    'age'     => $user->age,
                    'height'  => $user->height,
                    'weight'  => $user->weight,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Onboarding General Validation Failed', $e->errors());
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Onboarding General Error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json([
                'message'     => 'An error occurred during onboarding',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ================= Fitness Onboarding =================
    public function fitness(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $validated = $request->validate([
                'goal'              => 'required|string|max:100',
                'focus_area'        => 'required|string|max:100',
                'activity_level'    => 'required|string|in:sedentary,light,moderate,active,very_active',
                'workout_frequency' => [
                'required',
                 'string',
                \Illuminate\Validation\Rule::in([
                 '1-2 times a week',
                 '3-4 times a week',
                  '4-5 times a week',
                  'Every day',
    ]),
],    
                'injuries'          => 'nullable|string|max:500',
                'body_shape'        => 'required|string|in:slim,average,athletic,overweight,obese', // السؤال الجديد
            ]);

            $user->update($validated);

            Log::info('Onboarding Fitness completed', ['user_id' => $user->id, 'body_shape' => $validated['body_shape']]);

            return response()->json([
                'message' => 'Fitness onboarding completed successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Fitness Onboarding Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during fitness onboarding',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ================= Nutrition Onboarding =================
    public function nutrition(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $validated = $request->validate([
                'meals_per_day'      => 'required|integer|min:1|max:10',
                'eating_pattern'     => 'required|string|max:100',
                'water_intake'       => 'required|integer|min:1|max:10',
                'snacks'             => 'nullable|string|max:255',
                'largest_meal_time'  => 'required|string|max:100',     // When is usually your largest meal?
                'skips_meals'        => 'required|in:yes,no,sometimes', // Do you usually skip meals?
                'favorite_foods'     => 'nullable|string|max:500',     // What foods do you like to eat more of?
            ]);

            $user->update($validated);
            $user->update(['profile_completed' => true]);

            Log::info('Onboarding Nutrition completed', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Nutrition onboarding completed successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Nutrition Onboarding Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during nutrition onboarding',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

}
