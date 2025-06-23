<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
                'min:3',
            ],

            'email' => [
                'nullable',
                'email',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'max:255',
                'min:6',
            ],
        ];
    }
}
