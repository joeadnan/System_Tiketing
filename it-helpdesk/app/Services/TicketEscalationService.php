<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAssignmentHistory;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketEscalationService
{
    public function escalate(
        Ticket $ticket,
        User $fromUser,
        string $toLevel,
        string $reason,
        string $handoverNote,
        ?string $vendorName = null,
        ?string $vendorContact = null
    ): void {
        DB::transaction(function () use ($ticket, $fromUser, $toLevel, $reason, $handoverNote, $vendorName, $vendorContact) {
            $oldLevel = $ticket->assigned_team_level;
            $newAgent = null;

            if ($toLevel !== 'vendor') {
                $newAgent = app(TicketAssignmentService::class)->findAvailableAgent($toLevel);
            }

            TicketAssignmentHistory::create([
                'ticket_id' => $ticket->id,
                'assigned_from_user_id' => $fromUser->id,
                'assigned_to_user_id' => $newAgent?->id,
                'from_level' => $oldLevel,
                'to_level' => $toLevel,
                'vendor_name' => $vendorName,
                'vendor_contact' => $vendorContact,
                'reason' => $reason,
                'handover_note' => $handoverNote,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $fromUser->id,
                'comment' => "Escalation to {$toLevel}: " . $handoverNote,
                'type' => 'handover',
                'is_internal' => true,
            ]);

            $ticket->forceFill([
                'assigned_team_level' => $toLevel,
                'assigned_agent_id' => $newAgent?->id,
                'status' => Ticket::STATUS_IN_PROGRESS,
            ])->save();
        });
    }
}
