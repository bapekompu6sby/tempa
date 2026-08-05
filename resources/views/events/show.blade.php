@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Detail Pelatihan</h2>
    <div class="bg-white p-6 rounded shadow max-w-5xl mx-auto relative border-t-4 border-gray-200">
        {{-- Top-right action buttons --}}
        <div class="absolute top-4 right-4 flex space-x-2">
            @php
                $hasParticipants = $event->asns()->exists();
                $hasAsnEventSubject = \Illuminate\Support\Facades\DB::table('asn_event_subject')
                    ->whereIn('asn_event_id', \Illuminate\Support\Facades\DB::table('asn_event')->where('event_id', $event->id)->pluck('id'))
                    ->exists();
                $canGenerateMonitoring = $hasParticipants && $hasAsnEventSubject;
            @endphp
            <!-- <form method="POST" action="{{ route('events.calculateBehavioralScores', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded text-sm bg-purple-600 text-white hover:bg-purple-700 cursor-pointer" title="Calculate Behavioral Score">Calculate Behavioral Score</button>
            </form> -->
            <form method="POST" action="{{ route('events.generateMonitoringItems', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded text-sm {{ $canGenerateMonitoring ? 'bg-blue-600 text-white hover:bg-blue-700 cursor-pointer' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}" {{ $canGenerateMonitoring ? '' : 'disabled' }} title="{{ $canGenerateMonitoring ? 'Generate semua form monitoring untuk peserta' : 'Event harus memiliki peserta dan sudah di-inisiasi nilainya' }}">Generate Monitoring Items</button>
            </form>
            <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 bg-yellow-500 text-white rounded text-sm">Edit</a>
            <a href="{{ route('events.documents', $event) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">Dokumen</a>
            <form method="POST" action="{{ route('events.finish', $event) }}" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded text-sm cursor-pointer" onclick="return confirm('Tandai pelatihan sebagai selesai?')">Selesaikan Pelatihan</button>
            </form>
            <a href="{{ route('events.index') }}" class="px-3 py-1.5 bg-gray-300 rounded text-sm">Kembali</a>
        </div>
        {{-- Event Report File (moved below header) --}}
        @if(!empty($event->event_report_url))
        <div class="mb-6 mt-8 flex items-center justify-between bg-green-50 border border-green-200 rounded p-4 shadow-sm">
            <div>
                <div class="text-lg font-semibold text-green-900 mb-1 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    File Laporan Pelatihan
                </div>
                <div class="text-sm text-green-800 break-all">{{ basename($event->event_report_url) }}</div>
            </div>
            <div>
                <a href="{{ route('events.downloadReport', $event) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition cursor-pointer font-semibold">Download</a>
            </div>
        </div>
        @endif
        <div class="mb-4">
            <strong>Nama:</strong> {{ $event->name }}
        </div>
        <div class="mb-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><strong>Target:</strong> {{ $event->target ?? '-' }}</div>
            <div><strong>JP Kurmod:</strong> {{ $event->jp_module ?? '-' }}</div>
            <div><strong>JP Pengajar:</strong> {{ $event->jp_facilitator ?? '-' }}</div>
            <div><strong>Bidang:</strong> {{ $event->field ?? '-' }}</div>
        </div>
        <div class="mb-4">
            <strong>Model Pembelajaran:</strong>
            @php
                $lm = $event->learning_model ?? null;
                $lmLabel = null;
                $lmClass = 'inline-flex items-center px-2 py-0 text-[11px] leading-none font-semibold rounded-full';
                if ($lm === 'full_elearning') {
                    $lmLabel = 'E-Learning';
                    $lmClass .= ' bg-indigo-100 text-indigo-800 border border-indigo-200';
                } elseif ($lm === 'distance_learning') {
                    $lmLabel = 'Distance';
                    $lmClass .= ' bg-teal-100 text-teal-800 border border-teal-200';
                } elseif ($lm === 'blended_learning') {
                    $lmLabel = 'Blended';
                    $lmClass .= ' bg-orange-100 text-orange-800 border border-orange-200';
                } elseif ($lm === 'classical') {
                    $lmLabel = 'Klasikal';
                    $lmClass .= ' bg-pink-100 text-pink-800 border border-pink-200';
                }
            @endphp
            @if($lmLabel)
                <span class="{{ $lmClass }}">{{ $lmLabel }}</span>
            @else
                <span class="text-sm text-gray-600">-</span>
            @endif
        </div>
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
        <div class="mb-4">
            <strong>Status:</strong>
            <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded-full bg-gray-100 text-gray-800 font-semibold text-sm">{{ $displayStatus }}</span>
        </div>
        <div class="mb-4">
            <strong>Tanggal Mulai:</strong> {{ optional($event->start_date)->format('d M Y') }}
        </div>
        <div class="mb-4">
            <strong>Tanggal Selesai:</strong> {{ optional($event->end_date)->format('d M Y') }}
        </div>
        {{-- <div class="mb-4">
            <div class="grid grid-cols-3 gap-4">
                @php
                    $phases = ['persiapan' => 'Persiapan', 'pelaksanaan' => 'Pelaksanaan', 'pelaporan' => 'Pelaporan'];
                @endphp
                @foreach($phases as $key => $label)
                    @php
                        $total = $event->instructionCountByPhase($key);
                        $checked = $event->checkedInstructionCountByPhase($key);
                    @endphp
                    <div class="bg-gray-50 p-3 rounded shadow-sm">
                        <div class="text-sm text-gray-600">{{ $label }}</div>
                        <div class="text-lg font-semibold">{{ $total }} <span class="text-sm text-gray-500">instruksi</span></div>
                        <div class="text-sm text-green-600">{{ $checked }} checked</div>
                    </div>
                @endforeach
            </div>
        </div> --}}
        @if(!empty($event->note))
        <div class="mb-4">
            <strong>Catatan:</strong>
            <div class="mt-2 whitespace-pre-wrap bg-gray-50 p-3 rounded">{{ $event->note }}</div>
        </div>
        @endif
        
        {{-- Main Tabs --}}
        @php
            $main_tab = request('main_tab', 'peserta');
        @endphp
        <div class="mb-6 border-b">
            <nav class="flex space-x-4">
                {{-- <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'instruksi']) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $main_tab === 'instruksi' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">Instruksi</a> --}}
                <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'peserta']) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $main_tab === 'peserta' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">Peserta</a>
                <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'mata_pelatihan']) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $main_tab === 'mata_pelatihan' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">Mata Pelatihan</a>
            </nav>
        </div>

        @if($main_tab === 'instruksi')
        {{-- Event Instructions list (tabs + search like instructions index) --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2">Instruksi untuk Pelatihan</h3>

            {{-- Search + Tabs --}}
            <div class="mb-4 flex items-center justify-between">
                <form method="GET" action="{{ route('events.show', $event) }}" class="flex items-center space-x-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama atau detail..." class="border rounded px-3 py-2 w-64">

                    {{-- Phase select (single choice; 'all' shows all phases) --}}
                    <select name="phase" class="border rounded px-2 py-2 text-sm">
                        <option value="all" {{ (isset($phase) && $phase === 'all') ? 'selected' : '' }}>Semua Fase</option>
                        <option value="persiapan" {{ (isset($phase) && $phase === 'persiapan') ? 'selected' : '' }}>Persiapan</option>
                        <option value="pelaksanaan" {{ (isset($phase) && $phase === 'pelaksanaan') ? 'selected' : '' }}>Pelaksanaan</option>
                        <option value="pelaporan" {{ (isset($phase) && $phase === 'pelaporan') ? 'selected' : '' }}>Pelaporan</option>
                    </select>

                    <button type="submit" class="px-3 py-2 bg-gray-200 rounded">Cari</button>
                    @if(!empty($q) || (isset($phase) && $phase !== 'all'))
                        <a href="{{ route('events.show', ['event' => $event, 'tab' => $tab]) }}" class="ml-2 text-sm text-gray-600">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tabs --}}
            <div class="mb-4 border-b">
                    @php
                    $tabs = [
                        'semua' => 'Semua',
                        'pic' => 'PIC',
                        'host' => 'Host',
                        'petugas_kelas' => 'Petugas Kelas',
                    ];
                @endphp
                <nav class="flex space-x-2">
                    @foreach($tabs as $key => $label)
                        @php $active = ($tab === $key); @endphp
                        @php
                            // preserve q and phase when switching tabs inside event
                            $params = ['event' => $event, 'tab' => $key];
                            if(!empty($q)) $params['q'] = $q;
                            if(isset($phase) && $phase !== 'all') $params['phase'] = $phase;
                        @endphp
                        <a href="{{ route('events.show', $params) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $active ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            @php
                $list = $all ?? collect();
                if($tab === 'pic') { $list = $pic ?? collect(); }
                elseif($tab === 'host') { $list = $host ?? collect(); }
                elseif($tab === 'petugas_kelas') { $list = $pengamat ?? collect(); }

                // phase -> color mapping
                $phaseClasses = [
                    'persiapan' => ['border' => 'border-yellow-400', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-800'],
                    'pelaksanaan' => ['border' => 'border-green-400', 'bg' => 'bg-green-50', 'text' => 'text-green-800'],
                    'pembukaan_pelatihan' => ['border' => 'border-blue-400', 'bg' => 'bg-blue-50', 'text' => 'text-blue-800'],
                    'penutupan_pelatihan' => ['border' => 'border-purple-400', 'bg' => 'bg-purple-50', 'text' => 'text-purple-800'],
                    'evaluasi_pelatihan' => ['border' => 'border-red-400', 'bg' => 'bg-red-50', 'text' => 'text-red-800'],
                    'pasca_pelatihan' => ['border' => 'border-gray-400', 'bg' => 'bg-gray-50', 'text' => 'text-gray-800'],
                    // consolidated phase after normalization
                    'pelaporan' => ['border' => 'border-red-400', 'bg' => 'bg-red-50', 'text' => 'text-red-800'],
                ];
            @endphp

            <div class="bg-white shadow rounded overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">No.</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700"> </th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $ei)
                            @php $instr = $ei->instruction; @endphp
                            @php
                                $phase = $ei->phase ?? optional($instr)->phase ?? 'pelaksanaan';
                                $c = $phaseClasses[$phase] ?? $phaseClasses['pelaksanaan'];
                            @endphp
                            <tr id="ei-row-{{ $ei->id }}" class="border-t align-top border-l-4 {{ $c['border'] }} {{ $ei->checked ? 'bg-green-50' : '' }}">
                                    <td class="py-2 px-4 text-sm text-gray-700 font-semibold w-10">{{ $loop->iteration }}</td>
                                    <td class="py-2 px-4 w-20">
                                        <input type="checkbox" data-ei-id="{{ $ei->id }}" {{ $ei->checked ? 'checked' : '' }} class="ei-checkbox w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500" />
                                    </td>
                                    <td class="py-2 px-4">
                                        <div class="flex items-center">
                                            <span>{{ $instr->name ?? 'Instruksi #' . $ei->instruction_id }}</span>
                                            <span class="ml-3 px-2 py-0.5 text-xs font-medium rounded {{ $c['bg'] }} {{ $c['text'] }}">{{ ucwords(str_replace('_', ' ', $phase)) }}</span>
                                        </div>
                                    </td>
                                    
                                <td class="py-2 px-4 text-right w-20 whitespace-nowrap">
                                    <button type="button" class="view-detail-ei" data-id="{{ $ei->id }}" aria-expanded="false" title="Lihat detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 inline view-icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-ei-{{ $ei->id }}" class="detail-row hidden bg-gray-50">
                                <td class="py-3 px-4" colspan="4">
                                    <div class="mb-2">{!! nl2br(e(optional($instr)->detail)) !!}</div>

                                    @if(optional($instr)->linkable)
                                        @php
                                            $displayLabel = $ei->link_label ?? optional($instr)->link_label ?? null;
                                        @endphp
                                        <div id="ei-link-display-{{ $ei->id }}" class="ei-link-display" data-link-label="{{ $displayLabel }}">
                                            @if(!empty($ei->link))
                                                @php
                                                    // ensure rendered href has a scheme so browser treats it as absolute
                                                    $raw = $ei->link;
                                                    $href = (strpos($raw, '://') !== false) ? $raw : 'https://' . ltrim($raw, '/');
                                                    $label = $displayLabel;
                                                @endphp
                                                @if($label)
                                                    <span id="ei-link-label-{{ $ei->id }}" class="font-semibold text-black">{{ $label }}</span>
                                                    <span class="mx-1">:</span>
                                                @endif
                                                <a id="ei-link-anchor-{{ $ei->id }}" href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($raw, 20) }}</a>
                                                <button type="button" data-ei-id="{{ $ei->id }}" class="ei-link-edit ml-3 px-2 py-1 bg-yellow-500 text-white rounded text-sm">Edit</button>
                                            @else
                                                @if($displayLabel)
                                                    <span class="font-semibold text-black">{{ $displayLabel }}</span>
                                                @else
                                                    <span class="text-gray-600">Belum ada link.</span>
                                                @endif
                                            @endif
                                        </div>

                                            <div id="ei-link-form-{{ $ei->id }}" class="ei-link-form mt-2 {{ empty($ei->link) ? '' : 'hidden' }}">
                                            <div class="flex items-center space-x-2">
                                                <input name="link" type="text" value="{{ $ei->link }}" placeholder="Masukkan link..." class="border rounded px-3 py-2 w-full ei-link-input" />
                                                <button type="button" data-ei-id="{{ $ei->id }}" class="ei-link-save px-3 py-2 bg-green-600 text-white rounded text-sm">Simpan</button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-4 text-center text-gray-500" colspan="4">Tidak ada instruksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- pagination links (preserve current query params) --}}
                <div class="p-3">
                    @if(method_exists($list, 'links'))
                        <div class="mt-2">
                            {{ $list->appends(request()->except('page'))->links('vendor.pagination.light') }}
                        </div>
                    @endif
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('tr').forEach(function(row){
                        // attach click handler to rows to toggle detail if needed
                    });
                });
            </script>
        </div>
        @elseif($main_tab === 'peserta')
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-4">Peserta Pelatihan</h3>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{!! session('success') !!}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{!! session('error') !!}</span>
                </div>
            @endif

            <div class="bg-gray-50 p-6 rounded shadow mb-6 border-t-4 border-blue-500">
                <h3 class="text-md font-bold mb-4">Import ASN ke Pelatihan</h3>
                <form method="POST" action="{{ route('events.importAsn', $event) }}" enctype="multipart/form-data" class="flex items-end space-x-4">
                    @csrf
                    <div class="flex-grow">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Excel/CSV</label>
                        <input type="file" name="file" required class="w-full border p-2 rounded bg-white">
                        <p class="text-xs text-gray-500 mt-1">Gunakan template yang sama dengan Import ASN di menu ASN.</p>
                    </div>
                    <div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">Import Data</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIP</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jabatan</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Instansi</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Skor Sikap</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($participants as $asn)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $asn->nip ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap font-semibold">{{ $asn->name }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $asn->job_title ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ strtoupper($asn->asn_source ?? '-') }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <span class="font-semibold {{ $asn->pivot->total_behavioral_score >= 80 ? 'text-green-600' : ($asn->pivot->total_behavioral_score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($asn->pivot->total_behavioral_score, 1) }}
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                @if($asn->pivot->passing_status === 'lulus')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lulus</span>
                                @elseif($asn->pivot->passing_status === 'tidak_lulus')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Lulus</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                Belum ada peserta pelatihan ini. Silakan import data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">
                    @if(method_exists($participants, 'links'))
                        {{ $participants->links('vendor.pagination.light') }}
                    @endif
                </div>
            </div>
        </div>
        @elseif($main_tab === 'mata_pelatihan')
        <div class="mb-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Mata Pelatihan</h3>
                <form method="POST" action="{{ route('events.calculateBehavioralScores', $event) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700" onclick="return confirm('Hitung Nilai Sikap Perilaku?')">Kalkulasi Nilai Sikap Perilaku Peserta</button>
                </form>
            </div>
            
            <div class="mb-6 bg-gray-50 p-4 rounded border">
                <h4 class="font-semibold mb-3">Tambah Mata Pelatihan</h4>
                <form method="POST" action="{{ route('events.subjects.store', $event) }}" class="flex space-x-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Mata Pelatihan" class="border p-2 rounded flex-1" required>
                    <input type="number" name="jp" placeholder="JP" class="border p-2 rounded w-24" required min="1">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
                </form>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Mata Pelatihan</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32 text-center">JP</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-semibold">
                                <a href="{{ route('events.subjectMonitoring', ['event' => $event, 'subject' => $subject]) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $subject->name }}
                                </a>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                {{ $subject->jp }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <button type="button" onclick="document.getElementById('edit-subject-{{ $subject->id }}').classList.toggle('hidden')" class="text-blue-600 hover:underline">Edit</button>
                                <form method="POST" action="{{ route('events.subjects.destroy', [$event, $subject]) }}" class="inline-block ml-2" onsubmit="return confirm('Hapus mata pelatihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="edit-subject-{{ $subject->id }}" class="hidden bg-gray-50">
                            <td colspan="3" class="px-5 py-3 border-b border-gray-200">
                                <form method="POST" action="{{ route('events.subjects.update', [$event, $subject]) }}" class="flex space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $subject->name }}" class="border p-2 rounded flex-1" required>
                                    <input type="number" name="jp" value="{{ $subject->jp }}" class="border p-2 rounded w-24" required min="1">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                                    <button type="button" onclick="document.getElementById('edit-subject-{{ $subject->id }}').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                Belum ada mata pelatihan untuk event ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const token = '{{ csrf_token() }}';

        function normalizeLink(raw) {
            if (!raw) return raw;
            // if already has a scheme (http:, https:, mailto:, etc.), return as-is
            if (/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(raw)) return raw;
            // otherwise assume https
            return 'https://' + raw.replace(/^\/+/, '');
        }

        function truncate(str, max) {
            max = max || 20;
            if (!str) return '';
            return str.length > max ? str.slice(0, max - 1) + '…' : str;
        }

        async function toggleChecked(id, checkbox) {
            // disable while updating
            checkbox.disabled = true;
            try {
                const res = await fetch(`/event-instructions/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();

                // Update the row styling to indicate success when checked
                const row = document.getElementById('ei-row-' + id);
                if (row) {
                    if (data.checked) {
                        row.classList.add('bg-green-50');
                    } else {
                        row.classList.remove('bg-green-50');
                    }
                }
            } catch (err) {
                console.error(err);
                // revert checkbox on error
                checkbox.checked = !checkbox.checked;
                alert('Gagal memperbarui status.');
            } finally {
                checkbox.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // wire checkbox toggle
            document.querySelectorAll('.ei-checkbox').forEach(function (el) {
                el.addEventListener('change', function (e) {
                    const id = el.getAttribute('data-ei-id');
                    toggleChecked(id, el);
                });
            });

            // wire show/hide detail buttons
            document.querySelectorAll('.view-detail-ei').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var id = this.getAttribute('data-id');
                    var row = document.getElementById('detail-ei-' + id);
                    var expanded = this.getAttribute('aria-expanded') === 'true';
                    if (row) {
                        var svg = this.querySelector('.view-icon');
                        if (expanded) {
                            row.classList.add('hidden');
                            this.setAttribute('aria-expanded', 'false');
                            // set svg to open-eye (initial)
                            if (svg) {
                                svg.innerHTML = '<path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>'+
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

                // wire link edit/save buttons (for linkable instructions)
                document.querySelectorAll('.ei-link-edit').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var id = this.getAttribute('data-ei-id');
                        var display = document.getElementById('ei-link-display-' + id);
                        var form = document.getElementById('ei-link-form-' + id);
                        if (display) display.classList.add('hidden');
                        if (form) form.classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.ei-link-save').forEach(function(btn){
                    btn.addEventListener('click', async function(){
                        var id = this.getAttribute('data-ei-id');
                        var form = document.getElementById('ei-link-form-' + id);
                        var input = form ? form.querySelector('input[name="link"]') : null;
                        if (!input) return;
                        var val = input.value.trim();
                        this.disabled = true;
                        try {
                            const res = await fetch(`/event-instructions/${id}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ link: val })
                            });
                            if (!res.ok) throw new Error('Network error');
                            const data = await res.json();

                            // update display
                            var anchor = document.getElementById('ei-link-anchor-' + id);
                            var display = document.getElementById('ei-link-display-' + id);
                            if (anchor) {
                                const savedLink = data.eventInstruction.link || val;
                                const savedLabel = data.eventInstruction.link_label || display?.dataset?.linkLabel || '';
                                anchor.href = normalizeLink(savedLink);
                                anchor.textContent = truncate(savedLink, 20);

                                // update or create label span (bold black)
                                let labelEl = document.getElementById('ei-link-label-' + id);
                                if (savedLabel) {
                                    if (!labelEl && display) {
                                        labelEl = document.createElement('span');
                                        labelEl.id = 'ei-link-label-' + id;
                                        labelEl.className = 'font-semibold text-black';
                                        display.insertBefore(labelEl, anchor);
                                        // insert colon separator
                                        const sep = document.createElement('span');
                                        sep.className = 'mx-1';
                                        sep.textContent = ':';
                                        display.insertBefore(sep, anchor);
                                    }
                                    if (labelEl) labelEl.textContent = savedLabel;
                                }
                            } else if (display) {
                                // create label (if present) and anchor if not present
                                const newLink = data.eventInstruction.link || val;
                                const newLabel = data.eventInstruction.link_label || display.dataset.linkLabel || '';

                                if (newLabel) {
                                    var labelSpan = document.createElement('span');
                                    labelSpan.id = 'ei-link-label-' + id;
                                    labelSpan.className = 'font-semibold text-black';
                                    labelSpan.textContent = newLabel;
                                    display.insertBefore(labelSpan, display.firstChild);

                                    var sep = document.createElement('span');
                                    sep.className = 'mx-1';
                                    sep.textContent = ':';
                                    display.insertBefore(sep, display.firstChild.nextSibling);
                                }

                                var a = document.createElement('a');
                                a.id = 'ei-link-anchor-' + id;
                                a.href = normalizeLink(newLink);
                                a.target = '_blank';
                                a.rel = 'noopener noreferrer';
                                a.className = 'text-blue-600 underline';
                                a.textContent = truncate(newLink, 20);
                                display.insertBefore(a, display.firstChild.nextSibling || display.firstChild);
                            }

                            // hide form and show display
                            if (form) form.classList.add('hidden');
                            if (display) {
                                display.classList.remove('hidden');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Gagal menyimpan link.');
                        } finally {
                            this.disabled = false;
                        }
                    });
                });
        });
    })();
</script>
@endpush
