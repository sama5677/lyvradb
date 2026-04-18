<?php

namespace App\Http\Controllers;

use App\Models\UserPlan;
use App\Services\AiPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function generatePlan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'error_code' => 'AUTH_ERROR',
                ], 401);
            }

            // Check if plan already exists
            $existingPlan = UserPlan::where('user_id', $user->id)->first();
            if ($existingPlan) {
                return response()->json([
                    'success' => true,
                    'data' => $this->formatPlanResponse($existingPlan),
                    'message' => 'Plan already exists',
                ], 200);
            }

            // Validate onboarding data
            $filledFields = array_filter([
                $user->gender,
                $user->age,
                $user->height,
                $user->weight,
                $user->goal,
                $user->activity_level,
            ]);

            if (count($filledFields) < 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incomplete onboarding data',
                    'error_code' => 'INCOMPLETE_ONBOARDING',
                ], 400);
            }

            // Generate plan via AI service
            $aiService = new AiPlanService();
            $planData = $aiService->generatePlan($user);

            // Store plan in DB
            $plan = UserPlan::create([
                'user_id' => $user->id,
                'workout_split' => $planData['workout_split'] ?? '',
                'breakfast' => $planData['breakfast'] ?? [],
                'lunch' => $planData['lunch'] ?? [],
                'dinner' => $planData['dinner'] ?? [],
                'snack' => $planData['snack'] ?? null,
                'coach_motivation' => $planData['coach_motivation'] ?? '',
                'full_response' => $planData['full_response'] ?? [],
            ]);

            return response()->json([
                'success' => true,
                'data' => $this->formatPlanResponse($plan),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Generate plan error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ERROR',
            ], 500);
        }
    }

    public function myPlan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'error_code' => 'AUTH_ERROR',
                ], 401);
            }

            $plan = UserPlan::where('user_id', $user->id)->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No plan found',
                    'error_code' => 'NOT_FOUND',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatPlanResponse($plan),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get plan error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ERROR',
            ], 500);
        }
    }

    private function formatPlanResponse(UserPlan $plan): array
    {
        return [
            'workout' => [
                'workout_day_name' => $plan->workout_split ?? '',
            ],
            'nutrition' => [
                'breakfast' => $plan->breakfast ?? [],
                'lunch' => $plan->lunch ?? [],
                'dinner' => $plan->dinner ?? [],
                'snack' => $plan->snack ?? null,
            ],
            'coach_motivation' => $plan->coach_motivation ?? '',
        ];
    }
}
