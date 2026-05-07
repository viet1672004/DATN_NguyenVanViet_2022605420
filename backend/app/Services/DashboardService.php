<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $repo;

    public function __construct(DashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    // SUMMARY
    public function summary()
    {
        return [
            'total_users' => $this->repo->totalUsers(),

            'total_bookings' => $this->repo->totalBookings(),

            'total_payments' => $this->repo->totalPayments(),

            'total_revenue' => (float) $this->repo->totalRevenue(),

            'total_parks' => $this->repo->totalParks(),

            'total_tickets' => $this->repo->totalTickets(),
        ];
    }

    // CHART
    public function revenueChart()
    {
        return $this->repo->revenueChart();
    }

    // TOP TICKETS
    public function topTickets()
    {
        return $this->repo->topTickets();
    }

    // TOP PARKS
    public function topParks()
    {
        return $this->repo->topParks()
            ->map(function ($park) {

                $park->bookings_count =
                    (int) $park->bookings_count;

                return $park;
            });
    }

    // TABLE
    public function latestBookings()
    {
        return $this->repo->latestBookings();
    }

    public function latestPayments()
    {
        return $this->repo->latestPayments();
    }

    public function bookingStatus()
    {
        return $this->repo->bookingStatus();
    }

    public function paymentStatus()
    {
        return $this->repo->paymentStatus();
    }
}