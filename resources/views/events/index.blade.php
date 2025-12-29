@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Daftar Pelatihan</h2>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('events.create') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded">Tambah Pelatihan</a>
    </div>

    {{-- Filters: year and month --}}
    <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-3 mb-4">
        <div>
            <label class="text-sm text-gray-600">Tahun</label>
            <select name="year" class="ml-2 border rounded px-2 py-1 text-sm">
                <option value="" {{ empty($year) ? 'selected' : '' }}>Semua</option>
                @if(!empty($years))
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ (isset($year) && $year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">Bulan</label>
            <select name="month" class="ml-2 border rounded px-2 py-1 text-sm">
                <option value="">Semua</option>
                @php
                    $months = [1=> 'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                @endphp
                @foreach($months as $num => $label)
                    <option value="{{ $num }}" {{ (isset($month) && (int)$month === $num) ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Filter</button>
            @if(request()->hasAny(['year','month']) && (request('year') || request('month')))
                <a href="{{ route('events.index') }}" class="ml-2 text-sm text-gray-600">Reset</a>
            @endif
        </div>
    </form>

    <div class="space-y-4">
        @foreach($events as $ev)
            <div class="border rounded px-4 py-4 bg-white">
                {{-- Top: event detail --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-lg">{{ $ev->name }}</div>
                            @php
                                $status = $ev->status ?? '';
                                $statusLabels = [
                                    'tentative' => 'Tentative',
                                    'belum_dimulai' => 'Belum Dimulai',
                                    'persiapan' => 'Persiapan',
                                    'pelaksanaan' => 'Pelaksanaan',
                                    'pelaporan' => 'Pelaporan',
                                    'dibatalkan' => 'Dibatalkan',
                                    'selesai' => 'Selesai',
                                ];
                                $statusClasses = [
                                    'tentative' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold',
                                    'belum_dimulai' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold',
                                    'persiapan' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold',
                                    'pelaksanaan' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 text-xs font-semibold',
                                    'pelaporan' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-semibold',
                                    'dibatalkan' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-semibold',
                                    'selesai' => 'inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold',
                                ];
                                $stLabel = $statusLabels[$status] ?? $status;
                                $stClass = $statusClasses[$status] ?? 'inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold';
                            @endphp
                            <div class="mt-2">
                                <span class="{{ $stClass }}">{{ $stLabel }}</span>
                            </div>
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
