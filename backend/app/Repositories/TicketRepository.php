<?php

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository
{
    public function query()
    {
        return Ticket::query();
    }

    public function findById($id)
    {
        return Ticket::where('ID', $id)->first(); 
    }


    public function create($data)
    {
        return Ticket::create($data);
    }

    public function update($ticket, $data)
    {
        return $ticket->update($data);
    }

    public function delete($ticket)
    {
        return $ticket->delete();
    }
}