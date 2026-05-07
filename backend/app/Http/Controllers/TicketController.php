<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\IndexRequest;
use App\Http\Requests\Ticket\StoreRequest;
use App\Services\TicketService;

class TicketController extends Controller
{
    protected $service;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public function index(IndexRequest $request)
    {
        $data = $this->service->getList($request->validated());

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage()
        ]);
    }

    public function show($id)
    {
        $data = $this->service->getDetail($id);

        if (!$data) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        return response()->json($data);
    }

    public function store(StoreRequest $request)
    {
        return response()->json(
            $this->service->create($request->validated())
        );
    }

    public function update(StoreRequest $request, $id)
    {
        $ticket = $this->service->getDetail($id);

        if (!$ticket) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $this->service->update($ticket, $request->validated());

        return response()->json([
            'message' => 'Cập nhật thành công'
        ]);
    }

    public function destroy($id)
    {
        $ticket = $this->service->getDetail($id);

        if (!$ticket) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $this->service->delete($ticket);

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }

    public function dropdown()
    {
        return response()->json(
            \App\Models\Ticket::where('Status', 1)
                ->select('ID', 'TicketName', 'Price', 'ParkID')
                ->get()
        );
    }
}