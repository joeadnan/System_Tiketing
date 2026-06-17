<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCsatSurveyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $ticketId)
    {
    }

    public function handle(): void
    {
        $ticket = Ticket::with('reporter')->find($this->ticketId);

        if (!$ticket) {
            return;
        }

        // TODO: Kirim link survey CSAT ke email/WhatsApp pelapor.
        Log::info('Send CSAT survey', [
            'ticket_number' => $ticket->ticket_number,
            'reporter' => $ticket->reporter?->email,
        ]);
    }
}
