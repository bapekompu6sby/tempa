@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Kelengkapan Dokumen: {{ $event->name }}</h2>

    <div class="bg-white p-6 rounded shadow max-w-5xl mx-auto">
        <div class="mb-4 flex justify-between items-center">
            <div></div>
            <div class="flex space-x-2">
                <a href="{{ route('events.show', $event) }}" class="px-3 py-1.5 bg-gray-300 rounded text-sm">Kembali ke Pelatihan</a>
                <a href="{{ route('events.index') }}" class="px-3 py-1.5 bg-gray-200 rounded text-sm">Daftar Pelatihan</a>
            </div>
        </div>

        <div class="overflow-hidden shadow rounded bg-white">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama Dokumen</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Lampiran / Link</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Catatan</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        @php $hasAttachment = !empty($doc->link) || !empty($doc->file_path); @endphp
                        <tr class="border-t align-top {{ $hasAttachment ? 'bg-green-50' : '' }}" id="ed-row-{{ $doc->id }}">
                            <td class="py-2 px-4 align-top">{{ $doc->name }}</td>
                            <td class="py-2 px-4 align-top">
                                <div class="flex flex-col gap-1">
                                    @if(!empty($doc->file_path))
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->file_path, 30) }}</a>
                                    @endif
                                    @if(!empty($doc->link))
                                        @php
                                            $href = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/');
                                        @endphp
                                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->link, 30) }}</a>
                                    @endif
                                    @if(!$hasAttachment)
                                        <span class="text-gray-600">Belum ada dokumen</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4 align-top" id="ed-notes-{{ $doc->id }}">{{ $doc->notes ?? '-' }}</td>
                            <td class="py-2 px-4 align-top">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="ed-edit px-2 py-1 bg-yellow-500 text-white rounded text-sm" data-id="{{ $doc->id }}">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr id="ed-edit-row-{{ $doc->id }}" class="hidden bg-gray-50">
                            <td class="py-3 px-4" colspan="4">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Link Dokumen</label>
                                        <input type="text" name="link" value="{{ $doc->link }}" class="ed-link-input mt-1 block w-full border rounded px-3 py-2" placeholder="https://..." />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload file</label>
                                        <input type="file" name="file" class="ed-file-input mt-1 block w-full" />
                                        <div class="mt-2">
                                            <button type="button" class="ed-upload px-3 py-2 bg-green-600 text-white rounded text-sm" data-id="{{ $doc->id }}">Upload</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                        <textarea name="notes" class="ed-notes-input mt-1 block w-full border rounded px-3 py-2" rows="3">{{ $doc->notes }}</textarea>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" class="ed-save px-3 py-2 bg-blue-600 text-white rounded text-sm" data-id="{{ $doc->id }}">Simpan</button>
                                        <button type="button" class="ed-cancel px-3 py-2 bg-gray-300 rounded text-sm" data-id="{{ $doc->id }}">Batal</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-4 text-center text-gray-500" colspan="4">Tidak ada dokumen untuk pelatihan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const token = '{{ csrf_token() }}';

        function normalizeLink(raw) {
            if (!raw) return raw;
            if (/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(raw)) return raw;
            return 'https://' + raw.replace(/^\/+/, '');
        }

        document.addEventListener('DOMContentLoaded', function(){
            // toggle edit row
            document.querySelectorAll('.ed-edit').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');
                    const editRow = document.getElementById('ed-edit-row-' + id);
                    if (editRow) editRow.classList.toggle('hidden');
                });
            });

            // cancel
            document.querySelectorAll('.ed-cancel').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');
                    const editRow = document.getElementById('ed-edit-row-' + id);
                    if (editRow) editRow.classList.add('hidden');
                });
            });

            // save link & notes via PATCH
            document.querySelectorAll('.ed-save').forEach(function(btn){
                    btn.addEventListener('click', async function(){
                    const id = this.getAttribute('data-id');
                    const linkInput = document.querySelector('#ed-edit-row-' + id + ' .ed-link-input');
                    const notesInput = document.querySelector('#ed-edit-row-' + id + ' .ed-notes-input');
                    const saveBtn = this;
                    saveBtn.disabled = true;
                    try {
                        const payload = { link: linkInput ? linkInput.value.trim() : '', notes: notesInput ? notesInput.value.trim() : '' };
                        const res = await fetch(`/documents/${id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                        if (!res.ok) throw new Error('Network error');
                        const data = await res.json();

                        // update DOM to reflect saved values immediately
                        const saved = data.eventDocument || {};

                        // update attachment cell: link and file_path
                        const row = document.getElementById('ed-row-' + id);
                        const attachCell = row ? row.querySelector('td:nth-child(2) .flex') : null;
                        const hasAttachment = (saved.link && saved.link.length > 0) || (saved.file_path && saved.file_path.length > 0);
                        if (row) {
                            if (hasAttachment) row.classList.add('bg-green-50'); else row.classList.remove('bg-green-50');
                        }

                            if (attachCell) {
                            // clear current content and rebuild
                                attachCell.innerHTML = '';
                                // prefer an authoritative URL returned by server (saved.url)
                                if (saved.file_path) {
                                    const fileHref = saved.url || ('/storage/' + saved.file_path);
                                    const a = document.createElement('a');
                                    a.href = fileHref;
                                    a.target = '_blank';
                                    a.rel = 'noopener noreferrer';
                                    a.className = 'text-blue-600 underline';
                                    a.textContent = saved.file_path.length > 30 ? saved.file_path.slice(0,27) + '…' : saved.file_path;
                                    attachCell.appendChild(a);
                                }
                                if (saved.link) {
                                const br = document.createElement('div');
                                br.className = '';
                                attachCell.appendChild(br);
                                const href = (/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(saved.link)) ? saved.link : ('https://' + saved.link.replace(/^\/+/, ''));
                                const a2 = document.createElement('a');
                                a2.href = href;
                                a2.target = '_blank';
                                a2.rel = 'noopener noreferrer';
                                a2.className = 'text-blue-600 underline';
                                a2.textContent = saved.link.length > 30 ? saved.link.slice(0,27) + '…' : saved.link;
                                attachCell.appendChild(a2);
                            }
                            if (!hasAttachment) {
                                const span = document.createElement('span');
                                span.className = 'text-gray-600';
                                span.textContent = 'Belum ada dokumen';
                                attachCell.appendChild(span);
                            }
                        }

                        // update edit inputs with saved values and hide edit row
                        const editRow = document.getElementById('ed-edit-row-' + id);
                        if (editRow) {
                            const linkInp = editRow.querySelector('.ed-link-input');
                            const notesInp = editRow.querySelector('.ed-notes-input');
                            if (linkInp) linkInp.value = saved.link || '';
                            if (notesInp) notesInp.value = saved.notes || '';
                            editRow.classList.add('hidden');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal menyimpan perubahan.');
                    } finally {
                        saveBtn.disabled = false;
                    }
                });
            });

            // upload file
            document.querySelectorAll('.ed-upload').forEach(function(btn){
                btn.addEventListener('click', async function(){
                    const id = this.getAttribute('data-id');
                    const editRow = document.getElementById('ed-edit-row-' + id);
                    const fileInput = editRow ? editRow.querySelector('.ed-file-input') : null;
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        alert('Pilih file terlebih dahulu.');
                        return;
                    }
                    const file = fileInput.files[0];
                    const form = new FormData();
                    form.append('file', file);

                    try {
                        const res = await fetch(`/documents/${id}/upload`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            body: form
                        });
                        if (!res.ok) throw new Error('Upload failed');
                        const data = await res.json();
                        if (data.url) {
                            // update row: add link to file without reload
                            const saved = data.eventDocument || {};
                            const row = document.getElementById('ed-row-' + id);
                            if (row) {
                                const attachCell = row.querySelector('td:nth-child(2) .flex');
                                if (attachCell) {
                                    // prepend file link
                                    const a = document.createElement('a');
                                    a.href = data.url;
                                    a.target = '_blank';
                                    a.rel = 'noopener noreferrer';
                                    a.className = 'text-blue-600 underline';
                                    a.textContent = saved.file_path ? (saved.file_path.length > 30 ? saved.file_path.slice(0,27) + '…' : saved.file_path) : 'file';
                                    // insert at top
                                    attachCell.insertBefore(a, attachCell.firstChild);
                                }
                                row.classList.add('bg-green-50');
                            }
                            // also update edit inputs saved file path if present
                            const editRow = document.getElementById('ed-edit-row-' + id);
                            if (editRow) {
                                const fileInp = editRow.querySelector('.ed-file-input');
                                if (fileInp) fileInp.value = null;
                            }
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal mengunggah file.');
                    }
                });
            });
        });
    })();
</script>
@endpush
