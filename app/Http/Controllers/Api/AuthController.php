<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Swift_TransportException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'password' => 'required_without:apple_id',
            'apple_id' => 'nullable|string|required_without:password', 
        ]);
    
        // Login using Apple ID
        if ($request->filled('apple_id')) {
            $user = User::where('apple_id', $request->apple_id)->first();
    
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Apple ID',
                ], 401);
            }
        } else {
            // Login using email & password
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid credentials',
                ], 401);
            }
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'status' => 200,
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required_without:apple_id|string|min:8|confirmed',
            'phone_no' => 'required|string|max:15|unique:users',
            'apple_id' => 'nullable|string|unique:users|required_without:password',
            'fcm_token' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 422
            ], 422);
        }
    
        // Generate a dummy email if not provided
        $email = $request->email ?? "apple_{$request->apple_id}@example.com";
    
        // Create the user with or without a password
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => $request->filled('password') ? Hash::make($request->password) : null, // Allow null password
            'phone_no' => $request->phone_no,
            'apple_id' => $request->apple_id,
            'fcm_token' => $request->fcm_token,
            'role' => 'user',
        ]);
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'status' => 201,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
    
    


    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 401,
                'message' => 'User is already logged out or invalid token.',
            ], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully',
        ]);
    }


    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email address.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email.',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to send reset link. Please try again.',
            ], 500);
        } catch (Swift_TransportException $e) {
            // Log the actual error for debugging
            Log::error('Mail Sending Error: ' . $e->getMessage());

            // Return a user-friendly response
            return response()->json([
                'success' => false,
                'message' => 'Mail server is not configured. Please contact support.',
            ], 500);
        } catch (\Exception $e) {
            // Handle other exceptions if needed
            Log::error('General Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired token.',
        ], 400);
    }

    public function saveDeviceName(Request $request)
    {
        $request->validate([
            'device_name' => 'required',
        ]);

        $user = Auth::user();

        $user->device_name = $request->device_name;
        $user->save();

        return response()->json([
            'message' => 'Device name saved successfully',
            'device_name' => $user->device_name,
        ], 200);
    }

    public function getDeviceName()
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'Device name retrieved successfully',
            'device_name' => $user->device_name,
        ], 200);
    }

    public function showProfile(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'profile_image' => $user->profile_image ? asset('storage/profile_images/' . $user->profile_image) : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'phone_no' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', //5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password); // Hash the password if provided
        }
        $user->phone_no = $request->phone_no;

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($user->profile_image) {
                Storage::delete('public/profile_images/' . $user->profile_image);
            }

            $image = $request->file('image');
            $originalName = str_replace(' ', '_', $image->getClientOriginalName());
            $imageName = time() . '_' . $originalName;
            $image->storeAs('public/profile_images', $imageName);

            $user->profile_image = $imageName;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_no' => $user->phone_no,
                'image_url' => $user->profile_image ? asset('storage/profile_images/' . $user->profile_image) : null,
            ],
        ]);
    }

}
