@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Report KPI Ticketing</h1>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">Avg First Response</div>
        <div class="text-2xl font-bold">{{ $kpi['avg_first_response_minutes'] }} menit</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">Avg Resolution</div>
        <div class="text-2xl font-bold">{{ $kpi['avg_resolution_minutes'] }} menit</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">SLA Compliance</div>
        <div class="text-2xl font-bold">{{ $kpi['sla_compliance_rate'] }}%</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">FCR</div>
        <div class="text-2xl font-bold">{{ $kpi['fcr_rate'] }}%</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">CSAT</div>
        <div class="text-2xl font-bold">{{ $kpi['csat_score'] }}/5</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="text-sm text-slate-500">Backlog</div>
        <div class="text-2xl font-bold">{{ $kpi['backlog'] }}</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="font-bold mb-3">Volume Tiket per Kategori</h2>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr><th class="text-left p-3">Kategori</th><th class="text-left p-3">Total</th></tr>
        </thead>
        <tbody>
            @foreach($kpi['ticket_volume_by_category'] as $row)
                <tr class="border-t"><td class="p-3">{{ $row->category }}</td><td class="p-3">{{ $row->total }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
