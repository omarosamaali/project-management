<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PayoutMethod;
use App\Models\TrainerPaymentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainerPaymentProfileController extends Controller
{
    public function edit()
    {
        $user = $this->authorizeTrainer();

        $profile = $this->profileFor($user);
        $methods = PayoutMethod::query()->active()->with('fields')->orderBy('id')->get();

        return view('dashboard.academy.payment-profile.edit', [
            'profile' => $profile,
            'methods' => $methods,
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->authorizeTrainer();
        $profile = $this->profileFor($user);

        abort_if($profile->isLocked(), 422, __('messages.payment_profile_locked'));

        $method = PayoutMethod::query()->active()->with('fields')->findOrFail($request->input('payout_method_id'));

        $rules = [
            'payout_method_id' => 'required|exists:payout_methods,id',
            'id_card_front' => [$profile->id_card_front_path ? 'nullable' : 'required', 'image', 'max:4096'],
            'id_card_back' => [$profile->id_card_back_path ? 'nullable' : 'required', 'image', 'max:4096'],
        ];

        if ($method->isBankTransfer()) {
            $rules['bank_account_name'] = 'required|string|max:255';
            $rules['bank_iban'] = 'required|string|max:64';
            $rules['bank_name'] = 'required|string|max:255';
            $rules['bank_country'] = 'required|string|max:100';
        } else {
            foreach ($method->fields as $field) {
                $rules['field_values.'.$field->key] = ($field->is_required ? 'required' : 'nullable').'|string|max:1000';
            }
        }

        $data = $request->validate($rules, [
            'id_card_front.required' => __('messages.payment_profile_id_front_required'),
            'id_card_back.required' => __('messages.payment_profile_id_back_required'),
        ]);

        $update = [
            'payout_method_id' => $method->id,
        ];

        if ($method->isBankTransfer()) {
            $update['bank_account_name'] = $data['bank_account_name'];
            $update['bank_iban'] = $data['bank_iban'];
            $update['bank_name'] = $data['bank_name'];
            $update['bank_country'] = $data['bank_country'];
            $update['field_values'] = null;
        } else {
            $update['field_values'] = $data['field_values'] ?? [];
            $update['bank_account_name'] = null;
            $update['bank_iban'] = null;
            $update['bank_name'] = null;
            $update['bank_country'] = null;
        }

        if ($request->hasFile('id_card_front')) {
            if ($profile->id_card_front_path) {
                Storage::disk('public')->delete($profile->id_card_front_path);
            }
            $update['id_card_front_path'] = $request->file('id_card_front')->store('trainer-payment-profiles/id-cards', 'public');
        }

        if ($request->hasFile('id_card_back')) {
            if ($profile->id_card_back_path) {
                Storage::disk('public')->delete($profile->id_card_back_path);
            }
            $update['id_card_back_path'] = $request->file('id_card_back')->store('trainer-payment-profiles/id-cards', 'public');
        }

        $profile->fill($update);
        if (! $profile->configured_at) {
            $profile->configured_at = now();
        }
        $profile->save();

        // Keep admin trainer review docs in sync with payment-profile ID uploads.
        $userUpdates = [];
        if (! empty($update['id_card_front_path'])) {
            $userUpdates['id_card_front_path'] = $update['id_card_front_path'];
            $userUpdates['id_card_path'] = $update['id_card_front_path'];
        }
        if (! empty($update['id_card_back_path'])) {
            $userUpdates['id_card_back_path'] = $update['id_card_back_path'];
        }
        if ($userUpdates !== []) {
            $user->forceFill($userUpdates)->save();
        }

        return redirect()
            ->route('dashboard.academy.payment-profile.edit')
            ->with('success', __('messages.payment_profile_saved'));
    }

    protected function profileFor(User $user): TrainerPaymentProfile
    {
        return TrainerPaymentProfile::query()->firstOrCreate(['user_id' => $user->id]);
    }

    protected function authorizeTrainer(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isTrainer(), 403);

        return $user;
    }
}
