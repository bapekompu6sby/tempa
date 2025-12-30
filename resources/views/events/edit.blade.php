@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit Pelatihan</h2>
    <form action="{{ route('events.update', $event) }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow" enctype="multipart/form-data">
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
        <div class="mb-4 mt-8">
            <label for="event_report_file" class="block mb-2 font-semibold">File Laporan Pelatihan</label>
            <div class="flex items-center space-x-4">
                <label class="flex flex-col items-center px-4 py-6 bg-white text-blue-600 rounded-lg shadow-lg tracking-wide border border-blue-200 cursor-pointer hover:bg-blue-50 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-5 4h.01M12 20a2 2 0 100-4 2 2 0 000 4z" /></svg>
                    <span class="mt-2 text-base leading-normal">Pilih file (PDF/DOC/DOCX)</span>
                    <input type="file" name="event_report_file" id="event_report_file" class="hidden" accept=".pdf,.doc,.docx" onchange="document.getElementById('event-report-selected').textContent = this.files.length ? this.files[0].name : ''">
                </label>
                <span id="event-report-selected" class="ml-2 text-green-700 font-semibold"></span>
                @if($event->event_report_url)
                    <div>
                        <a href="{{ asset('storage/' . $event->event_report_url) }}" target="_blank" class="text-blue-600 underline">Lihat file laporan saat ini</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-6">
            <label for="document_drive_url" class="block mb-2 font-semibold">URL Dokumen Drive</label>
            <input type="url" name="document_drive_url" id="document_drive_url" class="w-full border px-3 py-2 rounded" value="{{ old('document_drive_url', $event->document_drive_url) }}" placeholder="https://drive.google.com/...">
        </div>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
        <a href="{{ route('events.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
