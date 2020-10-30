<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function index(TicketService $ticketService)
    {
        return response()->json(TicketResource::collection($ticketService->getOpenTickets()));
    }
}
