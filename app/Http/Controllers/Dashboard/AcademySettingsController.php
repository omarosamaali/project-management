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
            'cashoutMin' => Setting::academyTrainerCashoutMinimum(),
            'cashoutMax' => Setting::academyTrainerCashoutMaximum(),
            'embeddedMeetingsEnabled' => Setting::academyEmbeddedMeetingsEnabled(),
            'meetingApiConfigured' => filled(config('services.meeting.base_url'))
                && filled(config('services.meeting.api_key'))
                && filled(config('services.meeting.api_secret')),
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
            'cashout_min' => 'required|numeric|min:0|lte:cashout_max',
            'cashout_max' => 'required|numeric|min:0|gte:cashout_min',
            'academy_embedded_meetings_enabled' => 'nullable|boolean',
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

        Setting::set('academy_trainer_cashout_minimum', (string) $validated['cashout_min']);
        Setting::set('academy_trainer_cashout_maximum', (string) $validated['cashout_max']);
        Setting::set(
            'academy_embedded_meetings_enabled',
            $request->boolean('academy_embedded_meetings_enabled') ? '1' : '0'
        );

        return redirect()
            ->route('dashboard.academy.settings.edit')
            ->with('success', 'تم حفظ إعدادات الأكاديمية بنجاح.');
    }
}
