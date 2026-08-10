<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Setting;
use App\Services\WhatsAppOTPService;
use Illuminate\Console\Command;

class TestAcademyWhatsAppCommand extends Command
{
    protected $signature = 'whatsapp:test-academy
        {phone : رقم واتساب مع كود الدولة (مثال: 2010XXXXXXXXX)}
        {--type=all : confirmation|announce|exam|trainer|private|all}
        {--name=مستخدم تجريبي : الاسم الظاهر في BODY_1}
        {--course= : معرف دورة اختياري لتأكيد/إعلان}';

    protected $description = 'اختبار إشعارات واتساب الأكاديمية (قالب acadmy) لحالات المتدرب/المحاضر';

    public function handle(WhatsAppOTPService $whatsapp): int
    {
        $phone = (string) $this->argument('phone');
        $type = strtolower((string) $this->option('type'));
        $name = (string) $this->option('name');
        $courseId = $this->option('course');

        $hero = Setting::academyHeroImagePublicUrl();
        $this->info('صورة الهيدر (academy hero / fallback): '.$hero);
        $this->line('الرقم: '.$phone.' | النوع: '.$type);
        $this->newLine();

        $allowed = ['confirmation', 'announce', 'exam', 'trainer', 'private', 'all'];
        if (! in_array($type, $allowed, true)) {
            $this->error('نوع غير معروف. استخدم: '.implode('|', $allowed));

            return self::FAILURE;
        }

        $course = null;
        if ($courseId) {
            $course = Course::find($courseId);
            if (! $course) {
                $this->error("لا توجد دورة #{$courseId}");

                return self::FAILURE;
            }
        } else {
            $course = Course::query()->latest('id')->first();
        }

        $courseName = $course
            ? (string) ($course->name_ar ?: $course->name_en ?: ('دورة #'.$course->id))
            : 'دورة تجريبية';
        $courseUrl = $course ? $course->publicUrl() : \App\Support\AppDomains::liveAcademyBase().'/academy';

        $map = [
            'confirmation' => function () use ($whatsapp, $phone, $name, $courseName, $course) {
                return $whatsapp->sendCourseConfirmation($phone, $name, $courseName, $course ?? (object) []);
            },
            'announce' => function () use ($whatsapp, $phone, $name, $courseName, $courseUrl, $course) {
                $image = $course?->mainImageUrl();

                return $whatsapp->sendNewCourseAnnouncement(
                    $phone,
                    $name,
                    $courseName,
                    $courseUrl,
                    $image
                );
            },
            'exam' => function () use ($whatsapp, $phone, $name, $courseName) {
                return $whatsapp->sendExamSuccessNotification($phone, $name, $courseName.' — اختبار تجريبي', 9, 10);
            },
            'trainer' => function () use ($whatsapp, $phone, $name) {
                return $whatsapp->sendTrainerApprovedNotification(
                    $phone,
                    $name,
                    \App\Support\AppDomains::liveAcademyBase().'/login'
                );
            },
            'private' => function () use ($whatsapp, $phone, $name, $courseName) {
                return $whatsapp->sendAcademyNotification(
                    $phone,
                    $name,
                    "تم استلام طلبك للدورة الخاصة «{$courseName}». سيتواصل معك المحاضر لتحديد المواعيد. (اختبار)",
                );
            },
        ];

        $types = $type === 'all' ? array_keys($map) : [$type];
        $failed = 0;

        foreach ($types as $key) {
            $this->line("→ إرسال [{$key}] …");
            try {
                $ok = (bool) $map[$key]();
                if ($ok) {
                    $this->info("  ✓ نجح [{$key}]");
                } else {
                    $failed++;
                    $this->error("  ✗ فشل [{$key}] — راجع laravel.log / whatsapp_messages");
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  ✗ استثناء [{$key}]: ".$e->getMessage());
            }

            if ($type === 'all') {
                usleep(400_000);
            }
        }

        $this->newLine();
        if ($failed > 0) {
            $this->warn("انتهى مع {$failed} فشل/فشل.");

            return self::FAILURE;
        }

        $this->info('تم بنجاح.');

        return self::SUCCESS;
    }
}
