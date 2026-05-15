<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\IndexRequest;
use App\Http\Requests\Payment\StoreRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    // ================= PAY (MOMO / BANK) =================
    public function pay(StoreRequest $request)
    {
        try {
            $data = $this->service->pay($request);

            return response()->json([
                'message' => 'Thanh toán thành công',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ================= LIST =================
    public function index(IndexRequest $request)
    {
        return response()->json(
            $this->service->getAll($request)
        );
    }

    // ================= DETAIL =================
    public function show($id)
    {
        $data = $this->service->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Không tìm thấy payment'
            ], 404);
        }

        return response()->json($data);
    }

    // ================= VNPAY CREATE =================
    public function createVnpay(Request $request)
    {
        $booking = Booking::find($request->BookingID);

        if (!$booking) {
            return response()->json(['message' => 'Booking không tồn tại'], 400);
        }

        if ($booking->Status != 0) {
            return response()->json(['message' => 'Booking đã thanh toán'], 400);
        }

        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURN_URL');
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = $booking->ID;
        $vnp_Amount = $booking->TotalPrice * 100;

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toán booking " . $booking->BookingCode,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);

        $query = http_build_query($inputData);

        $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

        $url = $vnp_Url . "?" . $query . '&vnp_SecureHash=' . $secureHash;

        return response()->json([
            'url' => $url
        ]);
    }

    // ================= VNPAY RETURN =================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);

        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // ❌ Sai chữ ký => bỏ
        if ($secureHash !== $vnp_SecureHash) {
            return redirect(env('FRONTEND_URL') . '/pay-fail');
        }

        // ✅ Thành công
        if ($request->vnp_ResponseCode == "00") {

            $booking = Booking::find($request->vnp_TxnRef);

            if ($booking && $booking->Status == 0) {

                $exists = Payment::where('BookingID', $booking->ID)->first();

                if (!$exists) {
                    Payment::create([
                        'ID' => Str::uuid(),
                        'BookingID' => $booking->ID,
                        'PaymentMethod' => 'VNPAY',
                        'Amount' => $booking->TotalPrice,
                        'PaymentStatus' => Payment::STATUS_PAID,
                        'PaymentDate' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $booking->update([
                        'Status' => 1
                    ]);
                }
            }

            return redirect(env('FRONTEND_URL') . '/pay-success');
        }

        return redirect(env('FRONTEND_URL') . '/pay-fail');
    }

    public function exportInvoice($id)
    {
        $payment = Payment::with([
            'booking.user',
            'booking.park',
            'booking.details.ticket'
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'invoice.payment',
            compact('payment')
        );

        return $pdf->download(
            'hoa-don-' . $payment->ID . '.pdf'
        );
    }

}