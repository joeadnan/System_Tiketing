<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTicketNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $ticketId,
        public string $type
    ) {
    }

    public function handle(): void
    {
        $ticket = Ticket::with(['reporter', 'assignedAgent'])->find($this->ticketId);

        if (!$ticket) {
            return;
        }

        // TODO: Integrasikan Email, WhatsApp Gateway, Telegram, atau Web Push.
        Log::info('Ticket notification', [
            'ticket_number' => $ticket->ticket_number,
            'type' => $this->type,
        ]);
    }
}
