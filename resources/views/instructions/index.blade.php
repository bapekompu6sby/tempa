@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Instruksi</h2>
    <a href="{{ route('instructions.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">Tambah Instruksi</a>
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border">ID</th>
                <th class="py-2 px-4 border">Nama</th>
                <th class="py-2 px-4 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instructions as $instruction)
            <tr>
                <td class="py-2 px-4 border">{{ $instruction->id }}</td>
                <td class="py-2 px-4 border">{{ $instruction->name }}</td>
                <td class="py-2 px-4 border">
                    <a href="{{ route('instructions.show', $instruction) }}" class="text-blue-600">Lihat</a>
                    <a href="{{ route('instructions.edit', $instruction) }}" class="text-yellow-600 ml-2">Edit</a>
                    <form action="{{ route('instructions.destroy', $instruction) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 ml-2" onclick="return confirm('Hapus instruksi ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
