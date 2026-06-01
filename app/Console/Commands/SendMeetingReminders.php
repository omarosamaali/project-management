<?php

namespace App\Console\Commands;

use App\Models\ProjectMeeting;
use App\Services\WhatsAppOTPService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMeetingReminders extends Command
{
    protected $signature   = 'meetings:send-reminders';
    protected $description = 'إرسال تذكيرات الاجتماعات قبل 30 دقيقة من موعد البدء';

    public function handle(WhatsAppOTPService $whatsapp): void
    {
        $now          = Carbon::now();
        $windowStart  = $now->copy()->addMinutes(29);
        $windowEnd    = $now->copy()->addMinutes(31);

        $meetings = ProjectMeeting::with(['participants'])
            ->whereBetween('start_at', [$windowStart, $windowEnd])
            ->get();

        if ($meetings->isEmpty()) {
            $this->info('لا توجد اجتماعات تستحق التذكير الآن.');
            return;
        }

        foreach ($meetings as $meeting) {
            $projectTitle = $meeting->title;
            $startTime    = Carbon::parse($meeting->start_at)->format('H:i');
            $meetingLink  = $meeting->meeting_link ?? 'لا يوجد رابط';

            $acceptedParticipants = $meeting->participants
                ->filter(fn($u) => $u->pivot->status === 'accepted');

            foreach ($acceptedParticipants as $participant) {
                $this->sendWhatsAppReminder($whatsapp, $participant, $projectTitle, $startTime, $meetingLink);
                $this->sendEmailReminder($whatsapp, $participant, $projectTitle, $startTime, $meetingLink);
            }

            $this->info("تم إرسال التذكيرات للاجتماع: {$meeting->title} ({$acceptedParticipants->count()} مشاركين)");
            Log::info("[MEETING_REMINDER] تذكير الاجتماع #{$meeting->id}", [
                'title'        => $meeting->title,
                'start_at'     => $meeting->start_at,
                'participants' => $acceptedParticipants->count(),
            ]);
        }
    }

    private function sendWhatsAppReminder(WhatsAppOTPService $whatsapp, $participant, string $projectTitle, string $startTime, string $meetingLink): void
    {
        if (!$participant->phone) {
            return;
        }

        try {
            $eventText = "تذكير: اجتماع بعد 30 دقيقة — الوقت: {$startTime} — رابط الدخول: {$meetingLink}";
            $whatsapp->sendProjectNotification($participant->phone, $participant->name, $eventText, $projectTitle);
        } catch (\Exception $e) {
            Log::error("[MEETING_REMINDER] فشل الواتساب للمشارك #{$participant->id}: " . $e->getMessage());
        }
    }

    private function sendEmailReminder(WhatsAppOTPService $whatsapp, $participant, string $projectTitle, string $startTime, string $meetingLink): void
    {
        if (!$participant->email) {
            return;
        }

        $subject = "تذكير: اجتماع {$projectTitle} بعد 30 دقيقة";
        $body    = "مرحباً {$participant->name},\n\n" .
                   "نذكرك باجتماع مجدول بعد 30 دقيقة:\n\n" .
                   "📌 المشروع: {$projectTitle}\n" .
                   "🕐 وقت البدء: {$startTime}\n\n" .
                   "للانضمام للاجتماع اضغط على الرابط:\n{$meetingLink}\n\n" .
                   "في حال واجهتك أي مشكلة في الدخول يرجى التواصل مع مدير المشروع.\n\n" .
                   "---\nفريق Evorq Technologies\ninfo@evorq.com";

        try {
            \Illuminate\Support\Facades\Mail::raw(
                $body,
                function ($message) use ($participant, $subject) {
                    $message->to($participant->email, $participant->name)
                            ->subject($subject)
                            ->from(
                                config('mail.from.address', 'noreply@evorq.com'),
                                config('mail.from.name', 'Evorq')
                            );
                }
            );
        } catch (\Exception $e) {
            Log::error("[MEETING_REMINDER] فشل الإيميل للمشارك #{$participant->id}: " . $e->getMessage());
        }
    }
}
