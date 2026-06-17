<?php

namespace App\Http\Controllers;

use App\Http\Requests\EscalateTicketRequest;
use App\Models\Ticket;
use App\Services\TicketEscalationService;
use Illuminate\Http\RedirectResponse;

class TicketEscalationController extends Controller
{
    public function escalate(EscalateTicketRequest $request, Ticket $ticket, TicketEscalationService $service): RedirectResponse
    {
        $service->escalate(
            ticket: $ticket,
            fromUser: auth()->user(),
            toLevel: $request->to_level,
            reason: $request->reason,
            handoverNote: $request->handover_note,
            vendorName: $request->vendor_name,
            vendorContact: $request->vendor_contact,
        );

        return back()->with('success', 'Tiket berhasil dieskalasi.');
    }
}
