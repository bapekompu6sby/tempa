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
				<a href="{{ route('instructions.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-700 transition">Instruksi</a>
				<a href="{{ route('events.index') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-green-700 transition">Pelatihan</a>
			</div>

			{{-- Events list --}}
			<div class="bg-white p-4 rounded shadow">
				<h3 class="text-lg font-semibold mb-3">Daftar Pelatihan</h3>
				@if(isset($events) && $events->count())
					<ul class="space-y-2">
						@foreach($events as $ev)
							<li class="flex items-center justify-between border rounded px-3 py-2">
								<div>
									<div class="font-medium">{{ $ev->name }}</div>
									<div class="text-sm text-gray-600">{{ $ev->start_date }} @if($ev->end_date) - {{ $ev->end_date }}@endif</div>
								</div>
								<div class="flex items-center gap-2">
									<span class="text-sm text-gray-500 mr-2">{{ $ev->learning_model }}</span>
									<a href="{{ route('events.show', $ev) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Lihat</a>
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
