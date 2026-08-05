<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Setting;
use App\Models\TrainerCashoutRequest;
use App\Models\TrainerPaymentProfile;
use App\Models\User;
use App\Support\TrainerProfitWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TrainerCashoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $this->authorizeTrainer();

        $profile = TrainerPaymentProfile::query()->where('user_id', $user->id)->first();
        abort_unless($profile && $profile->isComplete(), 422, __('messages.trainer_cashout_profile_incomplete'));

        $min = Setting::academyTrainerCashoutMinimum();
        $max = Setting::academyTrainerCashoutMaximum();
        $available = TrainerProfitWallet::available($user);

        $data = $request->validate([
            'amount' => ['required', 'numeric', "min:{$min}", "max:{$max}"],
        ], [
            'amount.min' => __('messages.trainer_cashout_amount_min', ['min' => number_format($min, 2)]),
            'amount.max' => __('messages.trainer_cashout_amount_max', ['max' => number_format($max, 2)]),
        ]);

        $amount = round((float) $data['amount'], 2);

        if ($amount > $available) {
            return back()->withInput()->withErrors([
                'amount' => __('messages.trainer_cashout_amount_exceeds_available', ['available' => number_format($available, 2)]),
            ]);
        }

        $method = $profile->method;
        $snapshot = $method && $method->isBankTransfer()
            ? [
                'type' => 'bank',
                'bank_account_name' => $profile->bank_account_name,
                'bank_iban' => $profile->bank_iban,
                'bank_name' => $profile->bank_name,
                'bank_country' => $profile->bank_country,
            ]
            : [
                'type' => 'custom',
                'method_name' => $method?->title('ar'),
                'field_values' => $profile->field_values,
            ];

        $cashout = TrainerCashoutRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'AED',
            'status' => TrainerCashoutRequest::STATUS_PENDING_ADMIN,
            'payout_method_id' => $profile->payout_method_id,
            'payout_snapshot' => $snapshot,
            'available_balance_snapshot' => $available,
        ]);

        if (! $profile->isLocked()) {
            $profile->update(['locked_at' => now()]);
        }

        $this->notifyAdmins(
            __('messages.trainer_cashout_admin_notify_title'),
            __('messages.trainer_cashout_admin_notify_body', [
                'name' => $user->name,
                'amount' => number_format($amount, 2),
            ]),
            route('dashboard.academy.cashouts.index')
        );

        return back()->with('success', __('messages.trainer_cashout_submitted'));
    }

    public function confirm(TrainerCashoutRequest $cashout)
    {
        abort_unless((int) $cashout->user_id === (int) Auth::id(), 403);
        abort_unless($cashout->canTrainerConfirm(), 422);

        $cashout->update([
            'status' => TrainerCashoutRequest::STATUS_PAID,
            'trainer_confirmed_at' => now(),
        ]);

        return back()->with('success', __('messages.trainer_cashout_confirmed'));
    }

    public function adminIndex(Request $request)
    {
        $this->authorizeAdmin();

        $status = trim((string) $request->query('status', ''));

        $query = TrainerCashoutRequest::query()
            ->with(['user', 'payoutMethod', 'screenshots'])
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $cashouts = $query->paginate(20)->withQueryString();

        return view('dashboard.academy.cashouts.index', compact('cashouts', 'status'));
    }

    public function uploadScreenshot(Request $request, TrainerCashoutRequest $cashout)
    {
        $this->authorizeAdmin();
        abort_unless($cashout->canUploadScreenshots(), 422, __('messages.trainer_cashout_upload_locked'));

        $data = $request->validate([
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'kind' => 'required|in:pending,success,fail',
        ]);

        $path = $data['screenshot']->store('trainer-cashouts', 'public');

        $cashout->screenshots()->create([
            'path' => $path,
            'kind' => $data['kind'],
            'uploaded_by' => Auth::id(),
        ]);

        if ($cashout->status === TrainerCashoutRequest::STATUS_PENDING_ADMIN) {
            $cashout->update(['status' => TrainerCashoutRequest::STATUS_PROCESSING]);
        }

        return back()->with('success', __('messages.trainer_cashout_screenshot_uploaded'));
    }

    public function showScreenshotFile(\App\Models\TrainerCashoutScreenshot $screenshot)
    {
        $screenshot->loadMissing('request');
        abort_unless($screenshot->request, 404);
        $this->authorizeScreenshotAccess($screenshot->request);
        abort_unless($screenshot->existsOnDisk(), 404);

        return Storage::disk('public')->response($screenshot->path);
    }

    public function markReadyForTrainer(TrainerCashoutRequest $cashout)
    {
        $this->authorizeAdmin();
        abort_unless($cashout->canMarkReadyForTrainer(), 422, __('messages.trainer_cashout_mark_ready_blocked'));

        $cashout->update([
            'status' => TrainerCashoutRequest::STATUS_PENDING_TRAINER_CONFIRM,
            'trainer_confirm_due_at' => now()->addDay(),
            'trainer_confirmed_at' => null,
        ]);

        $cashout->loadMissing('user');
        if ($cashout->user) {
            AppNotification::notify(
                $cashout->user->id,
                __('messages.trainer_cashout_ready_notify_title'),
                __('messages.trainer_cashout_ready_notify_body', ['amount' => number_format((float) $cashout->amount, 2)]),
                route('dashboard.academy.my-profits'),
                'fa-money-bill-wave',
                'warning'
            );
        }

        return back()->with('success', __('messages.trainer_cashout_marked_ready'));
    }

    public function reject(Request $request, TrainerCashoutRequest $cashout)
    {
        $this->authorizeAdmin();
        abort_unless($cashout->canReject(), 422);

        $data = $request->validate([
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $cashout->update([
            'status' => TrainerCashoutRequest::STATUS_REJECTED,
            'admin_note' => $data['admin_note'] ?? $cashout->admin_note,
        ]);

        $cashout->loadMissing('user');
        if ($cashout->user) {
            AppNotification::notify(
                $cashout->user->id,
                __('messages.trainer_cashout_rejected_notify_title'),
                __('messages.trainer_cashout_rejected_notify_body', ['amount' => number_format((float) $cashout->amount, 2)]),
                route('dashboard.academy.my-profits'),
                'fa-circle-xmark',
                'error'
            );
        }

        return back()->with('success', __('messages.trainer_cashout_rejected'));
    }

    protected function authorizeScreenshotAccess(TrainerCashoutRequest $cashout): void
    {
        $user = Auth::user();
        abort_unless(
            $user && ($user->isAdmin() || (int) $cashout->user_id === (int) $user->id),
            403
        );
    }

    protected function notifyAdmins(string $title, string $message, string $url): void
    {
        User::query()->admins()->get()->each(function (User $admin) use ($title, $message, $url) {
            try {
                AppNotification::notify($admin->id, $title, $message, $url, 'fa-money-bill-wave', 'warning');
            } catch (\Throwable $e) {
                Log::warning('[TRAINER_CASHOUT] failed to notify admin', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    protected function authorizeTrainer(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isTrainer(), 403);

        return $user;
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user() instanceof User && Auth::user()->isAdmin(), 403);
    }
}
