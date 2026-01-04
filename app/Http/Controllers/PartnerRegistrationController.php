<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PartnerRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-partner');
    }

    /**
     * معالجة بيانات التسجيل ورفع الملفات
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات (Validation)
        $request->validate([
            'name'         => 'required|string|max:255',
            'country'      => 'required|string',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|string',
            'skills'       => 'required|array',
            'password'     => 'required|string|min:8|confirmed', // الحقل الجديد
            'avatar'       => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'id_image'     => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_video' => 'required|mimes:mp4,mov,avi|max:20480', // 20MB
        ], [
            'selfie_video.max' => 'حجم الفيديو كبير جداً، الحد الأقصى 20 ميجابايت',
            'email.unique'     => 'هذا البريد الإلكتروني مسجل مسبقاً لدينا',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 2. معالجة ورفع الملفات
                $paths = [];

                if ($request->hasFile('avatar')) {
                    $paths['avatar'] = $request->file('avatar')->store('partners/avatars', 'public');
                }

                if ($request->hasFile('id_image')) {
                    $paths['id_image'] = $request->file('id_image')->store('partners/documents', 'public');
                }

                if ($request->hasFile('selfie_video')) {
                    $paths['selfie_video'] = $request->file('selfie_video')->store('partners/videos', 'public');
                }

                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'phone'    => $request->phone,
                    'country'  => $request->country,
                    'password' => Hash::make($request->password),
                    'role'     => 'independent_partner',
                    'status'   => 'active',
                ]);

                $user->update([
                    'profile_photo_path' => $paths['avatar'],
                    'skills'             => json_encode($request->skills),
                    'id_card_path'       => $paths['id_image'],
                    'verification_video' => $paths['selfie_video'],
                ]);

                return redirect()->route('login')->with('success', 'تم استلام طلب انضمامك بنجاح! سيتم مراجعة الوثائق والرد عليك خلال 24 ساعة.');
            });
        } catch (\Exception $e) {
            if (isset($paths)) {
                foreach ($paths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            return back()->with('error', 'حدث خطأ أثناء التسجيل: ' . $e->getMessage());
        }
    }
}
