<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $rawBody = $request->getContent();
        $timestamp = (string) $request->header('X-Meeting-Timestamp', '');
        $signature = (string) $request->header('X-Meeting-Signature', '');

        if (! $this->signatureValid($timestamp, $rawBody, $signature)) {
            Log::warning('[MEETING] webhook signature invalid');

            return response()->json(['ok' => false], 401);
        }

        $payload = json_decode($rawBody, true);
        if (! is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'Invalid JSON'], 400);
        }

        $event = (string) ($payload['event'] ?? '');
        $meetingId = (string) ($payload['meetingId'] ?? '');
        $externalId = (string) ($payload['externalId'] ?? '');

        Log::info('[MEETING] webhook received', [
            'event' => $event,
            'meeting_id' => $meetingId,
            'external_id' => $externalId,
        ]);

        $course = null;
        if ($meetingId !== '') {
            $course = Course::query()->where('embedded_meeting_id', $meetingId)->first();
        }
        if (! $course && $externalId !== '' && str_starts_with($externalId, 'course-')) {
            $courseId = (int) substr($externalId, strlen('course-'));
            if ($courseId > 0) {
                $course = Course::query()->find($courseId);
            }
        }

        if ($course) {
            if ($event === 'meeting.ended') {
                $course->update(['embedded_meeting_status' => 'ENDED']);
            } elseif ($event === 'meeting.participant_joined') {
                if (($course->embedded_meeting_status ?? '') !== 'ENDED') {
                    $course->update(['embedded_meeting_status' => 'OPEN']);
                }
            }
        }

        return response()->json(['ok' => true]);
    }

    protected function signatureValid(string $timestamp, string $rawBody, string $signature): bool
    {
        if ($timestamp === '' || $signature === '') {
            return false;
        }

        $secret = (string) (
            config('services.meeting.webhook_secret')
            ?: config('services.meeting.api_secret')
            ?: ''
        );

        if ($secret === '') {
            return false;
        }

        // Reject stale timestamps (±5 minutes), accepting seconds or milliseconds.
        $ts = (int) $timestamp;
        if ($ts > 1_000_000_000_000) {
            $ts = (int) floor($ts / 1000);
        }
        if (abs(time() - $ts) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret);

        return hash_equals($expected, strtolower($signature))
            || hash_equals($expected, $signature);
    }
}
