<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentService
{
    protected $repo;

    public function __construct(PaymentRepository $repo)
    {
        $this->repo = $repo;
    }

    // ================= PAY =================
    public function pay($request)
    {
        return DB::transaction(function () use ($request) {

            $booking = Booking::where('ID', $request->BookingID)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                throw new \Exception("Không tìm thấy booking");
            }

            // 🔥 customer chỉ được thanh toán booking của mình
            $user = auth()->user();

            if (
                strtolower($user->role->RoleName) === 'customer'
                && $booking->UserID != $user->ID
            ) {
                throw new \Exception("Không có quyền thanh toán booking này");
            }

            if ($booking->Status != 0) {
                throw new \Exception("Booking không hợp lệ để thanh toán");
            }

            $oldPayment = $this->repo->findByBooking($booking->ID);

            if ($oldPayment) {
                throw new \Exception("Booking đã có thanh toán");
            }

            $payment = $this->repo->create([
                'ID' => (string) Str::uuid(),
                'BookingID' => $booking->ID,
                'PaymentMethod' => $request->PaymentMethod,
                'Amount' => $booking->TotalPrice,
                'PaymentStatus' => Payment::STATUS_PAID,
                'PaymentDate' => now()->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // update booking
            $booking->update([
                'Status' => 1
            ]);

            return $payment;
        });
    }

    // ================= LIST =================
    public function getAll($request)
    {
        $query = $this->repo->query()
            ->with('booking');

        $user = auth()->user();

        // 🔥 customer chỉ xem payment của mình
        if (strtolower($user->role->RoleName) === 'customer') {

            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('UserID', $user->ID);
            });
        }

        if ($request->status) {
            $query->where('PaymentStatus', $request->status);
        }

        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('PaymentMethod', 'like', "%$search%")
                  ->orWhereHas('booking', function ($b) use ($search) {

                      $b->where('BookingCode', 'like', "%$search%");
                  });
            });
        }

        if ($request->from_date) {
            $query->whereDate('PaymentDate', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('PaymentDate', '<=', $request->to_date);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
    }

    // ================= DETAIL =================
    public function find($id)
    {
        $item = $this->repo->query()
            ->with('booking')
            ->where('ID', $id)
            ->first();

        if (!$item) {
            return null;
        }

        $user = auth()->user();

        // 🔥 customer chỉ xem payment của mình
        if (
            strtolower($user->role->RoleName) === 'customer'
            && $item->booking->UserID != $user->ID
        ) {
            throw new \Exception("Không có quyền truy cập");
        }

        return $item;
    }
}