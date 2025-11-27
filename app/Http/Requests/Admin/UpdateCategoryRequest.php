<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'title' => 'required|max:55|min:2|regex:/^[a-zA-Z\s]*$/|unique:email_templates,title,' .base64_decode($this->id),
        ];
    }

    public function messages(): array
    {
        return [
            'title.regex' => 'Title field allow only alphabets.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => "Title",
        ];
    }
}
