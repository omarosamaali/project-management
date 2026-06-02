<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requests;
use App\Models\Support;
use App\Observers\RequestObserver;
use App\Observers\RequestsObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkTime;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    private function resolveWorkState($user): array
    {
        $today = Carbon::today()->toDateString();
        $records = WorkTime::where('user_id', $user->id)
            ->where('date', $today)
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        $status = 'off';
        $workedSeconds = 0;
        $currentStart = null;

        foreach ($records as $record) {
            if (in_array($record->type, ['حضور', 'دخول من الاستراحة'])) {
                $currentStart = Carbon::parse($today . ' ' . $record->start_time);
                $status = 'working';
            } elseif ($record->type === 'خروج للاستراحة') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(Carbon::parse($today . ' ' . $record->start_time));
                }
                $currentStart = null;
                $status = 'break';
            } elseif ($record->type === 'انصراف') {
                if ($currentStart) {
                    $workedSeconds += $currentStart->diffInSeconds(Carbon::parse($today . ' ' . $record->start_time));
                }
                $currentStart = null;
                $status = 'off';
            }
        }

        if ($status === 'working' && $currentStart) {
            $workedSeconds += $currentStart->diffInSeconds(now());
        }

        return [
            'status' => $status,
            'worked_seconds' => max(0, (int) $workedSeconds),
        ];
    }

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

            if (Auth::check() && Auth::user()->role == 'client') {
                $support = Support::where('user_id', Auth::user()->id)->first();
            }

            if (Auth::check() && Auth::user()->role === 'partner' && Auth::user()->is_employee) {
                $state = $this->resolveWorkState(Auth::user());
                $attendanceWidget = [
                    'status' => $state['status'],
                    'worked_seconds' => $state['worked_seconds'],
                    'show' => true,
                ];
            }

            $view->with('support', $support ?? null);
            $view->with('attendanceWidget', $attendanceWidget);
        });
    }
}
