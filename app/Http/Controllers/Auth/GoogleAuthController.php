<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Oauth2;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            // For login, we don't need calendar access, just basic profile info
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));

            // Only request basic profile info for login
            $client->setScopes([
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ]);

            $authUrl = $client->createAuthUrl();
            return redirect($authUrl);

        } catch (\Exception $e) {
            Log::error('Failed to initiate Google OAuth: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Failed to connect to Google. Please try again.');
        }
    }


    public function handleGoogleCallback(Request $request)
    {
        try {
            if ($request->has('error')) {
                return redirect()->route('login')->with('error', 'Google authentication failed.');
            }

            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));

            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            if (isset($token['error'])) {
                return redirect()->route('login')->with('error', 'Failed to get access token.');
            }

            $client->setAccessToken($token);
            $googleService = new Google_Service_Oauth2($client);
            $googleUser = $googleService->userinfo->get();

            // Find or create user
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Handle new user registration
                $names = explode(' ', $googleUser->name);
                $firstName = $names[0];
                $lastName = end($names);
                $middleName = count($names) > 2 ? implode(' ', array_slice($names, 1, -1)) : null;

                $user = User::create([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(24)),
                ]);
            }

            // Update Google ID if not set
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->id]);
            }

            // Log the user in
            Auth::login($user, true);

            return redirect()->intended($this->redirectBasedOnRole($user));

        } catch (\Exception $e) {
            Log::error('Google authentication failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    private function redirectBasedOnRole($user)
    {
        return match($user->role) {
            'admin' => '/admin/dashboard',
            'counselor' => '/counselor/dashboard',
            'student' => '/student/dashboard',
            default => '/dashboard',
        };
    }

    /**
     * Disconnect Google Calendar integration
     */
    public function disconnect()
    {
        try {
            $user = Auth::user();
            
            if ($user->google_token) {
                // Attempt to revoke the token
                $client = new Google_Client();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                
                $tokenData = is_array($user->google_token) ? $user->google_token : json_decode($user->google_token, true);
                $client->setAccessToken($tokenData);
                
                // Revoke token with Google
                $client->revokeToken();
            }

            // Clear Google data from user
            $user->update([
                'google_id' => null,
                'google_token' => null,
                'google_token_expires_at' => null,
            ]);

            Log::info("Google Calendar disconnected for user {$user->id}");
            
            return $this->redirectToDashboard()->with('success', 'Google Calendar disconnected successfully.');

        } catch (\Exception $e) {
            Log::error('Google disconnect failed: ' . $e->getMessage());
            
            // Even if revocation fails, clear the local data
            Auth::user()->update([
                'google_id' => null,
                'google_token' => null,
                'google_token_expires_at' => null,
            ]);
            
            return $this->redirectToDashboard()->with('success', 'Google Calendar disconnected successfully.');
        }
    }

    /**
     * Check Google Calendar connection status
     */
    public function status()
    {
        $user = Auth::user();
        $isConnected = !empty($user->google_token);
        
        $tokenExpired = false;
        if ($isConnected && $user->google_token_expires_at) {
            $tokenExpired = Carbon::now()->isAfter($user->google_token_expires_at);
        }

        return response()->json([
            'connected' => $isConnected,
            'expired' => $tokenExpired,
            'user_email' => $user->email,
            'google_id' => $user->google_id,
            'expires_at' => $user->google_token_expires_at?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Helper method to redirect to appropriate dashboard based on user role
     */
    private function redirectToDashboard()
    {
        $user = Auth::user();

        // If you have an is_admin boolean
        if ($user->is_admin ?? false) {
            return redirect()->route('admin.dashboard');
        }

        // If the user has a related counselor model
        if ($user->counselor) {
            return redirect()->route('counselor.dashboard');
        }

        // If the user has a related student model
        if ($user->student) {
            return redirect()->route('student.dashboard');
        }

        // Fallback
        return redirect()->route('dashboard');
    }

    /**
     * Debug method - remove in production
     */
    public function debug()
    {
        return response()->json([
            'config' => [
                'client_id' => config('services.google.client_id') ? 'Present' : 'Missing',
                'client_secret' => config('services.google.client_secret') ? 'Present' : 'Missing',
                'redirect' => config('services.google.redirect'),
            ],
            'user' => [
                'id' => auth()->id(),
                'email' => auth()->user()->email,
                'has_google_token' => auth()->user()->google_token ? true : false,
                'role' => auth()->user()->getRoleNames()->first(),
            ],
        ]);
    }
}