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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CockpitController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        [$requestIds, $specialIds] = $this->accessibleProjectIds($user);

        $taskStats = CockpitMetrics::taskStatsFor($user, $requestIds, $specialIds);
        $attendanceStats = CockpitMetrics::attendanceStatsFor($user);

        $notifications = AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();

        $allTasks = $this->loadAllTasks($user, $requestIds, $specialIds);
        $allStages = $this->loadAllStages($user, $requestIds, $specialIds);

        return view('dashboard', compact(
            'taskStats',
            'attendanceStats',
            'notifications',
            'allTasks',
            'allStages',
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

    private function loadAllTasks($user, array $requestIds, array $specialIds): Collection
    {
        $query = Task::with(['user', 'specialRequest', 'request.system', 'stage', 'requestStage']);

        if ($user->role === 'admin') {
            return $query->orderByDesc('updated_at')->get();
        }

        return $query
            ->where(function ($q) use ($requestIds, $specialIds) {
                if (!empty($specialIds)) {
                    $q->whereIn('special_request_id', $specialIds);
                }
                if (!empty($requestIds)) {
                    $q->orWhereIn('request_id', $requestIds);
                }
                if (empty($specialIds) && empty($requestIds)) {
                    $q->whereRaw('0 = 1');
                }
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    private function loadAllStages($user, array $requestIds, array $specialIds): Collection
    {
        $stages = collect();

        $projectStagesQuery = ProjectStage::with('specialRequest');
        $requestStagesQuery = RequestStage::with('request.system');

        if ($user->role !== 'admin') {
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
}
