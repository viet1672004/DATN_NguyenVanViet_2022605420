<?php

namespace App\Http\Requests\Park;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'   => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status'   => 'nullable|in:0,1',
            'page'     => 'nullable|integer',
            'per_page' => 'nullable|integer'
        ];
    }
}