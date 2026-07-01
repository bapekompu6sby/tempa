@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Peserta Pelatihan: {{ $event->name }}</h2>
        <a href="{{ route('events.show', $event) }}" class="px-4 py-2 bg-gray-300 rounded text-sm">Kembali</a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{!! session('success') !!}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{!! session('error') !!}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow mb-6 border-t-4 border-blue-500">
        <h3 class="text-lg font-bold mb-4">Import ASN ke Pelatihan</h3>
        <form method="POST" action="{{ route('events.importAsn', $event) }}" enctype="multipart/form-data" class="flex items-end space-x-4">
            @csrf
            <div class="flex-grow">
                <label class="block text-sm font-medium text-gray-700 mb-1">File Excel/CSV</label>
                <input type="file" name="file" required class="w-full border p-2 rounded">
                <p class="text-xs text-gray-500 mt-1">Gunakan template yang sama dengan Import ASN di menu ASN.</p>
            </div>
            <div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">Import Data</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        NIP
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jabatan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Instansi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participants as $asn)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $asn->nip ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap font-semibold">{{ $asn->name }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $asn->job_title ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ strtoupper($asn->asn_source ?? '-') }}</p>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        Belum ada peserta pelatihan ini. Silakan import data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $participants->links() }}
        </div>
    </div>
</div>
@endsection
