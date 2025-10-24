@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Detail Pelatihan</h2>
    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
        <div class="mb-4">
            <strong>Nama:</strong> {{ $event->name }}
        </div>
        <div class="mb-4">
            <strong>Model Pembelajaran:</strong> {{ $event->learning_model }}
        </div>
        <div class="mb-4">
            <strong>Tanggal Mulai:</strong> {{ $event->start_date }}
        </div>
        <div class="mb-4">
            <strong>Tanggal Selesai:</strong> {{ $event->end_date }}
        </div>
        <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
        <a href="{{ route('events.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Kembali</a>
    </div>
</div>
@endsection
