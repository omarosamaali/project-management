<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        $request->authenticate();

        $user = $request->user();

        // التحقق مما إذا كان الحساب محظوراً فور تسجيل الدخول
        if ($user->status === 'blocked') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'هذا الحساب محظور حالياً. يرجى التواصل مع الإدارة للمزيد من التفاصيل.',
            ]);
        }

        if ($user->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => __('messages.trainer_login_pending'),
            ]);
        }

        $request->session()->regenerate();

        if ($user && method_exists($user, 'isTrainee') && $user->isTrainee()) {
            return redirect()->route('academy.index');
        }

        if ($user && method_exists($user, 'isTrainer') && $user->isTrainer()) {
            return redirect()->route('academy.index');
        }

        return redirect()->route('system.index');
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
}
