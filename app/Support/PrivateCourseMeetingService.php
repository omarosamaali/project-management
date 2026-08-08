<?php

namespace App\Support;

use App\Models\Course;
use App\Models\PrivateCourseRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PrivateCourseMeetingService
{
    public function __construct(
        protected MeetingIntegrationClient $client
    ) {}

    public function usesEmbeddedMeetings(): bool
    {
        return Setting::academyEmbeddedMeetingsActive();
    }

    public function courseHasMeetingAccess(Course $course): bool
    {
        if (filled($course->online_link) || filled($course->embedded_meeting_id)) {
            return true;
        }

        return $course->isPrivate() && $this->usesEmbeddedMeetings();
    }

    /**
     * Create the remote meeting once for a private course (lazy).
     */
    public function ensureMeeting(Course $course): Course
    {
        if (! $course->isPrivate() || ! $this->usesEmbeddedMeetings()) {
            return $course;
        }

        if (filled($course->embedded_meeting_id)) {
            return $course;
        }

        $request = PrivateCourseRequest::query()
            ->where('private_course_id', $course->id)
            ->with(['trainee', 'trainer'])
            ->latest('id')
            ->first();

        $host = $course->trainer ?? $request?->trainer;
        $participant = $request?->trainee;

        $payload = [
            'title' => $course->name_ar ?: $course->name_en ?: ('Private course #' . $course->id),
            'externalId' => 'course-' . $course->id,
            'host' => [
                'externalUserId' => (string) ($host?->id ?? ('trainer-' . ($course->trainer_id ?: 0))),
                'name' => trim((string) ($host?->name ?? 'Trainer')) ?: 'Trainer',
            ],
            'maxParticipants' => 2,
            'metadata' => [
                'courseId' => (string) $course->id,
                'privateCourseRequestId' => $request ? (string) $request->id : null,
            ],
            'embed' => true,
            'webhookUrl' => route('webhooks.meetings'),
        ];

        if ($participant) {
            $payload['participant'] = [
                'externalUserId' => (string) $participant->id,
                'name' => trim((string) ($participant->name ?? 'Trainee')) ?: 'Trainee',
            ];
        }

        try {
            $result = $this->client->createMeeting($payload);
        } catch (Throwable $e) {
            Log::error('[MEETING] create failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $meetingId = (string) ($result['meetingId'] ?? '');
        if ($meetingId === '') {
            throw new RuntimeException('Meeting API returned no meetingId.');
        }

        $course->update([
            'embedded_meeting_id' => $meetingId,
            'embedded_meeting_status' => (string) ($result['status'] ?? 'OPEN'),
        ]);

        Log::info('[MEETING] created for private course', [
            'course_id' => $course->id,
            'meeting_id' => $meetingId,
        ]);

        return $course->fresh() ?? $course;
    }

    /**
     * Mint a fresh host/participant join URL for the current user.
     */
    public function joinUrlForUser(Course $course, User $user): ?string
    {
        if (! $this->usesEmbeddedMeetings() || ! $course->isPrivate()) {
            return null;
        }

        $course = $this->ensureMeeting($course);
        $meetingId = (string) ($course->embedded_meeting_id ?? '');
        if ($meetingId === '') {
            return null;
        }

        $isHost = $course->canModerateChat($user)
            || ((int) $course->trainer_id === (int) $user->id);

        $role = $isHost ? 'host' : 'participant';

        $result = $this->client->mintJoinToken($meetingId, [
            'role' => $role,
            'externalUserId' => (string) $user->id,
            'name' => trim((string) ($user->name ?? '')) ?: ($isHost ? 'Host' : 'Participant'),
            'embed' => true,
            'ttlSeconds' => 7200,
        ]);

        $joinUrl = $result['joinUrl'] ?? null;
        if (! is_string($joinUrl) || $joinUrl === '') {
            return null;
        }

        return $this->rewriteJoinUrlToConfiguredBase($joinUrl);
    }

    /**
     * Meeting APIs often mint join links using PUBLIC_APP_URL (e.g. LAN IP) even when
     * the LMS calls the service through ngrok. Force the LMS MEETING_BASE_URL origin.
     */
    protected function rewriteJoinUrlToConfiguredBase(string $joinUrl): string
    {
        $base = rtrim((string) config('services.meeting.base_url', ''), '/');
        if ($base === '') {
            return $joinUrl;
        }

        $baseParts = parse_url($base);
        $joinParts = parse_url($joinUrl);
        if (! is_array($baseParts) || ! is_array($joinParts) || empty($joinParts['path'])) {
            return $joinUrl;
        }

        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? null;
        if (! $host) {
            return $joinUrl;
        }

        $port = isset($baseParts['port']) ? ':'.$baseParts['port'] : '';
        $path = $joinParts['path'] ?? '/';
        $query = isset($joinParts['query']) ? '?'.$joinParts['query'] : '';
        $fragment = isset($joinParts['fragment']) ? '#'.$joinParts['fragment'] : '';

        $rewritten = $scheme.'://'.$host.$port.$path.$query.$fragment;

        if ($rewritten !== $joinUrl) {
            Log::info('[MEETING] rewrote join URL host', [
                'from' => $joinParts['host'] ?? null,
                'to' => $host,
            ]);
        }

        return $rewritten;
    }
}
