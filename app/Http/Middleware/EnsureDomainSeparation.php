<?php

namespace App\Http\Middleware;

use App\Support\AppDomains;
use App\Support\AuthUi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainSeparation
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! AppDomains::enabled()) {
            return $next($request);
        }

        $host = $request->getHost();
        $path = '/'.ltrim($request->path(), '/');
        if ($path === '//') {
            $path = '/';
        }

        if (AppDomains::isAcademyHost($host)) {
            session([AuthUi::SESSION_KEY => AuthUi::ACADEMY]);
        }

        $uri = $request->getRequestUri() ?: '/';

        if (AppDomains::isMainHost($host) && AppDomains::isAcademyOnlyPath($path)) {
            return redirect()->away(AppDomains::academyUrl($uri), 301);
        }

        if (AppDomains::isAcademyHost($host) && AppDomains::isMainOnlyPath($path)) {
            return redirect()->away(AppDomains::mainUrl($uri), 301);
        }

        return $next($request);
    }
}
