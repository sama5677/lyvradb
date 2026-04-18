<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // ================= Register =================
    public function register(Request $request)
    {
        try {
            $request->validate([
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|min:6',
                'fullName'  => 'required|string|max:255',
                'gender'    => 'nullable|string|in:male,female,other',
                'age'       => 'nullable|integer|min:10|max:100',
            ]);

            $fullName = trim($request->fullName ?? $request->full_name ?? 'Unnamed User');

            // بناء البيانات بدقة - نستخدم snake_case فقط
            $userData = [
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'full_name'  => $fullName,
            ];

            
            if ($request->filled('gender')) {
                $userData['gender'] = $request->gender;
            }

            if ($request->filled('age')) {
                $userData['age'] = $request->age;
            }

            $user = User::create($userData);

            return response()->json([
                'message' => 'User created successfully',
                'user'    => [
                    'id'       => $user->id,
                    'fullName' => $user->full_name,
                    'email'    => $user->email,
                    'gender'   => $user->gender ?? null,
                    'age'      => $user->age ?? null,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while creating the user',
                'debug'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ================= Login =================
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $user = Auth::user();

        return response()->json([
            'message'      => 'User logged in successfully',
            'accessToken'  => $token,
            'refreshToken' => JWTAuth::fromUser($user),
            'user'         => [
                'id'       => $user->id,
                'fullName' => $user->full_name,
                'email'    => $user->email,
                'gender'   => $user->gender ?? null,
                'age'      => $user->age ?? null,
            ]
        ]);
    }

    
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email|exists:users,email']);

            $user = User::where('email', $request->email)->firstOrFail();

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);

            Log::info("OTP generated for {$user->email}: {$otp}");

            try {
                Mail::raw("Your OTP code is: {$otp}\n\nThis code will expire in 10 minutes.", function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Reset OTP - LYVRA');
                });
            } catch (\Exception $e) {
                Log::error("Mail sending failed: " . $e->getMessage());
            }

            return response()->json(['message' => 'OTP generated successfully']);

        } catch (\Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred',
                'debug'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);
        if ($user->otp != $request->otp) return response()->json(['message' => 'Invalid OTP'], 400);
        if (!$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }

        $user->update(['otp' => null, 'otp_expires_at' => null]);

        return response()->json(['message' => 'OTP verified successfully']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'otp'          => 'required|digits:6',
            'new_password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);
        if ($user->otp != $request->otp || !$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'otp' => null,
            'otp_expires_at' => null
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function refreshToken()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['accessToken' => $newToken]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'User signed out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout failed'], 400);
        }
    }
}