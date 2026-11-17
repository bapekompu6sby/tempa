@extends('layout.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('asn.index') }}" class="hover:underline">ASN</a>
                <span>&rsaquo;</span>
                <span class="text-gray-700 font-semibold">Import Data ASN</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Import Data ASN</h1>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('asn.import.template') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Template
            </a>
            <a href="{{ route('asn.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm font-medium transition">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded shadow-sm" role="alert">
            <span class="block sm:inline">{!! session('success') !!}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm" role="alert">
            <span class="block sm:inline">{!! session('error') !!}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm" role="alert">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('asn._import_form', [
        'formAction' => route('asn.import.process'),
        'events' => $events ?? [],
        'selectedEventId' => $selectedEventId ?? '',
        'showTemplateButton' => false,
    ])
</div>
@endsection
