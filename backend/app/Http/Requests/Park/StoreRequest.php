<?php

namespace App\Http\Requests\Park;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ParkName'    => 'required|string|max:255',
            'Location'    => 'required|string|max:255',
            'Description' => 'nullable|string',
            'OpenTime'    => 'required|date_format:H:i',
            'CloseTime'   => 'required|date_format:H:i',
            'ImageFile'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'Status'      => 'required|in:0,1'
        ];
    }
}