<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function query()
    {
        return Payment::query()->with([
            'booking.user',
            'booking.park'
        ]);
    }

    public function create($data)
    {
        return Payment::create($data);
    }

    public function find($id)
    {
        return Payment::with([
            'booking.user',
            'booking.park'
        ])->find($id);
    }

    public function findByBooking($bookingId)
    {
        return Payment::where('BookingID', $bookingId)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}