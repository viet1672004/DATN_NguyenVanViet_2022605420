<?php

namespace App\Repositories;

use App\Models\Park;
use App\Models\Ticket;
use App\Models\Booking;

class ChatBotRepository
{
    /*
    |--------------------------------------------------------------------------
    | SEARCH TICKETS
    |--------------------------------------------------------------------------
    */

    public function searchTickets($message)
    {
        return Ticket::query()

            ->with('park')

            ->where('Status', 1)

            ->where(function ($query) use ($message) {

                $query->where(
                    'TicketName',
                    'like',
                    "%{$message}%"
                )

                ->orWhere(
                    'Description',
                    'like',
                    "%{$message}%"
                );
            })

            ->limit(5)

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH PARKS
    |--------------------------------------------------------------------------
    */

    public function searchParks($message)
    {
        return Park::query()

            ->where('Status', 1)

            ->where(function ($query) use ($message) {

                $query->where(
                    'ParkName',
                    'like',
                    "%{$message}%"
                )

                ->orWhere(
                    'Description',
                    'like',
                    "%{$message}%"
                )

                ->orWhere(
                    'Location',
                    'like',
                    "%{$message}%"
                );
            })

            ->limit(5)

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING
    |--------------------------------------------------------------------------
    */

    public function getBookingByCode($bookingCode)
    {
        return Booking::query()

            ->where(
                'BookingCode',
                $bookingCode
            )

            ->with('payment')

            ->first();
    }
}