@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Dashboard Ticketing</h1>
    <a href="{{ route('tickets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Buat Tiket</a>
</div>

<div class="grid md:grid-cols-4 gap-4">
    @foreach($summary as $label => $value)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm text-slate-500 uppercase">{{ str_replace('_', ' ', $label) }}</div>
            <div class="text-3xl font-bold mt-2">{{ $value }}</div>
        </div>
    @endforeach
</div>
@endsection
