@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Kalender Pelatihan</h1>
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
        }
        .event-tooltip {
            display: none;
            position: absolute;
            z-index: 10;
            left: 50%;
            top: 100%;
            transform: translateX(-50%);
            background: #fff;
            color: #222;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 8px 12px;
            white-space: pre-line;
            min-width: 180px;
            font-size: 12px;
        }
        .event-bar:hover .event-tooltip { display: block; }
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
                            $colors = ['bg-green-400', 'bg-yellow-300', 'bg-blue-400', 'bg-pink-400', 'bg-purple-400', 'bg-red-400', 'bg-orange-400', 'bg-cyan-400', 'bg-lime-400', 'bg-fuchsia-400', 'bg-amber-400', 'bg-emerald-400'];
                            $color = $colors[$loop->index % count($colors)];
                        @endphp
                        @for($i = 0; $i < count($allMonths); $i++)
                            @if($i == $startIdx)
                                <td colspan="{{ $endIdx - $startIdx + 1 }}" class="border px-0 py-1 text-center align-middle">
                                    <div class="event-bar {{ $color }} text-black font-semibold flex items-center justify-center rounded shadow mx-auto relative"
                                         style="width: 100%; max-width: 100%;">
                                        <span class="event-title">{{ \Illuminate\Support\Str::limit($event->name, 18) }}</span>
                                        <div class="event-tooltip">
                                            <strong>{{ $event->name }}</strong><br>
                                            Tanggal: {{ $event->start_date->format('d-m-Y') }} - {{ $event->end_date->format('d-m-Y') }}<br>
                                            @if(!empty($event->note))
                                            Catatan: {{ $event->note }}<br>
                                            @endif
                                            Model: {{ $event->learning_model ?? '-' }}
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
</div>
@endsection
