<?php

namespace App\Repositories;

use App\Models\BookingDetail;

class BookingDetailRepository
{
    public function insertMany($data)
    {
        return BookingDetail::insert($data);
    }

    public function deleteByBooking($bookingId)
    {
        return BookingDetail::where('BookingID', $bookingId)->delete();
    }

    public function create($data)
    {
        return BookingDetail::create($data);
    }

    public function updateById($id, $data)
    {
        return BookingDetail::where('ID', $id)->update($data);
    }

    public function deleteNotIn($bookingId, $ids)
    {
        if (empty($ids)) {
            return BookingDetail::where('BookingID', $bookingId)->delete();
        }

        return BookingDetail::where('BookingID', $bookingId)
            ->whereNotIn('ID', $ids)
            ->delete();
    }

}