<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => "required|min:3|max:100|regex:/^[A-Za-z0-9 ]+$/",
            "last_name" => "required|min:3|max:100|regex:/^[A-Za-z0-9 ]+$/",
            "mobile" => "required|numeric|digits_between:8,15",
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|extensions:jpeg,jpg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.image' => 'The file must be a valid image.',
            'profile_image.mimes' => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'profile_image.extensions' => 'The file extension must be jpeg, jpg, png, or gif.',
            'profile_image.max' => 'The image size must not exceed 2MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'profile_image' => 'profile image',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation errors for debugging
        Log::info('Profile Image Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'file' => $this->file('profile_image') ? [
                'name' => $this->file('profile_image')->getClientOriginalName(),
                'mime' => $this->file('profile_image')->getMimeType(),
                'size' => $this->file('profile_image')->getSize(),
            ] : null
        ]);

        parent::failedValidation($validator);
    }
}