<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <title>Hóa đơn thanh toán</title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 15px;
            color: #111;
            padding: 30px;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .brand {
            font-size: 30px;
            font-weight: bold;
            color: #3c8dbc;
        }

        .sub {
            color: #666;
            margin-top: 4px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 38px;
            color: #111;
        }

        .invoice-info {
            margin-top: 8px;
            color: #666;
            font-size: 13px;
        }

        .section {
            margin-top: 28px;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #3c8dbc;
            color: #3c8dbc;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            width: 220px;
            font-weight: bold;
        }

        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .ticket-table th {
            background: #4db5d7;
            color: black;
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .ticket-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            color: #111;
            font-size: 14px;
        }

        .ticket-table tbody tr:nth-child(even) {
            background: #f8f8f8;
        }

        .summary {
            width: 100%;
            margin-top: 25px;
        }

        .summary td {
            padding: 8px 0;
        }

        .summary .text {
            text-align: right;
            font-weight: bold;
        }

        .summary .value {
            width: 180px;
            text-align: right;
        }

        .grand-total {
            color: #e74c3c;
            font-size: 18px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #d4edda;
            color: #155724;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            margin-top: 60px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }

        .thank {
            font-size: 15px;
            font-weight: bold;
            color: #3c8dbc;
            margin-bottom: 8px;
        }

    </style>
</head>

<body>

    @php

        $statusText = match($payment->PaymentStatus) {
            'PAID' => 'Đã thanh toán',
            'PENDING' => 'Đang xử lý',
            'FAILED' => 'Thanh toán thất bại',
            default => $payment->PaymentStatus
        };

        function ticketTypeVN($type) {

            return match(strtoupper($type)) {

                'ADULT' => 'Người lớn',
                'CHILD' => 'Trẻ em',
                'COMBO' => 'Combo',
                'DATE' => 'Theo ngày',

                default => $type
            };
        }

    @endphp

    <!-- HEADER -->
    <table class="header">

        <tr>

            <td>

                <div class="brand">
                    FUN TICKET
                </div>

                <div class="sub">
                    Hệ thống đặt vé khu vui chơi trực tuyến
                </div>

                <div class="sub">
                    Hotline: 0328 709 600
                </div>

                <div class="sub">
                    Email: support@funticket.vn
                </div>

            </td>

            <td class="invoice-title">

                <h1>
                    HÓA ĐƠN
                </h1>

                <div class="invoice-info">
                    Mã hóa đơn:
                    INV-{{ strtoupper(substr($payment->ID, 0, 8)) }}
                </div>

                <div class="invoice-info">
                    Thời gian xuất:
                    {{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}
                </div>

                <div class="invoice-info">
                    Ngày thanh toán:
                    {{ date('d/m/Y H:i:s', strtotime($payment->PaymentDate)) }}
                </div>

            </td>

        </tr>

    </table>

    <!-- CUSTOMER -->
    <div class="section">

        <div class="section-title">
            THÔNG TIN KHÁCH HÀNG
        </div>

        <table class="info-table">

            <tr>
                <td class="label">
                    Mã booking:
                </td>

                <td>
                    {{ $payment->booking->BookingCode ?? '' }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Tên khách hàng:
                </td>

                <td>
                    {{ $payment->booking->user->Name ?? '' }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Khu vui chơi:
                </td>

                <td>
                    {{ $payment->booking->park->ParkName ?? '' }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Phương thức thanh toán:
                </td>

                <td>
                    {{ $payment->PaymentMethod }}
                </td>
            </tr>

            <tr>
                <td class="label">
                    Trạng thái thanh toán:
                </td>

                <td>
                    <span class="status">
                        {{ $statusText }}
                    </span>
                </td>
            </tr>

        </table>

    </div>

    <!-- DETAILS -->
    <div class="section">

        <div class="section-title">
            CHI TIẾT VÉ
        </div>

        <table class="ticket-table">

            <thead>

                <tr>
                    <th>STT</th>
                    <th>Tên vé</th>
                    <th>Loại vé</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>

            </thead>

            <tbody>

                @foreach($payment->booking->details as $index => $item)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item->ticket->TicketName ?? '' }}
                    </td>

                    <td>
                        {{ ticketTypeVN($item->ticket->TicketType ?? '') }}
                    </td>

                    <td>
                        {{ $item->Quantity }}
                    </td>

                    <td>
                        {{ number_format($item->Price) }} đ
                    </td>

                    <td>
                        {{ number_format($item->Price * $item->Quantity) }} đ
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <!-- SUMMARY -->
    <table class="summary">

        <tr>

            <td class="text">
                Tạm tính:
            </td>

            <td class="value">
                {{ number_format($payment->Amount) }} đ
            </td>

        </tr>

        <tr>

            <td class="text">
                VAT:
            </td>

            <td class="value">
                0 đ
            </td>

        </tr>

        <tr>

            <td class="text grand-total">
                TỔNG THANH TOÁN:
            </td>

            <td class="value grand-total">
                {{ number_format($payment->Amount) }} đ
            </td>

        </tr>

    </table>

    <!-- FOOTER -->
    <div class="footer">

        <div class="thank">
            Cảm ơn quý khách đã sử dụng dịch vụ của FunTicket!
        </div>

        <div>
            FunTicket - Website đặt vé khu vui chơi trực tuyến
        </div>

    </div>

</body>

</html>