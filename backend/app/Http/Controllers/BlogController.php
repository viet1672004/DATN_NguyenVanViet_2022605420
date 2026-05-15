<?php

namespace App\Http\Controllers;

use App\Services\BlogService;

use App\Http\Requests\Blog\IndexRequest;
use App\Http\Requests\Blog\StoreRequest;
use App\Http\Requests\Blog\UpdateRequest;

class BlogController extends Controller
{
    protected $service;

    public function __construct(BlogService $service)
    {
        $this->service = $service;
    }

    // Danh sách
    public function index(IndexRequest $request)
    {
        return response()->json(
            $this->service->index($request)
        );
    }

    // Chi tiết
    public function show($id)
    {
        return response()->json(
            $this->service->show($id)
        );
    }

    // Thêm
    public function store(StoreRequest $request)
    {
        return response()->json(
            $this->service->store($request)
        );
    }

    // Sửa
    public function update(UpdateRequest $request, $id)
    {
        return response()->json(
            $this->service->update($id, $request)
        );
    }

    // Xóa
    public function destroy($id)
    {
        return response()->json([
            'success' => $this->service->delete($id)
        ]);
    }
}