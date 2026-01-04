<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = Auth::id();

        // أحدث أداء للمستخدم
        $latestPerformance = Performance::where('user_id', $userId)
            ->latest('performance_date')
            ->first();

        // إذا لم يكن هناك بيانات، نعرض قيم افتراضية
        if (!$latestPerformance) {
            $latestPerformance = new Performance([
                'response_speed' => 0,
                'execution_time' => 0,
                'message_response_rate' => 0,
                'support_tickets_closed' => 0,
                'completed_tasks' => 0,
            ]);
        }

        // بيانات آخر 7 أيام للرسم البياني
        $weeklyData = Performance::where('user_id', $userId)
            ->where('performance_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('performance_date')
            ->get();

        // إذا لم يكن هناك بيانات أسبوعية، نملأ بقيم افتراضية
        if ($weeklyData->isEmpty()) {
            $weeklyData = collect();
            for ($i = 6; $i >= 0; $i--) {
                $weeklyData->push(new Performance([
                    'performance_date' => Carbon::now()->subDays($i),
                    'response_speed' => 0,
                    'execution_time' => 0,
                    'message_response_rate' => 0,
                    'support_tickets_closed' => 0,
                    'completed_tasks' => 0,
                ]));
            }
        }

        // حساب المتوسطات
        $averages = Performance::where('user_id', $userId)
            ->selectRaw('
                AVG(response_speed) as avg_response_speed,
                AVG(execution_time) as avg_execution_time,
                AVG(message_response_rate) as avg_message_response,
                SUM(support_tickets_closed) as total_support,
                SUM(completed_tasks) as total_tasks
            ')
            ->first();

        return view('dashboard.performance.show', compact(
            'latestPerformance',
            'weeklyData',
            'averages'
        ));
    }
}
