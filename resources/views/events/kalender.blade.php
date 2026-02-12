@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Kalender Pelatihan</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($months as $month => $events)
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-xl font-semibold mb-4">{{ $month }}</h2>
                @if(count($events) > 0)
                    <ul>
                        @foreach($events as $event)
                            <li class="mb-2">
                                <span class="font-medium">{{ $event->name }}</span><br>
                                <span class="text-sm text-gray-600">{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Tidak ada pelatihan.</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
