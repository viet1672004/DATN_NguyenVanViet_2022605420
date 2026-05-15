<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Http\Requests\Dashboard\IndexRequest;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    // ALL DASHBOARD
    public function index(IndexRequest $request)
    {
        $filters = [

            'from_date' => $request->from_date,

            'to_date' => $request->to_date,

        ];

        return response()->json([

            'summary' => $this->service->summary($filters),

            'revenue_chart' => $this->service->revenueChart($filters),

            'top_tickets' => $this->service->topTickets($filters),

            'top_parks' => $this->service->topParks($filters),

            'latest_bookings' => $this->service->latestBookings($filters),

            'latest_payments' => $this->service->latestPayments($filters),

            'booking_status' => $this->service->bookingStatus($filters),

            'payment_status' => $this->service->paymentStatus($filters),

        ]);
    }

    // SUMMARY CARD
    public function summary(IndexRequest $request)
    {
        return response()->json(
            $this->service->summary()
        );
    }

    // CHART
    public function revenueChart(IndexRequest $request)
    {
        return response()->json(
            $this->service->revenueChart()
        );
    }

    // TOP TICKETS
    public function topTickets(IndexRequest $request)
    {
        return response()->json(
            $this->service->topTickets()
        );
    }

    // TOP PARKS
    public function topParks(IndexRequest $request)
    {
        return response()->json(
            $this->service->topParks()
        );
    }

    // LATEST BOOKINGS
    public function latestBookings(IndexRequest $request)
    {
        return response()->json(

            $this->service->latestBookings(

                $request->from_date,

                $request->to_date

            )

        );
    }

    // LATEST PAYMENTS
    public function latestPayments(IndexRequest $request)
    {
        return response()->json(
            $this->service->latestPayments()
        );
    }

    // BOOKING STATUS
    public function bookingStatus(IndexRequest $request)
    {
        return response()->json(
            $this->service->bookingStatus()
        );
    }

    // PAYMENT STATUS
    public function paymentStatus(IndexRequest $request)
    {
        return response()->json(
            $this->service->paymentStatus()
        );
    }
}