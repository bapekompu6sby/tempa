@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Header & Navigation --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('events.index') }}" class="hover:underline">Pelatihan</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('events.show', $event) }}" class="hover:underline">{{ Str::limit($event->name, 40) }}</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-700 font-semibold">Dokumen</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Dokumen Pelatihan: {{ $event->name }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('events.show', $event) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm font-medium transition">Kembali ke Pelatihan</a>
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded text-sm font-medium transition">Daftar Pelatihan</a>
            </div>
        </div>

        {{-- Flash Notifications --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded shadow-sm">
                <div>{!! session('success') !!}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm">
                <div>{!! session('error') !!}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Upload / Add Document Form --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Tambah Dokumen Baru
            </h3>
            <p class="text-xs text-gray-500 mb-4">Masukkan nama dokumen, lalu unggah file dan/atau lampirkan link dokumen.</p>

            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="md:col-span-4">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                        Nama Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Daftar Hadir Peserta, SK Panitia..."
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-3">
                    <label for="file" class="block text-sm font-semibold text-gray-700 mb-1">
                        File Dokumen <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                    </label>
                    <input type="file" name="file" id="file"
                           class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-3">
                    <label for="link" class="block text-sm font-semibold text-gray-700 mb-1">
                        Link Dokumen <span class="text-xs font-normal text-gray-400">(Opsional)</span>
                    </label>
                    <input type="text" name="link" id="link" value="{{ old('link') }}"
                           placeholder="https://drive.google.com/..."
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold transition shadow-sm cursor-pointer flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Documents List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Daftar Dokumen Pelatihan</h3>
                <span class="text-xs bg-gray-200 text-gray-700 font-semibold px-2.5 py-0.5 rounded-full">
                    Total: {{ $documents->count() }} Dokumen
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-14">No</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Dokumen</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">File / Lampiran</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-center text-gray-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-900">
                                    {{ $doc->name }}
                                    @if(!empty($doc->notes))
                                        <p class="text-xs font-normal text-gray-500 mt-0.5">{{ $doc->notes }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <div class="flex flex-col gap-1">
                                        @if(!empty($doc->file_path))
                                            <a href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-blue-600 hover:underline font-medium">
                                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                {{ basename($doc->file_path) }}
                                            </a>
                                        @endif

                                        @if(isset($doc->files) && $doc->files->count() > 0)
                                            @foreach($doc->files as $file)
                                                <a href="{{ route('documents.files.download', $file) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-blue-600 hover:underline text-xs">
                                                    <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ $file->original_name }}
                                                </a>
                                            @endforeach
                                        @endif

                                        @if(!empty($doc->link))
                                            @php
                                                $href = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/');
                                            @endphp
                                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline text-xs break-all">
                                                {{ $doc->link }}
                                            </a>
                                        @endif

                                        @if(empty($doc->file_path) && (!isset($doc->files) || $doc->files->isEmpty()) && empty($doc->link))
                                            <span class="text-gray-400 italic text-xs">Tidak ada file</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-center">
                                    <div class="inline-flex items-center justify-center space-x-2">
                                        @if(!empty($doc->file_path))
                                            <a href="{{ route('documents.download', $doc) }}" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded text-xs font-semibold transition">
                                                Download
                                            </a>
                                        @endif
                                        @if(!empty($doc->link))
                                            @php
                                                $hrefBtn = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/');
                                            @endphp
                                            <a href="{{ $hrefBtn }}" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 rounded text-xs font-semibold transition">
                                                Buka Link
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('Hapus dokumen \"{{ addslashes($doc->name) }}\"?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded text-xs font-semibold transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 px-4 text-center text-sm text-gray-500">
                                    Belum ada dokumen yang diunggah untuk pelatihan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
