<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'items' => $this->service->getUsers($request)
        ]);
    }

    public function show($id)
    {
        return response()->json(
            $this->service->getUserDetail($id)
        );
    }

    public function toggleBlock($id)
    {
        $user = $this->service->toggleBlock($id);

        return response()->json([
            'message' => $user->Status == 1
                ? 'Đã mở khóa tài khoản'
                : 'Đã khóa tài khoản',
            'status' => $user->Status,
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $this->service->deleteUser($id);

        return response()->json([
            'message' => 'Xóa user thành công'
        ]);
    }
}