<?php

namespace App\Http\Requests\services;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'isPersonalizada' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
