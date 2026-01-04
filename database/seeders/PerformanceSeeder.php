<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Performance;
use App\Models\User;
use Carbon\Carbon;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['partner', 'design_partner', 'advertising_partner', 'admin'])->get();

        foreach ($users as $user) {
            echo "📊 Creating performance data for: {$user->name}\n";
            $performanceLevel = rand(1, 3);
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $improvementFactor = (30 - $i) / 30;
                switch ($performanceLevel) {
                    case 1:
                        $responseSpeed = rand(8, 15) - ($improvementFactor * 3);
                        $executionTime = (rand(15, 35) - ($improvementFactor * 10)) / 10;
                        $messageResponseRate = rand(90, 98) + ($improvementFactor * 2);
                        $supportTickets = rand(15, 25) + ($improvementFactor * 5);
                        $completedTasks = rand(8, 15) + ($improvementFactor * 3);
                        break;

                    case 2: // شريك جيد
                        $responseSpeed = rand(15, 25) - ($improvementFactor * 2);
                        $executionTime = (rand(30, 50) - ($improvementFactor * 8)) / 10;
                        $messageResponseRate = rand(80, 92) + ($improvementFactor * 3);
                        $supportTickets = rand(10, 18) + ($improvementFactor * 4);
                        $completedTasks = rand(5, 10) + ($improvementFactor * 2);
                        break;

                    case 3: // شريك متوسط
                        $responseSpeed = rand(20, 35) - ($improvementFactor * 1);
                        $executionTime = (rand(40, 70) - ($improvementFactor * 5)) / 10;
                        $messageResponseRate = rand(70, 85) + ($improvementFactor * 4);
                        $supportTickets = rand(5, 12) + ($improvementFactor * 3);
                        $completedTasks = rand(2, 7) + ($improvementFactor * 2);
                        break;
                }

                // تقليل الأداء في نهاية الأسبوع (الجمعة والسبت)
                if ($date->isFriday() || $date->isSaturday()) {
                    $responseSpeed += rand(5, 10);
                    $executionTime += rand(5, 15) / 10;
                    $messageResponseRate -= rand(5, 10);
                    $supportTickets -= rand(2, 5);
                    $completedTasks -= rand(1, 3);
                }

                // تحسين الأداء في منتصف الأسبوع (الثلاثاء، الأربعاء)
                if ($date->isTuesday() || $date->isWednesday()) {
                    $responseSpeed -= rand(2, 5);
                    $executionTime -= rand(2, 8) / 10;
                    $messageResponseRate += rand(2, 5);
                    $supportTickets += rand(2, 5);
                    $completedTasks += rand(1, 3);
                }

                // التأكد من أن القيم منطقية
                $responseSpeed = max(5, min(60, $responseSpeed));
                $executionTime = max(0.5, min(10, $executionTime));
                $messageResponseRate = max(50, min(100, $messageResponseRate));
                $supportTickets = max(0, $supportTickets);
                $completedTasks = max(0, $completedTasks);

                Performance::create([
                    'user_id' => $user->id,
                    'response_speed' => round($responseSpeed),
                    'execution_time' => round($executionTime, 1),
                    'message_response_rate' => round($messageResponseRate),
                    'support_tickets_closed' => round($supportTickets),
                    'completed_tasks' => round($completedTasks),
                    'performance_date' => $date,
                ]);
            }

            $levelText = $performanceLevel == 1 ? '⭐ ممتاز' : ($performanceLevel == 2 ? '👍 جيد' : '📈 متوسط');
            echo "✓ Created 30 days ({$levelText}) for {$user->name}\n\n";
        }

        echo "✅ Performance seeding completed successfully!\n";
        echo "📊 Total records created: " . Performance::count() . "\n";
    }
}
