@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Tambah Pelatihan</h2>
    <form action="{{ route('events.store') }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label for="name" class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label for="learning_model" class="block mb-2 font-semibold">Model Pembelajaran</label>
            <select name="learning_model" id="learning_model" class="w-full border px-3 py-2 rounded">
                <option value="" {{ old('learning_model') == '' ? 'selected' : '' }}>-- Pilih model pembelajaran --</option>
                <option value="full_elearning" {{ old('learning_model') == 'full_elearning' ? 'selected' : '' }}>Full E-Learning</option>
                <option value="distance_learning" {{ old('learning_model') == 'distance_learning' ? 'selected' : '' }}>Distance Learning</option>
                <option value="blended_learning" {{ old('learning_model') == 'blended_learning' ? 'selected' : '' }}>Blended Learning</option>
                <option value="classical" {{ old('learning_model') == 'classical' ? 'selected' : '' }}>Classical</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="start_date" class="block mb-2 font-semibold">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="w-full border px-3 py-2 rounded" value="{{ old('start_date') }}">
        </div>
        <div class="mb-4">
            <label for="end_date" class="block mb-2 font-semibold">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" class="w-full border px-3 py-2 rounded" value="{{ old('end_date') }}">
        </div>
        <div class="mb-4">
            <label for="note" class="block mb-2 font-semibold">Catatan</label>
            <textarea name="note" id="note" class="w-full border px-3 py-2 rounded" rows="4">{{ old('note') }}</textarea>
        </div>
        <div class="mb-4">
            <label for="target" class="block mb-2 font-semibold">Target</label>
            <input type="number" name="target" id="target" class="w-full border px-3 py-2 rounded" value="{{ old('target') }}" min="0">
        </div>
        <div class="mb-4">
            <label for="jp_module" class="block mb-2 font-semibold">JP Kurmod</label>
            <input type="number" name="jp_module" id="jp_module" class="w-full border px-3 py-2 rounded" value="{{ old('jp_module') }}" min="0">
        </div>
        <div class="mb-4">
            <label for="jp_facilitator" class="block mb-2 font-semibold">JP Pengajar</label>
            <input type="number" name="jp_facilitator" id="jp_facilitator" class="w-full border px-3 py-2 rounded" value="{{ old('jp_facilitator') }}" min="0">
        </div>
        <div class="mb-4">
            <label for="field" class="block mb-2 font-semibold">Bidang</label>
            <input type="text" name="field" id="field" class="w-full border px-3 py-2 rounded" value="{{ old('field') }}">
        </div>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
        <a href="{{ route('events.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
