@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Kalender Pelatihan</h1>
    <form method="GET" class="mb-4 flex items-center gap-2">
        <label for="year" class="font-medium">Tahun:</label>
        <select name="year" id="year" class="border rounded px-2 py-1" onchange="this.form.submit()">
            @foreach($years as $y)
                <option value="{{ $y }}" @if($y == $year) selected @endif>{{ $y }}</option>
            @endforeach
        </select>
        <label for="colorBy" class="font-medium ml-4">Indikator Warna:</label>
        <select id="colorBy" class="border rounded px-2 py-1">
            <option value="model" selected>Model Pelatihan</option>
            <option value="status">Status</option>
            <option value="field">Bidang</option>
        </select>
    </form>
    <style>
        .event-bar {
            min-height: 20px;
            font-size: 11px;
            padding: 0 4px;
            cursor: pointer;
            position: relative;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            /* Remove margin and max-width to fill cell */
            transition: box-shadow 0.2s;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .event-bar:active, .event-bar:focus {
            box-shadow: 0 0 0 2px #2563eb;
        }
        .month-col {
            width: 90px;
            min-width: 90px;
            max-width: 90px;
        }
        .event-title {
            display: inline-block;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }
        .modal-bg {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0; top: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            align-items: center;
            justify-content: center;
        }
        .modal-bg.active { display: flex; }
        .modal-content {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
            padding: 24px 32px;
            min-width: 300px;
            max-width: 90vw;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 20px;
            color: #888;
            cursor: pointer;
        }
    </style>
    <!-- Legend for learning model colors -->
    <div class="mb-4 flex flex-wrap gap-4 items-center">
        <span class="font-semibold mr-2" id="legend-title">Model Pelatihan:</span>
        <span id="legend-model">
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-blue-400 border border-gray-400 inline-block"></span> E-Learning</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-yellow-300 border border-gray-400 inline-block"></span> Distance</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-green-400 border border-gray-400 inline-block"></span> Blended</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-pink-400 border border-gray-400 inline-block"></span> Klasikal</span>
        </span>
        <span id="legend-status" style="display:none">
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-gray-400 border border-gray-400 inline-block"></span> Tentative</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-blue-300 border border-gray-400 inline-block"></span> Belum Dimulai</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-yellow-400 border border-gray-400 inline-block"></span> Persiapan</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-blue-500 border border-gray-400 inline-block"></span> Pelaksanaan</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-purple-400 border border-gray-400 inline-block"></span> Pelaporan</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-red-400 border border-gray-400 inline-block"></span> Dibatalkan</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-green-500 border border-gray-400 inline-block"></span> Selesai</span>
        </span>
        <span id="legend-field" style="display:none">
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-blue-400 border border-gray-400 inline-block"></span> SDA</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-orange-400 border border-gray-400 inline-block"></span> CKPS</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-green-400 border border-gray-400 inline-block"></span> BM</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-pink-400 border border-gray-400 inline-block"></span> PIW</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-purple-400 border border-gray-400 inline-block"></span> Manajemen</span>
            <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-gray-300 border border-gray-400 inline-block"></span> Lainnya</span>
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    @foreach($allMonths as $month)
                        <th class="border px-1 py-1 bg-gray-100 text-center month-col">{{ $month }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        @php
                            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                            $startIdx = ($event->start_date->format('Y') == $year) ? $event->start_date->format('n') - 1 : 0;
                            $endIdx = ($event->end_date->format('Y') == $year) ? $event->end_date->format('n') - 1 : count($allMonths) - 1;
                            // Color maps for each indicator
                            $modelColors = [
                                'full_elearning' => 'bg-blue-400',
                                'distance_learning' => 'bg-yellow-300',
                                'blended_learning' => 'bg-green-400',
                                'classical' => 'bg-pink-400',
                                null => 'bg-gray-300',
                                '' => 'bg-gray-300',
                            ];
                            $statusColors = [
                                'tentative' => 'bg-gray-400',
                                'belum_dimulai' => 'bg-blue-300',
                                'persiapan' => 'bg-orange-400',
                                'pelaksanaan' => 'bg-blue-500',
                                'pelaporan' => 'bg-purple-400',
                                'dibatalkan' => 'bg-red-400',
                                'selesai' => 'bg-green-500',
                                null => 'bg-gray-300',
                                '' => 'bg-gray-300',
                            ];
                            $fieldColors = [
                                'sda' => 'bg-blue-400',
                                'ckps' => 'bg-orange-400',
                                'bm' => 'bg-green-400',
                                'piw' => 'bg-pink-400',
                                'manajemen' => 'bg-purple-400',
                                null => 'bg-gray-300',
                                '' => 'bg-gray-300',
                            ];
                        @endphp
                        @php $skip = false; @endphp
                        @foreach($allMonths as $idx => $month)
                            @if($idx < $startIdx || $idx > $endIdx)
                                <td class="border px-0 py-1 month-col"></td>
                            @elseif($idx == $startIdx)
                                <td colspan="{{ $endIdx - $startIdx + 1 }}" class="border px-0 py-1 text-center align-middle">
                                    <div class="event-bar"
                                         data-model="{{ $modelColors[$event->learning_model ?? ''] ?? 'bg-gray-300' }}"
                                         data-status="{{ $statusColors[$event->status ?? ''] ?? 'bg-gray-300' }}"
                                         data-field="{{ $fieldColors[Str::lower($event->field ?? '')] ?? 'bg-gray-300' }}"
                                         tabindex="0"
                                         onclick="showEventModal({{ $event->id }})">
                                        <span class="event-title">{{ \Illuminate\Support\Str::limit($event->name, 18) }}</span>
                                    </div>
                                    <div id="event-modal-{{ $event->id }}" class="modal-bg" onclick="hideEventModal(event, {{ $event->id }})">
                                        <div class="modal-content" onclick="event.stopPropagation()">
                                            <span class="modal-close" onclick="hideEventModal(event, {{ $event->id }})">&times;</span>
                                            <h2 class="text-lg font-bold mb-2">{{ $event->name }}</h2>
                                            <div class="mb-1 text-sm">Tanggal: <b>{{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y') }}</b> - <b>{{ \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y') }}</b></div>
                                            @if(!empty($event->note))
                                            <div class="mb-1 text-sm"><b>Catatan:</b> {{ $event->note }}</div>
                                            @endif
                                            <div class="mb-1 text-sm">Target: <b>{{ $event->target ?? '-' }}</b></div>
                                            <div class="mb-1 text-sm">JP Kurmod: <b>{{ $event->jp_module ?? '-' }}</b></div>
                                            <div class="mb-1 text-sm">JP Pengajar: <b>{{ $event->jp_facilitator ?? '-' }}</b></div>
                                            <div class="mb-1 text-sm">Model: <b>{{ $event->learning_model ? \Illuminate\Support\Str::title(str_replace('_', ' ', $event->learning_model)) : '-' }}</b></div>
                                            <div class="mb-1 text-sm">Status: <b>{{ $event->status ?? '-' }}</b></div>
                                            <div class="mb-1 text-sm">Bidang: <b>{{ $event->field ?? '-' }}</b></div>
                                        </div>
                                    </div>
                                </td>
                                @php $skip = true; @endphp
                            @elseif($skip)
                                @php if($idx == $endIdx) $skip = false; @endphp
                                @continue
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($allMonths) }}" class="border px-4 py-2 text-center text-gray-500">Tidak ada pelatihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <script>
        function showEventModal(id) {
            document.getElementById('event-modal-' + id).classList.add('active');
        }
        function hideEventModal(e, id) {
            e.stopPropagation();
            document.getElementById('event-modal-' + id).classList.remove('active');
        }
        // Optional: close modal on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-bg.active').forEach(function(modal) {
                    modal.classList.remove('active');
                });
            }
        });

        // Color indicator logic
        function updateColorIndicator() {
            var colorBy = document.getElementById('colorBy').value;
            // Update legend
            document.getElementById('legend-model').style.display = colorBy === 'model' ? '' : 'none';
            document.getElementById('legend-status').style.display = colorBy === 'status' ? '' : 'none';
            document.getElementById('legend-field').style.display = colorBy === 'field' ? '' : 'none';
            document.getElementById('legend-title').textContent =
                colorBy === 'model' ? 'Model Pelatihan:' :
                colorBy === 'status' ? 'Status:' :
                'Bidang:';
            // Update event bar colors
            document.querySelectorAll('.event-bar').forEach(function(bar) {
                bar.classList.remove('bg-blue-400','bg-yellow-300','bg-green-400','bg-pink-400','bg-gray-300','bg-gray-400','bg-blue-500','bg-green-500','bg-red-400','bg-purple-400','bg-orange-400','bg-teal-400');
                var colorClass = bar.getAttribute('data-' + colorBy);
                if (colorClass) bar.classList.add(colorClass);
            });
        }
        document.getElementById('colorBy').addEventListener('change', updateColorIndicator);
        // Initial call
        updateColorIndicator();
    </script>
</div>
@endsection