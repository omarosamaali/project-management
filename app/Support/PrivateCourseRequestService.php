<?php

namespace App\Support;

use App\Exceptions\UserFacingException;
use App\Models\AppNotification;
use App\Models\Course;
use App\Models\Payment;
use App\Models\PrivateCourseRefund;
use App\Models\PrivateCourseRefundScreenshot;
use App\Models\PrivateCourseRequest;
use App\Models\PrivateCourseRequestEvent;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrivateCourseRequestService
{
    private const OPEN_STATUSES = [
        PrivateCourseRequest::STATUS_PENDING_TRAINER,
        PrivateCourseRequest::STATUS_DATES_PROPOSED,
        PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
        PrivateCourseRequest::STATUS_AWAITING_PAYMENT,
    ];

    public function createRequest(Course $source, User $trainee, ?string $note = null): PrivateCourseRequest
    {
        if (! $source->allows_private_requests) {
            throw new UserFacingException(__('messages.private_request_not_allowed'));
        }

        if ($source->isPrivate() || filled($source->private_of_course_id)) {
            throw new UserFacingException(__('messages.private_request_source_invalid'));
        }

        $existingOpen = PrivateCourseRequest::query()
            ->where('source_course_id', $source->id)
            ->where('trainee_id', $trainee->id)
            ->whereIn('status', self::OPEN_STATUSES)
            ->latest('id')
            ->first();

        if ($existingOpen) {
            throw new UserFacingException(
                __('messages.private_request_duplicate_open'),
                route('private-requests.show', $existingOpen),
            );
        }

        $privatePrice = $source->private_course_price ?? $source->price;

        $request = PrivateCourseRequest::create([
            'source_course_id' => $source->id,
            'trainee_id' => $trainee->id,
            'trainer_id' => $source->trainer_id,
            'private_price' => $privatePrice,
            'status' => PrivateCourseRequest::STATUS_PENDING_TRAINER,
            'trainee_note' => $note,
            'expires_at' => now()->addDays(3),
        ]);

        $this->addEvent($request, $trainee, 'created', $note);

        $courseName = $source->name_ar ?: $source->name_en;
        $url = $this->privateRequestUrl($request);

        $this->notifyUser(
            $trainee,
            'طلب دورة خاصة',
            "تم استلام طلبك للدورة الخاصة «{$courseName}». سيتواصل معك المحاضر لتحديد المواعيد.",
            $url,
            'fa-user-graduate',
            'info',
        );

        if ($source->trainer) {
            $this->notifyUser(
                $source->trainer,
                'طلب دورة خاصة جديد',
                "طلب متدرب دورة خاصة للدورة «{$courseName}». يرجى مراجعة الطلب وتحديد المواعيد.",
                $url,
                'fa-chalkboard-teacher',
                'warning',
            );
        } else {
            $this->notifyAdmins(
                'طلب دورة خاصة بدون محاضر',
                "طلب متدرب دورة خاصة للدورة «{$courseName}» (بدون محاضر معيّن). يرجى مراجعة الطلب.",
                $url,
            );
        }

        return $request->fresh(['sourceCourse', 'trainee', 'trainer']);
    }

    public function approveWithDates(
        PrivateCourseRequest $req,
        User $actor,
        Carbon $start,
        Carbon $end,
        ?string $note = null,
    ): void {
        if (! in_array($req->status, [
            PrivateCourseRequest::STATUS_PENDING_TRAINER,
            PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
        ], true)) {
            throw new UserFacingException(__('messages.private_request_invalid_status'));
        }

        if ($end->lte($start)) {
            throw new UserFacingException(__('messages.private_request_invalid_dates'));
        }

        $req->update([
            'proposed_start_at' => $start,
            'proposed_end_at' => $end,
            'dates_accepted_by_trainer_at' => now(),
            'dates_accepted_by_trainee_at' => null,
            'status' => PrivateCourseRequest::STATUS_DATES_PROPOSED,
            'trainer_responded_at' => now(),
        ]);

        $this->addEvent($req, $actor, 'dates_proposed', $note, [
            'proposed_start_at' => $start->toIso8601String(),
            'proposed_end_at' => $end->toIso8601String(),
        ]);

        $req->loadMissing(['sourceCourse', 'trainee']);
        $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
        $datesLabel = $start->format('Y-m-d H:i') . ' — ' . $end->format('Y-m-d H:i');
        $url = $this->privateRequestUrl($req);

        if ($req->trainee) {
            $this->notifyUser(
                $req->trainee,
                'مواعيد مقترحة لدورتك الخاصة',
                "اقترح المحاضر مواعيد للدورة «{$courseName}»: {$datesLabel}. يرجى قبول المواعيد أو طلب تعديل.",
                $url,
                'fa-calendar-check',
                'info',
            );
        }
    }

    public function traineeAcceptDates(PrivateCourseRequest $req, User $trainee): void
    {
        if ((int) $req->trainee_id !== (int) $trainee->id) {
            abort(403, __('messages.private_request_forbidden'));
        }

        if ($req->status !== PrivateCourseRequest::STATUS_DATES_PROPOSED) {
            throw new UserFacingException(__('messages.private_request_invalid_status'));
        }

        if (! $req->proposed_start_at || ! $req->proposed_end_at) {
            throw new UserFacingException(__('messages.private_request_no_proposed_dates'));
        }

        $req->update([
            'dates_accepted_by_trainee_at' => now(),
            'status' => PrivateCourseRequest::STATUS_AWAITING_PAYMENT,
            'payment_due_at' => now()->addDay(),
        ]);

        $this->addEvent($req, $trainee, 'dates_accepted_by_trainee');

        $req->loadMissing(['sourceCourse', 'trainer', 'trainee']);
        $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
        $url = $this->privateRequestUrl($req);
        $payNote = 'لديك 24 ساعة لإتمام الدفع.';

        if ($req->trainee) {
            $this->notifyUser(
                $req->trainee,
                'أكمل الدفع للدورة الخاصة',
                "تم قبول المواعيد للدورة «{$courseName}». {$payNote}",
                $url,
                'fa-credit-card',
                'warning',
            );
        }

        $owner = $req->trainer;
        if ($owner) {
            $this->notifyUser(
                $owner,
                'بانتظار دفع الدورة الخاصة',
                "قبل المتدرب المواعيد للدورة «{$courseName}». {$payNote}",
                $url,
                'fa-hourglass-half',
                'info',
            );
        }
    }

    public function traineeRequestDateChange(
        PrivateCourseRequest $req,
        User $trainee,
        Carbon $start,
        Carbon $end,
        ?string $message = null,
    ): void {
        if ((int) $req->trainee_id !== (int) $trainee->id) {
            abort(403, __('messages.private_request_forbidden'));
        }

        if ($req->status !== PrivateCourseRequest::STATUS_DATES_PROPOSED) {
            throw new UserFacingException(__('messages.private_request_invalid_status'));
        }

        if ($end->lte($start)) {
            throw new UserFacingException(__('messages.private_request_invalid_dates'));
        }

        $req->update([
            'status' => PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
            'dates_accepted_by_trainee_at' => null,
            'dates_accepted_by_trainer_at' => null,
            'proposed_start_at' => $start,
            'proposed_end_at' => $end,
            'expires_at' => now()->addDays(3),
        ]);

        $this->addEvent($req, $trainee, 'dates_change_requested', $message, [
            'proposed_start_at' => $start->toIso8601String(),
            'proposed_end_at' => $end->toIso8601String(),
        ]);

        $req->loadMissing(['sourceCourse', 'trainer']);
        $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
        $datesLabel = $start->format('Y-m-d H:i') . ' — ' . $end->format('Y-m-d H:i');
        $url = $this->privateRequestUrl($req);

        $owner = $req->trainer;
        if ($owner) {
            $this->notifyUser(
                $owner,
                'طلب تعديل مواعيد دورة خاصة',
                "طلب المتدرب مواعيد جديدة للدورة «{$courseName}»: {$datesLabel}.",
                $url,
                'fa-calendar-alt',
                'warning',
            );
        } else {
            $this->notifyAdmins(
                'طلب تعديل مواعيد دورة خاصة',
                "طلب المتدرب مواعيد جديدة للدورة «{$courseName}»: {$datesLabel}.",
                $url,
            );
        }
    }

    public function reject(PrivateCourseRequest $req, User $actor, string $reason): void
    {
        if (! in_array($req->status, [
            PrivateCourseRequest::STATUS_PENDING_TRAINER,
            PrivateCourseRequest::STATUS_DATES_PROPOSED,
            PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
        ], true)) {
            throw new UserFacingException(__('messages.private_request_invalid_status'));
        }

        $req->update([
            'status' => PrivateCourseRequest::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'trainer_responded_at' => now(),
        ]);

        $this->addEvent($req, $actor, 'rejected', $reason);

        $req->loadMissing(['sourceCourse', 'trainee']);
        $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
        $url = $this->privateRequestUrl($req);

        if ($req->trainee) {
            $this->notifyUser(
                $req->trainee,
                'تم رفض طلب الدورة الخاصة',
                "تم رفض طلبك للدورة «{$courseName}». السبب: {$reason}",
                $url,
                'fa-times-circle',
                'danger',
            );
        }
    }

    public function block(PrivateCourseRequest $req, User $actor, string $reason): void
    {
        if (! $actor->isAdmin()) {
            abort(403, __('messages.private_request_admin_only'));
        }

        if ($req->status === PrivateCourseRequest::STATUS_PAID) {
            throw new UserFacingException(__('messages.private_request_cannot_block_paid'));
        }

        $req->update([
            'status' => PrivateCourseRequest::STATUS_BLOCKED,
            'blocked_at' => now(),
            'blocked_by' => $actor->id,
            'block_reason' => $reason,
        ]);

        $this->addEvent($req, $actor, 'blocked', $reason);

        $req->loadMissing(['sourceCourse', 'trainee']);
        $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
        $url = $this->privateRequestUrl($req);

        if ($req->trainee) {
            $this->notifyUser(
                $req->trainee,
                'تم إيقاف طلب الدورة الخاصة',
                "تم إيقاف طلبك للدورة «{$courseName}». {$reason}",
                $url,
                'fa-ban',
                'danger',
            );
        }
    }

    public function markPaidAndClone(PrivateCourseRequest $req, Payment $payment): Course
    {
        if ($req->status !== PrivateCourseRequest::STATUS_AWAITING_PAYMENT) {
            throw new UserFacingException(__('messages.private_request_invalid_status'));
        }

        $this->assertPaymentStillAllowed($req);

        return DB::transaction(function () use ($req, $payment) {
            $req->loadMissing(['sourceCourse', 'trainee']);
            $source = $req->sourceCourse;

            if (! $source) {
                throw new UserFacingException(__('messages.private_request_source_missing'));
            }

            $start = Carbon::parse($req->proposed_start_at);
            $end = Carbon::parse($req->proposed_end_at);
            $restDays = $source->rest_days ?? [];

            $clone = Course::create([
                'name_ar' => trim(($source->name_ar ?: $source->name_en) . ' (خاص)'),
                'name_en' => trim(($source->name_en ?: $source->name_ar) . ' (Private)'),
                'location_type' => 'private',
                'price' => $req->private_price,
                'trainer_id' => $req->trainer_id ?: $source->trainer_id,
                'course_category_id' => $source->course_category_id,
                'description_ar' => $source->description_ar,
                'description_en' => $source->description_en,
                'requirements' => $source->requirements,
                'features' => $source->features,
                'suitable_for' => $source->suitable_for,
                'levels' => $source->levels,
                'main_image' => $source->main_image,
                'images' => $source->images,
                'video' => $source->video,
                'total_video_duration_seconds' => $source->total_video_duration_seconds,
                'online_link' => null,
                'allows_private_requests' => false,
                'private_of_course_id' => $source->id,
                'start_date' => $start,
                'end_date' => $end,
                // Required non-null column (enrollment deadline); private seats are already paid.
                'last_date' => $start,
                'count_days' => Course::computeCourseDays($start, $end, $restDays),
                'rest_days' => $restDays,
                'status' => 'active',
                'counter' => 1,
            ]);

            $payment->forceFill([
                'course_id' => $clone->id,
                'private_course_request_id' => $req->id,
            ])->save();

            if ($req->trainee && ! $clone->students()->where('user_id', $req->trainee_id)->exists()) {
                $clone->students()->attach($req->trainee_id, [
                    'price_paid' => $req->private_price,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
            }

            CourseProfitSplitter::applyToPayment($payment->fresh(), $clone);

            $req->update([
                'status' => PrivateCourseRequest::STATUS_PAID,
                'payment_id' => $payment->id,
                'private_course_id' => $clone->id,
            ]);

            $this->addEvent($req, $req->trainee, 'paid', null, [
                'payment_id' => $payment->id,
                'private_course_id' => $clone->id,
            ]);

            $courseName = $clone->name_ar ?: $clone->name_en;
            $courseUrl = $clone->publicUrl();
            $requestUrl = $this->privateRequestUrl($req);

            if ($req->trainee) {
                $this->notifyUser(
                    $req->trainee,
                    'تم تأكيد الدورة الخاصة',
                    "تم تأكيد الدفع للدورة «{$courseName}». يمكنك الانضمام للدورة من لوحة التحكم.",
                    $courseUrl,
                    'fa-check-circle',
                    'success',
                );
            }

            $owner = $req->trainer;
            if ($owner) {
                $this->notifyUser(
                    $owner,
                    'تم دفع الدورة الخاصة',
                    "أكمل المتدرب الدفع للدورة «{$courseName}». يرجى إعداد رابط الاجتماع قبل الموعد.",
                    $requestUrl,
                    'fa-link',
                    'info',
                );
            }

            return $clone;
        });
    }

    public function expireUnpaid(): int
    {
        $now = now();

        $requests = PrivateCourseRequest::query()
            ->where('status', PrivateCourseRequest::STATUS_AWAITING_PAYMENT)
            ->where(function ($query) use ($now) {
                $query
                    ->where(function ($q) use ($now) {
                        $q->whereNotNull('payment_due_at')
                            ->where('payment_due_at', '<', $now);
                    })
                    ->orWhere(function ($q) use ($now) {
                        // Confirmed schedule ended before payment succeeded.
                        $q->whereNotNull('proposed_end_at')
                            ->where('proposed_end_at', '<', $now);
                    });
            })
            ->with(['sourceCourse', 'trainee', 'trainer'])
            ->get();

        $count = 0;

        foreach ($requests as $req) {
            $schedulePassed = $req->proposed_end_at && $req->proposed_end_at->lt($now);
            $req->update(['status' => PrivateCourseRequest::STATUS_EXPIRED_UNPAID]);
            $this->addEvent($req, null, 'expired_unpaid', null, [
                'reason' => $schedulePassed ? 'schedule_passed' : 'payment_window',
            ]);

            $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
            $url = $this->privateRequestUrl($req);
            $message = $schedulePassed
                ? "انتهى موعد الدورة المقترح قبل إتمام الدفع لطلب «{$courseName}»، وتم إلغاء الطلب."
                : "انتهت مهلة الدفع (24 ساعة) لطلب الدورة الخاصة «{$courseName}».";

            if ($req->trainee) {
                $this->notifyUser($req->trainee, 'انتهت مهلة الدفع', $message, $url, 'fa-clock', 'warning');
            }

            if ($req->trainer) {
                $this->notifyUser($req->trainer, 'انتهت مهلة دفع دورة خاصة', $message, $url, 'fa-clock', 'info');
            }

            $count++;
        }

        return $count;
    }

    /**
     * Payment is blocked after the 24h window or after the confirmed schedule end.
     */
    public function assertPaymentStillAllowed(PrivateCourseRequest $req): void
    {
        if ($req->proposed_end_at && now()->greaterThan($req->proposed_end_at)) {
            throw new UserFacingException(__('messages.private_request_schedule_passed'));
        }

        if ($req->payment_due_at && now()->greaterThan($req->payment_due_at)) {
            throw new UserFacingException(__('messages.private_request_payment_expired'));
        }
    }

    public function paymentWindowExpired(PrivateCourseRequest $req): bool
    {
        if ($req->status !== PrivateCourseRequest::STATUS_AWAITING_PAYMENT) {
            return false;
        }

        if ($req->proposed_end_at && now()->greaterThan($req->proposed_end_at)) {
            return true;
        }

        if ($req->payment_due_at && now()->greaterThan($req->payment_due_at)) {
            return true;
        }

        return false;
    }

    public function expireBusy(): int
    {
        $requests = PrivateCourseRequest::query()
            ->whereIn('status', [
                PrivateCourseRequest::STATUS_PENDING_TRAINER,
                PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
            ])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['sourceCourse', 'trainee', 'trainer'])
            ->get();

        $count = 0;

        foreach ($requests as $req) {
            $req->update(['status' => PrivateCourseRequest::STATUS_EXPIRED_BUSY]);
            $this->addEvent($req, null, 'expired_busy');

            $courseName = $req->sourceCourse?->name_ar ?: $req->sourceCourse?->name_en;
            $url = $this->privateRequestUrl($req);
            $apology = "نعتذر، المحاضر مشغول ولم يرد على طلب الدورة الخاصة «{$courseName}» خلال المهلة المحددة.";

            if ($req->trainee) {
                $this->notifyUser($req->trainee, 'المحاضر مشغول', $apology, $url, 'fa-user-clock', 'warning');
            }

            if ($req->trainer) {
                $this->notifyUser(
                    $req->trainer,
                    'انتهت مهلة طلب دورة خاصة',
                    "انتهت مهلة الرد على طلب الدورة الخاصة «{$courseName}» بدون إجراء.",
                    $url,
                    'fa-exclamation-triangle',
                    'warning',
                );
            } elseif ($req->sourceCourse) {
                $this->notifyAdmins(
                    'انتهت مهلة طلب دورة خاصة',
                    "انتهت مهلة الرد على طلب الدورة الخاصة «{$courseName}» (بدون محاضر).",
                    $url,
                );
            }

            $count++;
        }

        return $count;
    }

    public function cancelMissingMeetingLinks(): int
    {
        $requests = PrivateCourseRequest::query()
            ->where('status', PrivateCourseRequest::STATUS_PAID)
            ->whereNotNull('private_course_id')
            ->with(['privateCourse', 'trainee', 'trainer', 'payment'])
            ->get()
            ->filter(function (PrivateCourseRequest $req) {
                $course = $req->privateCourse;
                if (! $course || $course->isCanceled()) {
                    return false;
                }

                if (filled($course->online_link)) {
                    return false;
                }

                if (! $course->start_date) {
                    return false;
                }

                return now()->greaterThanOrEqualTo(Carbon::parse($course->start_date));
            });

        $count = 0;

        foreach ($requests as $req) {
            DB::transaction(function () use ($req, &$count) {
                $course = $req->privateCourse;
                if (! $course) {
                    return;
                }

                $course->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'cancel_reason' => 'missing_meeting_link',
                ]);

                $req->update(['status' => PrivateCourseRequest::STATUS_CANCELED_NO_MEETING]);
                $this->addEvent($req, null, 'canceled_no_meeting');

                $refund = $this->createRefundForRequest($req);

                $courseName = $course->name_ar ?: $course->name_en;
                $url = $this->privateRequestUrl($req);
                $message = "تم إلغاء الدورة الخاصة «{$courseName}» لعدم توفر رابط الاجتماع. سيتم معالجة استرداد المبلغ.";

                if ($req->trainee) {
                    $this->notifyUser($req->trainee, 'إلغاء الدورة الخاصة', $message, $url, 'fa-video-slash', 'danger');
                }

                if ($req->trainer) {
                    $this->notifyUser(
                        $req->trainer,
                        'إلغاء دورة خاصة — رابط الاجتماع',
                        "تم إلغاء الدورة «{$courseName}» لعدم إضافة رابط الاجتماع قبل الموعد.",
                        $url,
                        'fa-link',
                        'danger',
                    );
                }

                Log::info('[PRIVATE_COURSE] canceled missing meeting link', [
                    'request_id' => $req->id,
                    'course_id' => $course->id,
                    'refund_id' => $refund->id,
                ]);

                $count++;
            });
        }

        return $count;
    }

    public function autoConfirmRefunds(): int
    {
        $refunds = PrivateCourseRefund::query()
            ->where('status', PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM)
            ->whereNotNull('trainee_confirm_due_at')
            ->where('trainee_confirm_due_at', '<=', now())
            ->whereNull('trainee_confirmed_at')
            ->whereHas('screenshots', function ($q) {
                $q->where('kind', PrivateCourseRefundScreenshot::KIND_SUCCESS);
            })
            ->with(['trainee', 'request.sourceCourse'])
            ->get();

        $count = 0;

        foreach ($refunds as $refund) {
            $refund->update([
                'status' => PrivateCourseRefund::STATUS_REFUNDED,
                'trainee_confirmed_at' => now(),
            ]);

            $req = $refund->request;
            if ($req) {
                $this->addEvent($req, null, 'refund_auto_confirmed', null, [
                    'refund_id' => $refund->id,
                ]);
            }

            $amount = number_format((float) $refund->amount, 2);
            $currency = $refund->currency ?: 'AED';
            $message = "تم إغلاق طلب الاسترداد تلقائياً بعد 24 ساعة بدون تأكيد. المبلغ: {$amount} {$currency}.";

            if ($refund->trainee) {
                $this->notifyUser(
                    $refund->trainee,
                    'تم إغلاق الاسترداد',
                    $message,
                    $this->privateRequestUrl($req),
                    'fa-money-bill-wave',
                    'info',
                );
            }

            $this->notifyAdmins(
                'تأكيد استرداد تلقائي',
                $message,
                $this->privateRequestUrl($req),
            );

            $count++;
        }

        return $count;
    }

    public function markRefundReadyForTrainee(PrivateCourseRefund $refund, User $admin): void
    {
        if (! $refund->canMarkReadyForTrainee()) {
            throw new \RuntimeException(__('messages.private_refund_mark_ready_blocked'));
        }

        $refund->update([
            'status' => PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM,
            'admin_id' => $admin->id,
            'trainee_confirm_due_at' => now()->addDay(),
            'trainee_confirmed_at' => null,
        ]);

        $refund->loadMissing(['trainee', 'request.sourceCourse']);

        if ($refund->request) {
            $this->addEvent($refund->request, $admin, 'refund_ready_for_trainee', null, [
                'refund_id' => $refund->id,
            ]);
        }

        $courseName = $refund->request?->sourceCourse?->name_ar
            ?: $refund->request?->sourceCourse?->name_en
            ?: '#'.$refund->id;
        $amount = number_format((float) $refund->amount, 2);
        $url = route('dashboard.academy.private-requests.trainee-index');

        if ($refund->trainee) {
            $this->notifyUser(
                $refund->trainee,
                'تأكيد استلام الاسترداد',
                "يرجى مراجعة إثباتات التحويل للدورة «{$courseName}» وتأكيد استلام مبلغ {$amount}.",
                $url,
                'fa-money-bill-wave',
                'warning',
            );
        }
    }

    public function createRefundForRequest(PrivateCourseRequest $req): PrivateCourseRefund
    {
        if ($req->refund()->exists()) {
            return $req->refund;
        }

        $req->loadMissing('payment');
        $payment = $req->payment;
        $amount = (float) ($payment?->original_price ?? $req->private_price ?? 0);

        return PrivateCourseRefund::create([
            'private_course_request_id' => $req->id,
            'payment_id' => $payment?->id,
            'trainee_id' => $req->trainee_id,
            'amount' => $amount,
            'currency' => $payment?->currency ?? 'AED',
            'status' => PrivateCourseRefund::STATUS_REQUIRED,
        ]);
    }

    public function addEvent(
        PrivateCourseRequest $req,
        ?User $actor,
        string $action,
        ?string $message = null,
        ?array $payload = null,
    ): PrivateCourseRequestEvent {
        return PrivateCourseRequestEvent::create([
            'private_course_request_id' => $req->id,
            'actor_id' => $actor?->id,
            'action' => $action,
            'message' => $message,
            'payload' => $payload,
        ]);
    }

    protected function privateRequestUrl(PrivateCourseRequest $req): string
    {
        return \App\Support\AppDomains::academyUrl('/private-requests/' . $req->id);
    }

    protected function notifyUser(
        User $user,
        string $title,
        string $message,
        ?string $url = null,
        string $icon = 'fa-bell',
        string $type = 'info',
    ): void {
        AppNotification::notify($user->id, $title, $message, $url, $icon, $type);

        try {
            $whatsapp = app(WhatsAppOTPService::class);

            if (filled($user->phone)) {
                $whatsapp->sendProjectNotification(
                    (string) $user->phone,
                    (string) $user->name,
                    $message,
                    $title,
                );
            } elseif (filled($user->email)) {
                $whatsapp->sendEmailNotification(
                    (string) $user->email,
                    (string) $user->name,
                    $title,
                    $message,
                );
            }
        } catch (\Throwable $e) {
            Log::error('[PRIVATE_COURSE] notification failed', [
                'user_id' => $user->id,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function notifyAdmins(string $title, string $message, ?string $url = null): void
    {
        User::admins()->each(function (User $admin) use ($title, $message, $url) {
            $this->notifyUser($admin, $title, $message, $url, 'fa-shield-alt', 'warning');
        });
    }
}
