<?php

namespace App\Http\Controllers;

use App\Services\ExerciseService;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    protected ExerciseService $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    /**
     * GET /api/categories
     */
    public function getCategories(): JsonResponse
    {
        $categories = $this->exerciseService->getAllCategories();

        return response()->json($categories);
    }

    /**
     * GET /api/categories/{plan_name}
     */
    public function getCategory(string $planName): JsonResponse
    {
        $category = $this->exerciseService->getCategory($planName);

        if (!$category) {
            return response()->json([
                'message' => 'Workout plan not found'
            ], 404);
        }

        return response()->json($category);
    }

    /**
     * GET /api/categories/{plan_name}/exercises
     */
    public function getAllExercises(string $planName): JsonResponse
    {
        $exercises = $this->exerciseService->getAllExercises($planName);

        if (!$exercises) {
            return response()->json([
                'message' => 'No exercises found'
            ], 404);
        }

        return response()->json($exercises);
    }

    /**
     * GET /api/categories/{plan_name}/exercises/{id}
     */
    public function getExercise(string $planName, int $exerciseId): JsonResponse
    {
        $exercise = $this->exerciseService->getExercise($planName, $exerciseId);

        if (!$exercise) {
            return response()->json([
                'message' => 'Exercise not found'
            ], 404);
        }

        return response()->json($exercise);
    }
}
