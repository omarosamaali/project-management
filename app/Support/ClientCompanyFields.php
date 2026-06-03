<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClientCompanyFields
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(User $user, bool $logoRequiredForNewBusiness = false): array
    {
        return [
            'account_type' => ['required', 'in:personal,business'],
            'company_name' => ['required_if:account_type,business', 'nullable', 'string', 'max:255'],
            'company_logo' => [
                Rule::requiredIf(function () use ($user, $logoRequiredForNewBusiness) {
                    $isBusiness = request()->input('account_type') === 'business';

                    return $isBusiness && $logoRequiredForNewBusiness && !$user->company_logo && !request()->hasFile('company_logo');
                }),
                'nullable',
                'image',
                'max:5120',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'account_type.required' => 'يرجى اختيار نوع الحساب.',
            'account_type.in' => 'نوع الحساب غير صالح.',
            'company_name.required_if' => 'اسم الشركة مطلوب للحساب التجاري.',
            'company_logo.required' => 'لوجو الشركة مطلوب للحساب التجاري.',
            'company_logo.image' => 'يجب أن يكون لوجو الشركة صورة.',
            'company_logo.max' => 'حجم لوجو الشركة يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }

    public static function apply(User $user, Request $request): void
    {
        $accountType = $request->input('account_type', $user->account_type ?? 'personal');
        $user->account_type = $accountType;

        if ($accountType === 'business') {
            $user->company_name = $request->input('company_name');

            if ($request->hasFile('company_logo')) {
                self::deleteLogo($user);
                $user->company_logo = $request->file('company_logo')->store('clients/company-logos', 'public');
            }

            return;
        }

        self::deleteLogo($user);
        $user->company_name = null;
        $user->company_logo = null;
    }

    public static function logoUrl(?User $user): ?string
    {
        if (!$user?->company_logo) {
            return null;
        }

        return asset('storage/' . $user->company_logo);
    }

    private static function deleteLogo(User $user): void
    {
        if ($user->company_logo && Storage::disk('public')->exists($user->company_logo)) {
            Storage::disk('public')->delete($user->company_logo);
        }
    }
}
