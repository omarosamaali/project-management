<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AcademyProfitController extends Controller
{
    private const SUCCESSFUL_PAYMENT_STATUSES = ['completed', 'success', 'paid', 'active'];

    public function index()
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);

        $percentage = Setting::academyTrainerProfitPercentage();
        $trainers = $this->loadTrainerProfitRows();

        $summary = [
            'trainers_count' => $trainers->count(),
            'courses_count' => (int) $trainers->sum('courses_count'),
            'subscriptions_count' => (int) $trainers->sum('subscriptions_count'),
            'gross_revenue' => (float) $trainers->sum('gross_revenue'),
            'trainer_profit' => (float) $trainers->sum('trainer_profit'),
        ];

        return view('dashboard.academy.profits.index', compact('trainers', 'percentage', 'summary'));
    }

    public function myProfits(Request $request)
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isTrainer(), 403);
        /** @var User $trainerUser */
        $trainerUser = $user;

        $percentage = Setting::academyTrainerProfitPercentage();
        $trainer = $this->buildTrainerProfitRow($trainerUser->load([
            'trainedCourses' => function ($query) {
                $query->withCount([
                    'payments as successful_payments_count' => function ($payments) {
                        $payments->whereIn('status', self::SUCCESSFUL_PAYMENT_STATUSES);
                    },
                ])->withSum([
                    'payments as successful_payments_sum_original_price' => function ($payments) {
                        $payments->whereIn('status', self::SUCCESSFUL_PAYMENT_STATUSES);
                    },
                ], 'original_price');
            },
        ]), $percentage);

        $allCourses = $trainer['courses'];
        $summary = [
            'courses_count' => $trainer['courses_count'],
            'subscriptions_count' => $trainer['subscriptions_count'],
            'gross_revenue' => $trainer['gross_revenue'],
            'trainer_profit' => $trainer['trainer_profit'],
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

        return view('dashboard.academy.profits.my-profits', compact('courses', 'percentage', 'summary'));
    }

    protected function loadTrainerProfitRows()
    {
        $percentage = Setting::academyTrainerProfitPercentage();

        return User::query()
            ->where('role', 'trainer')
            ->with([
                'trainedCourses' => function ($query) {
                    $query->withCount([
                        'payments as successful_payments_count' => function ($payments) {
                            $payments->whereIn('status', self::SUCCESSFUL_PAYMENT_STATUSES);
                        },
                    ])->withSum([
                        'payments as successful_payments_sum_original_price' => function ($payments) {
                            $payments->whereIn('status', self::SUCCESSFUL_PAYMENT_STATUSES);
                        },
                    ], 'original_price');
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (User $trainer) => $this->buildTrainerProfitRow($trainer, $percentage))
            ->filter(fn (array $trainer) => $trainer['courses_count'] > 0)
            ->values();
    }

    protected function buildTrainerProfitRow(User $trainer, float $percentage): array
    {
        $courses = $trainer->trainedCourses
            ->map(function ($course) use ($percentage) {
                $subscriptionsCount = (int) ($course->successful_payments_count ?? 0);
                $grossRevenue = (float) ($course->successful_payments_sum_original_price ?? 0);
                $trainerProfit = round($grossRevenue * ($percentage / 100), 2);

                return [
                    'id' => $course->id,
                    'name_ar' => $course->name_ar,
                    'name_en' => $course->name_en,
                    'subscriptions_count' => $subscriptionsCount,
                    'gross_revenue' => $grossRevenue,
                    'trainer_profit' => $trainerProfit,
                ];
            })
            ->sortByDesc('gross_revenue')
            ->values();

        $grossRevenue = (float) $courses->sum('gross_revenue');

        return [
            'id' => $trainer->id,
            'name' => $trainer->name,
            'courses_count' => $courses->count(),
            'subscriptions_count' => (int) $courses->sum('subscriptions_count'),
            'gross_revenue' => $grossRevenue,
            'trainer_profit' => round($grossRevenue * ($percentage / 100), 2),
            'courses' => $courses,
        ];
    }
}
