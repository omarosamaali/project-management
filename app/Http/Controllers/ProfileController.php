<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\ClientCompanyFields;
use App\Support\EmployeeProfileStats;
use App\Support\TrainerJourney;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
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

        return view('profile.edit', [
            'user' => $user,
            'employeeStats' => $employeeStats,
            'trainerJourney' => $trainerJourney,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // جلب البيانات التي تم التحقق منها من الـ Request
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // تحديث الحقول الإضافية بما فيها حقول المحفظة الجديدة
        $user->country = $request->country;
        $user->withdrawal_method = $request->withdrawal_method;
        $user->withdrawal_email = $request->withdrawal_email;
        $user->withdrawal_notes = $request->withdrawal_notes;

        // الحقول الجديدة التي أضفناها للمحفظة
        $user->wallet_type = $request->wallet_type;
        $user->wallet_full_name = $request->wallet_full_name;

        if ($user->role === 'client') {
            ClientCompanyFields::apply($user, $request);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
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
