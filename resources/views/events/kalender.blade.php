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
                            // Color by learning model
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
                        @for($i = 0; $i < count($allMonths); $i++)
                            @if($i == $startIdx)
                                <td colspan="{{ $endIdx - $startIdx + 1 }}" class="border px-0 py-1 text-center align-middle">
                                    <div class="event-bar {{ $color }} text-black font-semibold flex items-center justify-center rounded shadow mx-auto relative"
                                         style="width: 100%; max-width: 100%;"
                                         tabindex="0"
                                         onclick="showEventModal({{ $event->id }})">
                                        <span class="event-title">{{ \Illuminate\Support\Str::limit($event->name, 18) }}</span>
                                    </div>
                                    <div id="event-modal-{{ $event->id }}" class="modal-bg" onclick="hideEventModal(event, {{ $event->id }})">
                                        <div class="modal-content" onclick="event.stopPropagation()">
                                            <span class="modal-close" onclick="hideEventModal(event, {{ $event->id }})">&times;</span>
                                            <h2 class="text-lg font-bold mb-2">{{ $event->name }}</h2>
                                            <div class="mb-1 text-sm">Tanggal: <b>{{ $event->start_date->format('d-m-Y') }}</b> - <b>{{ $event->end_date->format('d-m-Y') }}</b></div>
                                            @if(!empty($event->note))
                                            <div class="mb-1 text-sm">Catatan: {{ $event->note }}</div>
                                            @endif
                                            <div class="mb-1 text-sm">Model: {{ $event->learning_model ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @php $i = $endIdx; @endphp
                            @else
                                <td class="border px-0 py-1 month-col"></td>
                            @endif
                        @endfor
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
    </script>
</div>
@endsection
