<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Services\TicketSlaService;
use Illuminate\Console\Command;

class CheckTicketSlaCommand extends Command
{
    protected $signature = 'tickets:check-sla';

    protected $description = 'Check ticket SLA warning and breach status';

    public function handle(TicketSlaService $slaService): int
    {
        Ticket::query()
            ->active()
            ->chunkById(100, function ($tickets) use ($slaService) {
                foreach ($tickets as $ticket) {
                    $slaService->check($ticket);
                }
            });

        $this->info('Ticket SLA checked successfully.');

        return self::SUCCESS;
    }
}
