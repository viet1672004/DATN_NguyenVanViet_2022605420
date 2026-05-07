<?php

namespace App\Http\Requests\Booking;

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
            'ParkID' => 'nullable|string|exists:Parks,ID',
            'BookingDateTime' => 'nullable|date',
            'Status' => 'nullable|in:0,1,2',

            'details' => 'nullable|array',

            'details.*.ID' => 'nullable|string|exists:BookingDetails,ID',
            'details.*.TicketID' => 'required_with:details|string|exists:Tickets,ID',
            'details.*.Quantity' => 'required_with:details|integer|min:1',
        ];
    }
}