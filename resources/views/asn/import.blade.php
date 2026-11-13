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

    <div class="bg-white shadow rounded-lg border border-gray-200">
        <form action="{{ route('asn.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Section 1: Upload XLSX / CSV --}}
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">1. Upload File Excel / CSV</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Unggah file <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>. File akan dibaca murni datanya saja (tanpa styling, diperlakukan seperti CSV).
                </p>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="file">
                        Pilih File (.xlsx, .xls, .csv) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-2">
                        Kolom yang didukung: <code>nip</code>, <code>name</code> (wajib), <code>job_title</code>, <code>phone_number</code>, <code>email</code>, <code>birth_city</code>, <code>birth_date</code>, <code>gender</code> (L/P), <code>rank_grade</code>, <code>latest_education</code>, <code>office_address</code>, <code>asn_type</code> (pns/cpns/pppk/lainnya), <code>asn_source</code> (pusat/daerah/lainnya), serta opsional <code>event_id</code>.
                    </p>
                </div>
            </div>

            {{-- Section 2: Optional Event Choice (Search-Select) for participant_event Relationship --}}
            @php
                $initialEventId = old('event_id', $selectedEventId ?? '');
                $initialEventLabel = '';
                foreach ($events ?? [] as $ev) {
                    if ((string)$ev->id === (string)$initialEventId) {
                        $initialEventLabel = '[ID: ' . $ev->id . '] ' . $ev->name . ($ev->start_date ? ' (' . $ev->start_date->format('d M Y') . ')' : '');
                        break;
                    }
                }
            @endphp
            <div class="p-6 bg-gray-50/60 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">2. Hubungkan ke Pelatihan (Opsional)</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Cari dan pilih pelatihan jika data ASN yang diimport ingin langsung didaftarkan sebagai peserta pada pelatihan tersebut (relasi <code>participant_event</code>).
                </p>

                <div class="relative" id="event-search-select-container">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="event_search_input">
                        Cari &amp; Pilih Pelatihan (Opsional)
                    </label>

                    <input type="hidden" name="event_id" id="event_id" value="{{ $initialEventId }}">

                    <div class="relative">
                        <input type="text"
                               id="event_search_input"
                               value="{{ $initialEventLabel }}"
                               placeholder="Ketik nama atau ID pelatihan untuk mencari... (Kosongkan jika hanya import ASN)"
                               autocomplete="off"
                               class="w-full border border-gray-300 rounded px-3 py-2.5 pr-16 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                        {{-- Clear Button --}}
                        <button type="button"
                                id="event_clear_btn"
                                title="Hapus pilihan pelatihan"
                                class="{{ $initialEventId ? '' : 'hidden' }} absolute inset-y-0 right-8 flex items-center px-2 text-gray-400 hover:text-red-500 focus:outline-none cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Dropdown Chevron --}}
                        <button type="button"
                                id="event_toggle_btn"
                                tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown Options --}}
                    <div id="event_dropdown_list"
                         class="hidden absolute z-30 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-64 overflow-y-auto divide-y divide-gray-100">
                        <div class="event-option px-3 py-2.5 text-sm text-gray-600 hover:bg-blue-50 cursor-pointer italic"
                             data-id=""
                             data-label="">
                            -- Tidak dihubungkan ke pelatihan (Hanya Import Data ASN) --
                        </div>
                        @foreach($events ?? [] as $ev)
                            @php
                                $evLabel = '[ID: ' . $ev->id . '] ' . $ev->name . ($ev->start_date ? ' (' . $ev->start_date->format('d M Y') . ')' : '');
                            @endphp
                            <div class="event-option px-3 py-2.5 text-sm text-gray-800 hover:bg-blue-50 cursor-pointer flex items-center justify-between {{ (string)$initialEventId === (string)$ev->id ? 'bg-blue-50 font-semibold text-blue-700' : '' }}"
                                 data-id="{{ $ev->id }}"
                                 data-label="{{ $evLabel }}">
                                <div>
                                    <span class="text-xs font-mono bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded mr-1.5">ID: {{ $ev->id }}</span>
                                    <span>{{ $ev->name }}</span>
                                </div>
                                @if($ev->start_date)
                                    <span class="text-xs text-gray-500 ml-2 whitespace-nowrap">{{ $ev->start_date->format('d M Y') }}</span>
                                @endif
                            </div>
                        @endforeach
                        <div id="event_no_results" class="hidden px-3 py-3 text-sm text-gray-500 text-center">
                            Pelatihan tidak ditemukan.
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Apabila dikosongkan, sistem tetap akan menghubungkan peserta ke pelatihan jika di dalam baris file terdapat kolom <code>event_id</code> yang valid.
                    </p>
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="px-6 py-5 bg-white rounded-b-lg flex items-center justify-end space-x-3">
                <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-base rounded-md shadow-md hover:shadow-lg transition cursor-pointer">
                    <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import Data ASN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('event-search-select-container');
    const hiddenInput = document.getElementById('event_id');
    const searchInput = document.getElementById('event_search_input');
    const clearBtn = document.getElementById('event_clear_btn');
    const toggleBtn = document.getElementById('event_toggle_btn');
    const dropdown = document.getElementById('event_dropdown_list');
    const options = Array.from(dropdown.querySelectorAll('.event-option'));
    const noResults = document.getElementById('event_no_results');

    let selectedId = hiddenInput.value || '';
    let selectedLabel = searchInput.value || '';

    function openDropdown() {
        dropdown.classList.remove('hidden');
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        // Restore label to currently selected item if user didn't pick a new one
        searchInput.value = selectedLabel;
        filterOptions('');
    }

    function updateClearButton() {
        if (selectedId || searchInput.value.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    function filterOptions(query) {
        const q = query.trim().toLowerCase();
        let visibleCount = 0;

        options.forEach(function (opt) {
            const label = (opt.getAttribute('data-label') || '').toLowerCase();
            const id = (opt.getAttribute('data-id') || '').toLowerCase();

            // Always show the "none" option when query is empty
            if (id === '') {
                opt.style.display = q === '' ? '' : 'none';
                return;
            }

            if (q === '' || label.includes(q) || id.includes(q)) {
                opt.style.display = '';
                visibleCount++;
            } else {
                opt.style.display = 'none';
            }
        });

        if (visibleCount === 0 && q !== '') {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    function selectOption(id, label) {
        selectedId = id;
        selectedLabel = label;
        hiddenInput.value = id;
        searchInput.value = label;

        options.forEach(function (opt) {
            if (opt.getAttribute('data-id') === id && id !== '') {
                opt.classList.add('bg-blue-50', 'font-semibold', 'text-blue-700');
            } else {
                opt.classList.remove('bg-blue-50', 'font-semibold', 'text-blue-700');
            }
        });

        updateClearButton();
        closeDropdown();
    }

    searchInput.addEventListener('focus', function () {
        openDropdown();
        filterOptions('');
        searchInput.select();
    });

    searchInput.addEventListener('input', function () {
        openDropdown();
        filterOptions(searchInput.value);
        updateClearButton();
    });

    toggleBtn.addEventListener('click', function () {
        if (dropdown.classList.contains('hidden')) {
            openDropdown();
            filterOptions('');
            searchInput.focus();
        } else {
            closeDropdown();
        }
    });

    clearBtn.addEventListener('click', function () {
        selectOption('', '');
        searchInput.focus();
    });

    options.forEach(function (opt) {
        opt.addEventListener('click', function () {
            const id = opt.getAttribute('data-id') || '';
            const label = opt.getAttribute('data-label') || '';
            selectOption(id, label);
        });
    });

    document.addEventListener('click', function (e) {
        if (!container.contains(e.target)) {
            closeDropdown();
        }
    });
});
</script>
@endpush
