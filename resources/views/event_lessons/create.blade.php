@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Tambah Mata Pelatihan</h2>
    <form action="{{ route('events.lessons.store', ['event' => $event->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block mb-2">Judul</label>
            <input type="text" name="title" class="border rounded px-3 py-2 w-full" required value="{{ old('title') }}">
        </div>
        <div class="mb-4">
            <label class="block mb-2">Deskripsi</label>
            <textarea name="description" class="border rounded px-3 py-2 w-full" required>{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        <a href="{{ route('events.lessons.index', ['event' => $event->id]) }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
