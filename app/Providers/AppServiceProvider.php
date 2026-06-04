<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requests;
use App\Models\Support;
use App\Observers\RequestObserver;
use App\Observers\RequestsObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Support\CountryNames;
use App\Support\WorkAttendanceState;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Requests::observe(RequestObserver::class);
        Requests::observe(RequestsObserver::class);

        View::composer('*', function () {
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();
            $user->name = CountryNames::ensureUtf8($user->name) ?? '';
            $user->email = CountryNames::ensureUtf8($user->email) ?? '';
            foreach (['note_title', 'note_details', 'company_name', 'withdrawal_notes'] as $field) {
                if ($user->{$field} !== null && $user->{$field} !== '') {
                    $user->{$field} = CountryNames::ensureUtf8((string) $user->{$field});
                }
            }
        });

        View::composer(['layouts.app', 'dashboard'], function ($view) {
            $attendanceWidget = null;
            $support = null;

            if (Auth::check() && Auth::user()->role == 'client') {
                $support = Support::where('user_id', Auth::user()->id)->first();
            }

            if (Auth::check() && WorkAttendanceState::isEmployeePartner(Auth::user())) {
                try {
                    $state = WorkAttendanceState::resolve(Auth::user());
                    $attendanceWidget = [
                        'status' => $state['status'],
                        'worked_seconds' => $state['worked_seconds'],
                        'show' => true,
                    ];
                } catch (\Throwable $e) {
                    Log::warning('[ATTENDANCE_WIDGET] resolve failed', [
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage(),
                    ]);
                    $attendanceWidget = [
                        'status' => 'off',
                        'worked_seconds' => 0,
                        'show' => true,
                    ];
                }
            }

            $view->with('support', $support);
            $view->with('attendanceWidget', $attendanceWidget);
        });
    }
}
