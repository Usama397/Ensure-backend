<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SocialAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = $this->findOrCreateUser($googleUser, 'google');
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => 'Google authentication failed',
            ]);
        }
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }
    public function handleFacebookCallback(Request $request)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => 'Facebook authentication failed',
            ]);
        }
    }

    // Helper function to find or create a user
    protected function findOrCreateUser($providerUser, $provider)
    {
        $user = User::where('provider_id', $providerUser->id)
            ->where('provider', $provider)
            ->first();

        if (!$user) {
            $user = User::where('email', $providerUser->email)->first();

            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerUser->id,
                ]);
            } else {
                $user = User::create([
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'provider' => $provider,
                    'provider_id' => $providerUser->id,
                    'password' => Hash::make(uniqid()),
                    'role' => 'user',
                    'phone_no' => $providerUser->phone_no ?? null,
                ]);
            }
        }

        return $user;
    }
}
