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

    private function filterByDate(
        $query,
        $column,
        $filters = []
    ) {

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                $column,
                '>=',
                $filters['from_date']
            );
        }

        if (!empty($filters['to_date'])) {

            $query->whereDate(
                $column,
                '<=',
                $filters['to_date']
            );
        }

        return $query;
    }

    public function totalUsers()
    {
        return User::count();
    }

    public function totalBookings($filters = [])
    {
        $query = Booking::query();

        $this->filterByDate(
            $query,
            'BookingDateTime',
            $filters
        );

        return $query->count();
    }

    public function totalPayments($filters = [])
    {
        $query = Payment::query();

        $this->filterByDate(
            $query,
            'PaymentDate',
            $filters
        );

        return $query->count();
    }

    public function totalRevenue($filters = [])
    {
        $query = Payment::where(
            'PaymentStatus',
            'PAID'
        );

        $this->filterByDate(
            $query,
            'PaymentDate',
            $filters
        );

        return $query->sum('Amount');
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

    public function revenueChart($filters = [])
    {
        $query = Payment::selectRaw("
                CAST(PaymentDate AS DATE) as date,
                SUM(CAST(Amount AS FLOAT)) as revenue
            ")
            ->where('PaymentStatus', 'PAID');

        $this->filterByDate(
            $query,
            'PaymentDate',
            $filters
        );

        return $query
            ->groupByRaw('CAST(PaymentDate AS DATE)')
            ->orderBy('date')
            ->get();
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

    public function latestBookings($filters = [])
    {
        $query = Booking::with([
            'user:ID,Name',
            'park:ID,ParkName'
        ]);

        $this->filterByDate(
            $query,
            'BookingDateTime',
            $filters
        );

        return $query
            ->orderByDesc('BookingDateTime')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | LATEST PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function latestPayments($filters = [])
    {
        $query = Payment::with([
            'booking:ID,BookingCode,TotalPrice'
        ]);

        $this->filterByDate(
            $query,
            'PaymentDate',
            $filters
        );

        return $query
            ->orderByDesc('PaymentDate')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING STATUS
    |--------------------------------------------------------------------------
    */

    public function bookingStatus($filters = [])
    {
        $query = Booking::selectRaw("
                CASE
                    WHEN Status = 1 THEN 'SUCCESS'
                    WHEN Status = 2 THEN 'CANCEL'
                    ELSE 'PENDING'
                END as status,
                CAST(COUNT(*) AS INT) as total
            ");

        $this->filterByDate(
            $query,
            'BookingDateTime',
            $filters
        );

        return $query
            ->groupBy('Status')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    public function paymentStatus($filters = [])
    {
        $query = Payment::selectRaw("
                PaymentStatus as status,
                CAST(COUNT(*) AS INT) as total
            ");

        $this->filterByDate(
            $query,
            'PaymentDate',
            $filters
        );

        return $query
            ->groupBy('PaymentStatus')
            ->get();
    }
}