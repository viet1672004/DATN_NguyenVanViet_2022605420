<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request)
    {
        return response()->json(
            $this->service->register($request),
            201
        );
    }

    public function login(LoginRequest $request)
    {
        $result = $this->service->login($request);

        if (!$result) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $this->service->logout($request);

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }
}