<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TrainerOffDay;
use App\Support\CourseScheduleCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerOffDayController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && $user->isTrainer(), 403);

        $days = TrainerOffDay::query()
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(30);

        return view('dashboard.academy.off-days.index', compact('days'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTrainer(), 403);

        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        TrainerOffDay::query()->updateOrCreate(
            ['user_id' => $user->id, 'date' => $data['date']],
            ['note' => $data['note'] ?? null]
        );

        $this->recomputeCourses($user->id);

        return back()->with('success', __('messages.trainer_off_days_saved') !== 'messages.trainer_off_days_saved'
            ? __('messages.trainer_off_days_saved')
            : 'تم حفظ يوم الإجازة.');
    }

    public function destroy(TrainerOffDay $offDay)
    {
        $user = Auth::user();
        abort_unless($user && $user->isTrainer() && (int) $offDay->user_id === (int) $user->id, 403);

        $offDay->delete();
        $this->recomputeCourses($user->id);

        return back()->with('success', __('messages.trainer_off_days_deleted'));
    }

    protected function recomputeCourses(int $trainerId): void
    {
        $offDates = TrainerOffDay::query()
            ->where('user_id', $trainerId)
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();

        Course::query()
            ->where('trainer_id', $trainerId)
            ->where('location_type', '!=', 'recorded')
            ->where('status', 'active')
            ->whereNotNull('start_date')
            ->where('count_days', '>', 0)
            ->get()
            ->each(function (Course $course) use ($offDates) {
                // Only apply off dates the course explicitly selected (intersect with current trainer offs).
                $selected = array_values(array_intersect(
                    array_map(
                        static fn ($d) => Carbon::parse($d)->toDateString(),
                        (array) ($course->off_dates ?? [])
                    ),
                    $offDates
                ));
                $course->off_dates = $selected;
                $result = CourseScheduleCalculator::forCourse($course, $selected);
                $course->end_date = $result['end_date'];
                $course->save();
            });
    }
}
