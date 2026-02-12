<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // أضف هذا الاستثناء لضمان وصول طلبات إعادة الإرسال
        if ($request->routeIs('otp.resend')) {
            return $next($request);
        }
        
        $user = Auth::user();

        if ($user && $user->role === 'independent_partner') {
            // لو لسه مأكدش الواتساب أو الإيميل
            if (!$user->whatsapp_verified || is_null($user->email_verified_at)) {
                // اسمح له فقط بزيارة صفحة التحقق أو تسجيل الخروج
                if (!$request->routeIs('otp.verify') && !$request->routeIs('logout') && !$request->is('verify-otp/*')) {
                    return redirect()->route('otp.verify');
                }
            }
        }

        return $next($request);
    }
}
