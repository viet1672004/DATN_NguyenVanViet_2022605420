<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Repositories\ChatBotRepository;
use App\Models\ChatHistory;
use Illuminate\Support\Str;

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
    | CHAT AI
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
                'reply' => 'Vui lòng đăng nhập để sử dụng chatbot.'
            ];
        }

        $message = trim($data['message'] ?? '');

        if (!$message) {

            return [
                'reply' => 'Bạn hãy nhập nội dung cần hỗ trợ.'
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
        | HANDLE AI
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
        | CLEAN RESPONSE
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
    | AI TICKET
    |--------------------------------------------------------------------------
    */

    private function ticketAI($message)
    {
        $tickets = $this->repo
            ->getTickets();

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filtered = $tickets->filter(function ($item) use ($message) {

            $text = strtolower(

                ($item->TicketName ?? '') . ' ' .
                ($item->Description ?? '') . ' ' .
                ($item->park->ParkName ?? '')
            );

            return $this->matchMessage(
                $text,
                $message
            );
        });

        if ($filtered->isNotEmpty()) {

            $tickets = $filtered;
        }

        /*
        |--------------------------------------------------------------------------
        | NO RESULT
        |--------------------------------------------------------------------------
        */

        if ($tickets->isEmpty()) {

            return [
                'reply' => 'Hiện tại mình chưa tìm thấy vé phù hợp với nhu cầu của bạn.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD DATA
        |--------------------------------------------------------------------------
        */

        $data = '';

        foreach ($tickets->take(5) as $item) {

            $data .= "

Tên vé:
{$item->TicketName}

Giá:
{$item->Price}

Khu:
{$item->park->ParkName}

Mô tả:
{$item->Description}

-------------------

";
        }

        /*
        |--------------------------------------------------------------------------
        | PROMPT
        |--------------------------------------------------------------------------
        */

        $prompt = "

Người dùng hỏi:
{$message}

Dữ liệu hệ thống:
{$data}

Yêu cầu:
- trả lời tự nhiên như nhân viên tư vấn thật
- ưu tiên đúng nhu cầu người dùng
- trả lời rõ ràng
- tối đa 4 câu
- không lan man
- không liệt kê toàn bộ dữ liệu
- nếu có vé phù hợp thì giới thiệu ngắn gọn
- chỉ dùng dữ liệu được cung cấp
- không bịa dữ liệu

";

        return [
            'reply' => $this->askGPT($prompt)
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | AI PARK
    |--------------------------------------------------------------------------
    */

    private function parkAI($message)
    {
        $parks = $this->repo
            ->getParks();

        /*
        |--------------------------------------------------------------------------
        | KEYWORDS
        |--------------------------------------------------------------------------
        */

        $keywords = $this->extractKeywords(
            $message
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filtered = $parks->filter(function ($item) use ($message, $keywords) {

            $text = strtolower(

                ($item->ParkName ?? '') . ' ' .
                ($item->Description ?? '') . ' ' .
                ($item->Location ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | MATCH KEYWORD
            |--------------------------------------------------------------------------
            */

            foreach ($keywords as $keyword) {

                if (
                    str_contains(
                        $text,
                        strtolower($keyword)
                    )
                ) {

                    return true;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | MATCH MESSAGE
            |--------------------------------------------------------------------------
            */

            return $this->matchMessage(
                $text,
                $message
            );
        });

        if ($filtered->isNotEmpty()) {

            $parks = $filtered;
        }

        /*
        |--------------------------------------------------------------------------
        | NO RESULT
        |--------------------------------------------------------------------------
        */

        if ($parks->isEmpty()) {

            return [
                'reply' => 'Hiện tại mình chưa tìm thấy khu vui chơi phù hợp với nhu cầu của bạn.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD DATA
        |--------------------------------------------------------------------------
        */

        $data = '';

        foreach ($parks->take(5) as $item) {

            $data .= "

Tên khu:
{$item->ParkName}

Địa điểm:
{$item->Location}

Mô tả:
{$item->Description}

Giờ mở cửa:
{$item->OpenTime}

Giờ đóng cửa:
{$item->CloseTime}

-------------------

";
        }

        /*
        |--------------------------------------------------------------------------
        | PROMPT
        |--------------------------------------------------------------------------
        */

        $prompt = "

        Người dùng hỏi:
        {$message}

        Dữ liệu hệ thống:
        {$data}

        Yêu cầu:
        - trả lời tự nhiên như người thật
        - đầy đủ ý
        - không được trả lời giữa chừng
        - tối đa 4 câu
        - không lan man
        - nếu có nhiều lựa chọn thì nêu rõ tên
        - phải nói rõ nội dung chính
        - chỉ dùng dữ liệu được cung cấp
        - không bịa dữ liệu

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
                'reply' => 'Bạn vui lòng gửi mã booking để mình kiểm tra giúp nhé.'
            ];
        }

        $booking = $this->repo
            ->getBookingByCode(
                $bookingCode
            );

        if (!$booking) {

            return [
                'reply' => 'Mình chưa tìm thấy đơn hàng này.'
            ];
        }

        return [
            'reply' => "

                    Mã booking {$booking->BookingCode} hiện đang ở trạng thái {$booking->Status}.

                    "
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

    Bạn là chatbot chăm sóc khách hàng của FunTicket.

    Phong cách:
    - trả lời tự nhiên như người thật
    - nói chuyện thân thiện
    - rõ ràng
    - đầy đủ ý
    - không cụt ngủn
    - không lan man
    - tối đa 4 câu
    - không dùng icon
    - không lặp lại lời chào

    Quy tắc:
    - chỉ dùng dữ liệu được cung cấp
    - không bịa dữ liệu
    - không suy diễn
    - luôn trả lời hoàn chỉnh câu
    - không được dừng giữa chừng
    - nếu đang liệt kê phải liệt kê đầy đủ
    - luôn ghi rõ tên khu hoặc tên vé
    - nếu có nhiều dữ liệu thì chọn dữ liệu phù hợp nhất

    Lịch sử hội thoại:
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

                ])->post(
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

                            'temperature' => 0.4,

                            'maxOutputTokens' => 500,

                            'topP' => 0.8
                        ]
                    ]
                );

            $data = $response->json();

            /*
            |--------------------------------------------------------------------------
            | QUOTA
            |--------------------------------------------------------------------------
            */

            if (
                isset($data['error']['message']) &&
                str_contains(
                    strtolower($data['error']['message']),
                    'quota'
                )
            ) {

                return 'Hệ thống AI đang quá tải tạm thời, bạn thử lại sau ít giây nhé.';
            }

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            if (isset($data['error'])) {

                return 'AI hiện chưa phản hồi ổn định, bạn thử lại sau nhé.';
            }

            /*
            |--------------------------------------------------------------------------
            | GET TEXT
            |--------------------------------------------------------------------------
            */

            $text = trim(
                $data['candidates'][0]['content']['parts'][0]['text']
                ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if (!$text) {

                return 'Mình chưa thể phản hồi lúc này.';
            }

            /*
            |--------------------------------------------------------------------------
            | CLEAN TEXT
            |--------------------------------------------------------------------------
            */

            $text = preg_replace('/\s+/', ' ', $text);

            /*
            |--------------------------------------------------------------------------
            | FIX CUT RESPONSE
            |--------------------------------------------------------------------------
            */

            $lastChar = mb_substr($text, -1);

            $validEnds = ['.', '!', '?', '"', '”'];

            if (!in_array($lastChar, $validEnds)) {

                $text .= '...';
            }

            return trim($text);

        } catch (\Exception $e) {

            return 'Hệ thống AI hiện đang bận, bạn thử lại sau nhé.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT INTENT
    |--------------------------------------------------------------------------
    */

    private function detectIntent($message)
    {
        $message = strtolower($message);

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
            str_contains($message, 'giải trí')

        ) {

            return 'park';
        }

        return 'general';
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT KEYWORDS
    |--------------------------------------------------------------------------
    */

    private function extractKeywords($message)
    {
        $message = strtolower($message);

        $keywords = [];

        /*
        |--------------------------------------------------------------------------
        | BEACH
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'biển')
        ) {

            $keywords = array_merge($keywords, [

                'biển',
                'bãi biển',
                'water',
                'công viên nước',
                'hồ bơi'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KID
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'trẻ em')
        ) {

            $keywords[] = 'trẻ em';
        }

        /*
        |--------------------------------------------------------------------------
        | ADVENTURE
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'mạo hiểm')
        ) {

            $keywords[] = 'mạo hiểm';
        }

        return array_unique($keywords);
    }

    /*
    |--------------------------------------------------------------------------
    | MATCH MESSAGE
    |--------------------------------------------------------------------------
    */

    private function matchMessage($text, $message)
    {
        $message = strtolower($message);

        $words = explode(
            ' ',
            $message
        );

        foreach ($words as $word) {

            $word = trim($word);

            if (
                strlen($word) < 2
            ) {
                continue;
            }

            if (
                str_contains(
                    $text,
                    $word
                )
            ) {

                return true;
            }
        }

        return false;
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

            return 'Mình có thể hỗ trợ bạn về vé, booking hoặc tư vấn khu vui chơi phù hợp.';
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

            return 'Rất vui vì đã hỗ trợ được bạn.';
        }

        /*
        |--------------------------------------------------------------------------
        | CONSULT
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($message, 'tư vấn')
        ) {

            return 'Bạn muốn đi biển, công viên nước hay khu vui chơi cảm giác mạnh để mình gợi ý phù hợp nhé?';
        }

        /*
        |--------------------------------------------------------------------------
        | SHORT MESSAGE
        |--------------------------------------------------------------------------
        */

        if (strlen($message) < 3) {

            return 'Bạn có thể nói rõ hơn để mình hỗ trợ chính xác hơn nhé.';
        }

        /*
        |--------------------------------------------------------------------------
        | AI FALLBACK
        |--------------------------------------------------------------------------
        */

        return $this->askGPT("

        Người dùng hỏi:
        {$message}

        Yêu cầu:
        - trả lời tự nhiên
        - đúng trọng tâm
        - tối đa 4 câu
        - không lan man
        - không bịa dữ liệu

        ");
    }
}