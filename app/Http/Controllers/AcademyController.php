<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseRating;
use App\Models\CourseWishlist;
use App\Models\Payment;
use App\Models\User;
use App\Support\AuthUi;
use App\Support\TrainerJourney;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AcademyController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $paidStatuses = ['completed', 'success', 'paid', 'active'];

        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $latestCourses = Course::query()
            ->publiclyListable()
            ->with(['category', 'trainer', 'units.items'])
            ->withCount([
                'payments as payments_count' => fn ($query) => $query->whereIn('status', $paidStatuses),
            ])
            ->latest('id')
            ->limit(7)
            ->get();

        $this->attachRatingStats($latestCourses);
        $this->attachOwnership($latestCourses);
        $this->attachWishlist($latestCourses);

        $reviews = CourseRating::query()
            ->whereNotNull('completed_at')
            ->with(['user', 'course'])
            ->latest('completed_at')
            ->limit(40)
            ->get()
            ->filter(function (CourseRating $r) {
                return $r->is_featured
                    || $r->feedbackText()
                    || $r->averageScaleScore() !== null;
            })
            ->take(24)
            ->values();

        $trainers = $this->publicTrainersQuery()
            ->limit(20)
            ->get();
        $this->enrichPublicTrainers($trainers);

        $myCoursesUrl = Auth::check() && Auth::user()->canLearnCourses()
            ? route('dashboard.my_courses.index')
            : route('login');

        return view('academy.index', compact(
            'categories',
            'latestCourses',
            'reviews',
            'trainers',
            'locale',
            'myCoursesUrl'
        ));
    }

    public function trainers(Request $request)
    {
        $locale = app()->getLocale();
        $q = trim((string) $request->query('q', ''));

        $trainersQuery = $this->publicTrainersQuery();

        if ($q !== '') {
            $trainersQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%');
            });
        }

        $trainers = $trainersQuery->paginate(12)->withQueryString();
        $this->enrichPublicTrainers($trainers->getCollection());

        return view('academy.trainers.index', compact('trainers', 'locale', 'q'));
    }

    /**
     * Public become-trainer landing + registration (new trainer accounts only).
     */
    public function becomeTrainer(Request $request)
    {
        AuthUi::resolve(AuthUi::ACADEMY);

        $user = Auth::user();
        if ($user && $user->isTrainer() && $user->status === 'active') {
            return redirect()->route('dashboard');
        }

        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $locale = app()->getLocale();
        $formBlocked = $user && ! $user->isTrainer();
        $journey = TrainerJourney::stateFor(
            ($user && $user->isTrainer()) ? $user : null
        );
        $journeyStep = $journey['step'];
        $completedSteps = $journey['completed'];
        $allDone = $journey['all_done'];
        $journeyHint = TrainerJourney::hintFor($journeyStep, $allDone);

        if ($user && $user->isTrainer() && $user->isPendingApproval()) {
            $formBlocked = true;
        }

        return view('academy.become-trainer', compact(
            'categories',
            'locale',
            'formBlocked',
            'journeyStep',
            'completedSteps',
            'allDone',
            'journeyHint',
            'user'
        ));
    }

    public function trainer(User $trainer)
    {
        abort_unless(
            $trainer->isTrainer()
                && $trainer->status === 'active'
                && ! $trainer->isBlocked(),
            404
        );

        $locale = app()->getLocale();
        $paidStatuses = ['completed', 'success', 'paid', 'active'];

        $trainer->load([
            'courseCategory',
            'trainedCourses' => fn ($query) => $query
                ->where('status', 'active')
                ->with(['category', 'trainer', 'units.items'])
                ->withCount([
                    'payments as payments_count' => fn ($q) => $q->whereIn('status', $paidStatuses),
                ])
                ->latest(),
        ]);

        $this->enrichPublicTrainers(collect([$trainer]));

        $courses = $trainer->trainedCourses;
        $this->attachRatingStats($courses);
        $this->attachOwnership($courses);
        $this->attachWishlist($courses);

        return view('academy.trainers.show', compact('trainer', 'courses', 'locale'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\User>
     */
    protected function publicTrainersQuery()
    {
        return User::query()
            ->where('role', 'trainer')
            ->where('status', 'active')
            ->notBlocked()
            ->with([
                'courseCategory',
                'trainedCourses' => fn ($query) => $query->where('status', 'active')->with('category'),
            ])
            ->withCount([
                'trainedCourses as active_courses_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->orderBy('name');
    }

    /**
     * @param  Collection<int, User>  $trainers
     */
    protected function enrichPublicTrainers(Collection $trainers): void
    {
        if ($trainers->isEmpty()) {
            return;
        }

        $allCourseIds = $trainers->flatMap(
            fn (User $trainer) => $trainer->trainedCourses->pluck('id')
        )->unique()->filter()->values();

        $ratingsByCourse = $allCourseIds->isEmpty()
            ? collect()
            : CourseRating::query()
                ->whereIn('course_id', $allCourseIds)
                ->whereNotNull('completed_at')
                ->get()
                ->groupBy('course_id');

        $learnerCounts = $allCourseIds->isEmpty()
            ? collect()
            : Payment::query()
                ->whereIn('course_id', $allCourseIds)
                ->whereIn('status', ['completed', 'success', 'paid', 'active'])
                ->selectRaw('course_id, COUNT(DISTINCT user_id) as learners')
                ->groupBy('course_id')
                ->pluck('learners', 'course_id');

        foreach ($trainers as $trainer) {
            $courseIds = $trainer->trainedCourses->pluck('id');
            $trainerScores = $courseIds
                ->flatMap(fn ($id) => $ratingsByCourse->get($id, collect()))
                ->map(fn (CourseRating $r) => $r->trainerScore())
                ->filter(fn ($s) => $s !== null);

            $categoryTitle = $trainer->courseCategory
                ? $trainer->courseCategory->title(app()->getLocale())
                : optional(
                    $trainer->trainedCourses
                        ->pluck('category')
                        ->filter()
                        ->groupBy('id')
                        ->sortByDesc(fn ($group) => $group->count())
                        ->map(fn ($group) => $group->first())
                        ->first()
                )->title(app()->getLocale());

            $trainer->academy_rating = $trainerScores->isNotEmpty()
                ? round($trainerScores->avg(), 1)
                : 0;
            $trainer->academy_category_label = $categoryTitle ?: null;
            $trainer->academy_courses_count = (int) ($trainer->active_courses_count
                ?? $trainer->trainedCourses->count());
            $trainer->academy_learners_count = (int) $courseIds
                ->sum(fn ($id) => (int) ($learnerCounts[$id] ?? 0));
        }
    }

    public function courses(Request $request)
    {
        $locale = app()->getLocale();
        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');
        $price = (string) $request->query('price', '');
        $categoryId = (int) $request->query('category', 0);
        $paidStatuses = ['completed', 'success', 'paid', 'active'];

        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $activeCategory = $categoryId > 0
            ? $categories->firstWhere('id', $categoryId)
            : null;

        $coursesQuery = Course::query()
            ->publiclyListable()
            ->with(['category', 'trainer', 'units.items'])
            ->withCount([
                'payments as payments_count' => fn ($query) => $query->whereIn('status', $paidStatuses),
            ]);

        if ($activeCategory) {
            $coursesQuery->where('course_category_id', $activeCategory->id);
        }

        $this->applyCourseListingFilters($coursesQuery, $q, $type, $price);

        $courses = $coursesQuery->latest()->paginate(12)->withQueryString();

        $this->attachRatingStats($courses->getCollection());
        $this->attachOwnership($courses->getCollection());
        $this->attachWishlist($courses->getCollection());

        $myCoursesUrl = Auth::check() && Auth::user()->canLearnCourses()
            ? route('dashboard.my_courses.index')
            : route('login');

        return view('academy.courses', compact(
            'categories',
            'activeCategory',
            'courses',
            'q',
            'type',
            'price',
            'locale',
            'myCoursesUrl'
        ));
    }

    public function category(Request $request, CourseCategory $category)
    {
        abort_unless($category->is_active, 404);

        $locale = app()->getLocale();
        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');
        $price = (string) $request->query('price', '');
        $paidStatuses = ['completed', 'success', 'paid', 'active'];

        $coursesQuery = Course::query()
            ->publiclyListable()
            ->where('course_category_id', $category->id)
            ->with(['category', 'trainer', 'units.items'])
            ->withCount([
                'payments as payments_count' => fn ($query) => $query->whereIn('status', $paidStatuses),
            ]);

        $this->applyCourseListingFilters($coursesQuery, $q, $type, $price);

        $courses = $coursesQuery->latest()->paginate(12)->withQueryString();

        $this->attachRatingStats($courses->getCollection());
        $this->attachOwnership($courses->getCollection());
        $this->attachWishlist($courses->getCollection());

        $myCoursesUrl = Auth::check() && Auth::user()->canLearnCourses()
            ? route('dashboard.my_courses.index')
            : route('login');

        return view('academy.category', compact(
            'category',
            'courses',
            'q',
            'type',
            'price',
            'locale',
            'myCoursesUrl'
        ));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Course>  $coursesQuery
     */
    protected function applyCourseListingFilters($coursesQuery, string $q, string $type, string $price): void
    {
        if ($q !== '') {
            $coursesQuery->where(function ($query) use ($q) {
                $query->where('name_ar', 'like', '%'.$q.'%')
                    ->orWhere('name_en', 'like', '%'.$q.'%')
                    ->orWhere('description_ar', 'like', '%'.$q.'%')
                    ->orWhere('description_en', 'like', '%'.$q.'%');
            });
        }

        if (in_array($type, ['online', 'recorded', 'on_site'], true)) {
            $coursesQuery->where('location_type', $type);
        }

        if ($price === 'free') {
            $coursesQuery->where('price', '<=', 0);
        } elseif ($price === 'paid') {
            $coursesQuery->where('price', '>', 0);
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    protected function attachRatingStats(Collection $courses): void
    {
        $ids = $courses->pluck('id')->unique()->filter()->values();
        if ($ids->isEmpty()) {
            return;
        }

        $grouped = CourseRating::query()
            ->whereIn('course_id', $ids)
            ->whereNotNull('completed_at')
            ->get(['course_id', 'answers'])
            ->groupBy('course_id');

        foreach ($courses as $course) {
            $ratings = $grouped->get($course->id, collect());
            $scores = $ratings
                ->map(fn (CourseRating $r) => $r->averageScaleScore())
                ->filter(fn ($s) => $s !== null);

            $course->academy_avg_rating = $scores->isNotEmpty() ? round($scores->avg(), 1) : null;
            $course->academy_ratings_count = $ratings->count();
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    protected function attachOwnership(Collection $courses): void
    {
        $userId = Auth::id();
        foreach ($courses as $course) {
            $course->academy_owned = false;
            $course->academy_payment = null;
            $course->academy_path_percent = 0;
        }

        if (! $userId || $courses->isEmpty()) {
            return;
        }

        $ids = $courses->pluck('id')->unique()->filter()->values();
        $payments = Payment::query()
            ->where('user_id', $userId)
            ->whereIn('course_id', $ids)
            ->whereIn('status', ['completed', 'success', 'paid', 'active'])
            ->latest()
            ->get()
            ->groupBy('course_id');

        foreach ($courses as $course) {
            $payment = $payments->get($course->id)?->first();
            if (! $payment) {
                continue;
            }

            $course->academy_owned = true;
            $course->academy_payment = $payment;
            if ($course->isRecorded()) {
                $course->academy_path_percent = (int) ($course->pathCompletionForUser($userId)['percent'] ?? 0);
            }
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    protected function attachWishlist(Collection $courses): void
    {
        foreach ($courses as $course) {
            $course->academy_wishlisted = false;
        }

        $userId = Auth::id();
        if (! $userId || $courses->isEmpty()) {
            return;
        }

        $ids = $courses->pluck('id')->unique()->filter()->values();
        $wishlisted = CourseWishlist::query()
            ->where('user_id', $userId)
            ->whereIn('course_id', $ids)
            ->pluck('course_id')
            ->flip();

        foreach ($courses as $course) {
            $course->academy_wishlisted = $wishlisted->has($course->id);
        }
    }
}
