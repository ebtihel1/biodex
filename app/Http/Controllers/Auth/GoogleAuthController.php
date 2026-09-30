<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            \Log::error('Google OAuth redirect error', ['error' => $e->getMessage()]);
            return redirect('/login')->with('error', 'Unable to connect to Google. Please try again.');
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Configure Guzzle to ignore SSL verification for development
            $guzzleClient = new \GuzzleHttp\Client([
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ]);
            
            // Set the Guzzle client for Socialite
            Socialite::driver('google')->setHttpClient($guzzleClient);
            
            $googleUser = Socialite::driver('google')->user();
            
            // Log pour debug
            \Log::info('Google OAuth callback received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId()
            ]);
            
            // Check if user already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // User exists, check if they have Google ID
                if (!$existingUser->google_id) {
                    // Update existing user with Google ID
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
                
                Auth::login($existingUser);
                $user = $existingUser;
                
                \Log::info('Existing user logged in', ['user_id' => $user->id, 'role' => $user->role?->name]);
            } else {
                // Create new user with citoyen role by default
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)), // Random password since they'll use Google
                    'email_verified_at' => now(), // Google emails are pre-verified
                    'role_id' => 1, // citoyen role by default
                ]);
                
                Auth::login($newUser);
                $user = $newUser;
                
                \Log::info('New user created and logged in', ['user_id' => $user->id, 'role' => $user->role?->name]);
            }
            
            // Redirect based on user role (same logic as AuthentifController)
            $role = $user->role ? $user->role->name : null;
            
            \Log::info('Redirecting user', ['role' => $role, 'user_id' => $user->id]);
            
            if ($role === 'admin' || $role === 'entreprise') {
                \Log::info('Redirecting to back office');
                return redirect()->route('back.home');
            }
            
            if ($role === 'citoyen') {
                \Log::info('Redirecting to front office');
                return redirect('/biodex');
            }
            
            // Default fallback - redirect to front office
            \Log::info('Default redirect to front office (no role or unknown role)');
            return redirect('/biodex');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect('/login')->with('error', 'Something went wrong with Google authentication: ' . $e->getMessage());
        }
    }
}