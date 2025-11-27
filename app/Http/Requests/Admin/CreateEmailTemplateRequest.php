<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmailTemplateRequest extends FormRequest
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
            'title' => 'required|min:2|max:55|unique:email_templates,title|regex:/^[a-zA-Z\s]*$/',
            'subject' => 'required|max:255|min:2|regex:/^[A-Za-z0-9 ]+$/',
            'content' => 'required|max:555',
        ];
    }

    public function messages(): array
    {
        return [
            'title.regex' => 'Title field allow only alphabets.',
            'content.required' => 'Don\'t forget to add the mail body.',
        ];
    }
}
