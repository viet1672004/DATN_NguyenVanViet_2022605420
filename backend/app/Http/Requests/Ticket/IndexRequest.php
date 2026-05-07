<?php

namespace App\Http\Requests\Ticket;

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
            'park_id'  => 'nullable|string',
            'status'   => 'nullable|in:0,1',
            'type'     => 'nullable|in:adult,child,combo,date',

            'page'     => 'nullable|integer',
            'per_page' => 'nullable|integer'
        ];
    }
}