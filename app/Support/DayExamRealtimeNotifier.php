<?php

namespace App\Support;

use App\Events\DayExamStartedForUser;
use App\Http\Controllers\CourseExamController;
use App\Models\Course;
use App\Models\CourseDayExam;
use App\Models\Payment;
use App\Models\User;
use Throwable;

class DayExamRealtimeNotifier
{
    /**
     * Push exam-started redirects to all attended learners on the course.
     */
    public static function notifyCourseAttendees(Course $course, CourseDayExam $dayExam): void
    {
        if (! $dayExam->isRunning()) {
            return;
        }

        $redirect = route('dashboard.courses.exam.take', [$course, $dayExam]);
        $courseName = (string) ($course->name_ar ?: $course->name_en ?: '');

        $userIds = Payment::query()
            ->where('course_id', $course->id)
            ->where('is_attended', true)
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->pluck('user_id')
            ->unique()
            ->filter();

        foreach ($userIds as $userId) {
            try {
                broadcast(new DayExamStartedForUser((int) $userId, $redirect, $courseName));
            } catch (Throwable) {
                // Realtime is best-effort; HTTP fallback still exists on page load.
            }
        }
    }

    /**
     * If this learner is attended and has a running day exam, notify them now.
     */
    public static function notifyUserIfExamPending(User|int $user): void
    {
        $userId = $user instanceof User ? (int) $user->id : (int) $user;
        $pending = CourseExamController::findPendingExamPayment($userId);
        if (! $pending) {
            return;
        }

        /** @var CourseDayExam $dayExam */
        $dayExam = $pending['dayExam'];
        $course = $pending['payment']->course;
        if (! $course) {
            return;
        }

        try {
            broadcast(new DayExamStartedForUser(
                $userId,
                route('dashboard.courses.exam.take', [$course, $dayExam]),
                (string) ($course->name_ar ?: $course->name_en ?: ''),
            ));
        } catch (Throwable) {
            // best-effort
        }
    }
}
