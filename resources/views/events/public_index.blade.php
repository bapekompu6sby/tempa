@extends('layout.app')

@section('content')
<div class="space-y-6">
    <header class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Publik - Daftar Pelatihan</h2>
        <p class="text-sm text-gray-600">Informasi acara yang dapat dilihat umum</p>
    </header>

    {{-- Filters: year and month --}}
    <form method="GET" action="{{ url('public/events') }}" class="flex items-center gap-3">
        <div>
            <label class="text-sm text-gray-600">Tahun</label>
            <select name="year" class="ml-2 border rounded px-2 py-1 text-sm">
                <option value="" {{ empty($month) ? 'selected' : '' }}>Semua</option>
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
        {{-- status is handled via tabs below (keeps form compact) --}}
        <div class="flex items-end">
            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Filter</button>
            @if(request()->hasAny(['year','month']) && (request('year') || request('month')))
                <a href="{{ url('public/events') }}" class="ml-2 text-sm text-gray-600">Reset</a>
            @endif
        </div>
    </form>

    {{-- Status tabs for quick filtering (preserve current year/month) --}}
    @php
        $baseParams = [];
        if (isset($year) && $year !== '') $baseParams['year'] = $year;
        if (isset($month) && $month !== '') $baseParams['month'] = $month;
        $tabStatuses = [
            '' => 'Semua',
            'belum_dimulai' => 'Belum Dimulai',
            'persiapan' => 'Persiapan',
            'pelaksanaan' => 'Pelaksanaan',
            'pelaporan' => 'Pelaporan',
            'tentative' => 'Tentative',
            'dibatalkan' => 'Dibatalkan',
            'selesai' => 'Selesai',
        ];
    @endphp
    <nav class="flex flex-wrap gap-2 mt-4">
        @foreach($tabStatuses as $key => $label)
            @php
                $params = $baseParams;
                if ($key !== '') $params['status'] = $key;
                $url = url('public/events') . (count($params) ? ('?' . http_build_query($params)) : '');
                $active = (isset($status) && $status === $key) || (empty($status) && $key === '');
            @endphp
            <a href="{{ $url }}" class="px-3 py-1 rounded text-sm font-medium {{ $active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @php
        // Summary counts for the currently fetched events (used like welcome page)
        $counts = [
            'tentative' => $events->where('status','tentative')->count(),
            'belum_dimulai' => $events->where('status','belum_dimulai')->count(),
            'persiapan' => $events->where('status','persiapan')->count(),
            'pelaksanaan' => $events->where('status','pelaksanaan')->count(),
            'pelaporan' => $events->where('status','pelaporan')->count(),
            'selesai' => $events->where('status','selesai')->count(),
        ];
    @endphp

    <div class="p-2 bg-gray-50 rounded">
        <div class="text-sm text-gray-700 font-semibold">Ringkasan</div>
        <div class="mt-1 grid grid-cols-3 gap-1 text-sm text-gray-700">
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Tentative</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-semibold">{{ $counts['tentative'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Belum Dimulai</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-semibold">{{ $counts['belum_dimulai'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Persiapan</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $counts['persiapan'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Pelaksanaan</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 font-semibold">{{ $counts['pelaksanaan'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Pelaporan</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-semibold">{{ $counts['pelaporan'] }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm">Selesai</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-semibold">{{ $counts['selesai'] }}</span>
            </div>
        </div>
    </div>

    @forelse($events as $ev)
        @php
            $highlightClass = ($ev->status === 'pelaksanaan') ? 'ring-2 ring-blue-200 bg-blue-50' : '';
        @endphp
        <div class="border rounded px-4 py-4 bg-white {{ $highlightClass }}">
            {{-- Top: event detail (read-only, no actions) --}}
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
                                <span class="ml-3 text-sm text-gray-600">{{ optional($ev->start_date)->format('d M Y') }} - {{ optional($ev->end_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- intentionally no action buttons in public view --}}
                </div>
            </div>

            {{-- Bottom: three phase blocks (non-clickable) --}}
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
                        $total = method_exists($ev, 'instructionCountByPhase') ? $ev->instructionCountByPhase($key) : ($ev->{$key . '_total'} ?? 0);
                        $checked = method_exists($ev, 'checkedInstructionCountByPhase') ? $ev->checkedInstructionCountByPhase($key) : ($ev->{$key . '_checked'} ?? 0);
                        $pct = $total > 0 ? (int) round(($checked / $total) * 100) : 0;
                        $barColor = ($pct === 100 && $total > 0) ? 'bg-green-500' : 'bg-red-500';
                        $bgColor = ($pct === 100 && $total > 0) ? 'bg-green-100' : 'bg-red-100';

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

                    <div class="block {{ $bgColor }} p-3 rounded">
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
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-gray-600">Tidak ada acara publik saat ini.</div>
    @endforelse
</div>
@endsection
