<?php

namespace App\Services;

use App\Jobs\SendTicketNotificationJob;
use App\Models\SlaPolicy;
use App\Models\Ticket;

class TicketSlaService
{
    public function applySla(Ticket $ticket): void
    {
        $sla = SlaPolicy::where('priority_code', $ticket->priority_code)
            ->where('is_active', true)
            ->firstOrFail();

        $ticket->forceFill([
            'first_response_due_at' => now()->copy()->addMinutes($sla->response_minutes),
            'resolution_due_at' => now()->copy()->addMinutes($sla->resolution_minutes),
        ])->save();
    }

    public function markFirstResponse(Ticket $ticket): void
    {
        if (!$ticket->first_responded_at) {
            $ticket->forceFill([
                'first_responded_at' => now(),
                'is_sla_response_breached' => $ticket->first_response_due_at
                    ? now()->greaterThan($ticket->first_response_due_at)
                    : false,
            ])->save();
        }
    }

    public function pause(Ticket $ticket): void
    {
        if (!$ticket->sla_paused_at) {
            $ticket->forceFill([
                'sla_paused_at' => now(),
            ])->save();
        }
    }

    public function resume(Ticket $ticket): void
    {
        if (!$ticket->sla_paused_at) {
            return;
        }

        $pausedMinutes = $ticket->sla_paused_at->diffInMinutes(now());

        $ticket->forceFill([
            'sla_total_paused_minutes' => $ticket->sla_total_paused_minutes + $pausedMinutes,
            'first_response_due_at' => $ticket->first_response_due_at
                ? $ticket->first_response_due_at->copy()->addMinutes($pausedMinutes)
                : null,
            'resolution_due_at' => $ticket->resolution_due_at
                ? $ticket->resolution_due_at->copy()->addMinutes($pausedMinutes)
                : null,
            'sla_paused_at' => null,
        ])->save();
    }

    public function check(Ticket $ticket): void
    {
        if ($ticket->status === Ticket::STATUS_PENDING_USER) {
            return;
        }

        $changes = [];

        if (
            !$ticket->is_sla_response_breached
            && !$ticket->first_responded_at
            && $ticket->first_response_due_at
            && now()->greaterThan($ticket->first_response_due_at)
        ) {
            $changes['is_sla_response_breached'] = true;
        }

        if (
            !$ticket->is_sla_resolution_breached
            && $ticket->resolution_due_at
            && now()->greaterThan($ticket->resolution_due_at)
        ) {
            $changes['is_sla_resolution_breached'] = true;
        }

        if ($changes !== []) {
            $ticket->forceFill($changes)->save();
        }

        $this->sendWarningIfNeeded($ticket->fresh());
    }

    private function sendWarningIfNeeded(Ticket $ticket): void
    {
        if ($ticket->sla_warning_sent_at || !$ticket->resolution_due_at) {
            return;
        }

        $sla = SlaPolicy::where('priority_code', $ticket->priority_code)
            ->where('is_active', true)
            ->first();

        if (!$sla) {
            return;
        }

        $start = $ticket->created_at;
        $due = $ticket->resolution_due_at;

        if (!$start || !$due) {
            return;
        }

        $totalSeconds = max(1, $start->diffInSeconds($due));
        $remainingSeconds = now()->diffInSeconds($due, false);

        if ($remainingSeconds <= 0) {
            return;
        }

        $remainingPercentage = ($remainingSeconds / $totalSeconds) * 100;

        if ($remainingPercentage <= $sla->warning_percentage) {
            $ticket->forceFill([
                'sla_warning_sent_at' => now(),
            ])->save();

            SendTicketNotificationJob::dispatch($ticket->id, 'sla_warning');
        }
    }
}
