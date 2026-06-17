@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Buat Tiket Baru</h1>

<form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Judul</label>
        <input name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Deskripsi</label>
        <textarea name="description" rows="5" class="w-full border rounded-lg px-3 py-2" required>{{ old('description') }}</textarea>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Departemen</label>
            <select name="department_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Pilih</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Lokasi</label>
            <select name="location_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Pilih</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select name="category_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Pilih</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }} - {{ $category->default_level }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Dampak</label>
            <select name="impact" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Pilih</option>
                <option value="wide" @selected(old('impact') === 'wide')>Luas</option>
                <option value="narrow" @selected(old('impact') === 'narrow')>Sempit</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Urgensi</label>
            <select name="urgency" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Pilih</option>
                <option value="high" @selected(old('urgency') === 'high')>Tinggi</option>
                <option value="low" @selected(old('urgency') === 'low')>Rendah</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Source</label>
            <select name="source" class="w-full border rounded-lg px-3 py-2" required>
                @foreach(['web','email','whatsapp','phone'] as $source)
                    <option value="{{ $source }}" @selected(old('source', 'web') === $source)>{{ $source }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Attachment Opsional</label>
        <input type="file" name="attachments[]" multiple class="w-full border rounded-lg px-3 py-2">
        <p class="text-xs text-slate-500 mt-1">Maksimal 5 file, masing-masing 5MB.</p>
    </div>

    <div class="flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan Tiket</button>
        <a href="{{ route('tickets.index') }}" class="bg-slate-200 px-4 py-2 rounded-lg">Batal</a>
    </div>
</form>
@endsection
