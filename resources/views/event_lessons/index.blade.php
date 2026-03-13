@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Mata Pelatihan</h2>
    <a href="{{ route('events.lessons.create', $event->id) }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">Tambah Mata Pelatihan</a>
    <div class="bg-white shadow rounded">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lessons as $lesson)
                <tr>
                    <td class="px-4 py-2">{{ $lesson->title }}</td>
                    <td class="px-4 py-2">{{ $lesson->description }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('events.lessons.show', [$event->id, $lesson->id]) }}" class="text-blue-600">Lihat</a> |
                        <a href="{{ route('events.lessons.edit', [$event->id, $lesson->id]) }}" class="text-yellow-600">Edit</a> |
                        <form action="{{ route('events.lessons.destroy', [$event->id, $lesson->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Hapus mata pelatihan ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
