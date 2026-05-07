<?php

namespace App\Http\Requests\Payment;

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
            'BookingID' => 'required|string|exists:Bookings,ID',
            'PaymentMethod' => 'required|string|max:50',
        ];
    }
}