<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'account_type' => ['required', 'in:personal,business'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:10'],
            'company_name' => ['required_if:account_type,business', 'nullable', 'string', 'max:255'],
            'company_logo' => ['required_if:account_type,business', 'nullable', 'image', 'max:5120'],
        ], [
            'account_type.required' => 'يرجى اختيار نوع الحساب.',
            'account_type.in' => 'نوع الحساب غير صالح.',
            'company_name.required_if' => 'اسم الشركة مطلوب للحساب التجاري.',
            'company_logo.required_if' => 'لوجو الشركة مطلوب للحساب التجاري.',
            'company_logo.image' => 'يجب أن يكون لوجو الشركة صورة.',
            'company_logo.max' => 'حجم لوجو الشركة يجب ألا يتجاوز 5 ميجابايت.',
        ]);

        $companyLogoPath = null;
        if ($request->account_type === 'business' && $request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store('clients/company-logos', 'public');
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
            'role' => 'client',
        ]);

        event(new Registered($user));

        // إشعار المدير والأدمن بتسجيل عميل جديد
        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $accountLabel = $user->account_type === 'business'
                ? "تجاري ({$user->company_name})"
                : 'شخصي';
            $whatsapp->notifyManager(
                "تسجيل عميل جديد — {$accountLabel} | الاسم: {$user->name} | الإيميل: {$user->email} | الهاتف: {$user->phone}",
                'تسجيل حسابات'
            );
        } catch (\Exception $e) {
            \Log::error("[REGISTER] فشل إشعار المدير: " . $e->getMessage());
        }

        // تسجيل دخول تلقائي
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'تم إنشاء حسابك بنجاح!');
    }
}