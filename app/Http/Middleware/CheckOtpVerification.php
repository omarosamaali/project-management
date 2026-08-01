<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOtpVerification
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('otp.resend', 'otp.verify', 'otp.email.check', 'otp.whatsapp.check', 'logout')) {
            return $next($request);
        }

        if ($request->is('verify-otp', 'verify-otp/*', 'resend-otp/*')) {
            return $next($request);
        }

        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }

        if ($user->role === 'independent_partner') {
            if (! $user->whatsapp_verified || is_null($user->email_verified_at)) {
                return redirect()->route('otp.verify');
            }
        }

        if ($user->needsEmailOtpVerification()) {
            return redirect()->route('otp.verify');
        }

        return $next($request);
    }
}
