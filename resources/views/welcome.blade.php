@extends('layout.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
	<div class="bg-white/80 backdrop-blur-sm p-6 rounded-xl shadow-xl text-center flex flex-col items-center mb-8">
		<img src="{{ asset('images/logowil6.png') }}" alt="Logo Wilayah VI Surabaya" class="w-36 h-36 object-contain mb-4" loading="lazy">
		<h2 class="text-5xl md:text-6xl font-extrabold mb-2" style="color:#203368; text-shadow: 0 2px 8px rgba(32,51,104,0.18);">TEMPA</h2>
		<p class="text-2xl md:text-3xl font-medium mb-0" style="color:#203368; opacity:0.85; text-shadow: 0 1px 6px rgba(32,51,104,0.12);">Teman Kepanitiaan</p>
	</div>
	@if(session('access_granted'))
		<div class="w-full max-w-3xl">
			<div class="flex gap-6 mb-6">
				<a href="{{ route('instructions.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-700 transition">Referensi Instruksi</a>
				<a href="{{ route('events.kalender') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-green-700 transition">Kalender Pelatihan</a>
			</div>

			{{-- Events list --}}
			<div class="bg-white p-4 rounded shadow">
				@php
					// Use the controller-provided monthly summary when available. Fallback to zeros.
					$monthName = \Carbon\Carbon::now()->format('Y');
					$counts = $summaryCounts ?? [
						'tentative' => 0,
						'belum_dimulai' => 0,
						'persiapan' => 0,
						'pelaksanaan' => 0,
						'pelaporan' => 0,
						'selesai' => 0,
					];
					$monthTotal = array_sum($counts);
				@endphp

				<div class="md:flex md:items-center md:justify-between md:gap-4 mb-2">
					<h3 class="text-lg font-semibold mb-0">Daftar Pelatihan</h3>
					<div class="mt-0 w-full md:flex-1">
						<div class="p-2 bg-gray-50 rounded">
							<div class="flex items-center justify-between">
								<div class="text-xs text-gray-700 font-semibold">{{ $monthName }}</div>
								<a href="{{ url('public/events') }}" class="text-xs px-3 py-1 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 transition ml-2">Lihat lebih detail</a>
							</div>
							<div class="mt-1 grid grid-cols-3 gap-1 text-xs text-gray-700">
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Tentative</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-semibold">{{ $counts['tentative'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Belum Dimulai</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-semibold">{{ $counts['belum_dimulai'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Persiapan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $counts['persiapan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Pelaksanaan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 font-semibold">{{ $counts['pelaksanaan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Pelaporan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-semibold">{{ $counts['pelaporan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Selesai</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-semibold">{{ $counts['selesai'] }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				@if(isset($events) && $events->count())
					<ul class="space-y-2">
						@foreach($events as $ev)
							<li class="border rounded px-4 py-4 bg-white">
								{{-- Top: event detail (match events.index) --}}
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
		<div class="w-full max-w-md bg-white rounded shadow px-4 py-2">
			@if($errors->any())
				<div class="mb-4 text-red-600">{{ $errors->first() }}</div>
			@endif

			<form method="POST" action="{{ url('/unlock') }}" class="mt-2">
				@csrf
				<div class="mb-0">
					<label class="block text-sm font-medium text-gray-700">Masukkan Password untuk panitia</label>
					<div class="flex">
						<input name="password" type="password" class="flex-1 border rounded-l px-2 py-2" required autofocus aria-label="Password">
						<button type="submit" class="ml-2 bg-blue-600 text-white px-3 py-2 rounded-r">Masuk Panitia</button>
					</div>
				</div>
			</form>
		</div>

		{{-- Always show read-only events list below the password form when not unlocked --}}
		<div class="w-full max-w-3xl mt-6">
			<div class="bg-white p-4 rounded shadow">
				@php
					$monthName = \Carbon\Carbon::now()->format('F Y');
					$counts = $summaryCounts ?? [
						'tentative' => 0,
						'belum_dimulai' => 0,
						'persiapan' => 0,
						'pelaksanaan' => 0,
						'pelaporan' => 0,
						'selesai' => 0,
					];
					$monthTotal = array_sum($counts);
				@endphp

				<div class="md:flex md:items-center md:justify-between md:gap-4 mb-2">
					<h3 class="text-lg font-semibold mb-0">Daftar Pelatihan</h3>
					<div class="mt-0 w-full md:flex-1">
						<div class="p-2 bg-gray-50 rounded">
								<div class="flex items-center justify-between">
									<div class="text-xs text-gray-700 font-semibold">{{ $monthName }}</div>
									<a href="{{ url('public/events') }}" class="text-xs px-3 py-1 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 transition ml-2">Lihat lebih detail</a>
								</div>
							<div class="mt-1 grid grid-cols-3 gap-1 text-xs text-gray-700">
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Tentative</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-semibold">{{ $counts['tentative'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Belum Dimulai</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-semibold">{{ $counts['belum_dimulai'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Persiapan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $counts['persiapan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Pelaksanaan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 font-semibold">{{ $counts['pelaksanaan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Pelaporan</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-semibold">{{ $counts['pelaporan'] }}</span>
								</div>
								<div class="flex items-center justify-between gap-2">
									<span class="text-[11px]">Selesai</span>
									<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-800 font-semibold">{{ $counts['selesai'] }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				@if(isset($events) && $events->count())
					<ul class="space-y-2">
						@foreach($events as $ev)
							<li class="border rounded px-4 py-4 bg-white">
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
