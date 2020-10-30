<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Collection;

class TicketService
{
    public function getOpenTickets(): Collection
    {
        return Ticket::with(['user', 'agent'])
            ->incomplete()
            ->orderBy('priority_id', 'desc')
            ->get();
    }
}
