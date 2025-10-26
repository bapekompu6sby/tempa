@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Daftar Instruksi</h2>
        <a href="{{ route('instructions.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Tambah Instruksi</a>
    </div>

    {{-- Search + Tabs --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('instructions.index') }}" class="flex items-center flex-wrap gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama atau detail..." class="border rounded px-3 py-2 w-64">

            {{-- Learning model checkboxes --}}
            <div class="flex items-center space-x-3 ml-4">
                @php
                    $f = $filters ?? [];
                @endphp
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="full_elearning" value="1" class="form-checkbox h-4 w-4" {{ !empty($f['full_elearning']) ? 'checked' : '' }}>
                    <span class="ml-2">Full E-Learning</span>
                </label>
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="distance_learning" value="1" class="form-checkbox h-4 w-4" {{ !empty($f['distance_learning']) ? 'checked' : '' }}>
                    <span class="ml-2">Distance</span>
                </label>
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="blended_learning" value="1" class="form-checkbox h-4 w-4" {{ !empty($f['blended_learning']) ? 'checked' : '' }}>
                    <span class="ml-2">Blended</span>
                </label>
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="classical" value="1" class="form-checkbox h-4 w-4" {{ !empty($f['classical']) ? 'checked' : '' }}>
                    <span class="ml-2">Classical</span>
                </label>
            </div>
            
            <div class="ml-4 flex items-center gap-2">
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded cursor-pointer">Cari</button>
                @if(!empty($q))
                    <a href="{{ route('instructions.index', ['tab' => $tab]) }}" class="ml-2 text-sm text-gray-600">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="mb-4 border-b">
        @php
            $tabs = [
                'semua' => 'Semua',
                'pic' => 'PIC',
                'host' => 'Host',
                'pengamat_kelas' => 'Pengamat Kelas',
            ];
        @endphp
        <nav class="flex space-x-2">
            @foreach($tabs as $key => $label)
                @php $active = ($tab === $key); @endphp
                <a href="{{ route('instructions.index', ['tab' => $key, 'q' => $q ?? null]) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $active ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    {{-- Tab content --}}
    @php
        $list = $all;
        if($tab === 'pic') { $list = $pic; }
        elseif($tab === 'host') { $list = $host; }
        elseif($tab === 'pengamat_kelas') { $list = $pengamat; }
    @endphp

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Learning Model</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($list as $instruction)
                        <tr class="border-t align-top">
                            <td class="py-2 px-4">{{ $instruction->name }}</td>
                            <td class="py-2 px-4">
                                {{-- badges for learning model (show only when true) --}}
                                <div class="flex flex-wrap gap-2">
                                    @if($instruction->full_elearning)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Full E-Learning</span>
                                    @endif
                                    @if($instruction->distance_learning)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Distance</span>
                                    @endif
                                    @if($instruction->blended_learning)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Blended</span>
                                    @endif
                                    @if($instruction->classical)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Classical</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                {{-- action icons: view (eye), edit (pencil), delete (trash) --}}
                                <button type="button" class="view-detail" data-id="{{ $instruction->id }}" aria-expanded="false" title="Lihat detail">
                                    <!-- single svg icon, we'll swap paths on toggle -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon h-5 w-5 text-blue-600 inline" viewBox="0 0 20 20" fill="currentColor">
                                        <!-- open eye paths (initial) -->
                                        <path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
                                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('instructions.edit', $instruction) }}" class="ml-3" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 010 2.828l-9.192 9.192a1 1 0 01-.464.263l-4 1a1 1 0 01-1.213-1.213l1-4a1 1 0 01.263-.464l9.192-9.192a2 2 0 012.828 0z"/></svg>
                                </a>
                                <form action="{{ route('instructions.destroy', $instruction) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Hapus instruksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 5a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1zm4 0a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr id="detail-{{ $instruction->id }}" class="detail-row hidden bg-gray-50">
                            <td class="py-3 px-4" colspan="3">{!! nl2br(e($instruction->detail)) !!}</td>
                        </tr>
                @empty
                <tr>
                            <td class="py-4 px-4 text-center text-gray-500" colspan="3">Tidak ada instruksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.view-detail').forEach(function(btn){
                        btn.addEventListener('click', function(){
                                    var id = this.getAttribute('data-id');
                                    var row = document.getElementById('detail-' + id);
                                    var expanded = this.getAttribute('aria-expanded') === 'true';
                                    if (row) {
                                        var svg = this.querySelector('.view-icon');
                                        if (expanded) {
                                            row.classList.add('hidden');
                                            this.setAttribute('aria-expanded', 'false');
                                            // set svg to open-eye (initial)
                                            if (svg) {
                                                svg.innerHTML = '<path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>' +
                                                                '<path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>';
                                            }
                                            this.setAttribute('title', 'Lihat detail');
                                        } else {
                                            row.classList.remove('hidden');
                                            this.setAttribute('aria-expanded', 'true');
                                            // set svg to a close / eye-off-like icon
                                            if (svg) {
                                                svg.innerHTML = '<path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>';
                                            }
                                            this.setAttribute('title', 'Sembunyikan detail');
                                        }
                                    }
                                });
                    });
                });
            </script>
</div>
@endsection
