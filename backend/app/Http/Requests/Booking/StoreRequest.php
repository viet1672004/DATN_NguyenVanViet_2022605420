<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ParkID' => 'required|string|exists:Parks,ID',
            'BookingDateTime' => 'required|date',

            'details' => 'required|array|min:1',

            'details.*.TicketID' => 'required|string|exists:Tickets,ID',
            'details.*.Quantity' => 'required|integer|min:1',
        ];
    }
}