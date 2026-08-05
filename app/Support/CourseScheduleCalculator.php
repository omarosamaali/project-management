<?php

namespace App\Support;

use App\Models\Course;
use Carbon\Carbon;

/**
 * Projects a course teaching schedule forward from a start date, skipping
 * rest days and trainer off days, until the required number of teaching
 * (session) days has been reached.
 */
class CourseScheduleCalculator
{
    private const WEEKDAYS = [
        'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
    ];

    /**
     * Walk day by day from $startAt (inclusive). A date counts as a teaching day
     * unless it appears in $restDayDates or $trainerOffDates. Once $sessionDays
     * teaching days have been counted, that date becomes the end date.
     *
     * @param  array<int, string>  $restDayDates     Rest dates as Y-m-d strings
     * @param  array<int, string>  $trainerOffDates  Trainer off dates as Y-m-d strings
     * @return array{
     *     end_date: Carbon,
     *     teaching_dates: array<int, string>,
     *     rest_dates: array<int, string>,
     *     off_dates: array<int, string>,
     *     calendar_span_days: int
     * }
     */
    public static function calculate(Carbon $startAt, int $sessionDays, array $restDayDates = [], array $trainerOffDates = []): array
    {
        $sessionDays = max(1, $sessionDays);
        $restSet = array_flip($restDayDates);
        $offSet = array_flip($trainerOffDates);

        $current = $startAt->copy()->startOfDay();
        $teachingDates = [];
        $restDatesUsed = [];
        $offDatesUsed = [];
        $teachingCount = 0;
        $lastTeachingDate = null;

        // Safety cap so a pathological all-rest-days input can never hang the request.
        $maxIterations = max($sessionDays * 30, 3650);
        $iterations = 0;

        while ($teachingCount < $sessionDays && $iterations < $maxIterations) {
            $key = $current->format('Y-m-d');

            if (isset($offSet[$key])) {
                $offDatesUsed[] = $key;
            } elseif (isset($restSet[$key])) {
                $restDatesUsed[] = $key;
            } else {
                $teachingCount++;
                $teachingDates[] = $key;
                $lastTeachingDate = $current->copy();
            }

            if ($teachingCount >= $sessionDays) {
                break;
            }

            $current->addDay();
            $iterations++;
        }

        // Preserve the start time-of-day on the resolved end date.
        $endDate = ($lastTeachingDate ?? $current)->copy();
        $endDate->setTimeFrom($startAt);

        $calendarSpanDays = $startAt->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1;

        return [
            'end_date' => $endDate,
            'teaching_dates' => $teachingDates,
            'rest_dates' => $restDatesUsed,
            'off_dates' => $offDatesUsed,
            'calendar_span_days' => (int) $calendarSpanDays,
        ];
    }

    /**
     * Expand a recurring weekly rest-day pattern (e.g. ['friday']) into concrete
     * Y-m-d dates over a bounded horizon, so it can be fed into calculate().
     *
     * @param  array<int, string>  $weekdays  Weekday names (sunday..saturday), any case
     * @return array<int, string>
     */
    public static function expandWeekdayRestDates(Carbon $startAt, array $weekdays, int $horizonDays = 400): array
    {
        $weekdays = array_values(array_intersect(
            array_map('strtolower', $weekdays),
            self::WEEKDAYS
        ));

        if (empty($weekdays)) {
            return [];
        }

        $dates = [];
        $current = $startAt->copy()->startOfDay();
        $horizonDays = max(1, $horizonDays);

        for ($i = 0; $i < $horizonDays; $i++) {
            if (in_array(strtolower($current->format('l')), $weekdays, true)) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Convenience wrapper for an existing Course: derives its rest-day dates from
     * the weekly rest_days pattern and combines them with the given trainer off dates.
     *
     * @param  array<int, string>  $trainerOffDates  Y-m-d dates
     * @return array{
     *     end_date: Carbon,
     *     teaching_dates: array<int, string>,
     *     rest_dates: array<int, string>,
     *     off_dates: array<int, string>,
     *     calendar_span_days: int
     * }
     */
    public static function forCourse(Course $course, array $trainerOffDates): array
    {
        $start = Carbon::parse($course->start_date);
        $sessionDays = max(1, (int) $course->count_days);
        $horizon = max($sessionDays * 3 + 60, 120);
        $restDates = self::expandWeekdayRestDates($start, (array) ($course->rest_days ?? []), $horizon);

        return self::calculate($start, $sessionDays, $restDates, $trainerOffDates);
    }
}
