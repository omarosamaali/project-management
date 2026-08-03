<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\WatermarkedUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AcademySettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        abort_unless($user instanceof \App\Models\User && $user->isAdmin(), 403);

        $profits = Setting::academyTrainerProfitPercentages();

        return view('dashboard.academy.settings', [
            'academyLogoUrl' => Setting::academyLogoUrl(),
            'academyHeroImageUrl' => Setting::academyHeroImageUrl(),
            'profitOnline' => $profits['online'],
            'profitRecorded' => $profits['recorded'],
            'profitPrivate' => $profits['private'],
            'profitOnsite' => $profits['onsite'],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_unless($user instanceof \App\Models\User && $user->isAdmin(), 403);

        $validated = $request->validate([
            'academy_logo' => 'nullable|image|max:2048',
            'academy_hero_image' => 'nullable|image|max:4096',
            'trainer_profit_online' => 'required|numeric|min:0|max:100',
            'trainer_profit_recorded' => 'required|numeric|min:0|max:100',
            'trainer_profit_private' => 'required|numeric|min:0|max:100',
            'trainer_profit_onsite' => 'nullable|numeric|min:0|max:100',
        ]);

        if (! Setting::hasStorage()) {
            return redirect()
                ->route('dashboard.academy.settings.edit')
                ->with('error', 'جدول الإعدادات غير موجود بعد. شغّل الترحيلات أولاً باستخدام php artisan migrate.');
        }

        if ($request->hasFile('academy_logo')) {
            $old = Setting::academyLogoPath();
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            Setting::set(
                'academy_logo_path',
                $request->file('academy_logo')->store('academy/settings', 'public')
            );
        }

        if ($request->hasFile('academy_hero_image')) {
            $old = Setting::academyHeroImagePath();
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            Setting::set(
                'academy_hero_image_path',
                WatermarkedUpload::store($request->file('academy_hero_image'), 'academy/settings')
            );
        }

        Setting::set('academy_trainer_profit_online', (string) $validated['trainer_profit_online']);
        Setting::set('academy_trainer_profit_recorded', (string) $validated['trainer_profit_recorded']);
        Setting::set('academy_trainer_profit_private', (string) $validated['trainer_profit_private']);

        if ($request->filled('trainer_profit_onsite')) {
            Setting::set('academy_trainer_profit_onsite', (string) $validated['trainer_profit_onsite']);
        } else {
            Setting::set('academy_trainer_profit_onsite', '');
        }

        return redirect()
            ->route('dashboard.academy.settings.edit')
            ->with('success', 'تم حفظ إعدادات الأكاديمية بنجاح.');
    }
}
