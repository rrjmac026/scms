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

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('No authenticated user when redirecting to Google OAuth');
                return redirect()->route('login')->with('error', 'Please login before connecting Google Calendar.');
            }

            $client = new Google_Client();
            $client->setClientId(config('services.google_calendar.client_id'));
            $client->setClientSecret(config('services.google_calendar.client_secret'));
            $client->setRedirectUri(config('services.google_calendar.redirect'));

            // Force offline access to get a refresh token
            $client->setAccessType('offline');
            $client->setPrompt('consent'); // Force consent every time
            $client->setIncludeGrantedScopes(true);

            // Scopes required for Calendar and basic user info
            $client->setScopes([
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ]);

            // Optional: state for security
            $state = base64_encode(json_encode([
                'user_id' => $user->id,
                'timestamp' => time(),
                'csrf' => csrf_token()
            ]));
            $client->setState($state);

            $authUrl = $client->createAuthUrl();
            Log::info('Redirecting user to Google OAuth (Calendar)', [
                'user_id' => $user->id,
                'auth_url' => $authUrl,
                'redirect_uri' => config('services.google_calendar.redirect')
            ]);

            return redirect($authUrl);

        } catch (\Exception $e) {
            Log::error('Failed to initiate Google OAuth: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to connect to Google Calendar. Please try again.');
        }
    }


    public function handleGoogleCallback(Request $request)
    {
        try {
            // Check if the user is authenticated
            $user = Auth::user();
            if (!$user) {
                Log::error('No authenticated user during Google callback');
                return redirect()->route('login')->with('error', 'Please login before connecting Google Calendar.');
            }

            // Handle errors from Google
            if ($request->has('error')) {
                Log::error('Google OAuth error: ' . $request->get('error'));
                return $this->redirectToDashboard()->with('error', 'Failed to connect to Google Calendar.');
            }

            // Create Google client
            $client = new Google_Client();
            $client->setClientId(config('services.google_calendar.client_id'));
            $client->setClientSecret(config('services.google_calendar.client_secret'));
            $client->setRedirectUri(config('services.google_calendar.redirect'));
            $client->setAccessType('offline'); // Needed for refresh token
            $client->setPrompt('consent');     // Force refresh token on reconnection

            $code = $request->get('code');
            if (!$code) {
                Log::error('No code received from Google callback');
                return $this->redirectToDashboard()->with('error', 'No authorization code received.');
            }

            // Exchange code for tokens
            $token = $client->fetchAccessTokenWithAuthCode($code);

            // Log token for debugging
            Log::info('Google token received', ['token' => $token]);

            if (isset($token['error'])) {
                Log::error('Google token error', $token);
                return $this->redirectToDashboard()->with('error', 'Failed to get access token: ' . $token['error']);
            }
            $client->setAccessToken($token['access_token']); // Set token for the request

            $googleService = new \Google_Service_Oauth2($client);
            $googleUser = $googleService->userinfo->get();

            // Store token and expiry
            $user->update([
                'google_id' => $googleUser->id,
                'google_token' => $token, // Laravel handles JSON conversion
                'google_token_expires_at' => isset($token['expires_in'])
                    ? now()->addSeconds($token['expires_in'])
                    : null,
            ]);

            Log::info("Google Calendar connected for user {$user->id}");

            return $this->redirectToDashboard()->with('success', 'Google Calendar connected successfully!');

        } catch (\Exception $e) {
            Log::error('Google Calendar connection failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return $this->redirectToDashboard()->with('error', 'Failed to connect to Google Calendar. Please try again.');
        }
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
                $client->setClientId(config('services.google_calendar.client_id'));
                $client->setClientSecret(config('services.google_calendar.client_secret'));
                
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
                'client_id' => config('services.google_calendar.client_id') ? 'Present' : 'Missing',
                'client_secret' => config('services.google_calendar.client_secret') ? 'Present' : 'Missing',
                'redirect' => config('services.google_calendar.redirect'),
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