<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    // =====================
    // ADMIN LIST
    // =====================
    public function getUsers($request)
    {
        $query = User::with('role')
            ->orderBy('Name');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('Name', 'like', "%{$request->search}%")
                  ->orWhere('Email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where(DB::raw('LOWER(RoleName)'), strtolower($request->role));
            });
        }

        if ($request->has('blocked') && $request->blocked !== '') {
            $status = $request->blocked == 1 ? 0 : 1;
            $query->where('Status', $status);
        }

        return $query->get();
    }

    // DETAIL
    public function findWithRelations($id)
    {
        return User::with(['role', 'bookings.park'])
            ->findOrFail($id);
    }

    // UPDATE PROFILE
    public function update($user, array $data)
    {
        return $user->update($data);
    }

    // UPDATE PASSWORD
    public function updatePassword($user, string $password)
    {
        $user->Password = $password;
        return $user->save();
    }

    // BLOCK
    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);

        // 1 = active, 0 = blocked
        $user->Status = $user->Status == 1 ? 0 : 1;
        $user->save();

        return $user;
    }

    // DELETE
    public function delete($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    // FIND BY EMAIL
    public function findByEmail(string $email): ?User
    {
        return User::where('Email', $email)->first();
    }

    // CREATE
    public function create(array $data): User
    {
        return User::create($data);
    }
}