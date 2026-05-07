<?php

namespace App\Http\Controllers;

use App\Http\Requests\Park\IndexRequest;
use App\Http\Requests\Park\StoreRequest;
use App\Services\ParkService;

class ParkController extends Controller
{
    protected $service;

    public function __construct(ParkService $service)
    {
        $this->service = $service;
    }

    // Danh sách
    public function index(IndexRequest $request)
    {
        $data = $this->service->getList($request->validated());

        return response()->json([
            'items' => $data->items(),
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage()
        ]);
    }

    // Chi tiết
    public function show($id)
    {
        $park = $this->service->getDetail($id);

        if (!$park) {
            return response()->json([
                'message' => 'Không tìm thấy khu vui chơi'
            ], 404);
        }

        return response()->json($park);
    }

    // Thêm mới
    public function store(StoreRequest $request)
    {
        $park = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Thêm thành công',
            'data' => $park
        ]);
    }

    public function update(StoreRequest $request, $id)
    {
        $park = $this->service->getDetail($id);

        if (!$park) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $this->service->update($park, $request->validated());

        return response()->json([
            'message' => 'Cập nhật thành công'
        ]);
    }

    public function destroy($id)
    {
        $park = $this->service->getDetail($id);

        if (!$park) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $this->service->delete($park);

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }
}