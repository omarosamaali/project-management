<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\CourseWishlist;
use App\Models\Payment;
use App\Support\AuthUi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseWishlistController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $locale = app()->getLocale();
        $paidStatuses = ['completed', 'success', 'paid', 'active'];

        $courses = $user->wishlistCourses()
            ->where('status', 'active')
            ->with(['category', 'trainer', 'units.items'])
            ->withCount([
                'payments as payments_count' => fn ($query) => $query->whereIn('status', $paidStatuses),
            ])
            ->latest('course_wishlists.created_at')
            ->paginate(12)
            ->withQueryString();

        $this->attachRatingStats($courses->getCollection());
        $this->attachOwnership($courses->getCollection());
        foreach ($courses as $course) {
            $course->academy_wishlisted = true;
        }

        return view('academy.wishlist', compact('courses', 'locale'));
    }

    public function toggle(Request $request, Course $course): JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'login_required' => true,
                    'login_url' => AuthUi::loginUrl(['redirect' => route('academy.wishlist.index')]),
                ], 401);
            }

            return redirect()->guest(AuthUi::loginUrl());
        }

        abort_unless($course->status === 'active', 404);

        $userId = (int) Auth::id();
        $existing = CourseWishlist::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            CourseWishlist::query()->create([
                'user_id' => $userId,
                'course_id' => $course->id,
            ]);
            $wishlisted = true;
        }

        $count = CourseWishlist::query()->where('user_id', $userId)->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'wishlisted' => $wishlisted,
                'count' => $count,
                'message' => $wishlisted
                    ? __('messages.academy_wishlist_added')
                    : __('messages.academy_wishlist_removed'),
            ]);
        }

        return back()->with(
            'success',
            $wishlisted
                ? __('messages.academy_wishlist_added')
                : __('messages.academy_wishlist_removed')
        );
    }

    protected function attachRatingStats($courses): void
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

    protected function attachOwnership($courses): void
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
}
