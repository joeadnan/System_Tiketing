<?php

namespace App\Services;

use App\Models\Ticket;

class TicketNumberService
{
    public function generate(): string
    {
        $prefix = 'TKT-' . now()->format('Ym');

        $lastTicket = Ticket::where('ticket_number', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
