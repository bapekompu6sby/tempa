@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Edit Instruksi</h2>
    <form action="{{ route('instructions.update', $instruction) }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block mb-2 font-semibold">Nama</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ old('name', $instruction->name) }}">
        </div>
        <div class="mb-4">
            <label for="role" class="block mb-2 font-semibold">Peran</label>
            <select name="role" id="role" class="w-full border px-3 py-2 rounded">
                <option value="" {{ old('role', $instruction->role) == '' ? 'selected' : '' }}>-- Pilih role --</option>
                <option value="pic" {{ old('role', $instruction->role) == 'pic' ? 'selected' : '' }}>PIC</option>
                <option value="host" {{ old('role', $instruction->role) == 'host' ? 'selected' : '' }}>Host</option>
                <option value="petugas_kelas" {{ old('role', $instruction->role) == 'petugas_kelas' ? 'selected' : '' }}>Petugas Kelas</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="detail" class="block mb-2 font-semibold">Detail</label>
            <textarea name="detail" id="detail" class="w-full border px-3 py-2 rounded">{{ old('detail', $instruction->detail) }}</textarea>
        </div>
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="linkable" id="linkable" class="mr-2" {{ old('linkable', $instruction->linkable) ? 'checked' : '' }}>
            <label for="linkable" class="font-medium">Linkable</label>
        </div>
        <div class="mb-4">
            <label class="block mb-2 font-semibold">Mode Pembelajaran</label>
            <div class="flex space-x-4 items-center">
                <label class="inline-flex items-center"><input type="checkbox" name="full_elearning" {{ old('full_elearning', $instruction->full_elearning) ? 'checked' : '' }} class="mr-2"> Full E-Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="distance_learning" {{ old('distance_learning', $instruction->distance_learning) ? 'checked' : '' }} class="mr-2"> Distance Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="blended_learning" {{ old('blended_learning', $instruction->blended_learning) ? 'checked' : '' }} class="mr-2"> Blended Learning</label>
                <label class="inline-flex items-center"><input type="checkbox" name="classical" {{ old('classical', $instruction->classical) ? 'checked' : '' }} class="mr-2"> Classical</label>
            </div>
        </div>
        <div class="mb-4">
            <label for="phase" class="block mb-2 font-semibold">Phase</label>
            <select name="phase" id="phase" class="w-full border px-3 py-2 rounded">
                @php
                    $phaseOld = old('phase', $instruction->phase ?? 'pelaksanaan');
                    $phases = [
                        'persiapan' => 'Persiapan',
                        'pelaksanaan' => 'Pelaksanaan',
                        'pembukaan_pelatihan' => 'Pembukaan Pelatihan',
                        'penutupan_pelatihan' => 'Penutupan Pelatihan',
                        'evaluasi_pelatihan' => 'Evaluasi Pelatihan',
                        'pasca_pelatihan' => 'Pasca Pelatihan',
                    ];
                @endphp
                @foreach($phases as $value => $label)
                    <option value="{{ $value }}" {{ $phaseOld === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        <a href="{{ route('instructions.index') }}" class="ml-2 px-4 py-2 bg-gray-300 rounded">Batal</a>
    </form>
</div>
@endsection
