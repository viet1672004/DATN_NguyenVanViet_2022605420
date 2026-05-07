<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register($request)
    {
        $role = Role::whereRaw('LOWER(RoleName) = ?', ['customer'])
            ->first();

        if (!$role) {
            throw new \Exception('Role customer không tồn tại');
        }

        $user = User::create([
            'ID'        => (string) Str::uuid(),
            'Name'      => $request->name,
            'Email'     => $request->email,
            'Password'  => Hash::make($request->password),
            'Phone'     => $request->phone,
            'RoleID'    => $role->ID,
            'Status'    => 1
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => [
                'ID' => $user->ID,
                'Name' => $user->Name,
                'Email' => $user->Email,
                'Role' => 'customer'
            ],

            'token' => $token
        ];
    }

    public function login($request)
    {
        $user = User::with('role')
            ->where('Email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->Password)) {
            return null;
        }

        // 🔥 CHECK STATUS
        if ($user->Status == 0) {

            throw new \Exception('Tài khoản đã bị khóa');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [

            'user' => [
                'ID' => $user->ID,
                'Name' => $user->Name,
                'Email' => $user->Email,
                'Role' => strtolower($user->role->RoleName)
            ],

            'token' => $token
        ];
    }

    public function logout($request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}