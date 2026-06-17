@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Daftar Tiket</h1>
    <a href="{{ route('tickets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Buat Tiket</a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-4 grid md:grid-cols-4 gap-3">
    <input name="q" value="{{ request('q') }}" placeholder="Cari nomor/judul" class="border rounded-lg px-3 py-2">
    <select name="status" class="border rounded-lg px-3 py-2">
        <option value="">Semua Status</option>
        @foreach(['open','in_progress','pending_user','resolved','closed','reopened','cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
        @endforeach
    </select>
    <select name="priority" class="border rounded-lg px-3 py-2">
        <option value="">Semua Prioritas</option>
        @foreach(['P1','P2','P3','P4'] as $priority)
            <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ $priority }}</option>
        @endforeach
    </select>
    <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left p-3">No Tiket</th>
                <th class="text-left p-3">Judul</th>
                <th class="text-left p-3">Prioritas</th>
                <th class="text-left p-3">Status</th>
                <th class="text-left p-3">Agent</th>
                <th class="text-left p-3">SLA Resolusi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr class="border-t hover:bg-slate-50">
                    <td class="p-3 font-semibold">
                        <a class="text-blue-700" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a>
                    </td>
                    <td class="p-3">{{ $ticket->title }}</td>
                    <td class="p-3">{{ $ticket->priority_code }} - {{ $ticket->priority_label }}</td>
                    <td class="p-3">{{ $ticket->status }}</td>
                    <td class="p-3">{{ $ticket->assignedAgent?->name ?? '-' }}</td>
                    <td class="p-3">
                        {{ $ticket->resolution_due_at?->format('d M Y H:i') ?? '-' }}
                        @if($ticket->is_sla_resolution_breached)
                            <span class="ml-2 text-red-600 font-bold">BREACH</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td class="p-4 text-center text-slate-500" colspan="6">Belum ada tiket.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
