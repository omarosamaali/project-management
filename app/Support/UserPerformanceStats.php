<?php

namespace App\Support;

use App\Models\Performance;
use App\Models\RequestMessage;
use App\Models\SpecialRequestMessage;
use App\Models\SupportMessage;
use App\Models\Task;
use App\Models\TechnicalSupport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserPerformanceStats
{
    public function __construct(private int $userId) {}

    public static function forUser(int $userId): self
    {
        return new self($userId);
    }

    public function build(): array
    {
        $since30 = Carbon::now()->subDays(30)->startOfDay();
        $since7 = Carbon::now()->subDays(6)->startOfDay();

        $completedTasks = $this->completedTasksCount($since30);
        $supportClosed = $this->closedSupportTicketsCount($since30);
        $avgResponseMinutes = $this->averageResponseMinutes($since30);
        $avgExecutionDays = $this->averageExecutionDays($since30);
        $messageRate = $this->messageResponseRate($since30);

        $responseScore = $this->responseSpeedScore($avgResponseMinutes);
        $executionScore = $this->executionTimeScore($avgExecutionDays);
        $supportScore = min(100, $supportClosed * 5);
        $tasksScore = min(100, $completedTasks * 8);

        $totalScore = (int) round((
            $responseScore +
            $executionScore +
            $messageRate +
            $supportScore +
            $tasksScore
        ) / 5);

        return [
            'period_label'           => 'آخر 30 يوماً',
            'response_speed'         => $avgResponseMinutes,
            'execution_time'         => round($avgExecutionDays, 1),
            'message_response_rate'  => $messageRate,
            'support_tickets_closed' => $supportClosed,
            'completed_tasks'        => $completedTasks,
            'total_score'            => max(0, min(100, $totalScore)),
            'response_score'         => $responseScore,
            'execution_score'        => $executionScore,
            'weekly_chart'           => $this->weeklyChartData($since7),
        ];
    }

    private function completedTasksCount(Carbon $since): int
    {
        return Task::query()
            ->where('user_id', $this->userId)
            ->where('status', 'منتهية')
            ->where('updated_at', '>=', $since)
            ->count();
    }

    private function closedSupportTicketsCount(Carbon $since): int
    {
        [$specialIds, $requestIds] = $this->partnerProjectIds();

        if ($specialIds->isEmpty() && $requestIds->isEmpty()) {
            return 0;
        }

        return TechnicalSupport::query()
            ->where(function ($q) use ($specialIds, $requestIds) {
                if ($specialIds->isNotEmpty()) {
                    $q->whereIn('special_request_id', $specialIds);
                }
                if ($requestIds->isNotEmpty()) {
                    $q->orWhereIn('request_id', $requestIds);
                }
            })
            ->whereIn('status', ['resolved', 'closed'])
            ->where('updated_at', '>=', $since)
            ->count();
    }

    /**
     * @return array{0: Collection, 1: Collection}
     */
    private function partnerProjectIds(): array
    {
        $rows = DB::table('special_request_partner')
            ->where('partner_id', $this->userId)
            ->get(['special_request_id', 'request_id']);

        return [
            $rows->pluck('special_request_id')->filter()->unique()->values(),
            $rows->pluck('request_id')->filter()->unique()->values(),
        ];
    }

    private function averageExecutionDays(Carbon $since): float
    {
        $tasks = Task::query()
            ->where('user_id', $this->userId)
            ->where('status', 'منتهية')
            ->where('updated_at', '>=', $since)
            ->whereNotNull('start_date')
            ->get(['start_date', 'updated_at']);

        if ($tasks->isEmpty()) {
            return (float) (Performance::query()
                ->where('user_id', $this->userId)
                ->where('performance_date', '>=', $since)
                ->where('execution_time', '>', 0)
                ->avg('execution_time') ?? 0);
        }

        $days = $tasks->map(function ($task) {
            $start = Carbon::parse($task->start_date)->startOfDay();
            $end = Carbon::parse($task->updated_at)->startOfDay();

            return max(0.1, $start->diffInDays($end) + 1);
        });

        return round($days->avg(), 1);
    }

    private function averageResponseMinutes(Carbon $since): int
    {
        $gaps = $this->collectReplyGaps($since);

        if ($gaps->isEmpty()) {
            $avg = Performance::query()
                ->where('user_id', $this->userId)
                ->where('performance_date', '>=', $since)
                ->where('response_speed', '>', 0)
                ->avg('response_speed');

            return (int) round($avg ?? 0);
        }

        return (int) round($gaps->avg());
    }

    private function messageResponseRate(Carbon $since): int
    {
        [$specialIds, $requestIds] = $this->partnerProjectIds();

        $needsReply = 0;
        $replied = 0;

        if ($specialIds->isNotEmpty()) {
            foreach ($specialIds as $projectId) {
                [$need, $done] = $this->replyStatsForMessages(
                    SpecialRequestMessage::query()
                        ->where('special_request_id', $projectId)
                        ->where('created_at', '>=', $since)
                        ->orderBy('created_at')
                        ->get()
                );
                $needsReply += $need;
                $replied += $done;
            }
        }

        if ($requestIds->isNotEmpty()) {
            foreach ($requestIds as $projectId) {
                [$need, $done] = $this->replyStatsForMessages(
                    RequestMessage::query()
                        ->where('request_id', $projectId)
                        ->where('created_at', '>=', $since)
                        ->orderBy('created_at')
                        ->get()
                );
                $needsReply += $need;
                $replied += $done;
            }
        }

        if ($needsReply > 0) {
            return (int) round(($replied / $needsReply) * 100);
        }

        $avg = Performance::query()
            ->where('user_id', $this->userId)
            ->where('performance_date', '>=', $since)
            ->where('message_response_rate', '>', 0)
            ->avg('message_response_rate');

        return (int) round($avg ?? 0);
    }

    /**
     * @param  Collection<int, object>  $messages
     * @return array{0: int, 1: int}
     */
    private function replyStatsForMessages(Collection $messages): array
    {
        $needsReply = 0;
        $replied = 0;
        $pending = null;

        foreach ($messages as $message) {
            if ((int) $message->user_id === $this->userId) {
                if ($pending) {
                    $replied++;
                    $pending = null;
                }
                continue;
            }

            if (!$pending) {
                $needsReply++;
                $pending = $message;
            }
        }

        return [$needsReply, $replied];
    }

    private function collectReplyGaps(Carbon $since): Collection
    {
        $gaps = collect();
        [$specialIds, $requestIds] = $this->partnerProjectIds();

        foreach ($specialIds as $projectId) {
            $gaps = $gaps->merge($this->replyGapsFromMessages(
                SpecialRequestMessage::query()
                    ->where('special_request_id', $projectId)
                    ->where('created_at', '>=', $since)
                    ->orderBy('created_at')
                    ->get()
            ));
        }

        foreach ($requestIds as $projectId) {
            $gaps = $gaps->merge($this->replyGapsFromMessages(
                RequestMessage::query()
                    ->where('request_id', $projectId)
                    ->where('created_at', '>=', $since)
                    ->orderBy('created_at')
                    ->get()
            ));
        }

        $supportIds = DB::table('supports')
            ->where('user_id', $this->userId)
            ->pluck('id');

        if ($supportIds->isNotEmpty()) {
            $gaps = $gaps->merge($this->replyGapsFromMessages(
                SupportMessage::query()
                    ->whereIn('support_id', $supportIds)
                    ->where('created_at', '>=', $since)
                    ->orderBy('created_at')
                    ->get()
            ));
        }

        return $gaps->filter(fn ($m) => $m > 0 && $m <= 24 * 60);
    }

    private function replyGapsFromMessages(Collection $messages): Collection
    {
        $gaps = collect();
        $waiting = null;

        foreach ($messages as $message) {
            if ((int) $message->user_id !== $this->userId) {
                $waiting = $message;
                continue;
            }

            if ($waiting) {
                $gaps->push($waiting->created_at->diffInMinutes($message->created_at));
                $waiting = null;
            }
        }

        return $gaps;
    }

    private function responseSpeedScore(int $minutes): int
    {
        if ($minutes <= 0) {
            return 0;
        }
        if ($minutes <= 20) {
            return 100;
        }
        if ($minutes <= 60) {
            return 80;
        }
        if ($minutes <= 120) {
            return 60;
        }
        if ($minutes <= 240) {
            return 40;
        }

        return 20;
    }

    private function executionTimeScore(float $days): int
    {
        if ($days <= 0) {
            return 0;
        }
        if ($days <= 2) {
            return 100;
        }
        if ($days <= 5) {
            return 80;
        }
        if ($days <= 10) {
            return 60;
        }
        if ($days <= 15) {
            return 40;
        }

        return 20;
    }

    private function weeklyChartData(Carbon $since7): array
    {
        $labels = [];
        $responseScores = [];
        $executionScores = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $since7->copy()->addDays($i);
            $dayEnd = $day->copy()->endOfDay();
            $labels[] = $day->locale('ar')->translatedFormat('D d M');

            $dayGaps = $this->collectReplyGaps($day->copy()->startOfDay());
            $avgMinutes = $dayGaps->isNotEmpty() ? (int) round($dayGaps->avg()) : 0;
            $responseScores[] = $this->responseSpeedScore($avgMinutes);

            $dayTasks = Task::query()
                ->where('user_id', $this->userId)
                ->where('status', 'منتهية')
                ->whereBetween('updated_at', [$day, $dayEnd])
                ->whereNotNull('start_date')
                ->get(['start_date', 'updated_at']);

            if ($dayTasks->isNotEmpty()) {
                $avgDays = $dayTasks->map(function ($task) {
                    $start = Carbon::parse($task->start_date)->startOfDay();
                    $end = Carbon::parse($task->updated_at)->startOfDay();

                    return max(0.1, $start->diffInDays($end) + 1);
                })->avg();
                $executionScores[] = $this->executionTimeScore((float) $avgDays);
            } else {
                $executionScores[] = 0;
            }
        }

        return [
            'labels'            => $labels,
            'response_scores'   => $responseScores,
            'execution_scores'  => $executionScores,
        ];
    }
}
