<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketReportService
{
    public function dashboardSummary(): array
    {
        return [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', Ticket::STATUS_OPEN)->count(),
            'in_progress' => Ticket::where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'pending_user' => Ticket::where('status', Ticket::STATUS_PENDING_USER)->count(),
            'resolved' => Ticket::where('status', Ticket::STATUS_RESOLVED)->count(),
            'closed' => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
            'sla_breach' => Ticket::where(function ($query) {
                $query->where('is_sla_response_breached', true)
                    ->orWhere('is_sla_resolution_breached', true);
            })->count(),
            'backlog' => Ticket::backlog()->count(),
        ];
    }

    public function kpi(): array
    {
        $closedOrResolved = Ticket::whereNotNull('resolved_at');
        $totalResolved = (clone $closedOrResolved)->count();

        $slaCompliant = Ticket::whereNotNull('resolved_at')
            ->where('is_sla_response_breached', false)
            ->where('is_sla_resolution_breached', false)
            ->count();

        $fcr = Ticket::whereNotNull('resolved_at')
            ->whereDoesntHave('assignmentHistories', function ($query) {
                $query->whereNotNull('from_level');
            })
            ->count();

        return [
            'avg_first_response_minutes' => round((float) Ticket::whereNotNull('first_responded_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_responded_at)) as avg_minutes')
                ->value('avg_minutes'), 2),
            'avg_resolution_minutes' => round((float) Ticket::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_minutes')
                ->value('avg_minutes'), 2),
            'sla_compliance_rate' => $totalResolved > 0 ? round(($slaCompliant / $totalResolved) * 100, 2) : 0,
            'fcr_rate' => $totalResolved > 0 ? round(($fcr / $totalResolved) * 100, 2) : 0,
            'csat_score' => round((float) DB::table('csat_surveys')->avg('rating'), 2),
            'reopen_rate' => $totalResolved > 0 ? round((Ticket::where('reopen_count', '>', 0)->count() / max(1, Ticket::count())) * 100, 2) : 0,
            'backlog' => Ticket::backlog()->count(),
            'ticket_volume_by_category' => Ticket::query()
                ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->selectRaw('ticket_categories.name as category, COUNT(*) as total')
                ->groupBy('ticket_categories.name')
                ->orderByDesc('total')
                ->get(),
        ];
    }
}
