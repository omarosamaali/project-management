<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademyProfitController extends Controller
{
    private const SUCCESSFUL_PAYMENT_STATUSES = ['completed', 'success', 'paid', 'active'];

    public function index()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);

        $percentages = Setting::academyTrainerProfitPercentages();
        $trainers = $this->loadTrainerProfitRows();

        $summary = [
            'trainers_count' => $trainers->count(),
            'courses_count' => (int) $trainers->sum('courses_count'),
            'subscriptions_count' => (int) $trainers->sum('subscriptions_count'),
            'gross_revenue' => (float) $trainers->sum('gross_revenue'),
            'trainer_profit' => (float) $trainers->sum('trainer_profit'),
            'platform_profit' => (float) $trainers->sum('platform_profit'),
        ];

        return view('dashboard.academy.profits.index', [
            'trainers' => $trainers,
            'percentage' => $percentages['online'] ?? 60,
            'percentages' => $percentages,
            'summary' => $summary,
        ]);
    }

    public function myProfits(Request $request)
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isTrainer(), 403);
        /** @var User $trainerUser */
        $trainerUser = $user;

        $percentages = Setting::academyTrainerProfitPercentages();
        $trainer = $this->buildTrainerProfitRow($trainerUser);

        $allCourses = $trainer['courses'];
        $summary = [
            'courses_count' => $trainer['courses_count'],
            'subscriptions_count' => $trainer['subscriptions_count'],
            'gross_revenue' => $trainer['gross_revenue'],
            'trainer_profit' => $trainer['trainer_profit'],
            'platform_profit' => $trainer['platform_profit'],
        ];

        $perPage = 9;
        $page = max(1, (int) $request->query('page', 1));
        $courses = new LengthAwarePaginator(
            $allCourses->forPage($page, $perPage)->values(),
            $allCourses->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('dashboard.academy.profits.my-profits', [
            'courses' => $courses,
            'percentage' => $percentages['online'] ?? 60,
            'percentages' => $percentages,
            'summary' => $summary,
        ]);
    }

    protected function loadTrainerProfitRows()
    {
        return User::query()
            ->where('role', 'trainer')
            ->orderBy('name')
            ->get()
            ->map(fn (User $trainer) => $this->buildTrainerProfitRow($trainer))
            ->filter(fn (array $trainer) => $trainer['courses_count'] > 0)
            ->values();
    }

    protected function buildTrainerProfitRow(User $trainer): array
    {
        $rows = Payment::query()
            ->select([
                'payments.course_id',
                DB::raw('COUNT(*) as subscriptions_count'),
                DB::raw('COALESCE(SUM(payments.original_price), 0) as gross_revenue'),
                DB::raw('COALESCE(SUM(payments.trainer_profit_amount), 0) as trainer_profit'),
                DB::raw('COALESCE(SUM(payments.platform_profit_amount), 0) as platform_profit'),
            ])
            ->join('courses', 'courses.id', '=', 'payments.course_id')
            ->where('courses.trainer_id', $trainer->id)
            ->whereIn('payments.status', self::SUCCESSFUL_PAYMENT_STATUSES)
            ->groupBy('payments.course_id')
            ->with(['course' => fn ($q) => $q->select('id', 'name_ar', 'name_en', 'location_type')])
            ->get();

        $courses = $rows
            ->map(function ($row) {
                $course = $row->course;

                return [
                    'id' => (int) $row->course_id,
                    'name_ar' => $course?->name_ar,
                    'name_en' => $course?->name_en,
                    'location_type' => $course?->location_type,
                    'subscriptions_count' => (int) $row->subscriptions_count,
                    'gross_revenue' => round((float) $row->gross_revenue, 2),
                    'trainer_profit' => round((float) $row->trainer_profit, 2),
                    'platform_profit' => round((float) $row->platform_profit, 2),
                ];
            })
            ->sortByDesc('gross_revenue')
            ->values();

        return [
            'id' => $trainer->id,
            'name' => $trainer->name,
            'courses_count' => $courses->count(),
            'subscriptions_count' => (int) $courses->sum('subscriptions_count'),
            'gross_revenue' => round((float) $courses->sum('gross_revenue'), 2),
            'trainer_profit' => round((float) $courses->sum('trainer_profit'), 2),
            'platform_profit' => round((float) $courses->sum('platform_profit'), 2),
            'courses' => $courses,
        ];
    }
}
