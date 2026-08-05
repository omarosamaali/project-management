<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CourseCategory;
use App\Support\ClientCompanyFields;
use App\Support\EmployeeProfileStats;
use App\Support\TrainerJourney;
use App\Support\WatermarkedUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $employeeStats = ($user->role === 'partner' && $user->is_employee)
            ? EmployeeProfileStats::forUser($user)
            : null;

        $trainerJourney = null;
        if ($user->isTrainer()) {
            $state = TrainerJourney::stateFor($user);
            $trainerJourney = [
                'step' => $state['step'],
                'completed' => $state['completed'],
                'all_done' => $state['all_done'],
                'hint' => TrainerJourney::hintFor($state['step'], $state['all_done']),
            ];
        }

        $categories = $user->isTrainer()
            ? CourseCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('profile.edit', [
            'user' => $user,
            'employeeStats' => $employeeStats,
            'trainerJourney' => $trainerJourney,
            'categories' => $categories,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Trainers cannot change email.
        if ($user->isTrainer()) {
            unset($validated['email']);
        }

        $user->fill($validated);

        if (! $user->isTrainer() && $user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->country = $request->input('country', $user->country);
        $user->phone = $request->input('phone', $user->phone);

        if ($user->role === 'partner') {
            $user->withdrawal_method = $request->withdrawal_method;
            $user->withdrawal_email = $request->withdrawal_email;
            $user->withdrawal_notes = $request->withdrawal_notes;
            $user->wallet_type = $request->wallet_type;
            $user->wallet_full_name = $request->wallet_full_name;
        }

        if ($user->role === 'client') {
            ClientCompanyFields::apply($user, $request);
        }

        if ($user->isTrainer()) {
            $user->trainer_bio = $request->input('trainer_bio', $user->trainer_bio);
            $user->linkedin_url = $request->input('linkedin_url', $user->linkedin_url);
            $user->teaching_language = $request->input('teaching_language', $user->teaching_language) ?: 'ar';
            $user->course_category_id = $request->input('course_category_id', $user->course_category_id);

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = WatermarkedUpload::store($request->file('avatar'), 'trainers/avatars');
            }

            $sampleType = $request->input('teaching_sample_type', $user->teachingSampleIsExternal() ? 'link' : 'upload');
            if ($sampleType === 'link') {
                if ($request->filled('teaching_sample_link')) {
                    if ($user->teaching_sample_path) {
                        Storage::disk('public')->delete($user->teaching_sample_path);
                        $user->teaching_sample_path = null;
                    }
                    $user->teaching_sample_link = $request->input('teaching_sample_link');
                }
            } elseif ($request->hasFile('teaching_sample') && $request->file('teaching_sample')->isValid()) {
                if ($user->teaching_sample_path) {
                    Storage::disk('public')->delete($user->teaching_sample_path);
                }
                $user->teaching_sample_path = $request->file('teaching_sample')->store('trainers/samples', 'public');
                $user->teaching_sample_link = null;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
