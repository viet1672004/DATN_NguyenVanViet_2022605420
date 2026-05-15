<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use Illuminate\Http\Request;

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
    public function me(Request $request)
    {
        return response()->json(
            $request->user()->load('role')
        );
    }

    // =====================
    // UPDATE PROFILE
    // =====================
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        $updated = $this->service->updateProfile(
            $user,
            $request->validated()
        );

        return response()->json([
            'message' => 'Cập nhật thành công',
            'user' => $updated
        ]);
    }

    // =====================
    // CHANGE PASSWORD
    // =====================
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        try {
            $this->service->changePassword(
                $user,
                $request->validated()
            );

            return response()->json([
                'message' => 'Đổi mật khẩu thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}