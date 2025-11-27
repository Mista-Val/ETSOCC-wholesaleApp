<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
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
            'subject' => 'required|max:255|min:2|regex:/^[A-Za-z0-9 ]+$/',
            'content' => 'required|max:555',
        ];
    }

    public function messages(): array
    {
        return [
            'title.regex' => 'Title field allow only alphabets.',
           
        ];
    }
}
