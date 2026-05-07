<?php

namespace App\Http\Requests\Ticket;

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
            'TicketName' => 'required|string|max:255',
            'Price' => 'required|numeric',
            'ParkID' => 'required|string',
            'Description' => 'nullable|string',

            'TicketType' => 'required|in:adult,child,combo,date',
            'NumberOfDays' => 'nullable|integer',
            'ComboInfo' => 'nullable|string',

            'Status' => 'required|in:0,1'
        ];
    }
}