<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function show()
   {
    $user = Auth::user();

    return response()->json([
        'status' => true,
        'data' => [
            'fullName' => $user->fullName,
            'email' => $user->email,
            'phone' => $user->phone,
            'bmi' => $user->calculateBMI()
        ]
    ]);
    }

    public function update(Request $request)
   {
    $user = Auth::user();

    $request->validate([
        'fullName' => 'required|string',
        'phone' => 'nullable|string'
    ]);

    $user->update($request->only(['fullName', 'phone']));

    return response()->json([
        'status' => true,
        'message' => 'Profile updated'
    ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Wrong password'
            ], 400);
        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    // ================= Get Profile (API) =================
    public function getProfile()
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

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'age' => $user->age,
                    'height' => $user->height,
                    'weight' => $user->weight,
                    'goal' => $user->goal,
                    'gender' => $user->gender,
                    'activity_level' => $user->activity_level,
                    'is_email_verified' => $user->is_email_verified,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ERROR',
            ], 500);
        }
    }

    // ================= Update Profile (API) =================
    public function updateProfile(Request $request)
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

            $request->validate([
                'full_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'age' => 'nullable|integer|min:1|max:150',
                'height' => 'nullable|numeric|min:50|max:300',
                'weight' => 'nullable|numeric|min:20|max:500',
                'goal' => 'nullable|string|max:100',
                'gender' => 'nullable|string|in:Male,Female,Other',
                'activity_level' => 'nullable|string|max:100',
            ]);

            $updateData = $request->only([
                'full_name',
                'phone',
                'age',
                'height',
                'weight',
                'goal',
                'gender',
                'activity_level',
            ]);

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'age' => $user->age,
                    'height' => $user->height,
                    'weight' => $user->weight,
                    'goal' => $user->goal,
                    'gender' => $user->gender,
                    'activity_level' => $user->activity_level,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'error_code' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ERROR',
            ], 500);
        }
    }

    // ================= Update Password (API) =================
    public function updatePassword(Request $request)
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

            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
                'new_password_confirmation' => 'required|string|same:new_password',
            ]);

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'error_code' => 'INVALID_PASSWORD',
                ], 400);
            }

            // Check if new password same as current
            if ($request->current_password === $request->new_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'New password cannot be the same as current password',
                    'error_code' => 'SAME_PASSWORD',
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'error_code' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'ERROR',
            ], 500);
        }
    }
}
