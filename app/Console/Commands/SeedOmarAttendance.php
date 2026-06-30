<?php

namespace App\Console\Commands;

use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SeedOmarAttendance extends Command
{
    protected $signature = 'attendance:seed-omar
                            {--from= : تاريخ البداية (Y-m-d)، الافتراضي: أول الشهر الحالي}
                            {--to=   : تاريخ النهاية (Y-m-d)، الافتراضي: اليوم}
                            {--country=EG : رمز البلد}
                            {--skip-friday  : تخطي الجمعة (افتراضي مفعّل)}
                            {--skip-saturday: تخطي السبت}';

    protected $description = 'إنشاء سجلات حضور وانصراف';

    private const USER_ID = 2;

    public function handle(): int
    {
        $from    = $this->option('from')    ? Carbon::parse($this->option('from'))    : Carbon::now()->startOfMonth();
        $to      = $this->option('to')      ? Carbon::parse($this->option('to'))      : Carbon::today();
        $country = $this->option('country') ?? 'AE';

        $this->info("📅 من: {$from->toDateString()} → إلى: {$to->toDateString()}");
        $this->info("👤 المستخدم: عمر أسامة (ID=" . self::USER_ID . ")");

        $created  = 0;
        $skipped  = 0;
        $current  = $from->copy();

        while ($current->lte($to)) {
            $dateStr = $current->toDateString();

            // تخطي الجمعة دائماً
            if ($current->isFriday()) {
                $current->addDay();
                continue;
            }

            // تخطي السبت إذا طُلب
            if ($this->option('skip-saturday') && $current->isSaturday()) {
                $current->addDay();
                continue;
            }

            // تخطي أيام لها سجلات موجودة بالفعل
            $exists = WorkTime::where('user_id', self::USER_ID)
                ->whereDate('date', $dateStr)
                ->exists();

            if ($exists) {
                $this->line("  ⏭  {$dateStr} — موجود مسبقاً، تم التخطي");
                $skipped++;
                $current->addDay();
                continue;
            }

            // وقت الحضور: عشوائي بين 8:50 و 9:04
            $checkInMinute  = rand(50, 64); // 50-63 = 8:50→8:63 = 8:50→9:03 ; 64 = 9:04
            $checkInHour    = 8 + intdiv($checkInMinute, 60);
            $checkInMin     = $checkInMinute % 60;
            $checkInSec     = rand(0, 59);
            $checkInTime    = sprintf('%02d:%02d:%02d', $checkInHour, $checkInMin, $checkInSec);

            // وقت الانصراف: عشوائي بين 18:00 و 18:20
            $checkOutMin    = rand(0, 20);
            $checkOutSec    = rand(0, 59);
            $checkOutTime   = sprintf('18:%02d:%02d', $checkOutMin, $checkOutSec);

            WorkTime::create([
                'user_id'   => self::USER_ID,
                'country'   => $country,
                'type'      => 'حضور',
                'source'    => 'web',
                'date'      => $dateStr,
                'start_time'=> $checkInTime,
                'end_time'  => null,
                'timezone'  => 'Asia/Dubai',
            ]);

            WorkTime::create([
                'user_id'   => self::USER_ID,
                'country'   => $country,
                'type'      => 'انصراف',
                'source'    => 'web',
                'date'      => $dateStr,
                'start_time'=> $checkOutTime,
                'end_time'  => null,
                'timezone'  => 'Asia/Dubai',
            ]);

            $this->line("  ✅  {$dateStr} — حضور: {$checkInTime} | انصراف: {$checkOutTime}");
            $created += 2;
            $current->addDay();
        }

        $this->newLine();
        $this->info("✔ تم إنشاء {$created} سجل | تم تخطي {$skipped} يوم");

        return self::SUCCESS;
    }
}
