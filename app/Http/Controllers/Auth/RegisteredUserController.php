<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\User;
use App\Services\WhatsAppOTPService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $categories = CourseCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('auth.register', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $isTrainer = $request->input('role') === 'trainer';

        $rules = [
            'account_type' => ['required', 'in:personal,business'],
            'role' => ['required', 'in:client,trainer,trainee'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:10'],
            'company_name' => ['required_if:account_type,business', 'nullable', 'string', 'max:255'],
            'company_logo' => ['required_if:account_type,business', 'nullable', 'image', 'max:5120'],
            'course_category_id' => ['exclude_unless:role,trainer', 'required', 'exists:course_categories,id'],
            'avatar' => ['exclude_unless:role,trainer', 'required', 'file', 'image', 'max:2048'],
            'id_card_front' => ['exclude_unless:role,trainer', 'required', 'file', 'image', 'max:4096'],
            'id_card_back' => ['exclude_unless:role,trainer', 'required', 'file', 'image', 'max:4096'],
            'accept_terms' => ['exclude_unless:role,trainer', 'accepted'],
        ];

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
            'id_card_front.required' => __('messages.trainer_id_front_required'),
            'id_card_front.file' => __('messages.trainer_id_front_required'),
            'id_card_front.image' => __('messages.trainer_image_invalid'),
            'id_card_front.max' => __('messages.trainer_image_max_4'),
            'id_card_back.required' => __('messages.trainer_id_back_required'),
            'id_card_back.file' => __('messages.trainer_id_back_required'),
            'id_card_back.image' => __('messages.trainer_image_invalid'),
            'id_card_back.max' => __('messages.trainer_image_max_4'),
            'accept_terms.accepted' => __('messages.trainer_terms_required'),
        ]);

        if ($isTrainer) {
            $missing = [];
            if (! $request->hasFile('avatar')) {
                $missing['avatar'] = __('messages.trainer_avatar_required');
            }
            if (! $request->hasFile('id_card_front')) {
                $missing['id_card_front'] = __('messages.trainer_id_front_required');
            }
            if (! $request->hasFile('id_card_back')) {
                $missing['id_card_back'] = __('messages.trainer_id_back_required');
            }
            if ($missing !== []) {
                return back()->withErrors($missing)->withInput();
            }

            $categoryOk = CourseCategory::query()
                ->whereKey($request->course_category_id)
                ->where('is_active', true)
                ->exists();
            if (! $categoryOk) {
                return back()->withErrors([
                    'course_category_id' => __('messages.trainer_category_invalid'),
                ])->withInput();
            }
        }

        $companyLogoPath = null;
        if ($request->account_type === 'business' && $request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('clients/company-logos', 'public');
        }

        $role = $request->role;
        $avatarPath = null;
        $idFrontPath = null;
        $idBackPath = null;

        if ($isTrainer) {
            $avatarPath = $request->file('avatar')->store('trainers/avatars', 'public');
            $idFrontPath = $request->file('id_card_front')->store('trainers/id-cards', 'public');
            $idBackPath = $request->file('id_card_back')->store('trainers/id-cards', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'account_type' => $request->account_type,
            'company_name' => $request->account_type === 'business' ? $request->company_name : null,
            'company_logo' => $companyLogoPath,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'email_verified_at' => now(),
            'country' => $request->country,
            'role' => $role,
            'status' => $isTrainer ? 'pending' : 'active',
            'avatar' => $avatarPath,
            'course_category_id' => $isTrainer ? $request->course_category_id : null,
            'id_card_front_path' => $idFrontPath,
            'id_card_back_path' => $idBackPath,
            'id_card_path' => $idFrontPath,
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
            \Log::error("[REGISTER] فشل إشعار المدير: " . $e->getMessage());
        }

        if ($isTrainer) {
            return redirect()
                ->route('login')
                ->with('success', __('messages.trainer_register_pending'));
        }

        Auth::login($user);

        $redirect = match ($role) {
            'trainee' => route('academy.index'),
            default => route('dashboard.requests.index'),
        };

        return redirect($redirect)->with('success', 'تم إنشاء حسابك بنجاح!');
    }
}
