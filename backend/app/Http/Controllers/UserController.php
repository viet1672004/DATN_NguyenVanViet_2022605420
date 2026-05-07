<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Services\UserService;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    // =====================
    // GET ME
    // =====================
    public function me()
    {
        return response()->json(auth()->user());
    }

    // =====================
    // UPDATE PROFILE
    // =====================
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        $this->service->updateProfile($user, $request->validated());

        return response()->json([
            'message' => 'Cập nhật thành công'
        ]);
    }

    // =====================
    // CHANGE PASSWORD
    // =====================
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $this->service->changePassword($user, $request->validated());

        return response()->json([
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}