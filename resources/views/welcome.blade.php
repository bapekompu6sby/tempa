@extends('layout.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <img src="{{ asset('images/logowil6.png') }}" alt="Logo Wilayah VI Surabaya" class="w-36 h-36 object-contain mb-4" loading="lazy">
	<h2 class="text-4xl font-bold mb-4">TEMPA</h2>
	<p class="text-xl text-gray-700 mb-2">Teman Kepanitiaan</p>
	<p class="text-sm text-gray-600 mb-8">Tempa atau Teman Panitia adalah aplikasi pendamping pelaksanaan pelatihan pada Bapekom PU VI Surabaya</p>
	<div class="flex gap-6">
		<a href="{{ route('instructions.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-700 transition">Instruksi</a>
		<a href="{{ route('events.index') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-green-700 transition">Pelatihan</a>
	</div>
</div>
@endsection
