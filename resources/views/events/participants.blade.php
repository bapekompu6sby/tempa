@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    {{-- Header & Navigation --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('events.index') }}" class="hover:underline">Pelatihan</a>
                <span>&rsaquo;</span>
                <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'peserta']) }}" class="hover:underline">{{ Str::limit($event->name, 40) }}</a>
                <span>&rsaquo;</span>
                <span class="text-gray-700 font-semibold">Manajemen Peserta</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Peserta: {{ $event->name }}</h2>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'peserta']) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm font-medium transition flex items-center">
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

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm" role="alert">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Import Box (synced with ASN Import, defaulted to this event) --}}
    <div class="mb-6">
        @include('asn._import_form', [
            'formAction' => route('events.importAsn', $event),
            'events' => $events ?? collect([$event]),
            'selectedEventId' => $event->id,
            'showTemplateButton' => true,
            'lockEvent' => true,
        ])
    </div>

    {{-- Actions & Search Bar --}}
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('events.participants', $event) }}" class="flex items-center space-x-2 flex-grow max-w-lg">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama atau NIP peserta..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded text-sm font-medium transition">
                Cari
            </button>
            @if(!empty($q))
                <a href="{{ route('events.participants', $event) }}" class="px-3 py-2 text-sm text-red-600 hover:underline">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex items-center space-x-2">
            <span class="text-xs bg-gray-100 text-gray-700 font-semibold px-3 py-1.5 rounded-full border border-gray-200">
                Total: {{ $participants->total() }} Peserta (50 / hal)
            </span>
            <form method="POST" action="{{ route('events.generateAsnEventSubjects', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded text-xs font-semibold hover:bg-blue-100 transition" title="Hubungkan seluruh peserta dengan mata pelatihan yang ada">
                    Sync ASN ke Mata Pelatihan
                </button>
            </form>
        </div>
    </div>

    {{-- Participants Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                        No
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        NIP
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jabatan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Instansi
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Skor Sikap
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status Kelulusan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participants as $asn)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        {{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap font-mono text-xs">{{ $asn->nip ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap font-semibold">{{ $asn->name }}</p>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-700 whitespace-no-wrap">{{ $asn->job_title ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-700 whitespace-no-wrap">{{ strtoupper($asn->asn_source ?? '-') }}</p>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                        <span class="font-semibold {{ ($asn->pivot->total_behavioral_score ?? 0) >= 80 ? 'text-green-600' : (($asn->pivot->total_behavioral_score ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($asn->pivot->total_behavioral_score ?? 0, 1) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                        @if($asn->pivot->passing_status === 'lulus')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lulus</span>
                        @elseif($asn->pivot->passing_status === 'tidak_lulus')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Lulus</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                        <form method="POST" action="{{ route('events.participants.destroy', ['event' => $event, 'asn' => $asn]) }}" onsubmit="return confirm('Hapus peserta {{ addslashes($asn->name) }} dari pelatihan ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        @if(!empty($q))
                            Tidak ada peserta yang cocok dengan kata kunci "{{ $q }}".
                        @else
                            Belum ada peserta pelatihan ini. Silakan import data peserta menggunakan form di atas.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-5 py-4 bg-white border-t border-gray-200">
            @if(method_exists($participants, 'links'))
                {{ $participants->links('vendor.pagination.light') }}
            @endif
        </div>
    </div>
</div>
@endsection
