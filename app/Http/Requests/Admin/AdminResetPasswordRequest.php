<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminResetPasswordRequest extends FormRequest
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
           'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password field is required.',
            'password.min' => 'Password should be minimum 6 character.',
            'confirmPassword.required' => 'Confirm password field is required.',
            'confirmPassword.same' => 'Password and confirm password must same.',
        ];
    }

}
