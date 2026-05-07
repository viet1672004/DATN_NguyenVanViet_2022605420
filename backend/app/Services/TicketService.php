<?php

namespace App\Services;

use App\Repositories\TicketRepository;

class TicketService
{
    protected $repo;

    public function __construct(TicketRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getList($filters)
    {
        $query = $this->repo->query()->with('park');

        if (!empty($filters['search'])) {
            $query->where('TicketName', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['park_id'])) {
            $query->where('ParkID', $filters['park_id']);
        }

        if (isset($filters['status'])) {
            $query->where('Status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('TicketType', $filters['type']);
        }

        return $query
        ->select('ID', 'TicketName', 'Price', 'ParkID', 'TicketType', 'Status')
        ->orderBy('ID', 'desc')
        ->paginate(10);
    }

    public function getDetail($id)
    {
        return $this->repo->findById($id);
    }

    public function create($data)
    {
        $data['ID'] = \Str::uuid();
        return $this->repo->create($data);
    }

    public function update($ticket, $data)
    {
        return $this->repo->update($ticket, $data);
    }

    public function delete($ticket)
    {
        return $this->repo->delete($ticket);
    }
}