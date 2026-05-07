<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Park;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    public function totalUsers()
    {
        return User::count();
    }

    public function totalBookings()
    {
        return Booking::count();
    }

    public function totalPayments()
    {
        return Payment::count();
    }

    public function totalRevenue()
    {
        return Payment::where('PaymentStatus', 'PAID')
            ->sum('Amount');
    }

    public function totalParks()
    {
        return Park::count();
    }

    public function totalTickets()
    {
        return Ticket::count();
    }

    /*
    |--------------------------------------------------------------------------
    | CHART
    |--------------------------------------------------------------------------
    */

    public function revenueChart()
    {
        return Payment::selectRaw("
                CAST(PaymentDate AS DATE) as date,
                SUM(CAST(Amount AS FLOAT)) as revenue
            ")
            ->where('PaymentStatus', 'PAID')
            ->groupByRaw('CAST(PaymentDate AS DATE)')
            ->orderByDesc('date')
            ->take(7)
            ->get()
            ->reverse()
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | TOP TICKETS
    |--------------------------------------------------------------------------
    */

    public function topTickets()
    {
        return Ticket::select(
                'Tickets.ID',
                'Tickets.TicketName',
                DB::raw('CAST(SUM(BookingDetails.Quantity) AS INT) as total_sold')
            )
            ->join(
                'BookingDetails',
                'Tickets.ID',
                '=',
                'BookingDetails.TicketID'
            )
            ->groupBy(
                'Tickets.ID',
                'Tickets.TicketName'
            )
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | TOP PARKS
    |--------------------------------------------------------------------------
    */

    public function topParks()
    {
        return Park::select(
                'ID',
                'ParkName',
                'Image'
            )
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | LATEST BOOKINGS
    |--------------------------------------------------------------------------
    */

    public function latestBookings()
    {
        return Booking::with([
            'user:ID,Name',
            'park:ID,ParkName'
                ])
            ->orderByDesc('BookingDateTime')
            ->take(5)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | LATEST PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function latestPayments()
    {
        return Payment::with([
                'booking:ID,BookingCode,TotalPrice'
            ])
            ->orderByDesc('PaymentDate')
            ->take(5)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING STATUS
    |--------------------------------------------------------------------------
    */

    public function bookingStatus()
    {
        return Booking::selectRaw("
                CASE
                    WHEN Status = 1 THEN 'SUCCESS'
                    WHEN Status = 2 THEN 'CANCEL'
                    ELSE 'PENDING'
                END as status,
                CAST(COUNT(*) AS INT) as total
            ")
            ->groupBy('Status')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function paymentStatus()
    {
        return Payment::selectRaw("
                PaymentStatus as status,
                CAST(COUNT(*) AS INT) as total
            ")
            ->groupBy('PaymentStatus')
            ->get();
    }
}