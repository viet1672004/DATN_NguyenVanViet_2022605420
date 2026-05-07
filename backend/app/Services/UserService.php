<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    // =========================
    // UPDATE PROFILE
    // =========================
    public function updateProfile($user, $data)
    {
        return $this->userRepo->update($user, [
            'Name'  => $data['Name'],
            'Email' => $data['Email'],
            'Phone' => $data['Phone'] ?? null,
        ]);
    }

    // =========================
    // CHANGE PASSWORD
    // =========================
    public function changePassword($user, $data)
    {
        // check mật khẩu cũ
        if (!Hash::check($data['current_password'], $user->Password)) {
            throw new \Exception("Mật khẩu hiện tại không đúng");
        }

        // update password
        return $this->userRepo->updatePassword(
            $user,
            Hash::make($data['new_password'])
        );
    }
}