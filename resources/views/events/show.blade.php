@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Detail Pelatihan</h2>
    <div class="bg-white p-6 rounded shadow max-w-5xl mx-auto relative border-t-4 border-gray-200">
        {{-- Top-right action buttons --}}
        <div class="absolute top-4 right-4 flex space-x-2">
            <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 bg-yellow-500 text-white rounded text-sm">Edit</a>
            <a href="{{ route('events.documents', $event) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm">Lihat Kelengkapan Dokumen</a>
            <a href="{{ route('events.index') }}" class="px-3 py-1.5 bg-gray-300 rounded text-sm">Kembali</a>
        </div>
        <div class="mb-4">
            <strong>Nama:</strong> {{ $event->name }}
        </div>
        <div class="mb-4">
            <strong>Model Pembelajaran:</strong> {{ $event->learning_model }}
        </div>
        <div class="mb-4">
            <strong>Tanggal Mulai:</strong> {{ $event->start_date }}
        </div>
        <div class="mb-4">
            <strong>Tanggal Selesai:</strong> {{ $event->end_date }}
        </div>
        @if(!empty($event->note))
        <div class="mb-4">
            <strong>Catatan:</strong>
            <div class="mt-2 whitespace-pre-wrap bg-gray-50 p-3 rounded">{{ $event->note }}</div>
        </div>
        @endif
        {{-- Event Instructions list (tabs + search like instructions index) --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2">Instruksi untuk Pelatihan</h3>

            {{-- Search + Tabs --}}
            <div class="mb-4 flex items-center justify-between">
                <form method="GET" action="{{ route('events.show', $event) }}" class="flex items-center space-x-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama atau detail..." class="border rounded px-3 py-2 w-64">
                    <button type="submit" class="px-3 py-2 bg-gray-200 rounded">Cari</button>
                    @if(!empty($q))
                        <a href="{{ route('events.show', ['event' => $event, 'tab' => $tab]) }}" class="ml-2 text-sm text-gray-600">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tabs --}}
            <div class="mb-4 border-b">
                    @php
                    $tabs = [
                        'semua' => 'Semua',
                        'pic' => 'PIC',
                        'host' => 'Host',
                        'petugas_kelas' => 'Petugas Kelas',
                    ];
                @endphp
                <nav class="flex space-x-2">
                    @foreach($tabs as $key => $label)
                        @php $active = ($tab === $key); @endphp
                        <a href="{{ route('events.show', ['event' => $event, 'tab' => $key, 'q' => $q ?? null]) }}" class="px-4 py-2 -mb-px border-b-2 font-medium {{ $active ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-800' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            @php
                $list = $all ?? collect();
                if($tab === 'pic') { $list = $pic ?? collect(); }
                elseif($tab === 'host') { $list = $host ?? collect(); }
                elseif($tab === 'petugas_kelas') { $list = $pengamat ?? collect(); }

                // phase -> color mapping
                $phaseClasses = [
                    'persiapan' => ['border' => 'border-yellow-400', 'bg' => 'bg-yellow-50', 'text' => 'text-yellow-800'],
                    'pelaksanaan' => ['border' => 'border-green-400', 'bg' => 'bg-green-50', 'text' => 'text-green-800'],
                    'pembukaan_pelatihan' => ['border' => 'border-blue-400', 'bg' => 'bg-blue-50', 'text' => 'text-blue-800'],
                    'penutupan_pelatihan' => ['border' => 'border-purple-400', 'bg' => 'bg-purple-50', 'text' => 'text-purple-800'],
                    'evaluasi_pelatihan' => ['border' => 'border-red-400', 'bg' => 'bg-red-50', 'text' => 'text-red-800'],
                    'pra_pelatihan' => ['border' => 'border-gray-400', 'bg' => 'bg-gray-50', 'text' => 'text-gray-800'],
                ];
            @endphp

            <div class="bg-white shadow rounded overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700"> </th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $ei)
                            @php $instr = $ei->instruction; @endphp
                            @php
                                $phase = $ei->phase ?? optional($instr)->phase ?? 'pelaksanaan';
                                $c = $phaseClasses[$phase] ?? $phaseClasses['pelaksanaan'];
                            @endphp
                            <tr id="ei-row-{{ $ei->id }}" class="border-t align-top border-l-4 {{ $c['border'] }} {{ $ei->checked ? 'bg-green-50' : '' }}">
                                    <td class="py-2 px-4 w-20">
                                        <input type="checkbox" data-ei-id="{{ $ei->id }}" {{ $ei->checked ? 'checked' : '' }} class="ei-checkbox w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500" />
                                    </td>
                                    <td class="py-2 px-4">
                                        <div class="flex items-center">
                                            <span>{{ $instr->name ?? 'Instruksi #' . $ei->instruction_id }}</span>
                                            <span class="ml-3 px-2 py-0.5 text-xs font-medium rounded {{ $c['bg'] }} {{ $c['text'] }}">{{ ucwords(str_replace('_', ' ', $phase)) }}</span>
                                        </div>
                                    </td>
                                    
                                <td class="py-2 px-4 text-right w-20 whitespace-nowrap">
                                    <button type="button" class="view-detail-ei" data-id="{{ $ei->id }}" aria-expanded="false" title="Lihat detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 inline view-icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
                                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-ei-{{ $ei->id }}" class="detail-row hidden bg-gray-50">
                                <td class="py-3 px-4" colspan="3">
                                    <div class="mb-2">{!! nl2br(e(optional($instr)->detail)) !!}</div>

                                    @if(optional($instr)->linkable)
                                        <div id="ei-link-display-{{ $ei->id }}" class="ei-link-display">
                                            @if(!empty($ei->link))
                                                @php
                                                    // ensure rendered href has a scheme so browser treats it as absolute
                                                    $raw = $ei->link;
                                                    $href = (strpos($raw, '://') !== false) ? $raw : 'https://' . ltrim($raw, '/');
                                                @endphp
                                                <a id="ei-link-anchor-{{ $ei->id }}" href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($raw, 20) }}</a>
                                                <button type="button" data-ei-id="{{ $ei->id }}" class="ei-link-edit ml-3 px-2 py-1 bg-yellow-500 text-white rounded text-sm">Edit</button>
                                            @else
                                                <span class="text-gray-600">Belum ada link.</span>
                                                <button type="button" data-ei-id="{{ $ei->id }}" class="ei-link-edit ml-3 px-2 py-1 bg-blue-500 text-white rounded text-sm">Tambah</button>
                                            @endif
                                        </div>

                                        <div id="ei-link-form-{{ $ei->id }}" class="ei-link-form mt-2 {{ empty($ei->link) ? '' : 'hidden' }}">
                                            <div class="flex items-center space-x-2">
                                                <input name="link" type="text" value="{{ $ei->link }}" placeholder="Masukkan link..." class="border rounded px-3 py-2 w-full ei-link-input" />
                                                <button type="button" data-ei-id="{{ $ei->id }}" class="ei-link-save px-3 py-2 bg-green-600 text-white rounded text-sm">Simpan</button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-4 text-center text-gray-500" colspan="4">Tidak ada instruksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('tr').forEach(function(row){
                        // attach click handler to rows to toggle detail if needed
                    });
                });
            </script>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const token = '{{ csrf_token() }}';

        function normalizeLink(raw) {
            if (!raw) return raw;
            // if already has a scheme (http:, https:, mailto:, etc.), return as-is
            if (/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(raw)) return raw;
            // otherwise assume https
            return 'https://' + raw.replace(/^\/+/, '');
        }

        function truncate(str, max) {
            max = max || 20;
            if (!str) return '';
            return str.length > max ? str.slice(0, max - 1) + '…' : str;
        }

        async function toggleChecked(id, checkbox) {
            // disable while updating
            checkbox.disabled = true;
            try {
                const res = await fetch(`/event-instructions/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();

                // Update the row styling to indicate success when checked
                const row = document.getElementById('ei-row-' + id);
                if (row) {
                    if (data.checked) {
                        row.classList.add('bg-green-50');
                    } else {
                        row.classList.remove('bg-green-50');
                    }
                }
            } catch (err) {
                console.error(err);
                // revert checkbox on error
                checkbox.checked = !checkbox.checked;
                alert('Gagal memperbarui status.');
            } finally {
                checkbox.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // wire checkbox toggle
            document.querySelectorAll('.ei-checkbox').forEach(function (el) {
                el.addEventListener('change', function (e) {
                    const id = el.getAttribute('data-ei-id');
                    toggleChecked(id, el);
                });
            });

            // wire show/hide detail buttons
            document.querySelectorAll('.view-detail-ei').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var id = this.getAttribute('data-id');
                    var row = document.getElementById('detail-ei-' + id);
                    var expanded = this.getAttribute('aria-expanded') === 'true';
                    if (row) {
                        var svg = this.querySelector('.view-icon');
                        if (expanded) {
                            row.classList.add('hidden');
                            this.setAttribute('aria-expanded', 'false');
                            // set svg to open-eye (initial)
                            if (svg) {
                                svg.innerHTML = '<path d="M10 3C5 3 1.73 6.11 0 10c1.73 3.89 5 7 10 7s8.27-3.11 10-7c-1.73-3.89-5-7-10-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>'+
                                                '<path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>';
                            }
                            this.setAttribute('title', 'Lihat detail');
                        } else {
                            row.classList.remove('hidden');
                            this.setAttribute('aria-expanded', 'true');
                            // set svg to a close / eye-off-like icon
                            if (svg) {
                                svg.innerHTML = '<path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>';
                            }
                            this.setAttribute('title', 'Sembunyikan detail');
                        }
                    }
                });
            });

                // wire link edit/save buttons (for linkable instructions)
                document.querySelectorAll('.ei-link-edit').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var id = this.getAttribute('data-ei-id');
                        var display = document.getElementById('ei-link-display-' + id);
                        var form = document.getElementById('ei-link-form-' + id);
                        if (display) display.classList.add('hidden');
                        if (form) form.classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.ei-link-save').forEach(function(btn){
                    btn.addEventListener('click', async function(){
                        var id = this.getAttribute('data-ei-id');
                        var form = document.getElementById('ei-link-form-' + id);
                        var input = form ? form.querySelector('input[name="link"]') : null;
                        if (!input) return;
                        var val = input.value.trim();
                        this.disabled = true;
                        try {
                            const res = await fetch(`/event-instructions/${id}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ link: val })
                            });
                            if (!res.ok) throw new Error('Network error');
                            const data = await res.json();

                            // update display
                            var anchor = document.getElementById('ei-link-anchor-' + id);
                            var display = document.getElementById('ei-link-display-' + id);
                            if (anchor) {
                                anchor.href = normalizeLink(data.eventInstruction.link || val);
                                anchor.textContent = truncate(data.eventInstruction.link || val, 20);
                            } else if (display) {
                                // create anchor if not present
                                var a = document.createElement('a');
                                a.id = 'ei-link-anchor-' + id;
                                a.href = normalizeLink(data.eventInstruction.link || val);
                                a.target = '_blank';
                                a.rel = 'noopener noreferrer';
                                a.className = 'text-blue-600 underline';
                                a.textContent = truncate(data.eventInstruction.link || val, 20);
                                display.insertBefore(a, display.firstChild);
                            }

                            // hide form and show display
                            if (form) form.classList.add('hidden');
                            if (display) {
                                display.classList.remove('hidden');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Gagal menyimpan link.');
                        } finally {
                            this.disabled = false;
                        }
                    });
                });
        });
    })();
</script>
@endpush
