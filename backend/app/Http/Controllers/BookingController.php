<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\Booking\IndexRequest;
use App\Http\Requests\Booking\StoreRequest;
use App\Http\Requests\Booking\UpdateRequest;
use App\Services\BookingService;

class BookingController extends Controller
{
    protected $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    // LIST
    public function index(IndexRequest $request)
    {
        $data = $this->service->getAll($request);

        return response()->json($data);
    }

    // DETAIL
    public function show($id)
    {
        $item = $this->service->find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json($item);
    }

    // CREATE
    public function store(StoreRequest $request)
    {
        return response()->json(
            $this->service->create($request)
        );
    }

    // UPDATE
    public function update(UpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request);

        if (!$result) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json($result);
    }

    // DELETE
    public function destroy($id)
    {
        return response()->json([
            'message' => 'Không hỗ trợ xóa booking'
        ], 400);
    }

    // Cancel
    public function cancel($id)
    {
        try {
            $result = $this->service->cancel($id);

            return response()->json([
                'message' => 'Hủy booking thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}