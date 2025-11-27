<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateFaqRequest extends FormRequest
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
            'question' => 'required|string|min:2',
            'answer' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean',
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
