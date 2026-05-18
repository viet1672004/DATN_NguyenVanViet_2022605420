<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Repositories\ChatBotRepository;
use App\Models\ChatHistory;
use Illuminate\Support\Facades\Session;

class ChatBotService
{
    protected $repo;

    public function __construct(
        ChatBotRepository $repo
    ) {
        $this->repo = $repo;
    }

    /*
    |--------------------------------------------------------------------------
    | CHAT
    |--------------------------------------------------------------------------
    */

    public function chat($data)
    {
        /*
        |--------------------------------------------------------------------------
        | CHECK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {

            return [
                'reply' => 'Vui lòng đăng nhập.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        $message = $this->cleanMessage(
            $data['message'] ?? ''
        );

        if (!$message) {

            return [
                'reply' => 'Vui lòng nhập nội dung.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DETECT INTENT
        |--------------------------------------------------------------------------
        */

        $intent = $this->detectIntent(
            $message
        );

        /*
        |--------------------------------------------------------------------------
        | HANDLE
        |--------------------------------------------------------------------------
        */

        switch ($intent) {

            case 'ticket':

                $result = $this->ticketAI(
                    $message
                );

                break;

            case 'park':

                $result = $this->parkAI(
                    $message
                );

                break;

            case 'booking':

                $result = $this->bookingAI(
                    $message
                );

                break;

            default:

                $result = [
                    'reply' => $this->generalAI(
                        $message
                    )
                ];
        }

        /*
        |--------------------------------------------------------------------------
        | CLEAN REPLY
        |--------------------------------------------------------------------------
        */

        $reply = trim(

            preg_replace(
                '/\s+/',
                ' ',
                $result['reply'] ?? ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE HISTORY
        |--------------------------------------------------------------------------
        */

        ChatHistory::create([

            'ID' => Str::uuid(),

            'UserID' => auth()->id(),

            'Message' => $message,

            'Reply' => $reply,

            'Intent' => $intent
        ]);

        return [
            'reply' => $reply
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAN MESSAGE
    |--------------------------------------------------------------------------
    */

    private function cleanMessage($message)
    {
        $message = strip_tags($message);

        $message = preg_replace(
            '/\s+/',
            ' ',
            $message
        );

        return trim($message);
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE
    |--------------------------------------------------------------------------
    */

    private function normalize($text)
    {
        $text = mb_strtolower($text);

        $text = preg_replace(
            '/[^\p{L}\p{N}\s]/u',
            '',
            $text
        );

        return trim($text);
    }

    /*
    |--------------------------------------------------------------------------
    | TICKET AI
    |--------------------------------------------------------------------------
    */

    private function ticketAI($message)
    {

    /*
    |----------------------------------------------------------------------
    | DETECT PARK FROM MESSAGE
    |----------------------------------------------------------------------
    */

    $parkName = null;

    $allParks = $this->repo
        ->getParks();

    foreach ($allParks as $park) {

        if (

            str_contains(
                mb_strtolower($message),
                mb_strtolower($park->ParkName)
            )

        ) {

            $parkName = $park->ParkName;

            Session::put(
                'last_park',
                $parkName
            );

            break;
        }
    }

    /*
    |----------------------------------------------------------------------
    | FALLBACK SESSION
    |----------------------------------------------------------------------
    */

    if (!$parkName) {

        $parkName = Session::get(
            'last_park'
        );
    }

    /*
    |----------------------------------------------------------------------
    | SEARCH
    |----------------------------------------------------------------------
    */

    if (

        $parkName &&

        (
            str_contains(
                mb_strtolower($message),
                'vé'
            )

            ||

            str_contains(
                mb_strtolower($message),
                'giá'
            )

            ||

            str_contains(
                mb_strtolower($message),
                'ticket'
            )
        )

    ) {

        $tickets = $this->repo
            ->getTicketsByParkName(
                $parkName
            );

    } else {

        $tickets = $this->repo
            ->searchTickets($message);
    }

    /*
    |----------------------------------------------------------------------
    | EMPTY
    |----------------------------------------------------------------------
    */

    if ($tickets->isEmpty()) {

        return [
            'reply' =>
                'Hiện chưa tìm thấy vé phù hợp.'
        ];
    }

    /*
    |----------------------------------------------------------------------
    | DATA
    |----------------------------------------------------------------------
    */

    $data = [];

    foreach ($tickets as $item) {

        $data[] = [

            'Tên vé' =>
                $item->TicketName,

            'Giá vé' =>
                number_format(
                    $item->Price
                ) . ' VNĐ',

            'Khu vui chơi' =>
                $item->park->ParkName ?? '',

            'Địa điểm' =>
                $item->park->Location ?? '',

            'Mô tả' =>
                $item->Description ?? '',

            'Giờ mở cửa' =>
                $item->park->OpenTime ?? '',

            'Giờ đóng cửa' =>
                $item->park->CloseTime ?? '',
        ];
    }

    /*
    |----------------------------------------------------------------------
    | PROMPT
    |----------------------------------------------------------------------
    */

    $prompt = "

Người dùng hỏi:
{$message}

Danh sách vé:
" . json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    ) . "

Yêu cầu:
- trả lời đầy đủ
- giới thiệu vé phù hợp
- nêu giá vé
- nêu khu vui chơi
- trình bày dễ đọc
- trả lời tự nhiên như nhân viên tư vấn
- không bịa thông tin

";

    return [
        'reply' => $this->askGPT($prompt)
    ];
}

    /*
    |--------------------------------------------------------------------------
    | PARK AI
    |--------------------------------------------------------------------------
    */

    private function parkAI($message)
    {
        /*
        |----------------------------------------------------------------------
        | SEARCH
        |----------------------------------------------------------------------
        */

        $parks = $this->repo
            ->searchParks($message);

        /*
        |----------------------------------------------------------------------
        | FALLBACK
        |----------------------------------------------------------------------
        */

        if ($parks->isEmpty()) {

            $parks = $this->repo
                ->getParks();
        }

        /*
        |----------------------------------------------------------------------
        | EMPTY
        |----------------------------------------------------------------------
        */

        if ($parks->isEmpty()) {

            return [
                'reply' =>
                    'Hiện chưa có dữ liệu khu vui chơi.'
            ];
        }

        /*
        |----------------------------------------------------------------------
        | SAVE CONTEXT
        |----------------------------------------------------------------------
        */

        Session::put(
            'last_park',
            $parks->first()->ParkName
        );

        /*
        |----------------------------------------------------------------------
        | DATA
        |----------------------------------------------------------------------
        */

        $data = [];

        foreach ($parks as $item) {

            $data[] = [

                'Tên khu' =>
                    $item->ParkName,

                'Địa điểm' =>
                    $item->Location,

                'Mô tả' =>
                    $item->Description,

                'Giờ mở cửa' =>
                    $item->OpenTime,

                'Giờ đóng cửa' =>
                    $item->CloseTime,
            ];
        }

        /*
        |----------------------------------------------------------------------
        | PROMPT
        |----------------------------------------------------------------------
        */

        $prompt = "

    Người dùng hỏi:
    {$message}

    Danh sách khu vui chơi:
    " . json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        ) . "

    Yêu cầu:
    - tư vấn chi tiết
    - ưu tiên khu phù hợp nhất
    - nếu liên quan biển thì ưu tiên:
    + công viên nước
    + hồ bơi
    + khu gần biển
    - giới thiệu nổi bật
    - trình bày dễ đọc
    - trả lời tự nhiên
    - không bịa thông tin

    ";

        return [
            'reply' => $this->askGPT($prompt)
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING AI
    |--------------------------------------------------------------------------
    */

    private function bookingAI($message)
    {
        preg_match(
            '/FT[0-9]+/',
            strtoupper($message),
            $matches
        );

        $bookingCode = $matches[0] ?? null;

        if (!$bookingCode) {

            return [
                'reply' => 'Vui lòng gửi mã booking.'
            ];
        }

        $booking = $this->repo
            ->getBookingByCode(
                $bookingCode
            );

        if (!$booking) {

            return [
                'reply' => 'Không tìm thấy booking.'
            ];
        }

        $statusMap = [

            'pending' =>
                'Chờ thanh toán',

            'paid' =>
                'Đã thanh toán',

            'cancelled' =>
                'Đã hủy',

            'completed' =>
                'Hoàn thành',
        ];

        $status = $statusMap[
            strtolower($booking->Status)
        ] ?? $booking->Status;

        return [
            'reply' =>
                "Booking {$booking->BookingCode}: {$status}."
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ASK GEMINI
    |--------------------------------------------------------------------------
    */

    private function askGPT($prompt)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | HISTORY
            |--------------------------------------------------------------------------
            */

            $histories = ChatHistory::query()

                ->where(
                    'UserID',
                    auth()->id()
                )

                ->latest('CreatedAt')

                ->limit(5)

                ->get();

            $historyText = '';

            foreach ($histories as $item) {

                $historyText .= "

Người dùng:
{$item->Message}

Chatbot:
{$item->Reply}

";
            }

            /*
            |--------------------------------------------------------------------------
            | FULL PROMPT
            |--------------------------------------------------------------------------
            */

            $fullPrompt = "

Bạn là chatbot hỗ trợ khách hàng của FunTicket.

Nguyên tắc:
- chỉ sử dụng dữ liệu được cung cấp
- không bịa thông tin
- nếu không có dữ liệu thì nói rõ
- trả lời đầy đủ
- trình bày rõ ràng
- ưu tiên trải nghiệm người dùng
- có thể xuống dòng cho dễ đọc
- giới thiệu chi tiết nếu phù hợp
- tư vấn tự nhiên như nhân viên hỗ trợ thật

Lịch sử:
{$historyText}

{$prompt}

";

            /*
            |--------------------------------------------------------------------------
            | API
            |--------------------------------------------------------------------------
            */

            $response = Http::timeout(30)

                ->withHeaders([

                    'Content-Type' => 'application/json',
                ])

                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
                    [

                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $fullPrompt
                                    ]
                                ]
                            ]
                        ],

                        'generationConfig' => [

                            'temperature' => 0.7,

                            'maxOutputTokens' => 10000,

                            'topP' => 0.9,

                            'topK'  => 40,
                        ]
                    ]
                );

            $data = $response->json();

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            if (isset($data['error'])) {

                return 'AI hiện chưa phản hồi.';
            }

            /*
            |--------------------------------------------------------------------------
            | TEXT
            |--------------------------------------------------------------------------
            */

            $text = trim(

                data_get(
                    $data,
                    'candidates.0.content.parts.0.text',
                    ''
                )
            );

            if (!$text) {

                return 'Không có dữ liệu.';
            }

            /*
            |--------------------------------------------------------------------------
            | CLEAN
            |--------------------------------------------------------------------------
            */

            $text = preg_replace(
                '/\s+/',
                ' ',
                $text
            );

            return trim($text);

        } catch (\Exception $e) {

            return 'Hệ thống AI đang bận.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT INTENT
    |--------------------------------------------------------------------------
    */

    private function detectIntent($message)
    {
        $message = $this->normalize(
            $message
        );

        /*
        |--------------------------------------------------------------------------
        | BOOKING
        |--------------------------------------------------------------------------
        */

        if (

            str_contains($message, 'booking') ||
            str_contains($message, 'đơn hàng') ||
            str_contains($message, 'mã đơn') ||
            preg_match('/FT[0-9]+/', strtoupper($message))

        ) {

            return 'booking';
        }

        /*
        |--------------------------------------------------------------------------
        | TICKET
        |--------------------------------------------------------------------------
        */

        if (

            str_contains($message, 'vé') ||
            str_contains($message, 'ticket') ||
            str_contains($message, 'combo') ||
            str_contains($message, 'giá') ||
            str_contains($message, 'bao nhiêu tiền')

        ) {

            return 'ticket';
        }

        /*
        |--------------------------------------------------------------------------
        | PARK
        |--------------------------------------------------------------------------
        */

        if (

            str_contains($message, 'khu') ||
            str_contains($message, 'công viên') ||
            str_contains($message, 'trò chơi') ||
            str_contains($message, 'đi chơi') ||
            str_contains($message, 'du lịch') ||
            str_contains($message, 'biển') ||
            str_contains($message, 'hồ bơi') ||
            str_contains($message, 'nước') ||
            str_contains($message, 'hải phòng') ||
            str_contains($message, 'sầm sơn')

        ) {

            return 'park';
        }

        return 'general';
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAL AI
    |--------------------------------------------------------------------------
    */

    private function generalAI($message)
    {
        $message = strtolower(
            trim($message)
        );

        /*
        |--------------------------------------------------------------------------
        | GREETING
        |--------------------------------------------------------------------------
        */

        if (

            str_contains($message, 'xin chào') ||
            str_contains($message, 'hello') ||
            $message === 'hi'

        ) {

            return 'Mình có thể hỗ trợ vé, booking và khu vui chơi.';
        }

        /*
        |--------------------------------------------------------------------------
        | THANKS
        |--------------------------------------------------------------------------
        */

        if (

            str_contains($message, 'cảm ơn') ||
            str_contains($message, 'thanks')

        ) {

            return 'Rất vui được hỗ trợ.';
        }

        /*
        |--------------------------------------------------------------------------
        | SHORT
        |--------------------------------------------------------------------------
        */

        if (strlen($message) < 2) {

            return 'Vui lòng nhập rõ hơn.';
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return $this->askGPT("

Người dùng hỏi:
{$message}

Quy tắc:
- trả lời ngắn
- đúng trọng tâm
- không bịa dữ liệu

");
    }
}