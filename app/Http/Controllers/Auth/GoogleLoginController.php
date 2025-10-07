<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    private const ALLOWED_DOMAIN = 'lccdo.edu.ph';

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['hd' => self::ALLOWED_DOMAIN, 'prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // ============================================
            // CRITICAL: VALIDATE DOMAIN **IMMEDIATELY**
            // BEFORE ANY DATABASE OPERATIONS
            // ============================================
            
            $email = strtolower(trim($googleUser->email));
            
            // Extract domain from email
            $domain = substr(strrchr($email, "@"), 1);
            
            // HARD STOP: If not university email, reject immediately
            if ($domain !== strtolower(self::ALLOWED_DOMAIN)) {
                Log::warning('🚫 BLOCKED: Non-university email attempted login', [
                    'email' => $email,
                    'domain' => $domain,
                    'google_id' => $googleUser->id,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);

                return redirect()->route('login')
                    ->withErrors(['email' => 'Only @lccdo.edu.ph email addresses are allowed to access this system.']);
            }

            // Verify Google Workspace hosted domain (extra security)
            $hostedDomain = $googleUser->user['hd'] ?? null;
            if ($hostedDomain !== self::ALLOWED_DOMAIN) {
                Log::warning('🚫 BLOCKED: Email domain passed but HD check failed', [
                    'email' => $email,
                    'hd_received' => $hostedDomain,
                    'ip' => request()->ip()
                ]);

                return redirect()->route('login')
                    ->withErrors(['email' => 'Only institutional Google Workspace accounts from @lccdo.edu.ph are allowed.']);
            }

            // ============================================
            // VALIDATION PASSED - SAFE TO PROCEED
            // ============================================

            // Parse name
            $nameParts = $this->parseFullName($googleUser->name);

            // Check if user exists (only university emails reach here)
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create new user - we're 100% certain this is a valid university email
                $user = User::create([
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(32)),
                    'email_verified_at' => now(),
                ]);

                Log::info('✅ New university user registered', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'domain' => $domain
                ]);
            } else {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }

                Log::info('✅ Existing user logged in', [
                    'user_id' => $user->id,
                    'email' => $email
                ]);
            }

            // Login the user
            Auth::login($user, true);

            return redirect()->intended($this->redirectBasedOnRole($user));

        } catch (Exception $e) {
            Log::error('❌ Google OAuth error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => request()->ip()
            ]);

            return redirect()->route('login')
                ->withErrors(['email' => 'Authentication failed. Please try again.']);
        }
    }

    /**
     * Parse full name into first, middle, and last name
     */
    private function parseFullName(string $fullName): array
    {
        $nameParts = explode(' ', trim($fullName));
        $count = count($nameParts);

        if ($count === 1) {
            return [
                'first_name' => $nameParts[0],
                'middle_name' => null,
                'last_name' => null,
            ];
        }

        if ($count === 2) {
            return [
                'first_name' => $nameParts[0],
                'middle_name' => null,
                'last_name' => $nameParts[1],
            ];
        }

        // 3 or more names
        return [
            'first_name' => $nameParts[0],
            'middle_name' => implode(' ', array_slice($nameParts, 1, -1)),
            'last_name' => end($nameParts),
        ];
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
}