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
                        @foreach($allMonths as $idx => $month)
                            @php
                                // $month is like 'Januari 2026', convert to month number and year
                                [$monthName, $monthYear] = explode(' ', $month);
                                $monthNum = array_search($monthName, [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                ]) + 1;
                                $cellDate = \Carbon\Carbon::createFromDate($monthYear, $monthNum, 1);
                                $startMonth = $event->start_date->copy()->startOfMonth();
                                $endMonth = $event->end_date->copy()->startOfMonth();
                                $isInRange = $cellDate->between($startMonth, $endMonth);
                            @endphp
                            <td class="border px-4 py-2 text-center align-middle">
                                @if($isInRange)
                                    <div class="bg-blue-500 text-white rounded p-2 text-xs font-semibold">
                                        {{ $event->name }}<br>
                                        <span class="block text-[10px] text-white opacity-80">{{ $event->start_date->format('d M') }} - {{ $event->end_date->format('d M') }}</span>
                                    </div>
                                @endif
                            </td>
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
</div>
@endsection
