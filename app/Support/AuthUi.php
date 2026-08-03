<?php

namespace App\Support;

class AuthUi
{
    public const SESSION_KEY = 'auth_ui';

    public const ACADEMY = 'academy';

    public const CLASSIC = 'classic';

    /**
     * Resolve UI mode from query (?ui=) or session. Persists explicit choices.
     */
    public static function resolve(?string $explicit = null): string
    {
        $candidate = $explicit ?? request()->query('ui');
        if (is_string($candidate) && in_array($candidate, [self::ACADEMY, self::CLASSIC], true)) {
            session([self::SESSION_KEY => $candidate]);

            return $candidate;
        }

        return session(self::SESSION_KEY, self::CLASSIC);
    }

    public static function isAcademy(?string $explicit = null): bool
    {
        return self::resolve($explicit) === self::ACADEMY;
    }

    /**
     * Remember context from the page the user is currently browsing.
     */
    public static function rememberFromCurrentPage(): void
    {
        if (\App\Support\AppDomains::isAcademyRequest()) {
            session([self::SESSION_KEY => self::ACADEMY]);

            return;
        }

        if (request()->routeIs('academy.*') || request()->routeIs('courses.show') || request()->routeIs('academy.home')) {
            session([self::SESSION_KEY => self::ACADEMY]);

            return;
        }

        if (
            request()->routeIs('system.*')
            || request()->routeIs('landing.*')
            || request()->routeIs('home')
            || request()->is('/')
        ) {
            session([self::SESSION_KEY => self::CLASSIC]);
        }
    }

    public static function loginUrl(array $extra = []): string
    {
        return route('login', array_merge(['ui' => self::resolve()], $extra));
    }

    public static function registerUrl(array $extra = []): string
    {
        return route('register', array_merge(['ui' => self::resolve()], $extra));
    }

    public static function passwordRequestUrl(array $extra = []): string
    {
        return route('password.request', array_merge(['ui' => self::resolve()], $extra));
    }

    public static function view(string $name): string
    {
        if (self::isAcademy()) {
            $academy = 'auth.academy.' . $name;
            if (view()->exists($academy)) {
                return $academy;
            }
        }

        return 'auth.' . $name;
    }
}
