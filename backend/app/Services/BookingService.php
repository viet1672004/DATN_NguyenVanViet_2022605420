<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\BookingDetailRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\Park;

class BookingService
{
    protected $bookingRepo;
    protected $detailRepo;

    public function __construct(
        BookingRepository $bookingRepo,
        BookingDetailRepository $detailRepo
    ) {
        $this->bookingRepo = $bookingRepo;
        $this->detailRepo = $detailRepo;
    }

    // ================= LIST =================
    public function getAll($request)    
    {
        $query = $this->bookingRepo->query()->with('user', 'park');

        $user = auth()->user();

        if (strtolower($user->role->RoleName) === 'customer') {

            $query->where('UserID', $user->ID);
        }

        if ($request->status !== null) {
            $query->where('Status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('BookingCode', 'like', "%$search%")
                  ->orWhere('UserID', 'like', "%$search%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('Name', 'like', "%$search%");
                  });
            });
        }

        if ($request->from_date) {
            $query->whereDate('BookingDateTime', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('BookingDateTime', '<=', $request->to_date);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10)
            ->through(function ($item) {

                $totalQuantity = $item->details->sum('Quantity');

                return [
                    'ID' => $item->ID,
                    'BookingCode' => $item->BookingCode,

                    'UserName' => $item->user->Name ?? '',
                    
                    'ParkName' => optional($item->park)->ParkName,

                    'BookingDateTime' => date('d/m/Y - H:i', strtotime($item->BookingDateTime)),

                    'Quantity' => $totalQuantity,

                    'TotalPrice' => number_format($item->TotalPrice, 0, ',', '.') . 'đ',

                    'Status' => $this->mapStatus($item->Status),
                ];
            });
    }

    public function find($id)
    {
        $item = $this->bookingRepo->query()
            ->with('user', 'details.ticket', 'park')
            ->where('ID', $id)
            ->first();

        if (!$item) return null;

        $user = auth()->user();

        if (
            strtolower($user->role->RoleName) === 'customer'
            && $item->UserID != $user->ID
        ) {
            throw new \Exception('Không có quyền truy cập');
        }

        return [
            'ID' => $item->ID,
            'BookingCode' => $item->BookingCode,
            'UserID' => $item->UserID,
            'UserName' => optional($item->user)->Name,
            'ParkID' => $item->ParkID,
            'ParkName' => optional($item->park)->ParkName,
            'BookingDateTime' => $item->BookingDateTime,
            'TotalPrice' => $item->TotalPrice,
            'Status' => $item->Status,

            'details' => $item->details->map(function ($d) {
                return [
                    'ID' => $d->ID,
                    'TicketID' => $d->TicketID,
                    'TicketName' => optional($d->ticket)->TicketName,
                    'TicketType' => optional($d->ticket)->TicketType,
                    'NumberOfDays' => optional($d->ticket)->NumberOfDays,
                    'ComboInfo' => optional($d->ticket)->ComboInfo,
                    'Quantity' => $d->Quantity,
                    'Price' => $d->Price,
                    'Total' => $d->Quantity * $d->Price,
                ];
            })
        ];
    }

    // ================= CREATE =================
    public function create($request)
    {
        return DB::transaction(function () use ($request) {

            $userId = auth()->user()->ID;

            $bookingId = (string) Str::uuid();
            $details = [];
            $total = 0;
            $parkId = $request->ParkID;

            foreach ($request->details as $item) {

                $ticket = Ticket::findOrFail($item['TicketID']);

                // 🔥 CHECK vé có thuộc park không
                if ($ticket->ParkID !== $parkId) {
                    throw new \Exception("Vé không thuộc khu đã chọn");
                }

                $price = $ticket->Price;

                $total += $price * $item['Quantity'];

                $details[] = [
                    'ID' => (string) Str::uuid(),
                    'BookingID' => $bookingId,
                    'TicketID' => $ticket->ID,
                    'Quantity' => $item['Quantity'],
                    'Price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->bookingRepo->create([
                'ID' => $bookingId,
                'UserID' => $userId,
                'ParkID' => $parkId,
                'BookingDate' => date('Y-m-d', strtotime($request->BookingDateTime)),
                'BookingDateTime' => $request->BookingDateTime,
                'TotalPrice' => $total,
                'Status' => 0,
            ]);

            $this->detailRepo->insertMany($details);

            return $this->bookingRepo->find($bookingId);
        });
    }

    // ================= UPDATE =================
    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {

            $booking = $this->bookingRepo->find($id);
            if (!$booking) return null;

            if ($booking->Status != 0) {
                throw new \Exception("Không được phép sửa booking này");
            }

            $isChangePark = $request->ParkID && $request->ParkID != $booking->ParkID;

            // 🔥 Nếu đổi park → xóa hết detail cũ
            if ($isChangePark) {
                $this->detailRepo->deleteByBooking($id);
            }

            $total = $booking->TotalPrice;
            $newDetailIds = [];

            if ($request->details !== null) {

                $total = 0;

                foreach ($request->details as $item) {

                    $ticket = Ticket::findOrFail($item['TicketID']);

                    $parkId = $request->ParkID ?? $booking->ParkID;

                    // 🔥 CHECK vé thuộc park
                    if ($ticket->ParkID !== $parkId) {
                        throw new \Exception("Vé không thuộc khu đã chọn");
                    }

                    $price = $ticket->Price;
                    $total += $price * $item['Quantity'];

                    // 🔥 Nếu KHÔNG đổi park + có ID → update
                    if (!$isChangePark && !empty($item['ID'])) {

                        $this->detailRepo->updateById($item['ID'], [
                            'TicketID' => $item['TicketID'],
                            'Quantity' => $item['Quantity'],
                            'Price' => $price,
                        ]);

                        $newDetailIds[] = $item['ID'];

                    } else {
                        // 🔥 insert mới
                        $detail = $this->detailRepo->create([
                            'ID' => (string) Str::uuid(),
                            'BookingID' => $id,
                            'TicketID' => $item['TicketID'],
                            'Quantity' => $item['Quantity'],
                            'Price' => $price,
                        ]);

                        $newDetailIds[] = $detail->ID;
                    }
                }

                // 🔥 CHỈ deleteNotIn khi KHÔNG đổi park
                if (!$isChangePark) {
                    $this->detailRepo->deleteNotIn($id, $newDetailIds);
                }
            }

            $booking->update([
                'ParkID' => $request->ParkID ?? $booking->ParkID,

                'BookingDate' => $request->BookingDateTime
                    ? date('Y-m-d', strtotime($request->BookingDateTime))
                    : $booking->BookingDate,

                'BookingDateTime' => $request->BookingDateTime ?? $booking->BookingDateTime,

                'TotalPrice' => $total,

                'Status' => $request->Status ?? $booking->Status
            ]);

            return $this->bookingRepo->find($id);
        });
    }

    // ================= DELETE =================
    public function delete($id)
    {
        throw new \Exception("Không được phép xóa booking. Vui lòng dùng chức năng hủy.");
    }

    // ================= Cancel =================
    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {

            $booking = $this->bookingRepo->find($id);

            if (!$booking) {
                throw new \Exception("Không tìm thấy booking");
            }

            if ($booking->Status != 0) {
                throw new \Exception("Chỉ được hủy khi đang chờ thanh toán");
            }

            $booking->update([
                'Status' => 2 // Đã hủy
            ]);

            return true;
        });
    }

    private function mapStatus($status)
    {
        switch ($status) {
            case 0:
                return [
                    'text' => 'Chờ Thanh Toán',
                    'icon' => '🟡',
                    'color' => 'warning'
                ];
            case 1:
                return [
                    'text' => 'Đã Thanh Toán',
                    'icon' => '🟢',
                    'color' => 'success'
                ];
            case 2:
                return [
                    'text' => 'Đã hủy',
                    'icon' => '🔴',
                    'color' => 'danger'
                ];
            default:
                return [
                    'text' => 'Không xác định',
                    'icon' => '⚪',
                    'color' => 'secondary'
                ];
        }
    }
}