<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'keyword' => 'nullable|string',
            'Status' => 'nullable|in:0,1',
            'isAdmin' => 'nullable|in:0,1',
        ];
    }
}