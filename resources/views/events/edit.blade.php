@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit Pelatihan</h2>
    <form action="{{ route('events.update', $event) }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ old('name', $event->name) }}">
        </div>
        <div class="mb-4">
            <label for="learning_model" class="block mb-2 font-semibold">Model Pembelajaran</label>
            <select name="learning_model" id="learning_model" class="w-full border px-3 py-2 rounded">
                <option value="" {{ old('learning_model', $event->learning_model) == '' ? 'selected' : '' }}>-- Pilih model pembelajaran --</option>
                <option value="full_elearning" {{ old('learning_model', $event->learning_model) == 'full_elearning' ? 'selected' : '' }}>Full E-Learning</option>
                <option value="distance_learning" {{ old('learning_model', $event->learning_model) == 'distance_learning' ? 'selected' : '' }}>Distance Learning</option>
                <option value="blended_learning" {{ old('learning_model', $event->learning_model) == 'blended_learning' ? 'selected' : '' }}>Blended Learning</option>
                <option value="classical" {{ old('learning_model', $event->learning_model) == 'classical' ? 'selected' : '' }}>Classical</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="start_date" class="block mb-2 font-semibold">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="w-full border px-3 py-2 rounded" value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}">
        </div>
        <div class="mb-4">
            <label for="end_date" class="block mb-2 font-semibold">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" class="w-full border px-3 py-2 rounded" value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}">
        </div>
        <div class="mb-4">
            <label for="note" class="block mb-2 font-semibold">Catatan</label>
            <textarea name="note" id="note" class="w-full border px-3 py-2 rounded" rows="4">{{ old('note', $event->note) }}</textarea>
        </div>
        <div class="mb-4">
            <label for="status" class="block mb-2 font-semibold">Status</label>
            <select name="status" id="status" class="w-full border px-3 py-2 rounded">
                <option value="tentative" {{ old('status', $event->status) == 'tentative' ? 'selected' : '' }}>Tentative</option>
                <option value="belum_dimulai" {{ old('status', $event->status) == 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                <option value="persiapan" {{ old('status', $event->status) == 'persiapan' ? 'selected' : '' }}>Persiapan</option>
                <option value="pelaksanaan" {{ old('status', $event->status) == 'pelaksanaan' ? 'selected' : '' }}>Pelaksanaan</option>
                <option value="pelaporan" {{ old('status', $event->status) == 'pelaporan' ? 'selected' : '' }}>Pelaporan</option>
                <option value="dibatalkan" {{ old('status', $event->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="selesai" {{ old('status', $event->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
        <a href="{{ route('events.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
