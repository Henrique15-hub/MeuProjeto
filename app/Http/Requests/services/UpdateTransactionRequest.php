<?php

namespace App\Http\Requests\services;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'type' => [
                'nullable',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],

            'category_name' => [
                'nullable',
                'string',
            ],
        ];
    }
}
