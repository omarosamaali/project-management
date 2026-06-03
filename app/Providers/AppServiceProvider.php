<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requests;
use App\Models\Support;
use App\Observers\RequestObserver;
use App\Observers\RequestsObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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

        View::composer(['layouts.app', 'dashboard'], function ($view) {
            $attendanceWidget = null;
            $support = null;

            if (Auth::check() && Auth::user()->role == 'client') {
                $support = Support::where('user_id', Auth::user()->id)->first();
            }

            if (Auth::check() && WorkAttendanceState::isEmployeePartner(Auth::user())) {
                $state = WorkAttendanceState::resolve(Auth::user());
                $attendanceWidget = [
                    'status' => $state['status'],
                    'worked_seconds' => $state['worked_seconds'],
                    'show' => true,
                ];
            }

            $view->with('support', $support);
            $view->with('attendanceWidget', $attendanceWidget);
        });
    }
}
