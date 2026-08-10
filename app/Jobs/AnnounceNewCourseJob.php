<?php

namespace App\Jobs;

use App\Mail\CourseAnnouncementMail;
use App\Models\AppNotification;
use App\Models\Course;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnnounceNewCourseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 900;

    public function __construct(public int $courseId)
    {
    }

    public function handle(WhatsAppOTPService $whatsapp): void
    {
        $course = Course::find($this->courseId);
        if (!$course || $course->status !== 'active') {
            return;
        }

        $courseName = $course->name_ar;
        $courseUrl = $course->publicUrl();

        User::whereIn('role', ['trainer', 'trainee'])
            ->notBlocked()
            ->select('id', 'name', 'phone', 'email')
            ->chunkById(100, function ($users) use ($whatsapp, $course, $courseName, $courseUrl) {
                foreach ($users as $user) {
                    try {
                        AppNotification::notify(
                            $user->id,
                            'دورة تدريبية جديدة',
                            "أطلقنا دورة تدريبية جديدة: {$courseName}. سارِع بالتسجيل الآن.",
                            $courseUrl,
                            'fa-graduation-cap',
                            'info'
                        );

                        if (!empty($user->phone)) {
                            $whatsapp->sendNewCourseAnnouncement(
                                $user->phone,
                                $user->name,
                                $courseName,
                                $courseUrl,
                            );
                        }

                        if (!empty($user->email)) {
                            Mail::to($user->email, $user->name)
                                ->send(new CourseAnnouncementMail($course, $user->name));
                        }
                    } catch (\Throwable $e) {
                        Log::error('[COURSE-ANNOUNCE] فشل إشعار مستخدم', [
                            'course_id' => $course->id,
                            'user_id' => $user->id,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            });
    }
}
