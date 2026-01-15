<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkTime;
use App\Models\EmployeeAdjustment;
use Carbon\Carbon;

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
        ')->first();

        $latestPerformance = Performance::where('user_id', $userId)->latest('performance_date')->first()
            ?? new Performance(['total_score' => 0]);

        $weeklyData = Performance::where('user_id', $userId)
            ->where('performance_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('performance_date')->get();

        $averages = Performance::where('user_id', $userId)
            ->selectRaw('SUM(support_tickets_closed) as total_support, SUM(completed_tasks) as total_tasks')
            ->first();

        return view('dashboard.performance.show', compact(
            'latestPerformance',
            'weeklyData',
            'averages',
            'totalLateMinutes',
            'financials',
            'workStats'
        ));
    }
}
