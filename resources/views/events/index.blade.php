@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Pelatihan</h2>
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('events.create') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded">Tambah Pelatihan</a>
    </div>

    <div class="space-y-4">
        @foreach($events as $ev)
            <div class="border rounded px-4 py-4 bg-white">
                {{-- Top: event detail --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-lg">{{ $ev->name }}</div>
                            <div class="mt-2">
                                @php
                                    $lm = $ev->learning_model ?? null;
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

                                <div class="flex items-center gap-3">
                                    @if($lmLabel)
                                        <span class="{{ $lmClass }}">{{ $lmLabel }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('events.show', $ev) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Lihat</a>
                            <a href="{{ route('events.edit', $ev) }}" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm">Edit</a>
                            <form action="{{ route('events.destroy', $ev) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pelatihan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Bottom: three phase blocks --}}
                @php
                    $phases = [
                        'persiapan' => 'Persiapan',
                        'pelaksanaan' => 'Pelaksanaan',
                        'pelaporan' => 'Pelaporan',
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($phases as $key => $label)
                        @php
                            // Use Event model helper methods to get accurate counts
                            $total = method_exists($ev, 'instructionCountByPhase') ? $ev->instructionCountByPhase($key) : ($ev->{$key . '_total'} ?? 0);
                            $checked = method_exists($ev, 'checkedInstructionCountByPhase') ? $ev->checkedInstructionCountByPhase($key) : ($ev->{$key . '_checked'} ?? 0);
                            $pct = $total > 0 ? (int) round(($checked / $total) * 100) : 0;
                            $barColor = ($pct === 100 && $total > 0) ? 'bg-green-500' : 'bg-red-500';
                            // lighter background matching the bar color (lower opacity appearance)
                            $bgColor = ($pct === 100 && $total > 0) ? 'bg-green-100' : 'bg-red-100';

                            // compute phase-specific date ranges
                            $phaseStart = null;
                            $phaseEnd = null;
                            if ($key === 'persiapan') {
                                $phaseStart = $ev->preparation_date ?? null;
                                $phaseEnd = $ev->start_date ?? null;
                            } elseif ($key === 'pelaksanaan') {
                                $phaseStart = $ev->start_date ?? null;
                                $phaseEnd = $ev->end_date ?? null;
                            } elseif ($key === 'pelaporan') {
                                $phaseStart = $ev->end_date ?? null;
                                $phaseEnd = $ev->report_date ?? null;
                            }
                            $phaseStartLabel = $phaseStart ? ($phaseStart instanceof \Carbon\Carbon ? $phaseStart->format('d M Y') : \Carbon\Carbon::parse($phaseStart)->format('d M Y')) : '-';
                            $phaseEndLabel = $phaseEnd ? ($phaseEnd instanceof \Carbon\Carbon ? $phaseEnd->format('d M Y') : \Carbon\Carbon::parse($phaseEnd)->format('d M Y')) : '-';
                        @endphp
                        <a href="{{ route('events.show', ['event' => $ev, 'phase' => $key]) }}" class="block {{ $bgColor }} p-3 rounded hover:shadow">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <div>
                                    <div class="font-medium">{{ $label }}</div>
                                    <div class="text-xs text-gray-500">{{ $phaseStartLabel }} &ndash; {{ $phaseEndLabel }}</div>
                                </div>
                                <div class="text-sm text-gray-600">{{ $checked }}/{{ $total }}</div>
                            </div>
                            <div class="w-full bg-gray-200 h-4 rounded overflow-hidden">
                                <div class="h-4 {{ $barColor }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
