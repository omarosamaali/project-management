<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\PartnerSystem;
use App\Models\ProjectStage;
use App\Models\RequestStage;
use App\Models\Requests;
use App\Models\SpecialRequest;
use App\Models\SpecialRequestPartner;
use App\Models\Task;
use App\Support\CockpitMetrics;
use App\Support\CountryNames;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CockpitController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        [$requestIds, $specialIds] = $this->accessibleProjectIds($user);

        $taskStats = CockpitMetrics::taskStatsFor($user, $requestIds, $specialIds);
        $projectStats = CockpitMetrics::projectStatsFor($user);
        $courseStats = CockpitMetrics::courseStatsFor($user);
        $attendanceStats = CockpitMetrics::attendanceStatsFor($user);
        $taskStatusFilter = $request->query('task_status');

        $notifications = AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get()
            ->each(function ($notification) {
                $notification->title = CountryNames::ensureUtf8($notification->title);
                $notification->message = CountryNames::ensureUtf8($notification->message);
            });

        $allTasks = $this->sanitizeTasks($this->loadAllTasks($user, $requestIds, $specialIds, $taskStatusFilter));
        $allStages = $this->sanitizeStages($this->loadAllStages($user, $requestIds, $specialIds));

        return view('dashboard', compact(
            'taskStats',
            'projectStats',
            'courseStats',
            'attendanceStats',
            'notifications',
            'allTasks',
            'allStages',
            'taskStatusFilter',
        ));
    }

    /**
     * @return array{0: array<int>, 1: array<int>}
     */
    private function accessibleProjectIds($user): array
    {
        if ($user->role === 'admin') {
            return [Requests::pluck('id')->all(), SpecialRequest::pluck('id')->all()];
        }

        if ($user->role === 'partner') {
            $systemIds = PartnerSystem::where('partner_id', $user->id)->pluck('system_id');
            $requestIds = Requests::whereIn('system_id', $systemIds)->pluck('id')->all();
            $specialIds = SpecialRequestPartner::where('partner_id', $user->id)
                ->whereNotNull('special_request_id')
                ->pluck('special_request_id')
                ->all();

            return [$requestIds, $specialIds];
        }

        return [
            Requests::forClient($user->id)->pluck('id')->all(),
            SpecialRequest::forClient($user->id)->pluck('id')->all(),
        ];
    }

    private function loadAllTasks($user, array $requestIds, array $specialIds, ?string $statusFilter = null): Collection
    {
        $query = Task::with(['user', 'specialRequest', 'request.system', 'stage', 'requestStage']);

        if ($user->role === 'admin') {
            $query->where('user_id', $user->id);
        } else {
            $query->where(function ($q) use ($requestIds, $specialIds) {
                if (!empty($specialIds)) {
                    $q->whereIn('special_request_id', $specialIds);
                }
                if (!empty($requestIds)) {
                    $q->orWhereIn('request_id', $requestIds);
                }
                if (empty($specialIds) && empty($requestIds)) {
                    $q->whereRaw('0 = 1');
                }
            });
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            if ($statusFilter === 'all') {
                // no filter
            } elseif ($statusFilter === 'remaining') {
                $query->where('status', '!=', 'منتهية');
            } else {
                $query->where('status', $statusFilter);
            }
        }

        return $query->orderByDesc('updated_at')->get();
    }

    private function loadAllStages($user, array $requestIds, array $specialIds): Collection
    {
        $stages = collect();

        $projectStagesQuery = ProjectStage::with('specialRequest');
        $requestStagesQuery = RequestStage::with('request.system');

        if ($user->role === 'admin') {
            $projectStageIds = Task::where('user_id', $user->id)
                ->whereNotNull('project_stage_id')
                ->distinct()
                ->pluck('project_stage_id');
            $requestStageIds = Task::where('user_id', $user->id)
                ->whereNotNull('request_stage_id')
                ->distinct()
                ->pluck('request_stage_id');

            if ($projectStageIds->isEmpty()) {
                $projectStagesQuery->whereRaw('0 = 1');
            } else {
                $projectStagesQuery->whereIn('id', $projectStageIds);
            }

            if ($requestStageIds->isEmpty()) {
                $requestStagesQuery->whereRaw('0 = 1');
            } else {
                $requestStagesQuery->whereIn('id', $requestStageIds);
            }
        } else {
            if (!empty($specialIds)) {
                $projectStagesQuery->whereIn('special_request_id', $specialIds);
            } else {
                $projectStagesQuery->whereRaw('0 = 1');
            }

            if (!empty($requestIds)) {
                $requestStagesQuery->whereIn('request_id', $requestIds);
            } else {
                $requestStagesQuery->whereRaw('0 = 1');
            }
        }

        foreach ($projectStagesQuery->orderByDesc('updated_at')->get() as $stage) {
            $stages->push([
                'type' => 'special',
                'id' => $stage->id,
                'title' => $stage->title,
                'status' => $stage->status,
                'end_date' => $stage->end_date,
                'project_name' => $stage->specialRequest?->title ?? ('مشروع #' . $stage->special_request_id),
                'project_id' => $stage->special_request_id,
                'url' => route('dashboard.special-request.show', $stage->special_request_id),
            ]);
        }

        foreach ($requestStagesQuery->orderByDesc('updated_at')->get() as $stage) {
            $stages->push([
                'type' => 'request',
                'id' => $stage->id,
                'title' => $stage->title,
                'status' => $stage->status,
                'end_date' => $stage->end_date,
                'project_name' => $stage->request?->system?->name_ar ?? ('طلب #' . $stage->request_id),
                'project_id' => $stage->request_id,
                'url' => route('dashboard.requests.show', $stage->request_id),
            ]);
        }

        return $stages->sortByDesc(fn ($s) => $s['end_date'] ?? '')->values();
    }

    private function sanitizeTasks(Collection $tasks): Collection
    {
        return $tasks->each(function (Task $task) {
            $task->title = CountryNames::ensureUtf8($task->title) ?? '';
            $task->status = CountryNames::ensureUtf8($task->status) ?? '';
            if ($task->user) {
                $task->user->name = CountryNames::ensureUtf8($task->user->name) ?? '';
            }
            if ($task->specialRequest) {
                $task->specialRequest->title = CountryNames::ensureUtf8($task->specialRequest->title) ?? '';
            }
            if ($task->request?->system) {
                $task->request->system->name_ar = CountryNames::ensureUtf8($task->request->system->name_ar) ?? '';
            }
            if ($task->stage) {
                $task->stage->title = CountryNames::ensureUtf8($task->stage->title) ?? '';
            }
            if ($task->requestStage) {
                $task->requestStage->title = CountryNames::ensureUtf8($task->requestStage->title) ?? '';
            }
        });
    }

    private function sanitizeStages(Collection $stages): Collection
    {
        return $stages->map(function (array $stage) {
            $stage['title'] = CountryNames::ensureUtf8($stage['title'] ?? '') ?? '';
            $stage['project_name'] = CountryNames::ensureUtf8($stage['project_name'] ?? '') ?? '';

            return $stage;
        });
    }
}
