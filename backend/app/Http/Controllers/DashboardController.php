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
        return response()->json([

            'summary' => $this->service->summary(),

            'revenue_chart' => $this->service->revenueChart(),

            'top_tickets' => $this->service->topTickets(),

            'top_parks' => $this->service->topParks(),

            'latest_bookings' => $this->service->latestBookings(),

            'latest_payments' => $this->service->latestPayments(),

            'booking_status' => $this->service->bookingStatus(),

            'payment_status' => $this->service->paymentStatus(),
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
            $this->service->latestBookings()
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