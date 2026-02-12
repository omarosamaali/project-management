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

        // التحقق مما إذا كان الحساب محظوراً فور تسجيل الدخول
        if ($request->user()->status === 'blocked') {
            // تسجيل الخروج فوراً لإنهاء الجلسة التي فُتحت
            auth()->logout();

            // تدمير الجلسة ومسح التوكن للأمان
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // العودة مع رسالة خطأ
            return redirect()->route('login')->withErrors([
                'email' => 'هذا الحساب محظور حالياً. يرجى التواصل مع الإدارة للمزيد من التفاصيل.',
            ]);
        }

        $request->session()->regenerate();

        // التوجيه حسب الدور (Role)
        if ($request->user()->role === 'client') {
            return redirect()->route('dashboard.requests.index');
        }

        return redirect()->intended(route('dashboard', absolute: false));
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
