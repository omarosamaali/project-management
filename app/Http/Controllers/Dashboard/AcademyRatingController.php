<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CourseRating;
use Illuminate\Http\Request;

class AcademyRatingController extends Controller
{
    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'غير مصرح لك بعرض تقييمات الأكاديمية.');
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $search = trim((string) $request->input('search', ''));

        $ratings = CourseRating::query()
            ->with(['user', 'course'])
            ->whereNotNull('completed_at')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('name_ar', 'like', '%' . $search . '%')
                            ->orWhere('name_en', 'like', '%' . $search . '%');
                    });
                });
            })
            ->latest('completed_at')
            ->paginate(15)
            ->withQueryString();

        $questions = config('course_rating.questions', []);

        return view('dashboard.academy.ratings.index', compact('ratings', 'questions', 'search'));
    }

    public function show(CourseRating $rating)
    {
        $this->authorizeAdmin();
        abort_unless($rating->isCompleted(), 404);

        $rating->load(['user', 'course', 'payment']);
        $questions = config('course_rating.questions', []);

        return view('dashboard.academy.ratings.show', compact('rating', 'questions'));
    }
}
