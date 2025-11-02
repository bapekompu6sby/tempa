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
                                        <a href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->file_path, 30) }}</a>
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
                                        <div class="mt-1 flex items-center gap-2">
                                                <input type="file" name="files[]" multiple class="ed-file-input hidden" id="ed-file-{{ $doc->id }}" />
                                                <label for="ed-file-{{ $doc->id }}" class="inline-block px-3 py-2 bg-gray-200 text-gray-800 rounded cursor-pointer text-sm">Pilih file</label>
                                                <span class="text-sm text-gray-500 file-selected" id="ed-file-name-{{ $doc->id }}">{{ $doc->file_path ? \Illuminate\Support\Str::limit($doc->file_path, 30) : '' }}</span>
                                            </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                        <textarea name="notes" class="ed-notes-input mt-1 block w-full border rounded px-3 py-2" rows="3">{{ $doc->notes }}</textarea>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" class="ed-save px-3 py-2 bg-blue-600 text-white rounded text-sm cursor-pointer" data-id="{{ $doc->id }}">Simpan</button>
                                        <button type="button" class="ed-cancel px-3 py-2 bg-gray-300 rounded text-sm cursor-pointer" data-id="{{ $doc->id }}">Batal</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    
                        {{-- attachments list for this document (multiple files) --}}
                        <tr id="ed-files-row-{{ $doc->id }}" class="bg-white">
                            <td colspan="4" class="py-2 px-4">
                                <div class="text-sm font-medium mb-2">Lampiran</div>
                                <ul id="ed-files-list-{{ $doc->id }}" class="space-y-1">
                                    @foreach($doc->files as $file)
                                        <li id="file-{{ $file->id }}" class="flex items-center justify-between">
                                            <a href="{{ route('documents.files.download', $file) }}" class="text-blue-600 underline">{{ $file->original_name }}</a>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('documents.files.download', $file) }}" class="text-sm text-gray-600">Download</a>
                                                <button type="button" data-file-id="{{ $file->id }}" class="delete-file-btn text-sm text-red-600">Hapus</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
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
                        const fileInput = document.querySelector('#ed-edit-row-' + id + ' .ed-file-input');
                        const fileNameSpan = document.getElementById('ed-file-name-' + id);
                            const saveBtn = this;
                        saveBtn.disabled = true;
                        try {
                            let uploadResult = null;
                            // handle multiple files upload if present
                            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                                const form = new FormData();
                                for (let i = 0; i < fileInput.files.length; i++) {
                                    form.append('files[]', fileInput.files[i]);
                                }
                                const upRes = await fetch(`/documents/${id}/files`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token
                                    },
                                    body: form
                                });
                                if (!upRes.ok) throw new Error('Upload failed');
                                uploadResult = await upRes.json();
                            }

                            // then save link & notes via PATCH
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

                            // update attachment cell: we now render file list separately; update row bg
                            const row = document.getElementById('ed-row-' + id);
                            const hasAttachment = (data.eventDocument && (data.eventDocument.link || data.eventDocument.file_path)) || (uploadResult && uploadResult.files && uploadResult.files.length > 0);
                            if (row) {
                                if (hasAttachment) row.classList.add('bg-green-50'); else row.classList.remove('bg-green-50');
                            }

                            // update attachments list DOM if uploadResult returned files
                            if (uploadResult && uploadResult.files) {
                                const list = document.getElementById('ed-files-list-' + id);
                                if (list) {
                                    uploadResult.files.forEach(function(f){
                                        const li = document.createElement('li');
                                        li.id = 'file-' + f.id;
                                        li.className = 'flex items-center justify-between';
                                        const a = document.createElement('a');
                                        a.href = '/documents/files/' + f.id + '/download';
                                        a.className = 'text-blue-600 underline';
                                        a.textContent = f.original_name;
                                        const right = document.createElement('div');
                                        right.className = 'flex items-center gap-2';
                                        const down = document.createElement('a');
                                        down.href = '/documents/files/' + f.id + '/download';
                                        down.className = 'text-sm text-gray-600';
                                        down.textContent = 'Download';
                                        const del = document.createElement('button');
                                        del.type = 'button';
                                        del.dataset.fileId = f.id;
                                        del.className = 'delete-file-btn text-sm text-red-600';
                                        del.textContent = 'Hapus';
                                        right.appendChild(down);
                                        right.appendChild(del);
                                        li.appendChild(a);
                                        li.appendChild(right);
                                        list.appendChild(li);
                                    });
                                }
                            }

                            // update edit inputs with saved values and hide edit row
                            const editRow = document.getElementById('ed-edit-row-' + id);
                            if (editRow) {
                                const linkInp = editRow.querySelector('.ed-link-input');
                                const notesInp = editRow.querySelector('.ed-notes-input');
                                const fileInp = editRow.querySelector('.ed-file-input');
                                const fileName = editRow.querySelector('.file-selected');
                                if (linkInp) linkInp.value = (data.eventDocument && data.eventDocument.link) ? data.eventDocument.link : (linkInput ? linkInput.value.trim() : '');
                                if (notesInp) notesInp.value = (data.eventDocument && data.eventDocument.notes) ? data.eventDocument.notes : (notesInput ? notesInput.value.trim() : '');
                                if (fileInp) fileInp.value = null;
                                if (fileName) fileName.textContent = uploadResult && uploadResult.files && uploadResult.files.length ? uploadResult.files.map(f=>f.original_name).join(', ') : '';
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

            // handle file selection UI (show selected filename)
            document.querySelectorAll('.ed-file-input').forEach(function(input){
                input.addEventListener('change', function(){
                    const id = this.id.replace('ed-file-', '');
                    const nameSpan = document.getElementById('ed-file-name-' + id);
                    if (this.files && this.files.length > 0) {
                        // show multiple file names comma separated
                        const names = Array.from(this.files).map(f => f.name).join(', ');
                        nameSpan.textContent = names;
                    } else {
                        nameSpan.textContent = '';
                    }
                });
            });

            // delete file handler (delegated)
            document.addEventListener('click', async function(e){
                if (e.target && e.target.classList.contains('delete-file-btn')) {
                    const id = e.target.dataset.fileId;
                    if (!confirm('Hapus lampiran ini?')) return;
                    try {
                        const res = await fetch('/documents/files/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': token }
                        });
                        if (!res.ok) throw new Error('Delete failed');
                        // remove from DOM
                        const li = document.getElementById('file-' + id);
                        if (li) li.remove();
                    } catch (err) {
                        console.error(err);
                        alert('Gagal menghapus lampiran.');
                    }
                }
            });

            // upload file if present, then PATCH link & notes in a single Save action
            // ed-save handler already handles PATCH; we'll extend it to upload first when file exists
        });
    })();
</script>
@endpush
