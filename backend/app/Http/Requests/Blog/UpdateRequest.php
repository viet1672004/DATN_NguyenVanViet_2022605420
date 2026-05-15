<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Title' => 'required|max:255',

            'BannerImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'Summary' => 'nullable',

            'Content' => 'required',

            'Tags' => 'nullable',

            'ParkID' => 'required|exists:Parks,ID',

            'UserID' => 'nullable',

            'Status' => 'nullable|in:0,1',
        ];
    }
}