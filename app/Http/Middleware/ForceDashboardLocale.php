<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceDashboardLocale
{
    /**
     * Dashboard UI is Arabic/RTL — keep select labels and translations in Arabic.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('dashboard', 'dashboard/*')) {
            App::setLocale('ar');
        }

        return $next($request);
    }
}
