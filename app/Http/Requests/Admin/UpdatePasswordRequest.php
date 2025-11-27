<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'old_password' => "required",
            'password' =>  'required|min:8',
            'password_confirmation' => "required|same:password",
        ];
    }

    public function messages(): array
    {
        return [
            
        ];
    }

    public function attributes(): array
    {
        return [
            
        ];
    }
}
