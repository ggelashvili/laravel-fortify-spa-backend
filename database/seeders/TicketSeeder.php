<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::query()->pluck('id');

        Ticket::factory()
            ->times(5)
            ->afterMaking(
                function (Ticket $ticket) use ($users) {
                    $ticketUsers = $users->random(2);

                    $ticket->user_id  = $ticketUsers->first();
                    $ticket->agent_id = $ticketUsers->last();
                }
            )
            ->create();
    }
}
