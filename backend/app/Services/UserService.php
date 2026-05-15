<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    // =====================
    // UPDATE PROFILE
    // =====================
    public function updateProfile($user, $data)
    {
        $this->repo->update($user, [
            'Name'  => $data['Name'],
            'Email' => $data['Email'],
            'Phone' => $data['Phone'] ?? null,
        ]);

        return $user->fresh()->load('role');
    }

    // =====================
    // CHANGE PASSWORD
    // =====================
    public function changePassword($user, $data)
    {
        if (!Hash::check($data['current_password'], $user->Password)) {
            throw new \Exception("Mật khẩu hiện tại không đúng");
        }

        return $this->repo->updatePassword(
            $user,
            Hash::make($data['new_password'])
        );
    }

    // =====================
    // ADMIN LIST
    // =====================
    public function getUsers($request)
    {
        return $this->repo->getUsers($request);
    }

    // DETAIL
    public function getUserDetail($id)
    {
        return $this->repo->findWithRelations($id);
    }

    // BLOCK / UNBLOCK
    public function toggleBlock($id)
    {
        return $this->repo->toggleBlock($id);
    }

    // DELETE
    public function deleteUser($id)
    {
        $this->repo->delete($id);

        return ['message' => 'Đã xóa người dùng'];
    }
}