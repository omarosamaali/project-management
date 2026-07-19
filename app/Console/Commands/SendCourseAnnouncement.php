<?php

namespace App\Console\Commands;

use App\Mail\CourseAnnouncementMail;
use App\Models\AppNotification;
use App\Models\Course;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCourseAnnouncement extends Command
{
    protected $signature = 'course:announce
        {course : معرف الدورة (ID)}
        {--audience=clients : الجمهور المستهدف: clients | enrolled | all}
        {--channel=all : قناة الإرسال: all | whatsapp | email}
        {--test= : إرسال رسالة تجريبية واحدة فقط لرقم هاتف أو بريد}';

    protected $description = 'إرسال إعلان دورة تدريبية (واتساب + بريد) لكل العملاء لدورة محددة';

    public function handle(WhatsAppOTPService $whatsapp): int
    {
        $course = Course::find($this->argument('course'));
        if (!$course) {
            $this->error("لا توجد دورة بالمعرف: {$this->argument('course')}");
            return self::FAILURE;
        }

        $channel = $this->option('channel');
        $courseName = $course->name_ar;
        $courseUrl = $course->publicUrl();
        $imageUrl = $course->mainImageUrl();

        $this->info("الدورة: {$courseName} (#{$course->id})");
        $this->line("الرابط: {$courseUrl}");
        $this->line("القناة: {$channel} | الجمهور: {$this->option('audience')}");
        $this->newLine();

        // ── وضع الاختبار: إرسال لمستلم واحد فقط ──
        if ($test = $this->option('test')) {
            return $this->sendTest($whatsapp, $course, $test, $channel, $courseName, $courseUrl, $imageUrl);
        }

        $recipients = $this->resolveRecipients($course);
        $total = $recipients->count();

        if ($total === 0) {
            $this->warn('لا يوجد مستلمون مطابقون.');
            return self::SUCCESS;
        }

        if (!$this->confirm("سيتم الإرسال إلى {$total} مستلم. هل تريد المتابعة؟", true)) {
            $this->line('تم الإلغاء.');
            return self::SUCCESS;
        }

        $waSent = 0; $waFail = 0; $mailSent = 0; $mailFail = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($recipients as $user) {
            try {
                AppNotification::notify(
                    $user->id,
                    'دورة تدريبية جديدة',
                    "أطلقنا دورة تدريبية جديدة: {$courseName}. سارِع بالتسجيل الآن.",
                    $courseUrl,
                    'fa-graduation-cap',
                    'info'
                );
            } catch (\Throwable $e) {
                // in-app notification failure shouldn't stop the rest
            }

            if ($channel !== 'email' && !empty($user->phone)) {
                try {
                    $ok = $whatsapp->sendNewCourseAnnouncement($user->phone, $user->name, $courseName, $courseUrl, $imageUrl);
                    $ok ? $waSent++ : $waFail++;
                } catch (\Throwable $e) {
                    $waFail++;
                    Log::error('[COURSE-ANNOUNCE][WA] ' . $e->getMessage(), ['user_id' => $user->id]);
                }
            }

            if ($channel !== 'whatsapp' && !empty($user->email)) {
                try {
                    Mail::to($user->email, $user->name)->send(new CourseAnnouncementMail($course, $user->name));
                    $mailSent++;
                } catch (\Throwable $e) {
                    $mailFail++;
                    Log::error('[COURSE-ANNOUNCE][MAIL] ' . $e->getMessage(), ['user_id' => $user->id]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['القناة', 'نجح', 'فشل'],
            [
                ['واتساب', $waSent, $waFail],
                ['البريد', $mailSent, $mailFail],
            ]
        );

        $this->info('تم الانتهاء من إرسال إعلان الدورة.');
        return self::SUCCESS;
    }

    private function resolveRecipients(Course $course)
    {
        return match ($this->option('audience')) {
            'enrolled' => $course->students()->select('users.id', 'users.name', 'users.phone', 'users.email')->get(),
            'all' => User::notBlocked()->select('id', 'name', 'phone', 'email')->get(),
            default => User::where('role', 'client')->notBlocked()
                ->select('id', 'name', 'phone', 'email')->get(),
        };
    }

    private function sendTest(
        WhatsAppOTPService $whatsapp,
        Course $course,
        string $test,
        string $channel,
        string $courseName,
        string $courseUrl,
        string $imageUrl,
    ): int {
        $isEmail = str_contains($test, '@');

        if ($isEmail) {
            try {
                Mail::to($test)->send(new CourseAnnouncementMail($course, 'عميلنا العزيز'));
                $this->info("تم إرسال بريد تجريبي إلى: {$test}");
            } catch (\Throwable $e) {
                $this->error('فشل إرسال البريد: ' . $e->getMessage());
                return self::FAILURE;
            }
        } else {
            $ok = $whatsapp->sendNewCourseAnnouncement($test, 'عميلنا العزيز', $courseName, $courseUrl, $imageUrl);
            $ok
                ? $this->info("تم إرسال واتساب تجريبي إلى: {$test}")
                : $this->error("فشل إرسال الواتساب إلى: {$test} — راجع سجل whatsapp_messages / laravel.log");
        }

        return self::SUCCESS;
    }
}
