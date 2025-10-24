@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Detail Instruksi</h2>
    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
        <div class="mb-4">
            <strong>Nama:</strong> {{ $instruction->name }}
        </div>
        <div class="mb-4">
            <strong>Detail:</strong> {{ $instruction->detail }}
        </div>
        <a href="{{ route('instructions.edit', $instruction) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
        <a href="{{ route('instructions.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Kembali</a>
    </div>
</div>
@endsection
