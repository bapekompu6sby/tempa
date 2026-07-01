@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Detail ASN: {{ $asn->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('asn.edit', $asn) }}" class="px-4 py-2 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">Edit</a>
            <a href="{{ route('asn.index') }}" class="px-4 py-2 bg-gray-300 rounded text-sm hover:bg-gray-400">Kembali</a>
        </div>
    </div>

    <!-- Data Pribadi / Atribut ASN -->
    <div class="bg-white p-6 rounded shadow mb-8 border-t-4 border-blue-600">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informasi Dasar</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-8">
            <div>
                <span class="block text-sm text-gray-500 font-medium">NIP</span>
                <span class="block text-lg font-semibold text-gray-900">{{ $asn->nip ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 font-medium">Nama Lengkap</span>
                <span class="block text-lg font-semibold text-gray-900">{{ $asn->name }}</span>
            </div>
            
            <div>
                <span class="block text-sm text-gray-500 font-medium">Jabatan</span>
                <span class="block text-base text-gray-900">{{ $asn->job_title ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 font-medium">Pangkat / Golongan</span>
                <span class="block text-base text-gray-900">{{ $asn->rank_grade ?? '-' }}</span>
            </div>

            <div>
                <span class="block text-sm text-gray-500 font-medium">Email</span>
                <span class="block text-base text-gray-900">{{ $asn->email ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 font-medium">No. HP / WhatsApp</span>
                <span class="block text-base text-gray-900">{{ $asn->phone_number ?? '-' }}</span>
            </div>

            <div>
                <span class="block text-sm text-gray-500 font-medium">Tempat, Tanggal Lahir</span>
                <span class="block text-base text-gray-900">
                    {{ $asn->birth_city ?? '-' }}, 
                    {{ $asn->birth_date ? \Carbon\Carbon::parse($asn->birth_date)->translatedFormat('d F Y') : '-' }}
                </span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 font-medium">Jenis Kelamin</span>
                <span class="block text-base text-gray-900">
                    @if($asn->gender == 'L') Laki-laki @elseif($asn->gender == 'P') Perempuan @else - @endif
                </span>
            </div>

            <div>
                <span class="block text-sm text-gray-500 font-medium">Pendidikan Terakhir</span>
                <span class="block text-base text-gray-900">{{ $asn->latest_education ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 font-medium">Tipe ASN</span>
                <span class="block text-base text-gray-900">{{ strtoupper($asn->asn_type ?? '-') }}</span>
            </div>

            <div>
                <span class="block text-sm text-gray-500 font-medium">Sumber Instansi</span>
                <span class="block text-base text-gray-900">{{ strtoupper($asn->asn_source ?? '-') }}</span>
            </div>
            <div class="md:col-span-3">
                <span class="block text-sm text-gray-500 font-medium">Alamat Kantor</span>
                <span class="block text-base text-gray-900">{{ $asn->office_address ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Riwayat Pelatihan (Events) -->
    <div class="bg-white p-6 rounded shadow border-t-4 border-green-500">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Riwayat Pelatihan</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nama Pelatihan
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal Pelaksanaan
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Model Pembelajaran
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($asn->events->sortByDesc('start_date') as $event)
                    <tr>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:underline font-semibold">{{ $event->name }}</a>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            {{ $event->start_date ? $event->start_date->format('d M Y') : '-' }} 
                            - 
                            {{ $event->end_date ? $event->end_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            @php
                                $lm = $event->learning_model ?? null;
                                $lmLabel = '-';
                                if ($lm === 'full_elearning') $lmLabel = 'E-Learning';
                                elseif ($lm === 'distance_learning') $lmLabel = 'Distance';
                                elseif ($lm === 'blended_learning') $lmLabel = 'Blended';
                                elseif ($lm === 'classical') $lmLabel = 'Klasikal';
                            @endphp
                            {{ $lmLabel }}
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            @php
                                $statusLabels = [
                                    'tentative' => 'Tentative',
                                    'belum_dimulai' => 'Belum Dimulai',
                                    'persiapan' => 'Persiapan',
                                    'pelaksanaan' => 'Pelaksanaan',
                                    'pelaporan' => 'Pelaporan',
                                    'dibatalkan' => 'Dibatalkan',
                                    'selesai' => 'Selesai',
                                ];
                                $displayStatus = $statusLabels[$event->status] ?? $event->status;
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $displayStatus }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                            ASN ini belum mengikuti pelatihan apapun.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
