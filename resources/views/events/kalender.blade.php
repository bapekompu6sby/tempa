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
    </form>
    <style>
        .event-bar {
            min-height: 20px;
            font-size: 11px;
            padding: 0 4px;
            cursor: pointer;
            position: relative;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 0 auto;
            transition: box-shadow 0.2s;
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
        <span class="font-semibold mr-2">Model Pelatihan:</span>
        <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-blue-400 border border-gray-400 inline-block"></span> E-Learning</span>
        <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-yellow-300 border border-gray-400 inline-block"></span> Distance</span>
        <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-green-400 border border-gray-400 inline-block"></span> Blended</span>
        <span class="inline-flex items-center gap-1"><span class="w-4 h-4 rounded-full bg-pink-400 border border-gray-400 inline-block"></span> Klasikal</span>
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
                            $startIdx = ($event->start_date instanceof \Carbon\Carbon ? $event->start_date : \Carbon\Carbon::parse($event->start_date))->format('Y') == $year ? ($event->start_date instanceof \Carbon\Carbon ? $event->start_date : \Carbon\Carbon::parse($event->start_date))->format('n') - 1 : 0;
                            $endIdx = ($event->end_date instanceof \Carbon\Carbon ? $event->end_date : \Carbon\Carbon::parse($event->end_date))->format('Y') == $year ? ($event->end_date instanceof \Carbon\Carbon ? $event->end_date : \Carbon\Carbon::parse($event->end_date))->format('n') - 1 : count($allMonths) - 1;
                            $modelColors = [
                                'full_elearning' => 'bg-blue-400',
                                'distance_learning' => 'bg-yellow-300',
                                'blended_learning' => 'bg-green-400',
                                'classical' => 'bg-pink-400',
                                null => 'bg-gray-300',
                                '' => 'bg-gray-300',
                            ];
                            $color = $modelColors[$event->learning_model ?? ''] ?? 'bg-gray-300';
                        @endphp
                        @php $i = 0; @endphp
                        @while($i < count($allMonths))
                            @if($i == $startIdx)
                                <td colspan="{{ $endIdx - $startIdx + 1 }}" class="border px-0 py-1 text-center align-middle">
                                    <div class="event-bar {{ $color }} text-black font-semibold flex items-center justify-center rounded shadow mx-auto relative"
                                         style="width: 100%; max-width: 100%;"
                                         tabindex="0"
                                         data-event='@json($event)'
                                         onclick="showEventModal(this)">
                                        <span class="event-title">{{ \Illuminate\Support\Str::limit($event->name, 18) }}</span>
                                    </div>
                                </td>
                                @php $i = $endIdx + 1; @endphp
                            @else
                                <td class="border px-0 py-1 month-col"></td>
                                @php $i++; @endphp
                            @endif
                        @endwhile
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($allMonths) }}" class="border px-4 py-2 text-center text-gray-500">Tidak ada pelatihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Single modal for all events -->
    <div id="event-modal" class="modal-bg" onclick="hideEventModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="modal-close" onclick="hideEventModal(event)">&times;</span>
            <h2 id="modal-event-name" class="text-lg font-bold mb-2"></h2>
            <div class="mb-1 text-sm">Status: <span id="modal-event-status" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"></span></div>
            <div class="mb-1 text-sm">Tanggal: <b id="modal-event-date"></b></div>
            <div class="mb-1 text-sm" id="modal-event-note"></div>
            <div class="mb-1 text-sm">Target: <b id="modal-event-target"></b></div>
            <div class="mb-1 text-sm">JP Kurmod: <b id="modal-event-jp-module"></b></div>
            <div class="mb-1 text-sm">JP Pengajar: <b id="modal-event-jp-facilitator"></b></div>
            <div class="mb-1 text-sm">Model: <b id="modal-event-model"></b></div>
        </div>
    </div>
    <script>
        const statusLabels = {
            'tentative': 'Tentative',
            'belum_dimulai': 'Belum Dimulai',
            'persiapan': 'Persiapan',
            'pelaksanaan': 'Pelaksanaan',
            'pelaporan': 'Pelaporan',
            'dibatalkan': 'Dibatalkan',
            'selesai': 'Selesai',
        };
        const statusClasses = {
            'tentative': 'inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold',
            'belum_dimulai': 'inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold',
            'persiapan': 'inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold',
            'pelaksanaan': 'inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 text-xs font-semibold',
            'pelaporan': 'inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-semibold',
            'dibatalkan': 'inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-semibold',
            'selesai': 'inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold',
        };
        const stModel = {
            'full_elearning': 'E-Learning',
            'distance_learning': 'Distance',
            'blended_learning': 'Blended',
            'classical': 'Klasikal',
            null: '-',
            '': '-'
        };
        function showEventModal(el) {
            const event = JSON.parse(el.getAttribute('data-event'));
            document.getElementById('modal-event-name').textContent = event.name || '-';
            // Status
            const stLabel = statusLabels[event.status] || event.status || '-';
            const stClass = statusClasses[event.status] || 'inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold';
            const statusSpan = document.getElementById('modal-event-status');
            statusSpan.textContent = stLabel;
            statusSpan.className = stClass;
            // Date
            let start = event.start_date ? new Date(event.start_date) : null;
            let end = event.end_date ? new Date(event.end_date) : null;
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            function formatDate(d) {
                if (!d) return '-';
                return d.getDate().toString().padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
            }
            document.getElementById('modal-event-date').textContent = `${formatDate(start)} - ${formatDate(end)}`;
            // Note
            const noteDiv = document.getElementById('modal-event-note');
            if (event.note) {
                noteDiv.innerHTML = `<b>Catatan:</b> ${event.note}`;
                noteDiv.style.display = '';
            } else {
                noteDiv.innerHTML = '';
                noteDiv.style.display = 'none';
            }
            document.getElementById('modal-event-target').textContent = event.target ?? '-';
            document.getElementById('modal-event-jp-module').textContent = event.jp_module ?? '-';
            document.getElementById('modal-event-jp-facilitator').textContent = event.jp_facilitator ?? '-';
            document.getElementById('modal-event-model').textContent = stModel[event.learning_model] ?? '-';
            document.getElementById('event-modal').classList.add('active');
        }
        function hideEventModal(e) {
            e.stopPropagation();
            document.getElementById('event-modal').classList.remove('active');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('event-modal').classList.remove('active');
            }
        });
    </script>
</div>
@endsection