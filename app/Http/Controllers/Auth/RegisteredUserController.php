<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\OTPController;
use App\Mail\TrainerPendingApprovalMail;
use App\Models\AppNotification;
use App\Models\CourseCategory;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use App\Support\AuthUi;
use App\Support\WatermarkedUpload;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        AuthUi::resolve($request->query('ui'));

        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view(AuthUi::view('register'), compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        AuthUi::resolve($request->input('ui', $request->query('ui')));
        $isAcademy = AuthUi::isAcademy();

        // Academy registrations are always personal accounts (no client / business flow).
        if ($isAcademy) {
            $request->merge(['account_type' => 'personal']);
        }

        $isTrainer = $request->input('role') === 'trainer';

        $rules = [
            'account_type' => $isAcademy
                ? ['required', 'in:personal']
                : ['required', 'in:personal,business'],
            'role' => $isAcademy
                ? ['required', 'in:trainer,trainee']
                : ['required', 'in:client,trainer,trainee'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:10'],
            'company_name' => ['required_if:account_type,business', 'nullable', 'string', 'max:255'],
            'company_logo' => ['required_if:account_type,business', 'nullable', 'image', 'max:5120'],
            'course_category_id' => ['exclude_unless:role,trainer', 'required', 'exists:course_categories,id'],
            'teaching_language' => ['exclude_unless:role,trainer', 'nullable', 'in:ar,en'],
            'avatar' => ['exclude_unless:role,trainer', 'required', 'file', 'image', 'max:2048'],
            'linkedin_url' => [
                'exclude_unless:role,trainer',
                'required',
                'url',
                'max:500',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.+/i',
            ],
            'teaching_sample' => ['exclude_unless:role,trainer', 'nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-m4v', 'max:307200'],
            'trainer_bio' => ['exclude_unless:role,trainer', 'required', 'string', 'min:120', 'max:2000'],
            'accept_terms' => ['exclude_unless:role,trainer', 'accepted'],
        ];

        try {
            $request->validate($rules, [
                'account_type.required' => 'يرجى اختيار نوع الحساب.',
                'account_type.in' => 'نوع الحساب غير صالح.',
                'role.required' => 'يرجى اختيار نوع العضوية.',
                'role.in' => 'نوع العضوية غير صالح.',
                'company_name.required_if' => 'اسم الشركة مطلوب للحساب التجاري.',
                'company_logo.required_if' => 'لوجو الشركة مطلوب للحساب التجاري.',
                'company_logo.image' => 'يجب أن يكون لوجو الشركة صورة.',
                'company_logo.max' => 'حجم لوجو الشركة يجب ألا يتجاوز 5 ميجابايت.',
                'course_category_id.required' => __('messages.trainer_category_required'),
                'course_category_id.exists' => __('messages.trainer_category_invalid'),
                'avatar.required' => __('messages.trainer_avatar_required'),
                'avatar.file' => __('messages.trainer_avatar_required'),
                'avatar.image' => __('messages.trainer_image_invalid'),
                'avatar.max' => __('messages.trainer_image_max_2'),
                'linkedin_url.required' => __('messages.trainer_linkedin_required'),
                'linkedin_url.url' => __('messages.trainer_linkedin_invalid'),
                'linkedin_url.regex' => __('messages.trainer_linkedin_invalid'),
                'teaching_sample.mimetypes' => __('messages.trainer_sample_mimes'),
                'teaching_sample.max' => __('messages.trainer_sample_max'),
                'trainer_bio.required' => __('messages.trainer_bio_required'),
                'trainer_bio.min' => __('messages.trainer_bio_min'),
                'trainer_bio.max' => __('messages.trainer_bio_max'),
                'accept_terms.accepted' => __('messages.trainer_terms_required'),
            ]);
        } catch (ValidationException $e) {
            if ($isTrainer && $isAcademy) {
                throw $e->redirectTo(route('academy.become-trainer'));
            }

            throw $e;
        }

        if ($isTrainer) {
            $missing = [];
            if (! $request->hasFile('avatar')) {
                $missing['avatar'] = __('messages.trainer_avatar_required');
            }
            if ($missing !== []) {
                return $this->trainerRegistrationRedirect($isAcademy)
                    ->withErrors($missing)
                    ->withInput();
            }

            $categoryOk = CourseCategory::query()
                ->whereKey($request->course_category_id)
                ->where('is_active', true)
                ->exists();
            if (! $categoryOk) {
                return $this->trainerRegistrationRedirect($isAcademy)
                    ->withErrors([
                        'course_category_id' => __('messages.trainer_category_invalid'),
                    ])
                    ->withInput();
            }
        }

        $companyLogoPath = null;
        if (! $isAcademy && $request->account_type === 'business' && $request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('clients/company-logos', 'public');
        }

        $role = $request->role;
        $avatarPath = null;
        $teachingSamplePath = null;

        if ($isTrainer) {
            $avatarPath = WatermarkedUpload::store($request->file('avatar'), 'trainers/avatars');
            if ($request->hasFile('teaching_sample')) {
                $teachingSamplePath = $request->file('teaching_sample')->store('trainers/samples', 'public');
            }
        }

        $accountType = $isAcademy ? 'personal' : $request->account_type;
        $isTrainee = $role === 'trainee';

        $user = User::create([
            'name' => $request->name,
            'account_type' => $accountType,
            'company_name' => $accountType === 'business' ? $request->company_name : null,
            'company_logo' => $companyLogoPath,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'email_verified_at' => $isTrainee ? null : now(),
            'country' => $request->country,
            'role' => $role,
            'status' => $isTrainer ? 'pending' : 'active',
            'avatar' => $avatarPath,
            'course_category_id' => $isTrainer ? $request->course_category_id : null,
            'teaching_language' => $isTrainer ? ($request->input('teaching_language') ?: 'ar') : null,
            'linkedin_url' => $isTrainer ? $request->input('linkedin_url') : null,
            'teaching_sample_path' => $teachingSamplePath,
            'trainer_bio' => $isTrainer ? $request->input('trainer_bio') : null,
            'terms_accepted_at' => $isTrainer ? now() : null,
        ]);

        event(new Registered($user));

        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $accountLabel = $user->account_type === 'business'
                ? "تجاري ({$user->company_name})"
                : 'شخصي';
            $pendingNote = $isTrainer ? ' | بانتظار موافقة الإدارة' : '';
            $whatsapp->notifyManager(
                "تسجيل {$user->role_name} جديد — {$accountLabel} | الاسم: {$user->name} | الإيميل: {$user->email} | الهاتف: {$user->phone}{$pendingNote}",
                'تسجيل حسابات'
            );
        } catch (\Exception $e) {
            Log::error('[REGISTER] فشل إشعار المدير: '.$e->getMessage());
        }

        if ($isTrainer) {
            $this->notifyAdminsOfPendingTrainer($user->fresh(['courseCategory']));

            return redirect()
                ->route('login', ['ui' => 'academy', 'trainer_applied' => 1])
                ->with('success', __('messages.trainer_register_pending'));
        }

        if ($isTrainee) {
            try {
                app(OTPController::class)->issueAndSendEmailOtp($user);
            } catch (\Exception $e) {
                \Log::error('[REGISTER] فشل إرسال OTP للبريد: '.$e->getMessage());
            }

            Auth::login($user);

            $requestedRedirect = $request->input('redirect') ?: $request->query('redirect');
            if (is_string($requestedRedirect) && $requestedRedirect !== '') {
                $host = parse_url($requestedRedirect, PHP_URL_HOST);
                $appHost = parse_url(url('/'), PHP_URL_HOST);
                if ($host === null || $host === $appHost) {
                    session(['url.intended' => $requestedRedirect]);
                }
            }

            if ($isAcademy) {
                AuthUi::resolve(AuthUi::ACADEMY);
            }

            return redirect()
                ->route('otp.verify')
                ->with('success', 'أرسلنا رمز تحقق إلى بريدك الإلكتروني. أدخله لتفعيل حسابك.');
        }

        Auth::login($user);

        $requestedRedirect = $request->input('redirect') ?: $request->query('redirect');
        if (is_string($requestedRedirect) && $requestedRedirect !== '') {
            $host = parse_url($requestedRedirect, PHP_URL_HOST);
            $appHost = parse_url(url('/'), PHP_URL_HOST);
            if ($host === null || $host === $appHost) {
                return redirect()->to($requestedRedirect)->with('success', 'تم إنشاء حسابك بنجاح!');
            }
        }

        $redirect = match ($role) {
            'trainee', 'trainer' => route('academy.index'),
            default => route('dashboard.requests.index'),
        };

        return redirect($redirect)->with('success', 'تم إنشاء حسابك بنجاح!');
    }

    /**
     * In-app notification + email for every admin when a trainer awaits approval.
     */
    protected function notifyAdminsOfPendingTrainer(User $trainer): void
    {
        $reviewUrl = route('dashboard.trainers.show', $trainer);
        $title = __('messages.trainer_pending_notification_title');
        $message = __('messages.trainer_pending_notification_body', [
            'name' => $trainer->name,
        ]);

        User::admins()->get()->each(function (User $admin) use ($trainer, $reviewUrl, $title, $message) {
            try {
                AppNotification::notify(
                    $admin->id,
                    $title,
                    $message,
                    $reviewUrl,
                    'fa-user-clock',
                    'warning',
                );
            } catch (\Throwable $e) {
                Log::warning('[REGISTER] فشل إشعار التطبيق للأدمن', [
                    'admin_id' => $admin->id,
                    'trainer_id' => $trainer->id,
                    'error' => $e->getMessage(),
                ]);
            }

            if (! filled($admin->email)) {
                return;
            }

            try {
                Mail::to($admin->email, $admin->name)->send(
                    new TrainerPendingApprovalMail($trainer, $reviewUrl)
                );
            } catch (\Throwable $e) {
                Log::warning('[REGISTER] فشل بريد الأدمن لطلب محاضر', [
                    'admin_id' => $admin->id,
                    'trainer_id' => $trainer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Academy trainers apply from the become-trainer page; keep validation errors there.
     */
    protected function trainerRegistrationRedirect(bool $isAcademy): RedirectResponse
    {
        if ($isAcademy) {
            return redirect()->route('academy.become-trainer');
        }

        return redirect()->back();
    }
}
