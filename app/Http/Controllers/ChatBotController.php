<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $user = $request->input('user', []);
            $message = $request->input('message');

            if (!$message) {
                return ResponseHelper::error('Validation failed', [
                    'message' => ['Message is required']
                ], 422);
            }

            /*
            ===============================
            SMART AI PROMPT ENGINE
            ===============================
            */

            $prompt = "
You are an Advanced AI Health, Fitness, and Nutrition Assistant.

Your role is to simulate a real AI fitness system.

User Information:
Age: " . ($user['age'] ?? 'N/A') . "
Weight: " . ($user['weight'] ?? 'N/A') . "
Height: " . ($user['height'] ?? 'N/A') . "
Gender: " . ($user['gender'] ?? 'N/A') . "
Goal: " . ($user['goal'] ?? 'N/A') . "

User Question:
{$message}

AI Rules:
1. Analyze user's goal first.
2. Provide:
   - Nutrition advice
   - Workout recommendations
   - Health tips
3. Format response as:
   • Analysis  
   • Diet Plan  
   • Workout Plan  
   • Advice  

4. Keep response practical and professional.
5. Respond in the same language as user.
";

            /*
            ===============================
            GEMINI API CALL
            ===============================
            */

            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return ResponseHelper::error('AI Error', 'Missing Gemini API Key', 500);
            }

            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            $data = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return ResponseHelper::error('AI request failed', curl_error($ch), 500);
            }

            curl_close($ch);

            $decoded = json_decode($response, true);

            $reply = $decoded['candidates'][0]['content']['parts'][0]['text']
                ?? "لا يمكن توليد رد حالياً";

            return ResponseHelper::success('AI response generated successfully', [
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            return ResponseHelper::error('Chatbot failed', $e->getMessage(), 500);
        }
    }
}