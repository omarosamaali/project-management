<?php

namespace App\Http\Controllers;

use App\Events\CourseChatLockToggled;
use App\Events\CourseChatMessageCreated;
use App\Events\CourseChatMessageUpdated;
use App\Events\CourseChatUserModerationChanged;
use App\Models\Course;
use App\Models\CourseChatBlock;
use App\Models\CourseChatMessage;
use App\Models\Payment;
use App\Support\MeetingLink;
use App\Support\PrivateCourseMeetingService;
use App\Support\YouTubeLive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CourseLectureController extends Controller
{
    /**
     * In-app lecture room: YouTube Live embed (or external tab) + live discussion chat.
     */
    public function show(Payment $payment)
    {
        $payment->load('course');
        $course = $payment->course;
        abort_unless($course && (int) $payment->user_id === (int) Auth::id(), 403);
        abort_unless(
            in_array($course->location_type, ['online', 'private'], true) && $course->hasLiveMeetingAccess(),
            404
        );

        $now = Carbon::now();
        $openBefore = YouTubeLive::openBeforeMinutes();
        $start = Carbon::parse($course->start_date)->subMinutes($openBefore);
        $end = Carbon::parse($course->end_date);
        $inWindow = $now->between($start, $end);
        $meetingOpenAt = Carbon::parse($course->start_date)->subMinutes($openBefore);
        $meetingCloseAt = Carbon::parse($course->end_date);
        $canModerate = $course->canModerateChat(Auth::user());
        $meetingAvailable = $inWindow || $canModerate;
        $secondsUntilOpen = $now->gte($meetingOpenAt)
            ? 0
            : (int) $now->diffInSeconds($meetingOpenAt);

        if (!$payment->is_attended && !$inWindow && !$canModerate && !Auth::user()->isAdmin()) {
            return redirect()
                ->route('dashboard.my_courses.show', $payment->id)
                ->with('error', 'المحاضرة غير متاحة حالياً أو لم يتم تسجيل حضورك بعد.');
        }

        $canChat = $course->canAccessLectureChat(Auth::user());
        $isBlocked = $course->isUserChatBlocked(Auth::id());
        $chatLocked = (bool) $course->chat_locked_for_trainees;

        $lecture = $this->resolveLectureMedia($course, Auth::user(), $meetingAvailable);

        return $this->lectureResponse(array_merge(compact(
            'payment',
            'course',
            'canChat',
            'canModerate',
            'isBlocked',
            'chatLocked',
            'inWindow',
            'meetingAvailable',
            'meetingOpenAt',
            'meetingCloseAt',
            'secondsUntilOpen'
        ), $lecture));
    }

    /**
     * Moderator (admin/trainer) lecture room without a payment row.
     */
    public function showForManager(Course $course)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);
        abort_unless(
            in_array($course->location_type, ['online', 'private'], true) && $course->hasLiveMeetingAccess(),
            404
        );

        $payment = null;
        $canChat = true;
        $canModerate = true;
        $isBlocked = false;
        $chatLocked = (bool) $course->chat_locked_for_trainees;
        $inWindow = true;
        $meetingAvailable = true;
        $meetingOpenAt = Carbon::parse($course->start_date)->subMinutes(YouTubeLive::openBeforeMinutes());
        $meetingCloseAt = Carbon::parse($course->end_date);
        $secondsUntilOpen = 0;

        $lecture = $this->resolveLectureMedia($course, Auth::user(), $meetingAvailable);

        return $this->lectureResponse(array_merge(compact(
            'payment',
            'course',
            'canChat',
            'canModerate',
            'isBlocked',
            'chatLocked',
            'inWindow',
            'meetingAvailable',
            'meetingOpenAt',
            'meetingCloseAt',
            'secondsUntilOpen'
        ), $lecture));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function lectureResponse(array $data)
    {
        $response = response()->view('dashboard.my_courses.lecture', $data);

        if (! empty($data['useEmbeddedMeeting'])) {
            // Let Chromium delegate mic/camera into the meeting iframe origin.
            $response->headers->set(
                'Permissions-Policy',
                'microphone=*, camera=*, display-capture=*, autoplay=*, fullscreen=*'
            );
        }

        return $response;
    }

    /**
     * @return array{
     *     meeting: array,
     *     youtubeEmbed: ?string,
     *     showEmbed: bool,
     *     openExternalTab: bool,
     *     useEmbeddedMeeting: bool,
     *     embeddedJoinUrl: ?string,
     *     embeddedMeetingError: ?string
     * }
     */
    protected function resolveLectureMedia(Course $course, $user, bool $meetingAvailable): array
    {
        $useEmbedded = $course->usesEmbeddedMeeting();
        $embeddedJoinUrl = null;
        $embeddedMeetingError = null;

        if ($useEmbedded && $meetingAvailable) {
            try {
                $service = app(PrivateCourseMeetingService::class);
                if (! $service->usesEmbeddedMeetings()) {
                    $base = (string) config('services.meeting.base_url', '');
                    $embeddedMeetingError = 'الاجتماعات المضمّنة مفعّلة لكن إعدادات MEETING_* غير مكتملة في البيئة.'
                        .' (base='.($base !== '' ? parse_url($base, PHP_URL_HOST) : 'missing').')';
                } else {
                    $embeddedJoinUrl = $service->joinUrlForUser($course, $user);
                    $course->refresh();
                    if (! $embeddedJoinUrl) {
                        $embeddedMeetingError = 'تعذّر إنشاء رابط الانضمام للاجتماع المضمّن.';
                    }
                }
            } catch (Throwable $e) {
                Log::warning('[MEETING] join URL failed', [
                    'course_id' => $course->id,
                    'user_id' => $user?->id,
                    'meeting_base' => config('services.meeting.base_url'),
                    'error' => $e->getMessage(),
                ]);
                // Always surface a short technical hint — this feature is experimental.
                $hint = $this->summarizeMeetingError($e->getMessage());
                $embeddedMeetingError = 'خدمة الاجتماعات غير متاحة حالياً. حاول لاحقاً.'
                    .($hint !== '' ? ' — '.$hint : '');
            }
        }

        $meeting = MeetingLink::analyze($course->online_link);
        if ($useEmbedded) {
            $meeting['platform'] = 'embedded';
            $meeting['platform_label'] = 'اجتماع مضمّن';
        }

        $youtubeEmbed = $useEmbedded ? null : YouTubeLive::embedUrl($course->online_link);

        // Chromium blocks mic/camera in cross-origin iframes when the academy page is HTTP.
        // Still allow embedding when the join URL is HTTPS (e.g. ngrok) so the room can load.
        $parentSecure = request()->secure();
        $joinIsHttps = is_string($embeddedJoinUrl)
            && str_starts_with(strtolower($embeddedJoinUrl), 'https://');
        $embeddedInsecureParent = $useEmbedded && ! $parentSecure;

        if ($useEmbedded) {
            $showEmbed = $parentSecure || $joinIsHttps || filled($embeddedMeetingError) || ! $meetingAvailable;
            $openExternalTab = $embeddedInsecureParent && filled($embeddedJoinUrl) && ! $joinIsHttps;
            if ($openExternalTab) {
                $showEmbed = false;
            }
        } else {
            $showEmbed = $youtubeEmbed !== null;
            $openExternalTab = ! $showEmbed && filled($course->online_link);
        }

        return [
            'meeting' => $meeting,
            'youtubeEmbed' => $youtubeEmbed,
            'showEmbed' => $showEmbed,
            'openExternalTab' => $openExternalTab,
            'useEmbeddedMeeting' => $useEmbedded,
            'embeddedJoinUrl' => $embeddedJoinUrl,
            'embeddedMeetingError' => $embeddedMeetingError,
            'embeddedInsecureParent' => $embeddedInsecureParent,
        ];
    }

    public function messages(Request $request, Course $course)
    {
        abort_unless($course->canAccessLectureChat(Auth::user()), 403);

        $afterId = (int) $request->query('after_id', 0);
        $canModerate = $course->canModerateChat(Auth::user());
        $userId = (int) Auth::id();

        if ($afterId > 0) {
            $query = CourseChatMessage::with('user:id,name,role,avatar')
                ->where('course_id', $course->id)
                ->where('id', '>', $afterId)
                ->orderBy('id');
            if (! $canModerate) {
                // Others' hidden messages stay invisible; authors still see their own.
                $query->where(function ($q) use ($userId) {
                    $q->where('is_hidden', false)
                        ->orWhere('user_id', $userId);
                });
            }
            $messages = $query->limit(100)->get();
        } else {
            $query = CourseChatMessage::with('user:id,name,role,avatar')
                ->where('course_id', $course->id)
                ->orderByDesc('id')
                ->limit(100);
            if (! $canModerate) {
                $query->where(function ($q) use ($userId) {
                    $q->where('is_hidden', false)
                        ->orWhere('user_id', $userId);
                });
            }
            $messages = $query->get()->sortBy('id')->values();
        }

        $payload = $messages->map(fn ($m) => $this->formatMessage($m, $canModerate));

        $blockedIds = [];
        if ($canModerate) {
            $blockedIds = CourseChatBlock::where('course_id', $course->id)->pluck('user_id')->all();
        }

        $purgeIds = [];
        $ownHidden = [];
        if (! $canModerate) {
            $purgeIds = CourseChatMessage::query()
                ->where('course_id', $course->id)
                ->where('is_hidden', true)
                ->where('user_id', '!=', $userId)
                ->pluck('id')
                ->all();

            $ownHidden = CourseChatMessage::with('user:id,name,role,avatar')
                ->where('course_id', $course->id)
                ->where('user_id', $userId)
                ->where('is_hidden', true)
                ->orderBy('id')
                ->get()
                ->map(fn ($m) => $this->formatMessage($m, false))
                ->values()
                ->all();
        }

        return response()->json([
            'messages' => $payload->values(),
            'blocked_user_ids' => $blockedIds,
            'purge_ids' => $purgeIds,
            'own_hidden' => $ownHidden,
            'is_blocked' => $course->isUserChatBlocked(Auth::id()),
            'chat_locked' => (bool) $course->chat_locked_for_trainees,
            'can_send' => $course->canSendChatMessage(Auth::user()),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function storeMessage(Request $request, Course $course)
    {
        abort_unless($course->canAccessLectureChat(Auth::user()), 403);

        if ($course->canModerateChat(Auth::user()) === false && $course->isUserChatBlocked(Auth::id())) {
            return response()->json(['message' => 'تم حظرك من المشاركة في النقاش.'], 403);
        }

        if (!$course->canModerateChat(Auth::user()) && $course->chat_locked_for_trainees) {
            return response()->json(['message' => 'تم إيقاف إرسال الرسائل من قبل المحاضر.'], 403);
        }

        // Non-moderators must be attended
        if (!$course->canModerateChat(Auth::user())) {
            $attended = Payment::where('course_id', $course->id)
                ->where('user_id', Auth::id())
                ->where('is_attended', true)
                ->exists();
            if (!$attended) {
                return response()->json(['message' => 'النقاش متاح للحضور المسجلين فقط.'], 403);
            }
        }

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ], [
            'body.required' => 'اكتب رسالة أولاً',
            'body.max' => 'الرسالة طويلة جداً (حد أقصى 1000 حرف)',
        ]);

        $body = trim(strip_tags($data['body']));
        if ($body === '') {
            return response()->json(['message' => 'اكتب رسالة أولاً'], 422);
        }

        $message = CourseChatMessage::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'body' => $body,
        ]);
        $message->load('user:id,name,role,avatar');

        $this->safeBroadcast(
            CourseChatMessageCreated::fromMessage(
                $message,
                $this->formatMessageForBroadcast($message)
            )
        );

        return response()->json([
            'message' => $this->formatMessage($message, $course->canModerateChat(Auth::user())),
        ], 201);
    }

    public function hideMessage(Course $course, CourseChatMessage $message)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);
        abort_unless((int) $message->course_id === (int) $course->id, 404);

        $message->update([
            'is_hidden' => true,
            'hidden_by' => Auth::id(),
            'hidden_at' => now(),
        ]);

        $fresh = $message->fresh('user');
        $this->safeBroadcast(new CourseChatMessageUpdated(
            (int) $course->id,
            $this->formatMessageForBroadcast($fresh)
        ));

        return response()->json(['ok' => true, 'message' => $this->formatMessage($fresh, true)]);
    }

    public function unhideMessage(Course $course, CourseChatMessage $message)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);
        abort_unless((int) $message->course_id === (int) $course->id, 404);

        $message->update([
            'is_hidden' => false,
            'hidden_by' => null,
            'hidden_at' => null,
        ]);

        $fresh = $message->fresh('user');
        $this->safeBroadcast(new CourseChatMessageUpdated(
            (int) $course->id,
            $this->formatMessageForBroadcast($fresh)
        ));

        return response()->json(['ok' => true, 'message' => $this->formatMessage($fresh, true)]);
    }

    public function blockUser(Request $request, Course $course)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);

        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $targetId = (int) $data['user_id'];
        if ($targetId === (int) Auth::id()) {
            return response()->json(['message' => 'لا يمكن حظر نفسك.'], 422);
        }

        $target = \App\Models\User::findOrFail($targetId);
        if ($target->isAdmin() || $target->managesCourse($course)) {
            return response()->json(['message' => 'لا يمكن حظر المحاضر أو الإدارة.'], 422);
        }

        CourseChatBlock::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $targetId],
            ['blocked_by' => Auth::id(), 'reason' => $data['reason'] ?? null]
        );

        $this->safeBroadcast(new CourseChatUserModerationChanged((int) $course->id, $targetId, true));

        return response()->json(['ok' => true]);
    }

    public function unblockUser(Course $course, int $userId)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);

        CourseChatBlock::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->delete();

        $this->safeBroadcast(new CourseChatUserModerationChanged((int) $course->id, (int) $userId, false));

        return response()->json(['ok' => true]);
    }

    /**
     * Toggle whether trainees may send chat messages (moderators stay allowed).
     */
    public function toggleChatLock(Request $request, Course $course)
    {
        abort_unless($course->canModerateChat(Auth::user()), 403);

        $data = $request->validate([
            'locked' => 'required|boolean',
        ]);

        $course->update([
            'chat_locked_for_trainees' => (bool) $data['locked'],
        ]);

        $this->safeBroadcast(new CourseChatLockToggled(
            (int) $course->id,
            (bool) $course->chat_locked_for_trainees
        ));

        return response()->json([
            'ok' => true,
            'chat_locked' => (bool) $course->chat_locked_for_trainees,
        ]);
    }

    /**
     * Post-course (or anytime) archived discussion page.
     */
    public function archive(Course $course)
    {
        abort_unless($course->canAccessLectureChat(Auth::user()), 403);

        $canModerate = $course->canModerateChat(Auth::user());
        $chatLocked = (bool) $course->chat_locked_for_trainees;
        $payment = Payment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('dashboard.courses.chat-archive', compact('course', 'canModerate', 'payment', 'chatLocked'));
    }

    protected function formatMessage(CourseChatMessage $m, bool $canModerate, ?int $viewerId = null): array
    {
        $user = $m->user;
        $name = trim((string) ($user->name ?? 'مستخدم'));
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: ['م'];
        $first = mb_strtoupper(mb_substr($parts[0], 0, 1));
        $last = count($parts) > 1
            ? mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1))
            : '';
        $initials = $first . $last;

        $avatar = $user?->avatar
            ? asset('storage/' . ltrim($user->avatar, '/'))
            : null;

        $palette = [
            '#e11d48', '#ea580c', '#ca8a04', '#16a34a',
            '#0891b2', '#2563eb', '#7c3aed', '#db2777',
            '#0d9488', '#4f46e5', '#c026d3', '#dc2626',
        ];
        $color = $palette[((int) ($user?->id ?? $m->user_id)) % count($palette)];
        $viewerId = $viewerId ?? (int) Auth::id();

        return [
            'id' => $m->id,
            'body' => $m->body,
            'user_id' => $m->user_id,
            'user_name' => $name !== '' ? $name : 'مستخدم',
            'user_role' => $user->role ?? null,
            'user_avatar' => $avatar,
            'user_initials' => $initials !== '' ? $initials : 'م',
            'user_color' => $color,
            'is_mine' => (int) $m->user_id === (int) $viewerId,
            'is_hidden' => (bool) $m->is_hidden,
            'created_at' => $m->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            'created_at_human' => $m->created_at?->diffForHumans(),
            'can_moderate' => $canModerate,
        ];
    }

    /**
     * Neutral payload for broadcast (clients set is_mine locally).
     *
     * @return array<string, mixed>
     */
    protected function formatMessageForBroadcast(CourseChatMessage $m): array
    {
        $payload = $this->formatMessage($m, true, 0);
        $payload['is_mine'] = false;
        $payload['can_moderate'] = false;

        return $payload;
    }

    /**
     * Short, safe hint for the lecture UI (no secrets).
     */
    protected function summarizeMeetingError(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        if (str_contains($raw, 'ngrok')) {
            return 'تحذير متصفح ngrok يمنع iframe — يُفتح الاجتماع في تبويب جديد، أو استخدم نفقاً بدون صفحة تحذير.';
        }
        if (str_contains($raw, 'cURL error 28') || str_contains(strtolower($raw), 'timed out')) {
            return 'انتهت مهلة الاتصال بخادم الاجتماعات (تحقق أن MEETING_BASE_URL يصل من هذا السيرفر، مثل رابط ngrok).';
        }
        if (str_contains($raw, 'cURL error 6') || str_contains(strtolower($raw), 'could not resolve')) {
            return 'تعذّر حل اسم مضيف MEETING_BASE_URL من السيرفر.';
        }
        if (str_contains($raw, 'cURL error 7') || str_contains(strtolower($raw), 'failed to connect')) {
            return 'تعذّر الاتصال بـ MEETING_BASE_URL (السيرفر لا يصل للعنوان، غالباً IP محلي من الاستضافة).';
        }
        if (str_contains($raw, 'cURL error 60') || str_contains(strtolower($raw), 'ssl certificate')) {
            return 'مشكلة شهادة SSL — جرّب MEETING_SSL_VERIFY=false محلياً فقط.';
        }
        if (preg_match('/HTTP (\d{3})/i', $raw, $m)) {
            if ($m[1] === '409') {
                return 'تعارض 409: الاجتماع منتهٍ أو ممتلئ أو externalId مكرر — سيتم إنشاء اجتماع جديد تلقائياً بعد التحديث.';
            }

            return 'ردّ API بالخطأ HTTP '.$m[1].' (مفاتيح HMAC أو حالة الاجتماع).';
        }
        if (str_contains($raw, 'not configured')) {
            return 'MEETING_BASE_URL / MEETING_API_KEY / MEETING_API_SECRET غير مكتملة على هذا السيرفر.';
        }

        // Keep last path segment of URL noise out; cap length.
        $clean = preg_replace('/https?:\/\/[^\s]+/i', '[url]', $raw) ?? $raw;

        return mb_substr($clean, 0, 180);
    }

    /**
     * Broadcast without failing the HTTP request when Reverb is down.
     */
    protected function safeBroadcast(object $event): void
    {
        try {
            broadcast($event)->toOthers();
        } catch (Throwable $e) {
            Log::warning('[CHAT] broadcast failed', [
                'event' => $event::class,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
