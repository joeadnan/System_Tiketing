<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketAssignmentController extends Controller
{
    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_agent_id' => ['required', 'exists:users,id'],
        ]);

        $agent = User::findOrFail($validated['assigned_agent_id']);

        $ticket->update([
            'assigned_agent_id' => $agent->id,
            'assigned_team_level' => $agent->level,
        ]);

        return back()->with('success', 'Agent berhasil diassign.');
    }
}
