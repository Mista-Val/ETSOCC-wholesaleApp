<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCmsPageRequest extends FormRequest
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
            'title' => "required|max:50|min:2|unique:cms_pages,title,".base64_decode($this->id),
            'content' => "required",
            'meta_description' => "required",
            'meta_keywords' => "required",
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'=>"The banner image field required.",
            'title.regex' => 'Title field allow only alphabets.'
        ];
    }

    public function attributes(): array
    {
        return [
            
        ];
    }
}

