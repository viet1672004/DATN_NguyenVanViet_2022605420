<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cho phép
    }

    public function rules(): array
    {
        return [
            'Name' => 'required|string|max:255',
            'Email' => 'required|email',
            'Phone' => 'required|string|max:20',
        ];
    }
}