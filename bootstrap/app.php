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
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsureAccountNotBlocked::class,
            \App\Http\Middleware\CheckOtpVerification::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'dashboard/api-messages',
            'dashboard/api-messages/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $previous = url()->previous();
            $registerUrl = route('register');
            $loginUrl = route('login');

            if (str_contains($previous, 'register') || $request->routeIs('register')) {
                return redirect()->route('register')
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['csrf' => 'انتهت صلاحية الجلسة، يرجى المحاولة مرة أخرى.']);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['csrf' => 'انتهت صلاحية الجلسة، يرجى المحاولة مرة أخرى.']);
        });
    })->create();
