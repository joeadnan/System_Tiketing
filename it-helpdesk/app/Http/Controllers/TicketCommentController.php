<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketCommentRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketStatusHistory;
use App\Services\TicketSlaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TicketCommentController extends Controller
{
    public function store(TicketCommentRequest $request, Ticket $ticket, TicketSlaService $slaService): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket, $slaService) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'comment' => $request->comment,
                'type' => 'comment',
                'is_internal' => (bool) $request->boolean('is_internal'),
            ]);

            if (auth()->user()->isAgent() || auth()->user()->isManager()) {
                $slaService->markFirstResponse($ticket);
            }

            if ($request->filled('status') && $request->status !== $ticket->status) {
                $oldStatus = $ticket->status;

                if ($request->status === Ticket::STATUS_PENDING_USER) {
                    $slaService->pause($ticket);
                } elseif ($ticket->status === Ticket::STATUS_PENDING_USER) {
                    $slaService->resume($ticket);
                }

                $ticket->forceFill(['status' => $request->status])->save();

                TicketStatusHistory::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'note' => $request->comment,
                ]);
            }
        });

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
