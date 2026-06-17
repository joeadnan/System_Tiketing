<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Department;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketStatusHistory;
use App\Services\TicketAssignmentService;
use App\Services\TicketNumberService;
use App\Services\TicketPriorityService;
use App\Services\TicketSlaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::with(['reporter', 'department', 'location', 'category', 'assignedAgent'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority_code', $request->priority))
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('ticket_number', 'like', '%' . $request->q . '%')
                        ->orWhere('title', 'like', '%' . $request->q . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('tickets.create', [
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'categories' => TicketCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(
        StoreTicketRequest $request,
        TicketNumberService $numberService,
        TicketPriorityService $priorityService,
        TicketSlaService $slaService,
        TicketAssignmentService $assignmentService
    ): RedirectResponse {
        $ticket = DB::transaction(function () use ($request, $numberService, $priorityService, $slaService, $assignmentService) {
            $priority = $priorityService->determine($request->impact, $request->urgency);

            $ticket = Ticket::create([
                'ticket_number' => $numberService->generate(),
                'title' => $request->title,
                'description' => $request->description,
                'reporter_id' => auth()->id(),
                'department_id' => $request->department_id,
                'location_id' => $request->location_id,
                'category_id' => $request->category_id,
                'impact' => $request->impact,
                'urgency' => $request->urgency,
                'priority_code' => $priority['code'],
                'priority_label' => $priority['label'],
                'source' => $request->source,
                'status' => Ticket::STATUS_OPEN,
            ]);

            $this->storeAttachments($request, $ticket);
            $slaService->applySla($ticket);
            $assignmentService->assign($ticket, null, auth()->user());

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'old_status' => null,
                'new_status' => Ticket::STATUS_OPEN,
                'note' => 'Ticket created',
            ]);

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'reporter',
            'department',
            'location',
            'category',
            'assignedAgent',
            'comments.user',
            'attachments',
            'statusHistories.user',
            'assignmentHistories.assignedFrom',
            'assignmentHistories.assignedTo',
            'csatSurvey',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        return view('tickets.edit', [
            'ticket' => $ticket,
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'categories' => TicketCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(
        UpdateTicketRequest $request,
        Ticket $ticket,
        TicketPriorityService $priorityService,
        TicketSlaService $slaService
    ): RedirectResponse {
        DB::transaction(function () use ($request, $ticket, $priorityService, $slaService) {
            $priority = $priorityService->determine($request->impact, $request->urgency);
            $priorityChanged = $ticket->priority_code !== $priority['code'];

            $ticket->update([
                'title' => $request->title,
                'description' => $request->description,
                'department_id' => $request->department_id,
                'location_id' => $request->location_id,
                'category_id' => $request->category_id,
                'impact' => $request->impact,
                'urgency' => $request->urgency,
                'priority_code' => $priority['code'],
                'priority_label' => $priority['label'],
            ]);

            if ($priorityChanged && !in_array($ticket->status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED], true)) {
                $slaService->applySla($ticket->fresh());
            }
        });

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tiket berhasil diupdate.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => Ticket::STATUS_CANCELLED]);
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibatalkan.');
    }

    private function storeAttachments(StoreTicketRequest $request, Ticket $ticket): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('ticket-attachments/' . $ticket->ticket_number, 'public');

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
