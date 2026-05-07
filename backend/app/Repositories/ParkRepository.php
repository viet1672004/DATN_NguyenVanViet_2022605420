<?php

namespace App\Repositories;

use App\Models\Park;

class ParkRepository
{
    public function query()
    {
        return Park::query();
    }

    public function findById($id)
    {
        return Park::where('ID', $id)->first();
    }

    public function create(array $data)
    {
        return Park::create($data);
    }

    public function update($park, array $data)
    {
        return $park->update($data);
    }

    public function delete($park)
    {
        return $park->delete();
    }
}