@extends('layout.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
	<img src="{{ asset('images/logowil6.png') }}" alt="Logo Wilayah VI Surabaya" class="w-36 h-36 object-contain mb-4" loading="lazy">
	<h2 class="text-4xl font-bold mb-4">TEMPA</h2>
	<p class="text-xl text-gray-700 mb-2">Teman Kepanitiaan</p>
	<p class="text-sm text-gray-600 mb-8">Tempa atau Teman Panitia adalah aplikasi pendamping pelaksanaan pelatihan pada Bapekom PU VI Surabaya</p>

	@if(session('access_granted'))
		<div class="w-full max-w-3xl">
			<div class="flex gap-6 mb-6">
				<a href="{{ route('instructions.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-700 transition">Referensi Instruksi</a>
			</div>

			{{-- Events list --}}
			<div class="bg-white p-4 rounded shadow">
				<h3 class="text-lg font-semibold mb-3">Daftar Pelatihan</h3>
				@if(isset($events) && $events->count())
					<ul class="space-y-2">
						@foreach($events as $ev)
							<li class="border rounded px-3 py-3">
								{{-- Top: event detail --}}
								<div class="mb-3">
									<div class="font-medium text-lg">{{ $ev->name }}</div>
									<div class="text-sm text-gray-600">{{ $ev->start_date }} @if($ev->end_date) - {{ $ev->end_date }}@endif</div>
									<div class="mt-2 flex items-center justify-between">
										<span class="text-sm text-gray-500">{{ $ev->learning_model }}</span>
										<a href="{{ route('events.show', $ev) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Lihat</a>
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
											// Prefer model helper methods (they query event_instructions) when available
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
							</li>
						@endforeach
					</ul>
				@else
					<div class="text-gray-600">Belum ada pelatihan.</div>
				@endif
			</div>
		</div>
	@else
		<div class="w-full max-w-md bg-white p-6 rounded shadow">
			@if($errors->any())
				<div class="mb-4 text-red-600">{{ $errors->first() }}</div>
			@endif

			<form method="POST" action="{{ url('/unlock') }}">
				@csrf
				<div class="mb-4">
					<label class="block text-sm font-medium text-gray-700">Masukkan Password untuk Melanjutkan</label>
					<input name="password" type="password" class="mt-1 block w-full border rounded px-3 py-2" required autofocus>
				</div>

				<div class="flex items-center justify-between">
					<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Buka Aplikasi</button>
				</div>
			</form>
		</div>
	@endif

</div>
@endsection
