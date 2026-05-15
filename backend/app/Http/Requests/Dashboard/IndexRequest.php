<?php

namespace App\Http\Requests\Dashboard;

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

            'range' => 'nullable|in:today,week,month,year',

            'from_date' => 'nullable|date',

            'to_date' => 'nullable|date|after_or_equal:from_date',

        ];
    }
}