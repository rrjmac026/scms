<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\AuditLogHelper; // <-- added import

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
    {
        // Validate input
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Try to find the user
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            // Audit: failed login - user not found
            AuditLogHelper::log('login_failed', "Failed login attempt for email {$credentials['email']} (user not found)");
            return back()->withErrors([
                'email' => 'No account found for this email.',
            ]);
        }

        // 🚫 Block inactive users
        if ($user->status === 'inactive') {
            // Audit: failed login - account inactive
            AuditLogHelper::log('login_failed', "Failed login attempt for user_id {$user->id} (account inactive)");
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        // Try to authenticate
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Audit: failed login - invalid credentials
            AuditLogHelper::log('login_failed', "Failed login attempt for email {$credentials['email']} (invalid credentials)");
            return back()->withErrors([
                'email' => 'Invalid credentials. Please try again.',
            ]);
        }

        // Authentication passed, regenerate session
        $request->session()->regenerate();

        // Get authenticated user
        $user = Auth::user();

        // ✅ Two-factor check
        if ($user->two_factor_secret && !session('auth.two_factor.authenticated')) {
            // Audit: 2FA required / initiation
            AuditLogHelper::log('login_2fa_initiated', "Login requires 2FA for user_id {$user->id}");

            Auth::logout(); // Logout temporarily
            
            // Store user ID for verification
            session([
                'auth.two_factor.user_id' => $user->id,
                'auth.two_factor.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('two-factor.challenge');
        }

        // ✅ Record last login time
        $user->update([
            'last_login_at' => now(),
        ]);

        // Audit: successful login (non-2FA)
        AuditLogHelper::log('login_success', "User {$user->id} logged in");

        // ✅ Redirect based on role
        return match($user->role) {
            'admin' => redirect()->intended('admin/dashboard'),
            'counselor' => redirect()->intended('counselor/dashboard'),
            'student' => redirect()->intended('student/dashboard'),
            default => redirect()->intended('dashboard'),
        };
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Capture current user for audit before logout
        $user = Auth::user();
        if ($user) {
            AuditLogHelper::log('logout', "User {$user->id} logged out");
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show 2FA challenge form
     */
    public function showTwoFactorChallenge(): View|RedirectResponse
    {
        if (!session('auth.two_factor.user_id')) {
            return redirect()->route('login');
        }

        // Audit: 2FA challenge viewed
        $uid = session('auth.two_factor.user_id');
        AuditLogHelper::log('2fa_challenge_viewed', "2FA challenge viewed for user_id {$uid}");

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code
     */
    public function twoFactorChallenge(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required_without:recovery_code|string|size:6',
            'recovery_code' => 'nullable|string',
        ]);

        $user = \App\Models\User::find(session('auth.two_factor.user_id'));

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'email' => 'Authentication session expired. Please try again.',
            ]);
        }

        $isValid = false;

        // Check if using recovery code
        if ($request->filled('recovery_code')) {
            $isValid = $user->verifyRecoveryCode($request->code);
        } else {
            // Regular 2FA code verification
            $isValid = $user->verifyTwoFactorCode($request->code);
        }

        if (!$isValid) {
            // Audit: failed 2FA attempt
            AuditLogHelper::log('login_2fa_failed', "Failed 2FA attempt for user_id {$user->id}");
            return back()->withErrors([
                'code' => 'The provided authentication code was invalid.',
            ]);
        }

        Auth::login($user, session('auth.two_factor.remember', false));
        
        session()->forget([
            'auth.two_factor.user_id',
            'auth.two_factor.remember',
        ]);
        
        session(['auth.two_factor.authenticated' => true]);

        // Update last login
        $user->update(['last_login_at' => now()]);
        
        // Audit: successful 2FA login
        AuditLogHelper::log('login_success_2fa', "User {$user->id} logged in via 2FA");

        return redirect()->intended(match($user->role) {
            'admin' => 'admin/dashboard',
            'counselor' => 'counselor/dashboard',
            'student' => 'student/dashboard',
            default => 'dashboard',
        });
    }

    /**
     * Log login related activity (optional - remove if you don't have ActivityLog)
     */
    private function logLoginActivity($user, string $action, Request $request): void 
    {
        // Uncomment if you have ActivityLog model
        /*
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => 'User',
            'model_id' => $user->id,
            'details' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'performed_at' => now(),
        ]);
        */
    }
}