<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\ForceDashboardLocale::class,
            \App\Http\Middleware\EnsureAccountNotBlocked::class,
            \App\Http\Middleware\CheckOtpVerification::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'dashboard/api-messages',
            'dashboard/api-messages/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\JsonException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'خطأ في ترميز البيانات. تواصل مع الدعم.'], 500);
            }

            return response(
                '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>خطأ</title></head>'
                . '<body style="font-family:sans-serif;padding:2rem;text-align:center">'
                . '<h1>تعذّر عرض الصفحة</h1>'
                . '<p>بيانات غير صالحة في النظام (ترميز UTF-8). راجع <code>storage/logs/laravel.log</code> أو تواصل مع الدعم.</p>'
                . '</body></html>',
                500,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مرة أخرى.',
                    'session_expired' => true,
                    'login_url' => route('login'),
                ], 401);
            }

            return redirect()->guest(route('login'))
                ->with('session_expired', true)
                ->with('error', 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مرة أخرى.');
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $message = 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مرة أخرى.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'session_expired' => true,
                    'login_url' => route('login'),
                ], 419);
            }

            $previous = url()->previous();

            // Keep the user on the register form so they can retry without losing context
            if (str_contains((string) $previous, 'register') || $request->routeIs('register')) {
                return redirect()->route('register')
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['csrf' => 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة والمحاولة مرة أخرى.'])
                    ->with('error', 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة والمحاولة مرة أخرى.');
            }

            return redirect()->route('login')
                ->with('session_expired', true)
                ->with('error', $message);
        });
    })->create();
