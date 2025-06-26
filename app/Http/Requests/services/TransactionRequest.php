<?php

namespace App\Http\Requests\services;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
                'required',
                'numeric',
                'gt:0',
            ],

            'type' => [
                'required',
                'string',
            ],

            'description' => [
                'required',
                'string',
            ],

            'date' => [
                'required',
            ],

            'category_name' => [
                'nullable',
                'string',
            ],
        ];
    }
}
