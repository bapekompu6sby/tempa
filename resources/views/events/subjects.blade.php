@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    {{-- Header & Navigation --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('events.index') }}" class="hover:underline">Pelatihan</a>
                <span>&rsaquo;</span>
                <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'mata_pelatihan']) }}" class="hover:underline">{{ Str::limit($event->name, 40) }}</a>
                <span>&rsaquo;</span>
                <span class="text-gray-700 font-semibold">Manajemen Mata Pelatihan</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Mata Pelatihan: {{ $event->name }}</h2>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'mata_pelatihan']) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm font-medium transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Detail Pelatihan
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded shadow-sm flex items-center justify-between">
            <div>{!! session('success') !!}</div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm flex items-center justify-between">
            <div>{!! session('error') !!}</div>
        </div>
    @endif

    {{-- Top Action Buttons --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm text-gray-600">
            Total Mata Pelatihan: <span class="font-bold text-gray-900">{{ $subjects->count() }}</span> | Total JP: <span class="font-bold text-gray-900">{{ $subjects->sum('jp') }} JP</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('events.calculateBehavioralScores', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm font-semibold transition flex items-center" onclick="return confirm('Kalkulasi nilai sikap perilaku seluruh peserta untuk semua mata pelatihan?')">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Kalkulasi Nilai Sikap Perilaku
                </button>
            </form>
            <form method="POST" action="{{ route('events.generateAsnEventSubjects', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold transition flex items-center" title="Sinkronisasi seluruh mata pelatihan dengan peserta">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sync ASN ke Mata Pelatihan
                </button>
            </form>
        </div>
    </div>

    {{-- Form Tambah Mata Pelatihan --}}
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h3 class="text-md font-bold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tambah Mata Pelatihan Baru
        </h3>
        <form method="POST" action="{{ route('events.subjects.store', $event) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex-grow">
                <input type="text" name="name" placeholder="Nama Mata Pelatihan (cth: Pengantar Kebijakan Publik)" class="w-full border border-gray-300 p-2.5 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="w-full sm:w-32">
                <input type="number" name="jp" placeholder="JP" min="1" class="w-full border border-gray-300 p-2.5 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- Table Mata Pelatihan --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                        No
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama Mata Pelatihan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        JP
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">
                        Monitoring Sikap
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm font-semibold text-gray-900">
                        <a href="{{ route('events.subjectMonitoring', ['event' => $event, 'subject' => $subject]) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                            {{ $subject->name }}
                        </a>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center font-medium text-gray-700">
                        {{ $subject->jp }} JP
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                        <a href="{{ route('events.subjectMonitoring', ['event' => $event, 'subject' => $subject]) }}" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                            Form Monitoring &rarr;
                        </a>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                        <button type="button" onclick="document.getElementById('edit-subject-{{ $subject->id }}').classList.toggle('hidden')" class="text-blue-600 hover:text-blue-800 font-semibold text-xs hover:underline mr-2">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('events.subjects.destroy', [$event, $subject]) }}" class="inline-block" onsubmit="return confirm('Hapus mata pelatihan {{ addslashes($subject->name) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                {{-- Edit Inline Row --}}
                <tr id="edit-subject-{{ $subject->id }}" class="hidden bg-blue-50">
                    <td colspan="5" class="px-5 py-3 border-b border-gray-200">
                        <form method="POST" action="{{ route('events.subjects.update', [$event, $subject]) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            @csrf
                            @method('PUT')
                            <div class="flex-grow">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Mata Pelatihan:</label>
                                <input type="text" name="name" value="{{ $subject->name }}" class="w-full border border-gray-300 p-2 rounded text-sm bg-white" required>
                            </div>
                            <div class="w-full sm:w-28">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">JP:</label>
                                <input type="number" name="jp" value="{{ $subject->jp }}" class="w-full border border-gray-300 p-2 rounded text-sm bg-white" required min="1">
                            </div>
                            <div class="flex items-end space-x-2 pt-2 sm:pt-4">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs font-semibold transition">
                                    Simpan Perubahan
                                </button>
                                <button type="button" onclick="document.getElementById('edit-subject-{{ $subject->id }}').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-xs font-semibold transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        Belum ada mata pelatihan untuk event ini. Silakan tambahkan mata pelatihan melalui form di atas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
