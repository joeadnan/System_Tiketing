@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $ticket->ticket_number }}</h1>
        <p class="text-slate-500">{{ $ticket->title }}</p>
    </div>
    <a href="{{ route('tickets.edit', $ticket) }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg">Edit</a>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">Deskripsi</h2>
            <p class="whitespace-pre-line">{{ $ticket->description }}</p>
        </section>

        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">Komentar & Aktivitas</h2>
            <div class="space-y-3 mb-5">
                @forelse($ticket->comments as $comment)
                    <div class="border rounded-lg p-3 {{ $comment->is_internal ? 'bg-amber-50' : 'bg-slate-50' }}">
                        <div class="text-xs text-slate-500">
                            {{ $comment->user?->name ?? 'System' }} · {{ $comment->type }} · {{ $comment->created_at->format('d M Y H:i') }}
                            @if($comment->is_internal) · Internal @endif
                        </div>
                        <p class="mt-1 whitespace-pre-line">{{ $comment->comment }}</p>
                    </div>
                @empty
                    <p class="text-slate-500">Belum ada komentar.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="space-y-3">
                @csrf
                <textarea name="comment" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Tulis komentar/update..." required></textarea>
                <div class="grid md:grid-cols-3 gap-3">
                    <select name="status" class="border rounded-lg px-3 py-2">
                        <option value="">Tidak ubah status</option>
                        @foreach(['open','in_progress','pending_user','resolved','closed','reopened','cancelled'] as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_internal" value="1"> Internal note
                    </label>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Kirim</button>
                </div>
            </form>
        </section>

        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">Resolve Ticket</h2>
            <form method="POST" action="{{ route('tickets.resolve', $ticket) }}" class="space-y-3">
                @csrf
                <input name="root_cause" class="w-full border rounded-lg px-3 py-2" placeholder="Root cause" required>
                <textarea name="resolution_note" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Catatan resolusi" required></textarea>
                <textarea name="prevention_note" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Tindakan pencegahan"></textarea>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg">Mark Resolved</button>
            </form>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">Info Tiket</h2>
            <dl class="text-sm space-y-2">
                <div><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ $ticket->status }}</dd></div>
                <div><dt class="text-slate-500">Prioritas</dt><dd class="font-semibold">{{ $ticket->priority_code }} - {{ $ticket->priority_label }}</dd></div>
                <div><dt class="text-slate-500">Pelapor</dt><dd>{{ $ticket->reporter?->name }}</dd></div>
                <div><dt class="text-slate-500">Agent</dt><dd>{{ $ticket->assignedAgent?->name ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Level</dt><dd>{{ $ticket->assigned_team_level ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Kategori</dt><dd>{{ $ticket->category?->name }}</dd></div>
                <div><dt class="text-slate-500">Departemen</dt><dd>{{ $ticket->department?->name }}</dd></div>
                <div><dt class="text-slate-500">Lokasi</dt><dd>{{ $ticket->location?->name }}</dd></div>
                <div><dt class="text-slate-500">Source</dt><dd>{{ $ticket->source }}</dd></div>
            </dl>
        </section>

        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">SLA</h2>
            <div class="text-sm space-y-2">
                <p>First response due: <br><strong>{{ $ticket->first_response_due_at?->format('d M Y H:i') ?? '-' }}</strong></p>
                <p>Resolution due: <br><strong>{{ $ticket->resolution_due_at?->format('d M Y H:i') ?? '-' }}</strong></p>
                <p>Response breach: <strong>{{ $ticket->is_sla_response_breached ? 'Ya' : 'Tidak' }}</strong></p>
                <p>Resolution breach: <strong>{{ $ticket->is_sla_resolution_breached ? 'Ya' : 'Tidak' }}</strong></p>
                <p>Total pause: <strong>{{ $ticket->sla_total_paused_minutes }} menit</strong></p>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold mb-3">Eskalasi</h2>
            <form method="POST" action="{{ route('tickets.escalate', $ticket) }}" class="space-y-3">
                @csrf
                <select name="to_level" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="L2">L2</option>
                    <option value="L3">L3</option>
                    <option value="vendor">Vendor Eksternal</option>
                </select>
                <input name="reason" class="w-full border rounded-lg px-3 py-2" placeholder="Alasan eskalasi" required>
                <textarea name="handover_note" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Handover note" required></textarea>
                <input name="vendor_name" class="w-full border rounded-lg px-3 py-2" placeholder="Nama vendor jika eksternal">
                <input name="vendor_contact" class="w-full border rounded-lg px-3 py-2" placeholder="Kontak vendor jika eksternal">
                <button class="bg-orange-600 text-white px-4 py-2 rounded-lg w-full">Eskalasi</button>
            </form>
        </section>

        @if($ticket->status === 'resolved')
            <section class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-bold mb-3">Close Ticket</h2>
                <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                    @csrf
                    <label class="flex items-center gap-2 text-sm mb-3">
                        <input type="checkbox" name="user_confirmed" value="1" required> User sudah konfirmasi selesai
                    </label>
                    <button class="bg-slate-900 text-white px-4 py-2 rounded-lg w-full">Tutup Tiket</button>
                </form>
            </section>
        @endif

        @if($ticket->status === 'closed' && $ticket->canBeReopened())
            <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded-lg w-full">Reopen Ticket</button>
            </form>
        @endif

        @if($ticket->status === 'closed' && !$ticket->csatSurvey)
            <section class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-bold mb-3">CSAT Survey</h2>
                <form method="POST" action="{{ route('tickets.csat.store', $ticket) }}" class="space-y-3">
                    @csrf
                    <select name="rating" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="5">5 - Sangat Puas</option>
                        <option value="4">4 - Puas</option>
                        <option value="3">3 - Cukup</option>
                        <option value="2">2 - Kurang</option>
                        <option value="1">1 - Tidak Puas</option>
                    </select>
                    <textarea name="comment" class="w-full border rounded-lg px-3 py-2" placeholder="Komentar"></textarea>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg w-full">Kirim Survey</button>
                </form>
            </section>
        @endif
    </aside>
</div>
@endsection
