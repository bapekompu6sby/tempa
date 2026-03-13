@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Detail Mata Pelatihan</h2>
    <div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
        <div class="mb-4">
            <strong>Judul:</strong> {{ $lesson->title }}
        </div>
        <div class="mb-4">
            <strong>Deskripsi:</strong> {{ $lesson->description }}
        </div>
        <a href="{{ route('events.lessons.edit', ['event' => $event->id, 'lesson' => $lesson->id]) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
        <a href="{{ route('events.lessons.index', ['event' => $event->id]) }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Kembali</a>
    </div>
</div>
@endsection
