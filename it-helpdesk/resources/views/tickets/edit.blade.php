@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Tiket {{ $ticket->ticket_number }}</h1>

<form method="POST" action="{{ route('tickets.update', $ticket) }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium mb-1">Judul</label>
        <input name="title" value="{{ old('title', $ticket->title) }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2" required>{{ old('description', $ticket->description) }}</textarea>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <select name="department_id" class="border rounded-lg px-3 py-2" required>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $ticket->department_id) == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        <select name="location_id" class="border rounded-lg px-3 py-2" required>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $ticket->location_id) == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>
        <select name="category_id" class="border rounded-lg px-3 py-2" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $ticket->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <select name="impact" class="border rounded-lg px-3 py-2" required>
            <option value="wide" @selected(old('impact', $ticket->impact) === 'wide')>Dampak Luas</option>
            <option value="narrow" @selected(old('impact', $ticket->impact) === 'narrow')>Dampak Sempit</option>
        </select>
        <select name="urgency" class="border rounded-lg px-3 py-2" required>
            <option value="high" @selected(old('urgency', $ticket->urgency) === 'high')>Urgensi Tinggi</option>
            <option value="low" @selected(old('urgency', $ticket->urgency) === 'low')>Urgensi Rendah</option>
        </select>
    </div>

    <div class="flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Update</button>
        <a href="{{ route('tickets.show', $ticket) }}" class="bg-slate-200 px-4 py-2 rounded-lg">Batal</a>
    </div>
</form>
@endsection
