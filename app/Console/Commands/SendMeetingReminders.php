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
            $projectTitle   = $meeting->title;
            $dateRange      = $meeting->formatted_date_range;
            $meetingLink    = $meeting->meeting_link ?? 'لا يوجد رابط';
            $meetingTypeLabel = $meeting->meeting_type_label;

            $acceptedParticipants = $meeting->participants
                ->filter(fn($u) => $u->pivot->status === 'accepted');

            foreach ($acceptedParticipants as $participant) {
                $this->sendWhatsAppReminder($whatsapp, $participant, $projectTitle, $dateRange, $meetingLink, $meetingTypeLabel);
                $this->sendEmailReminder($participant, $projectTitle, $dateRange, $meetingLink, $meetingTypeLabel);
            }

            $this->info("تم إرسال التذكيرات للاجتماع: {$meeting->title} ({$acceptedParticipants->count()} مشاركين)");
            Log::info("[MEETING_REMINDER] تذكير الاجتماع #{$meeting->id}", [
                'title'        => $meeting->title,
                'start_at'     => $meeting->start_at,
                'participants' => $acceptedParticipants->count(),
            ]);
        }
    }

    private function sendWhatsAppReminder(WhatsAppOTPService $whatsapp, \App\Models\User $participant, string $projectTitle, string $dateRange, string $meetingLink, string $meetingTypeLabel): void
    {
        if (!$participant->phone) {
            return;
        }

        try {
            $linkPart = ($meetingTypeLabel === 'أونلاين' && $meetingLink !== 'لا يوجد رابط')
                ? " — رابط الاجتماع: {$meetingLink}"
                : '';
            $eventText = "تذكير: اجتماع بعد 30 دقيقة"
                . " — {$dateRange}"
                . " — المنطقة الزمنية: Asia/Dubai"
                . " — النوع: {$meetingTypeLabel}"
                . $linkPart;
            $whatsapp->sendProjectNotification($participant->phone, $participant->name, $eventText, $projectTitle);
        } catch (\Exception $e) {
            Log::error("[MEETING_REMINDER] فشل الواتساب للمشارك #{$participant->id}: " . $e->getMessage());
        }
    }

    private function sendEmailReminder(\App\Models\User $participant, string $projectTitle, string $dateRange, string $meetingLink, string $meetingTypeLabel): void
    {
        if (!$participant->email) {
            return;
        }

        $subject = "تذكير: اجتماع {$projectTitle} بعد 30 دقيقة";
        $linkLine = $meetingTypeLabel === 'أونلاين'
            ? "للانضمام للاجتماع اضغط على الرابط:\n{$meetingLink}\n\n"
            : "الاجتماع حضوري، يرجى الحضور في الموعد المحدد.\n\n";
        $body = "مرحباً {$participant->name},\n\n" .
                "نذكرك باجتماع مجدول بعد 30 دقيقة:\n\n" .
                "📌 الاجتماع: {$projectTitle}\n" .
                "📅 {$dateRange}\n" .
                "🏷️ النوع: {$meetingTypeLabel}\n\n" .
                $linkLine .
                "في حال واجهتك أي مشكلة يرجى التواصل مع مدير المشروع.\n\n" .
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
