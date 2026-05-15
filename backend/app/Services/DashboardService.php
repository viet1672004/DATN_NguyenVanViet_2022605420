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
    public function summary($filters = [])
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
    public function revenueChart($filters = [])
    {
        return $this->repo->revenueChart($filters);
    }

    // TOP TICKETS
    public function topTickets($filters = [])
    {
        return $this->repo->topTickets($filters);
    }

    public function topParks($filters = [])
    {
        return $this->repo->topParks($filters)
            ->map(function ($park) {

                $park->bookings_count =
                    (int) $park->bookings_count;

                return $park;
            });
    }

    public function latestBookings($filters = [])
    {
        return $this->repo->latestBookings(
            $filters
        );
    }

    public function latestPayments($filters = [])
    {
        return $this->repo->latestPayments($filters);
    }

    public function bookingStatus($filters = [])
    {
        return $this->repo->bookingStatus($filters);
    }

    public function paymentStatus($filters = [])
    {
        return $this->repo->paymentStatus($filters);
    }
}