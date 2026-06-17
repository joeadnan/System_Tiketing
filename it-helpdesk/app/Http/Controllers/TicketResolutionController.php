<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseTicketRequest;
use App\Http\Requests\ResolveTicketRequest;
use App\Jobs\SendCsatSurveyJob;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TicketResolutionController extends Controller
{
    public function resolve(ResolveTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => Ticket::STATUS_RESOLVED,
                'resolved_at' => now(),
                'resolution_note' => $request->resolution_note,
                'root_cause' => $request->root_cause,
                'prevention_note' => $request->prevention_note,
                'is_sla_resolution_breached' => $ticket->resolution_due_at
                    ? now()->greaterThan($ticket->resolution_due_at)
                    : false,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'comment' => $request->resolution_note,
                'type' => 'resolution',
                'is_internal' => false,
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => Ticket::STATUS_RESOLVED,
                'note' => 'Ticket resolved',
            ]);
        });

        return back()->with('success', 'Tiket berhasil diselesaikan.');
    }

    public function close(CloseTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => Ticket::STATUS_CLOSED,
                'closed_at' => now(),
                'user_confirmed_at' => now(),
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => Ticket::STATUS_CLOSED,
                'note' => 'Ticket closed by user confirmation',
            ]);
        });

        SendCsatSurveyJob::dispatch($ticket->id);

        return back()->with('success', 'Tiket berhasil ditutup.');
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        if (!$ticket->canBeReopened()) {
            return back()->with('error', 'Tiket hanya dapat dibuka kembali maksimal 72 jam setelah ditutup.');
        }

        DB::transaction(function () use ($ticket) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status' => Ticket::STATUS_REOPENED,
                'reopened_at' => now(),
                'reopen_count' => $ticket->reopen_count + 1,
                'resolved_at' => null,
                'closed_at' => null,
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => Ticket::STATUS_REOPENED,
                'note' => 'Ticket reopened within 72 hours',
            ]);
        });

        return back()->with('success', 'Tiket berhasil dibuka kembali.');
    }
}
