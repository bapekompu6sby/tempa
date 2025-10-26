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
            <label for="role" class="block mb-2 font-semibold">Peran</label>
            <select name="role" id="role" class="w-full border px-3 py-2 rounded">
                <option value="" {{ old('role') == '' ? 'selected' : '' }}>-- Pilih role --</option>
                <option value="pic" {{ old('role') == 'pic' ? 'selected' : '' }}>PIC</option>
                <option value="host" {{ old('role') == 'host' ? 'selected' : '' }}>Host</option>
                <option value="petugas_kelas" {{ old('role') == 'petugas_kelas' ? 'selected' : '' }}>Petugas Kelas</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="detail" class="block mb-2 font-semibold">Detail</label>
            <textarea name="detail" id="detail" class="w-full border px-3 py-2 rounded">{{ old('detail') }}</textarea>
        </div>
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="linkable" id="linkable" class="mr-2" {{ old('linkable') ? 'checked' : '' }}>
            <label for="linkable" class="font-medium">Linkable</label>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Learning Model</label>
            <div class="flex space-x-4 items-center">
                <label class="inline-flex items-center"><input type="checkbox" name="full_elearning" {{ old('full_elearning') ? 'checked' : '' }} class="mr-2"> Full E-Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="distance_learning" {{ old('distance_learning') ? 'checked' : '' }} class="mr-2"> Distance Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="blended_learning" {{ old('blended_learning') ? 'checked' : '' }} class="mr-2"> Blended Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="classical" {{ old('classical') ? 'checked' : '' }} class="mr-2"> Classical</label>
            </div>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        <a href="{{ route('instructions.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
