<?php

namespace App\Repositories;

use App\Models\Park;
use App\Models\Ticket;
use App\Models\Booking;

class ChatBotRepository
{
    /*
    |--------------------------------------------------------------------------
    | PARKS
    |--------------------------------------------------------------------------
    */

    public function getParks()
    {
        return Park::query()
            ->where('Status', 1)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | TICKETS
    |--------------------------------------------------------------------------
    */

    public function getTickets()
    {
        return Ticket::query()
            ->where('Status', 1)
            ->with('park')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING
    |--------------------------------------------------------------------------
    */

    public function getBookingByCode(
        $bookingCode
    ) {
        return Booking::query()
            ->where(
                'BookingCode',
                $bookingCode
            )
            ->with('payment')
            ->first();
    }
}