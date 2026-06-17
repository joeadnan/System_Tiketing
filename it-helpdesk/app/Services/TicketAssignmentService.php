<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAssignmentHistory;
use App\Models\User;

class TicketAssignmentService
{
    public function assign(Ticket $ticket, ?string $level = null, ?User $assignedBy = null, ?string $reason = null): void
    {
        $level = $level ?: ($ticket->category?->default_level ?? 'L1');
        $agent = $this->findAvailableAgent($level);

        $ticket->forceFill([
            'assigned_team_level' => $level,
            'assigned_agent_id' => $agent?->id,
        ])->save();

        if ($agent) {
            $agent->forceFill(['last_assigned_at' => now()])->save();
        }

        TicketAssignmentHistory::create([
            'ticket_id' => $ticket->id,
            'assigned_from_user_id' => $assignedBy?->id,
            'assigned_to_user_id' => $agent?->id,
            'from_level' => null,
            'to_level' => $level,
            'reason' => $reason ?: 'Auto assignment by load balancing',
        ]);
    }

    public function findAvailableAgent(string $level): ?User
    {
        $role = match ($level) {
            'L1' => 'l1_agent',
            'L2' => 'l2_agent',
            'L3' => 'l3_agent',
            default => null,
        };

        if (!$role) {
            return null;
        }

        return User::query()
            ->where('role', $role)
            ->where('is_active', true)
            ->withCount([
                'assignedTickets as active_tickets_count' => function ($query) {
                    $query->whereIn('status', [
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_IN_PROGRESS,
                        Ticket::STATUS_PENDING_USER,
                        Ticket::STATUS_REOPENED,
                    ]);
                },
            ])
            ->orderBy('active_tickets_count')
            ->orderByRaw('last_assigned_at IS NULL DESC')
            ->orderBy('last_assigned_at')
            ->first();
    }
}
