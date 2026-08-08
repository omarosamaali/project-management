<?php

use App\Models\Course;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['web', 'auth']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('course.{courseId}.chat', function ($user, int $courseId) {
    $course = Course::query()->find($courseId);
    if (! $course) {
        return false;
    }

    return $course->canAccessLectureChat($user);
});
