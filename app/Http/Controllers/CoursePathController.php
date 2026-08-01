<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePathItem;
use App\Models\CoursePathProgress;
use App\Models\Payment;
use App\Support\ShufflesExamQuestions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoursePathController extends Controller
{
    use ShufflesExamQuestions;

    protected function enrolledPayment(Course $course): Payment
    {
        $payment = Payment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->first();

        abort_unless($payment, 403);
        abort_unless($course->isRecorded(), 404);

        return $payment;
    }

    /**
     * Trainee educational path player.
     */
    public function show(Payment $payment)
    {
        abort_unless((int) $payment->user_id === (int) Auth::id(), 403);
        $course = $payment->course;
        abort_unless($course && $course->isRecorded(), 404);

        $course->load(['units.items.examQuestions.answers']);
        $progress = $course->pathProgressForUser(Auth::id());
        $items = $course->orderedPathItems();
        $user = Auth::user();

        $requestedItemId = (int) request('item', 0);
        $hadExplicitItem = $requestedItemId > 0;
        $current = $hadExplicitItem ? $items->firstWhere('id', $requestedItemId) : null;

        // Reopen course: resume the last step the trainee stopped on
        if (!$current) {
            $lastId = (int) ($payment->last_path_item_id ?? 0);
            if ($lastId > 0) {
                $candidate = $items->firstWhere('id', $lastId);
                if ($candidate && $course->canUserAccessPathItem($user, $candidate)) {
                    $current = $candidate;
                }
            }
        }

        if (!$current) {
            // First incomplete, or first item
            $current = $items->first(function ($item) use ($progress) {
                $p = $progress->get($item->id);

                return !$p || !$p->is_completed;
            }) ?? $items->first();
        }

        if ($current && !$course->canUserAccessPathItem($user, $current)) {
            $current = $items->first(function ($item) use ($course, $user) {
                return $course->canUserAccessPathItem($user, $item);
            }) ?? $items->first();
        }

        // Persist last active step whenever the trainee lands on a path item
        if ($current && (int) ($payment->last_path_item_id ?? 0) !== (int) $current->id) {
            $payment->forceFill(['last_path_item_id' => $current->id])->save();
        }

        // Keep URL in sync so refresh stays on the resumed step
        if (!$hadExplicitItem && $current) {
            return redirect()->route('dashboard.my_courses.path', [
                'payment' => $payment->id,
                'item' => $current->id,
            ]);
        }

        if ($current && $current->isExam()) {
            $this->applyExamShuffleForPathItem($course, $current, $progress);
        }

        $this->markPathCompletedIfDone($course);

        return view('dashboard.my_courses.path', compact('payment', 'course', 'progress', 'items', 'current'));
    }

    /**
     * Persist and apply the same question/answer shuffle used by live day exams.
     */
    protected function applyExamShuffleForPathItem(Course $course, CoursePathItem $item, $progress): void
    {
        $questions = $item->examQuestions;
        if ($questions->isEmpty()) {
            return;
        }

        $progressRow = $progress->get($item->id);

        if (!$progressRow) {
            $progressRow = CoursePathProgress::create([
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'path_item_id' => $item->id,
                'shuffle_map' => $this->buildShuffleMap($questions),
            ]);
            $progress->put($item->id, $progressRow);
        } elseif (empty($progressRow->shuffle_map)) {
            $progressRow->update(['shuffle_map' => $this->buildShuffleMap($questions)]);
        }

        $item->setRelation(
            'examQuestions',
            $this->applyShuffleMap($questions, $progressRow->shuffle_map)
        );
    }


    /**
     * Heartbeat: update watched seconds; complete lesson when near full duration.
     */
    public function progress(Request $request, Course $course, CoursePathItem $item)
    {
        $this->enrolledPayment($course);
        $item->loadMissing('unit');
        abort_unless($item->unit && (int) $item->unit->course_id === (int) $course->id, 404);
        abort_unless($course->canUserAccessPathItem(Auth::user(), $item), 403);
        abort_unless($item->isLesson(), 422);

        $data = $request->validate([
            'watched_seconds' => 'nullable|integer|min:0',
            'position_seconds' => 'nullable|integer|min:0',
            'played_seconds' => 'nullable|integer|min:0',
        ]);

        // position = farthest timeline point; played = actual playback seconds
        $position = (int) ($data['position_seconds'] ?? $data['watched_seconds'] ?? 0);
        $played = (int) ($data['played_seconds'] ?? 0);
        $duration = (int) ($item->video_duration_seconds ?? 0);
        $ratio = $course->pathLessonCompleteRatio();
        $threshold = $duration > 0 ? (int) floor($duration * $ratio) : 0;

        $progress = CoursePathProgress::firstOrNew([
            'user_id' => Auth::id(),
            'path_item_id' => $item->id,
        ]);
        $progress->course_id = $course->id;
        $progress->video_watched_seconds = max((int) $progress->video_watched_seconds, $position);
        $progress->video_played_seconds = max((int) ($progress->video_played_seconds ?? 0), $played);

        $shouldComplete = $duration > 0
            && $threshold > 0
            && (int) $progress->video_watched_seconds >= $threshold
            && (int) $progress->video_played_seconds >= $threshold;

        if ($shouldComplete && !$progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        $pathCompleted = $this->markPathCompletedIfDone($course);
        $requireComplete = $course->requiresPathLessonComplete();

        return response()->json([
            'ok' => true,
            'is_completed' => (bool) $progress->is_completed,
            'watched_seconds' => (int) $progress->video_watched_seconds,
            'position_seconds' => (int) $progress->video_watched_seconds,
            'played_seconds' => (int) $progress->video_played_seconds,
            'threshold_seconds' => $threshold,
            'next_unlocked' => $requireComplete ? (bool) $progress->is_completed : true,
            'require_complete' => $requireComplete,
            'path_completed' => $pathCompleted,
        ]);
    }

    /**
     * Submit path-item exam (supports retakes).
     */
    public function submitExam(Request $request, Course $course, CoursePathItem $item)
    {
        $this->enrolledPayment($course);
        $item->loadMissing('unit');
        abort_unless($item->unit && (int) $item->unit->course_id === (int) $course->id, 404);
        abort_unless($course->canUserAccessPathItem(Auth::user(), $item), 403);
        abort_unless($item->isExam(), 422);

        $item->load('examQuestions.answers');
        $data = $request->validate([
            'answers' => 'nullable|array',
            'answers.*' => 'nullable|integer',
            'time_spent_seconds' => 'nullable|integer|min:0',
            'timed_out' => 'nullable|boolean',
        ]);

        $answers = $data['answers'] ?? [];
        $score = 0;
        $total = $item->examQuestions->count();
        $stored = [];

        foreach ($item->examQuestions as $q) {
            $chosenId = isset($answers[$q->id]) ? (int) $answers[$q->id] : null;
            $correct = $q->answers->firstWhere('is_correct', true);
            $isRight = $correct && $chosenId && (int) $correct->id === $chosenId;
            if ($isRight) {
                $score++;
            }
            $stored[(string) $q->id] = $chosenId;
        }

        $passScore = (int) ($item->exam_pass_score ?? max(1, $total));
        $passed = $score >= $passScore;
        $durationLimit = max(1, (int) ($item->exam_duration_minutes ?? 30)) * 60;
        $timedOut = (bool) ($data['timed_out'] ?? false);
        $timeSpent = $timedOut
            ? $durationLimit
            : min($durationLimit, max(0, (int) ($data['time_spent_seconds'] ?? 0)));

        $progress = CoursePathProgress::firstOrNew([
            'user_id' => Auth::id(),
            'path_item_id' => $item->id,
        ]);
        $progress->course_id = $course->id;
        $alreadyCompleted = (bool) $progress->is_completed;

        $progress->exam_score = $score;
        $progress->exam_passed = $passed;
        $progress->exam_answers = $stored;
        $progress->exam_time_spent_seconds = $timeSpent;
        // Keep path unlock if previously passed; last attempt still updates score/state above
        $progress->is_completed = $passed || $alreadyCompleted;
        if ($progress->is_completed && !$progress->completed_at) {
            $progress->completed_at = now();
        }
        $progress->save();

        $pathCompleted = $this->markPathCompletedIfDone($course);

        return response()->json([
            'ok' => true,
            'passed' => $passed,
            'score' => $score,
            'total' => $total,
            'pass_score' => $passScore,
            'time_spent_seconds' => $timeSpent,
            'timed_out' => $timedOut,
            'is_completed' => (bool) $progress->is_completed,
            'path_completed' => $pathCompleted,
        ]);
    }

    /**
     * When every path step is done, mark the enrollment as a completed/ended course.
     */
    protected function markPathCompletedIfDone(Course $course): bool
    {
        if (!$course->isPathFullyCompletedBy(Auth::id())) {
            return false;
        }

        Payment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereNull('path_completed_at')
            ->update(['path_completed_at' => now()]);

        return true;
    }

    /**
     * Stream an uploaded lesson video with HTTP Range support (required for seeking).
     */
    public function stream(Course $course, CoursePathItem $item)
    {
        $this->enrolledPayment($course);
        $item->loadMissing('unit');
        abort_unless($item->unit && (int) $item->unit->course_id === (int) $course->id, 404);
        abort_unless($course->canUserAccessPathItem(Auth::user(), $item), 403);
        abort_unless($item->isLesson() && $item->video_path, 404);

        $path = \Illuminate\Support\Facades\Storage::disk('public')->path($item->video_path);
        abort_unless(is_file($path), 404);

        $mime = mime_content_type($path) ?: 'video/mp4';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Persist a captured video-frame thumbnail for an uploaded lesson (once).
     */
    public function saveThumbnail(Request $request, Course $course, CoursePathItem $item)
    {
        $this->enrolledPayment($course);
        $item->loadMissing('unit');
        abort_unless($item->unit && (int) $item->unit->course_id === (int) $course->id, 404);
        abort_unless($course->canUserAccessPathItem(Auth::user(), $item), 403);
        abort_unless($item->isLesson() && $item->video_path, 422);

        if ($item->video_thumbnail_path) {
            return response()->json([
                'ok' => true,
                'already' => true,
                'url' => $item->thumbnailUrl(),
            ]);
        }

        $data = $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $path = $request->file('thumbnail')->store('courses/path-thumbnails', 'public');
        $item->update(['video_thumbnail_path' => $path]);

        return response()->json([
            'ok' => true,
            'url' => $item->fresh()->thumbnailUrl(),
        ]);
    }
}
