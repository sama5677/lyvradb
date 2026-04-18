<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPlanService
{
    private const API_URL = 'https://lyvramodel-production.up.railway.app/generate-plan';
    private const TIMEOUT = 15;

    public function generatePlan(User $user): array
    {
        $payload = $this->buildPayload($user);
        
        Log::info('AI Plan Service: Sending payload', ['user_id' => $user->id, 'payload' => $payload]);

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->retry(2, 100)
                ->post(self::API_URL, $payload);

            Log::info('AI Plan Service: Response received', [
                'user_id' => $user->id,
                'status' => $response->status(),
            ]);

            if (!$response->successful()) {
                Log::error('AI service error response', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('AI service returned status ' . $response->status());
            }

            $data = $response->json();
            
            Log::info('AI Plan Service: Response data', ['data' => $data]);

            if (!$data || !is_array($data)) {
                throw new \Exception('Invalid AI response format');
            }

            return [
                'workout_split' => $data['workout']['workout_day_name'] ?? 'Default Workout',
                'breakfast' => $data['nutrition']['breakfast'] ?? [],
                'lunch' => $data['nutrition']['lunch'] ?? [],
                'dinner' => $data['nutrition']['dinner'] ?? [],
                'snack' => $data['nutrition']['snack'] ?? null,
                'coach_motivation' => $data['coach_motivation'] ?? '',
                'full_response' => $data,
            ];

        } catch (\Illuminate\Http\Client\TimeoutException $e) {
            Log::error('AI service timeout', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw new \Exception('AI service timeout');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AI service connection error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw new \Exception('Unable to connect to AI service');
        } catch (\Exception $e) {
            Log::error('AI service error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildPayload(User $user): array
    {
        return [
            'weight' => (float) ($user->weight ?? 0),
            'height' => (float) ($user->height ?? 0),
            'age' => (int) ($user->age ?? 0),
            'goal' => $this->normalizeGoal($user->goal),
            'gender' => (string) ($user->gender ?? ''),
            'num_day_workout' => (string) ($user->workout_frequency ?? ''),
            'activity_level' => (string) ($user->activity_level ?? ''),
            'fav_food' => (string) ($user->eating_pattern ?? ''),
            'snacks' => (bool) ($user->snacks ?? false),
        ];
    }

    private function normalizeGoal(?string $goal): string
    {
        $goal = strtolower(trim((string) $goal));

        if (str_contains($goal, 'build') || str_contains($goal, 'muscle')) {
            return 'Build Muscle';
        }

        if (str_contains($goal, 'lose') || str_contains($goal, 'weight')) {
            return 'Lose Weight';
        }

        return 'Keep Fit';
    }
}
