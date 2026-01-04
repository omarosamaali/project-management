<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requests;
use App\Models\Support;
use App\Observers\RequestObserver;
use App\Observers\RequestsObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
            if (Auth::check() && Auth::user()->role == 'client') {
                $support = Support::where('user_id', Auth::user()->id)->first();
            }
            $view->with('support', $support ?? null);
        });
    }
}
