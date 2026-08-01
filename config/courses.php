<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Recorded path: require completing each step before the next
    |--------------------------------------------------------------------------
    |
    | When true (default), trainees must finish the current lesson (watch to
    | the end) or pass the exam before the next path item unlocks.
    | When false, all path items are accessible without sequential completion.
    |
    | Env: COURSE_PATH_REQUIRE_LESSON_COMPLETE=true|false
    | If the variable is missing from .env, completion is required.
    |
    */
    'path_require_lesson_complete' => filter_var(
        env('COURSE_PATH_REQUIRE_LESSON_COMPLETE', true),
        FILTER_VALIDATE_BOOLEAN
    ),

    /*
    |--------------------------------------------------------------------------
    | Lesson completion threshold (fraction of duration)
    |--------------------------------------------------------------------------
    |
    | Both actual play time AND farthest timeline position must reach this
    | fraction of the video duration before the lesson is marked complete.
    |
    */
    'path_lesson_complete_ratio' => 0.9,

    /*
    |--------------------------------------------------------------------------
    | Online meeting / live stream
    |--------------------------------------------------------------------------
    |
    | YouTube Live is embedded in the lecture room. External links (Zoom,
    | Meet, etc.) open in a new tab while the chat page stays open.
    |
    | Env: COURSE_MEETING_OPEN_BEFORE_MINUTES (falls back to JITSI_OPEN_BEFORE_MINUTES)
    |
    */
    'meeting' => [
        'open_before_minutes' => (int) env(
            'COURSE_MEETING_OPEN_BEFORE_MINUTES',
            env('JITSI_OPEN_BEFORE_MINUTES', 30)
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trainer course price ceiling (AED)
    |--------------------------------------------------------------------------
    |
    | Trainers cannot set a course price above this amount. Admins are unlimited.
    |
    */
    'trainer_max_price' => (float) env('COURSE_TRAINER_MAX_PRICE', 400),

];
