@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Kalender Pelatihan</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="border px-4 py-2 bg-gray-100">Nama Event</th>
                    @foreach($allMonths as $month)
                        <th class="border px-4 py-2 bg-gray-100">{{ $month }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td class="border px-4 py-2 font-medium">
                            {{ $event->name }}<br>
                            <span class="text-xs text-gray-600">{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</span>
                        </td>
                        @foreach($allMonths as $month)
                            @php
                                $startMonth = $event->start_date->format('F Y');
                                $endMonth = $event->end_date->format('F Y');
                                $currentMonth = $month;
                                $isInRange = (strtotime('01 ' . $currentMonth) >= strtotime('01 ' . $startMonth)) && (strtotime('01 ' . $currentMonth) <= strtotime('01 ' . $endMonth));
                            @endphp
                            <td class="border px-4 py-2 text-center">
                                @if($isInRange)
                                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($allMonths) + 1 }}" class="border px-4 py-2 text-center text-gray-500">Tidak ada pelatihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
