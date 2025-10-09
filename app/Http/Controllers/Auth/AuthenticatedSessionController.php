<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
            return back()->withErrors([
                'email' => 'No account found for this email.',
            ]);
        }

        // 🚫 Block inactive users
        if ($user->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        // Try to authenticate
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
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
        
        // Log successful 2FA login (optional)
        // $this->logLoginActivity($user, 'login_success_2fa', $request);

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