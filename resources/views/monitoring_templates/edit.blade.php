@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-6">Edit Monitoring Template</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('monitoring-templates.update', $monitoringTemplate) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">Nama</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $monitoringTemplate->name) }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">Kategori</label>
                <select name="category" class="w-full border rounded px-3 py-2" required>
                    <option value="">Pilih Kategori</option>
                    <option value="sikap" {{ old('category', $monitoringTemplate->category) == 'sikap' ? 'selected' : '' }}>Sikap</option>
                    <option value="sarpras" {{ old('category', $monitoringTemplate->category) == 'sarpras' ? 'selected' : '' }}>Sarpras</option>
                    <option value="administrasi" {{ old('category', $monitoringTemplate->category) == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                    <option value="administrasi_peserta" {{ old('category', $monitoringTemplate->category) == 'administrasi_peserta' ? 'selected' : '' }}>Administrasi Peserta</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">Sub Kategori</label>
                <input type="text" name="sub_category" class="w-full border rounded px-3 py-2" value="{{ old('sub_category', $monitoringTemplate->sub_category) }}" placeholder="Opsional">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-2 font-semibold">Ownership</label>
                <select name="ownership" class="w-full border rounded px-3 py-2" required>
                    <option value="">Pilih Ownership</option>
                    <option value="event" {{ old('ownership', $monitoringTemplate->ownership) == 'event' ? 'selected' : '' }}>Event</option>
                    <option value="event_subject" {{ old('ownership', $monitoringTemplate->ownership) == 'event_subject' ? 'selected' : '' }}>Event Subject</option>
                </select>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">Update</button>
                <a href="{{ route('monitoring-templates.index') }}" class="text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
