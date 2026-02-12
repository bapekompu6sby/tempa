@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Kalender Pelatihan</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    @foreach($allMonths as $month)
                        <th class="border px-4 py-2 bg-gray-100 text-center">{{ explode(' ', $month)[0] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        @php
                            $monthNames = ['Januari', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                            $startIdx = ($event->start_date->format('Y') == $year) ? $event->start_date->format('n') - 1 : 0;
                            $endIdx = ($event->end_date->format('Y') == $year) ? $event->end_date->format('n') - 1 : count($allMonths) - 1;
                            // Color coding for neatness
                            $colors = ['bg-green-400', 'bg-yellow-300', 'bg-blue-400', 'bg-pink-400', 'bg-purple-400', 'bg-red-400', 'bg-orange-400'];
                            $color = $colors[$loop->index % count($colors)];
                        @endphp
                        @for($i = 0; $i < count($allMonths); $i++)
                            @if($i == $startIdx)
                                <td colspan="{{ $endIdx - $startIdx + 1 }}" class="border px-0 py-2 text-center align-middle">
                                    <div class="h-full w-full flex flex-col items-center justify-center rounded shadow {{ $color }} text-black font-semibold text-base"
                                         style="min-height: 36px;">
                                        <span>{{ $event->name }}</span>
                                        <span class="block text-xs font-normal">{{ $event->start_date->format('d-m-Y') }} - {{ $event->end_date->format('d-m-Y') }}</span>
                                    </div>
                                </td>
                                @php $i = $endIdx; @endphp
                            @else
                                <td class="border px-0 py-2"></td>
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
