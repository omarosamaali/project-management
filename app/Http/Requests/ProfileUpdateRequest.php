<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\ClientCompanyFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // Trainers cannot change registration email; others keep unique email validation.
        if (! $user->isTrainer()) {
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ];
        }

        if ($user->role === 'client') {
            $rules = array_merge($rules, ClientCompanyFields::rules($user));
        }

        if ($user->isTrainer()) {
            $rules = array_merge($rules, [
                'avatar' => ['nullable', 'file', 'image', 'max:2048'],
                'trainer_bio' => ['nullable', 'string', 'min:120', 'max:2000'],
                'linkedin_url' => [
                    'nullable',
                    'url',
                    'max:500',
                    'regex:/^https?:\/\/(www\.)?linkedin\.com\/.+/i',
                ],
                'teaching_language' => ['nullable', 'in:ar,en'],
                'course_category_id' => ['nullable', 'exists:course_categories,id'],
                'teaching_sample_type' => ['nullable', 'in:upload,link'],
                'teaching_sample' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-m4v', 'max:307200'],
                'teaching_sample_link' => ['nullable', 'url', 'max:1000'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(ClientCompanyFields::messages(), [
            'linkedin_url.regex' => __('messages.trainer_linkedin_invalid'),
            'linkedin_url.url' => __('messages.trainer_linkedin_invalid'),
            'trainer_bio.min' => __('messages.trainer_bio_min'),
            'teaching_sample_link.url' => __('messages.trainer_sample_link_invalid'),
        ]);
    }
}
