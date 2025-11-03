@extends('layout.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
	<div class="bg-white/80 backdrop-blur-sm p-6 rounded-xl shadow-xl text-center flex flex-col items-center">
		<img src="{{ asset('images/logowil6.png') }}" alt="Logo Wilayah VI Surabaya" class="w-36 h-36 object-contain mb-4" loading="lazy">
		<h2 class="text-5xl md:text-6xl font-extrabold mb-2" style="color:#203368; text-shadow: 0 2px 8px rgba(32,51,104,0.18);">TEMPA</h2>
		<p class="text-2xl md:text-3xl font-medium mb-0" style="color:#203368; opacity:0.85; text-shadow: 0 1px 6px rgba(32,51,104,0.12);">Teman Kepanitiaan</p>
	</div>
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
							<li class="border rounded px-4 py-4 bg-white">
								{{-- Top: event detail (match events.index) --}}
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
										</div>
									</div>
								</div>

								{{-- Bottom: three phase blocks (match events.index) --}}
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
							</li>
						@endforeach
						</ul>
				@else
					<div class="text-gray-600">Belum ada pelatihan.</div>
				@endif
			</div>
		</div>
	@else
		<div class="w-full max-w-md bg-white rounded shadow">
			@if($errors->any())
				<div class="mb-4 text-red-600">{{ $errors->first() }}</div>
			@endif

			<form method="POST" action="{{ url('/unlock') }}">
				@csrf
				<div class="mb-0">
					<label class="block text-sm font-medium text-gray-700">Masukkan Password untuk panitia</label>
					<div class="flex">
						<input name="password" type="password" class="flex-1 border rounded-l px-3 py-2" required autofocus aria-label="Password">
						<button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-r">Masuk Panitia</button>
					</div>
				</div>
			</form>
		</div>

		{{-- Always show read-only events list below the password form when not unlocked --}}
		<div class="w-full max-w-3xl mt-6">
			<div class="bg-white p-4 rounded shadow">
				<h3 class="text-lg font-semibold mb-3">Daftar Pelatihan</h3>
				@if(isset($events) && $events->count())
					<ul class="space-y-2">
						@foreach($events as $ev)
							<li class="border rounded px-4 py-4 bg-white">
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

									</div>
								</div>

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
								</li>
							@endforeach
						</ul>
					@else
						<div class="text-gray-600">Belum ada pelatihan.</div>
					@endif
			</div>
		</div>
	@endif

</div>
@endsection
