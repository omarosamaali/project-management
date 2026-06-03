<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAdjustment;
use App\Models\WorkTime;
use App\Support\UserPerformanceStats;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $workStats = WorkTime::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalLateMinutes = 0;
        foreach ($workStats->where('type', 'حضور') as $record) {
            $startTime = Carbon::parse($record->start_time);
            if ($startTime->hour >= 9 && $startTime->minute > 0) {
                $totalLateMinutes += $startTime->diffInMinutes(Carbon::parse('09:00:00'));
            }
        }

        $financials = EmployeeAdjustment::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('
                SUM(CASE WHEN type = "bonus" THEN amount ELSE 0 END) as total_bonuses,
                SUM(CASE WHEN type = "deduction" THEN amount ELSE 0 END) as total_deductions
            ')
            ->first();

        $stats = UserPerformanceStats::forUser($userId)->build();

        return view('dashboard.performance.show', compact(
            'stats',
            'totalLateMinutes',
            'financials',
            'workStats'
        ));
    }
}
