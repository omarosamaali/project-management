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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'email_verified_at' => now(),
            'country' => $request->country,
            
        ]);

        event(new Registered($user));

        // إشعار المدير والأدمن بتسجيل عميل جديد
        try {
            $whatsapp = app(WhatsAppOTPService::class);
            $whatsapp->notifyManager(
                "تسجيل عميل جديد — الاسم: {$user->name} | الإيميل: {$user->email} | الهاتف: {$user->phone}",
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