<?php

namespace App\Http\Controllers\Auth;

use App\Support\AuthUi;
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
    public function create(Request $request): View
    {
        AuthUi::resolve($request->query('ui'));

        return view(AuthUi::view('login'));
    }

    /**
     * Handle an incoming authentication request.
     */ 
    public function store(LoginRequest $request): RedirectResponse
    {
        AuthUi::resolve($request->input('ui', $request->query('ui')));

        $request->authenticate();

        $user = $request->user();

        // التحقق مما إذا كان الحساب محظوراً فور تسجيل الدخول
        if ($user->status === 'blocked') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(AuthUi::loginUrl())->withErrors([
                'email' => 'هذا الحساب محظور حالياً. يرجى التواصل مع الإدارة للمزيد من التفاصيل.',
            ]);
        }

        if ($user->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(AuthUi::loginUrl())->withErrors([
                'email' => __('messages.trainer_login_pending'),
            ]);
        }

        $request->session()->regenerate();

        $redirect = $request->input('redirect') ?: $request->query('redirect');
        if (is_string($redirect) && $redirect !== '') {
            $host = parse_url($redirect, PHP_URL_HOST);
            $appHost = parse_url(url('/'), PHP_URL_HOST);
            if ($host === null || $host === $appHost) {
                return redirect()->to($redirect);
            }
        }

        if ($user && method_exists($user, 'isTrainee') && $user->isTrainee()) {
            return redirect()->intended(route('academy.index'));
        }

        if ($user && method_exists($user, 'isTrainer') && $user->isTrainer()) {
            return redirect()->intended(route('academy.index'));
        }

        return redirect()->intended(route('system.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $toAcademyHome = $user
            && method_exists($user, 'usesAcademyShell')
            && $user->usesAcademyShell();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($toAcademyHome) {
            session([AuthUi::SESSION_KEY => AuthUi::ACADEMY]);

            return redirect()->route('academy.index');
        }

        return redirect('/');
    }
}
