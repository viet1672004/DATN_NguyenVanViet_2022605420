<?php

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository
{
    public function query()
    {
        return Booking::query()->with('details');
    }

    public function create($data)
    {
        return Booking::create($data);
    }

    public function find($id)
    {
        return Booking::with('details')->find($id);
    }

    public function delete($id)
    {
        return Booking::destroy($id);
    }
}