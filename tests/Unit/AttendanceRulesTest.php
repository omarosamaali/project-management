<?php

use App\Models\User;
use App\Models\WorkTime;
use App\Support\AttendanceRules;
use Carbon\Carbon;
use Illuminate\Support\Collection;

test('minute rate is derived from salary and daily hours', function () {
    $user = new User([
        'salary_amount' => 2340,
        'daily_work_hours' => 9,
    ]);

    expect(AttendanceRules::hourRate($user))->toBe(10.0);
});

test('first check-in after work end is after scheduled end', function () {
    $user = new User(['work_end_time' => '18:00:00']);
    $at = Carbon::parse('2026-06-02 19:00:00');

    expect($at->gt(AttendanceRules::scheduledEnd($user, '2026-06-02')))->toBeTrue();
});

test('overtime minutes counted only after evening checkout and second check-in', function () {
    $user = new User([
        'work_end_time' => '18:00:00',
        'overtime_hourly_rate' => 15,
    ]);
    $date = '2026-06-02';

    $records = new Collection([
        new WorkTime(['type' => 'حضور', 'date' => $date, 'start_time' => '09:00:00']),
        new WorkTime(['type' => 'انصراف', 'date' => $date, 'start_time' => '18:30:00']),
        new WorkTime(['type' => 'حضور', 'date' => $date, 'start_time' => '19:00:00']),
        new WorkTime(['type' => 'انصراف', 'date' => $date, 'start_time' => '20:00:00']),
    ]);

    $minutes = AttendanceRules::overtimeMinutesForDay($user, $date, $records);

    expect($minutes)->toBe(60);
});

test('weekly off uses vacation_days from profile', function () {
    $user = new User([
        'vacation_days' => ['friday', 'saturday'],
    ]);

    expect(AttendanceRules::isWeeklyOff($user, Carbon::parse('2026-06-05')))->toBeTrue();
    expect(AttendanceRules::isWeeklyOff($user, Carbon::parse('2026-06-06')))->toBeTrue();
    expect(AttendanceRules::isWeeklyOff($user, Carbon::parse('2026-06-08')))->toBeFalse();
});
