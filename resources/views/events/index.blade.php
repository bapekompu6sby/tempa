@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Pelatihan</h2>
    <a href="{{ route('events.create') }}" class="mb-4 inline-block px-4 py-2 bg-green-600 text-white rounded">Tambah Pelatihan</a>
    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="py-2 px-4 border">ID</th>
                <th class="py-2 px-4 border">Nama</th>
                <th class="py-2 px-4 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td class="py-2 px-4 border">{{ $event->id }}</td>
                <td class="py-2 px-4 border">{{ $event->name }}</td>
                <td class="py-2 px-4 border">
                    <a href="{{ route('events.show', $event) }}" class="text-green-600">Lihat</a>
                    <a href="{{ route('events.edit', $event) }}" class="text-yellow-600 ml-2">Edit</a>
                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 ml-2" onclick="return confirm('Hapus pelatihan ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
