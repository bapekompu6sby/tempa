@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Tambah Instruksi</h2>
    <form action="{{ route('instructions.store') }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label for="name" class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label for="detail" class="block mb-2 font-semibold">Detail</label>
            <textarea name="detail" id="detail" class="w-full border px-3 py-2 rounded">{{ old('detail') }}</textarea>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        <a href="{{ route('instructions.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
