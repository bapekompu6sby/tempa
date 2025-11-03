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
                        @php $hasAttachment = !empty($doc->link) || (!empty($doc->file_path) || (isset($doc->files) && $doc->files->count() > 0)); @endphp
                        <tr class="border-t align-top {{ $hasAttachment ? 'bg-green-50' : '' }}" id="ed-row-{{ $doc->id }}">
                            <td class="py-2 px-4 align-top">{{ $doc->name }}</td>
                            <td class="py-2 px-4 align-top">
                                <div class="flex flex-col gap-1">
                                    @if(!empty($doc->file_path))
                                        <a id="ed-filepath-display-{{ $doc->id }}" href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->file_path, 30) }}</a>
                                    @endif
                                    @if(!empty($doc->link))
                                        @php
                                            $href = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/');
                                        @endphp
                                        <a id="ed-link-display-{{ $doc->id }}" href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->link, 30) }}</a>
                                    @endif
                                    {{-- If there are uploaded attachment records but no file_path/link to show in the main cell, indicate presence of attachments --}}
                                    @if((isset($doc->files) && $doc->files->count() > 0) && empty($doc->file_path) && empty($doc->link))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-green-100 text-green-800">Ada lampiran ({{ $doc->files->count() }})</span>
                                    @endif
                                    @if(!$hasAttachment)
                                        <span class="text-gray-600">Belum ada dokumen</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4 align-top" id="ed-notes-{{ $doc->id }}">{{ $doc->notes ?? '-' }}</td>
                            <td class="py-2 px-4 align-top">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="ed-view px-2 py-1 bg-blue-500 text-white rounded text-sm" data-id="{{ $doc->id }}">Lihat File</button>
                                    <button type="button" class="ed-edit px-2 py-1 bg-yellow-500 text-white rounded text-sm" data-id="{{ $doc->id }}">Edit</button>
                                </div>
                            </td>
                        </tr>
                        <tr id="ed-view-row-{{ $doc->id }}" class="hidden bg-white">
                            <td class="py-3 px-4" colspan="4">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Link Dokumen</label>
                                        @if(!empty($doc->link))
                                            @php $hrefView = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/'); @endphp
                                            <div class="mt-1"><a id="view-only-link-{{ $doc->id }}" href="{{ $hrefView }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->link, 200) }}</a></div>
                                        @else
                                            <div class="mt-1 text-gray-600">-</div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lampiran</label>
                                        <ul class="space-y-1 mt-2">
                                            @forelse($doc->files as $file)
                                                <li class="flex items-center justify-between">
                                                    <span class="text-gray-800">{{ $file->original_name }}</span>
                                                    <a href="{{ route('documents.files.download', $file) }}" class="px-2 py-1 bg-blue-500 text-white rounded text-sm">Download</a>
                                                </li>
                                            @empty
                                                <li class="text-gray-600">Tidak ada lampiran</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                        <div class="mt-1 text-gray-700 whitespace-pre-wrap" id="view-only-notes-{{ $doc->id }}">{{ $doc->notes ?? '-' }}</div>
                                    </div>
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

                                    {{-- Inline attachments list shown while editing (moved above notes) --}}
                                    <div id="ed-files-inline-{{ $doc->id }}" class="mt-2">
                                        <div class="text-sm font-medium mb-2">Lampiran</div>
                                        @if(!empty($doc->link))
                                            @php $hrefView = (strpos($doc->link, '://') !== false) ? $doc->link : 'https://' . ltrim($doc->link, '/'); @endphp
                                            <div class="mb-2"><a id="ed-link-view-{{ $doc->id }}" href="{{ $hrefView }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($doc->link, 80) }}</a></div>
                                        @else
                                            <div id="ed-link-view-{{ $doc->id }}"></div>
                                        @endif
                                        <ul id="ed-files-list-{{ $doc->id }}" class="space-y-1">
                                            @foreach($doc->files as $file)
                                                <li id="file-{{ $file->id }}" class="flex items-center justify-between">
                                                    <a href="{{ route('documents.files.download', $file) }}" class="text-blue-600 underline">{{ $file->original_name }}</a>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ route('documents.files.download', $file) }}" class="px-2 py-1 bg-blue-500 text-white rounded text-sm">Download</a>
                                                        <button type="button" data-file-id="{{ $file->id }}" class="delete-file-btn hidden text-sm text-red-600">Hapus</button>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
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
                    
                        {{-- attachments list previously rendered in a separate row removed (now inline in edit row) --}}
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
            // open edit row (hide all views and other edits first). If this edit is already open, close it.
            document.querySelectorAll('.ed-edit').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');

                    const editRow = document.getElementById('ed-edit-row-' + id);
                    const filesInline = document.getElementById('ed-files-inline-' + id);
                    const list = document.getElementById('ed-files-list-' + id);

                    // if this edit row is already visible, close it (toggle off)
                    if (editRow && !editRow.classList.contains('hidden')) {
                        editRow.classList.add('hidden');
                        if (filesInline) filesInline.classList.add('hidden');
                        if (list) list.querySelectorAll('.delete-file-btn').forEach(function(d){ d.classList.add('hidden'); });
                        return;
                    }

                    // otherwise hide all edit rows and inline files blocks, and hide delete buttons globally
                    document.querySelectorAll('[id^="ed-edit-row-"]').forEach(function(r){ r.classList.add('hidden'); });
                    document.querySelectorAll('[id^="ed-files-inline-"]').forEach(function(r){ r.classList.add('hidden'); });
                    document.querySelectorAll('.delete-file-btn').forEach(function(d){ d.classList.add('hidden'); });

                    // show the selected edit row and inline files block, reveal delete buttons inside it
                    if (editRow) editRow.classList.remove('hidden');
                    if (filesInline) filesInline.classList.remove('hidden');
                    if (list) list.querySelectorAll('.delete-file-btn').forEach(function(d){ d.classList.remove('hidden'); });
                });
            });

            // cancel
            document.querySelectorAll('.ed-cancel').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');
                    const editRow = document.getElementById('ed-edit-row-' + id);
                    if (editRow) editRow.classList.add('hidden');
                    // also hide files row and hide delete buttons
                    const filesInline = document.getElementById('ed-files-inline-' + id);
                    const list = document.getElementById('ed-files-list-' + id);
                    if (filesInline) filesInline.classList.add('hidden');
                    if (list) list.querySelectorAll('.delete-file-btn').forEach(function(d){ d.classList.add('hidden'); });
                });
            });

            // save link & notes via PATCH
            // view files button -> toggles read-only view row (link, attachments with download, notes)
            document.querySelectorAll('.ed-view').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');

                    const viewRow = document.getElementById('ed-view-row-' + id);
                    const alreadyVisible = viewRow && !viewRow.classList.contains('hidden');

                    // hide all edit rows, view rows and inline edit blocks first
                    document.querySelectorAll('[id^="ed-edit-row-"]').forEach(function(r){ r.classList.add('hidden'); });
                    document.querySelectorAll('[id^="ed-view-row-"]').forEach(function(r){ r.classList.add('hidden'); });
                    document.querySelectorAll('[id^="ed-files-inline-"]').forEach(function(r){ r.classList.add('hidden'); });

                    // hide all delete buttons (view is read-only)
                    document.querySelectorAll('.delete-file-btn').forEach(function(d){ d.classList.add('hidden'); });

                    // if it was visible, we've just hidden everything; stop here (acts like toggle)
                    if (alreadyVisible) return;

                    // otherwise, show the read-only view row
                    if (viewRow) viewRow.classList.remove('hidden');
                });
            });

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
                                // gather pending uploads for this doc (staged client-side) and any files still in the input
                                let uploadResult = null;
                                const pending = window._ed_pendingUploads && window._ed_pendingUploads[id] ? window._ed_pendingUploads[id].slice() : [];
                                const inputFiles = (fileInput && fileInput.files && fileInput.files.length) ? Array.from(fileInput.files) : [];
                                const filesToUpload = pending.concat(inputFiles);
                                if (filesToUpload.length > 0) {
                                    // use the helper to upload files (same endpoint)
                                    uploadResult = await uploadFilesForDoc(id, filesToUpload);
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

                            // Update displayed link in the main list and in the files view if returned by the server
                            try {
                                const docData = data.eventDocument || {};
                                // main link display
                                const mainLink = document.getElementById('ed-link-display-' + id);
                                const mainFilePath = document.getElementById('ed-filepath-display-' + id);
                                if (docData.link) {
                                    const href = normalizeLink(docData.link);
                                    if (mainLink) {
                                        mainLink.href = href;
                                        mainLink.textContent = docData.link.length > 30 ? docData.link.slice(0,30) + '...' : docData.link;
                                    } else {
                                        // create anchor and insert at top of the cell
                                        const cell = document.querySelector('#ed-row-' + id + ' td:nth-child(2) .flex');
                                        if (cell) {
                                            const a = document.createElement('a');
                                            a.id = 'ed-link-display-' + id;
                                            a.href = href;
                                            a.target = '_blank';
                                            a.rel = 'noopener noreferrer';
                                            a.className = 'text-blue-600 underline';
                                            a.textContent = docData.link.length > 30 ? docData.link.slice(0,30) + '...' : docData.link;
                                            cell.insertBefore(a, cell.firstChild);
                                        }
                                    }
                                    // update files view link
                                    const viewLink = document.getElementById('ed-link-view-' + id);
                                    if (viewLink) {
                                        viewLink.innerHTML = '';
                                        const a2 = document.createElement('a');
                                        a2.id = 'ed-link-view-' + id + '-a';
                                        a2.href = normalizeLink(docData.link);
                                        a2.target = '_blank';
                                        a2.rel = 'noopener noreferrer';
                                        a2.className = 'text-blue-600 underline';
                                        a2.textContent = docData.link.length > 80 ? docData.link.slice(0,80) + '...' : docData.link;
                                        viewLink.appendChild(a2);
                                    }
                                } else {
                                    // no link returned: remove anchors if present
                                    const ml = document.getElementById('ed-link-display-' + id);
                                    if (ml) ml.remove();
                                    const viewLink = document.getElementById('ed-link-view-' + id);
                                    if (viewLink) viewLink.innerHTML = '';
                                }
                                // if server returned a file_path (legacy), update anchor href/text
                                if (docData.file_path) {
                                    if (mainFilePath) {
                                        mainFilePath.href = '/documents/' + id + '/download';
                                        mainFilePath.textContent = docData.file_path.length > 30 ? docData.file_path.slice(0,30) + '...' : docData.file_path;
                                    }
                                }
                            } catch (errUpdate) {
                                console.error('Failed to update link display', errUpdate);
                            }

                            // clear pending queue and remove pending DOM items if upload succeeded
                            if (uploadResult && uploadResult.files) {
                                // remove pending markers (elements with class pending-file) for this doc
                                const list = document.getElementById('ed-files-list-' + id);
                                if (list) {
                                    // remove pending-file elements
                                    list.querySelectorAll('.pending-file').forEach(function(n){ n.remove(); });
                                }
                                // append server-returned files (the helper also appends, but ensure delete button visibility)
                                // the uploadFilesForDoc already appended files; nothing more to do here
                            }
                            // clear pendingUploads for this id
                            if (window._ed_pendingUploads && window._ed_pendingUploads[id]) {
                                window._ed_pendingUploads[id] = [];
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

            // handle file selection UI (show selected filename) and support immediate single-file uploads when user clicked 'Tambah Lampiran'
            // pending uploads staged on client until user presses Save
            window._ed_pendingUploads = window._ed_pendingUploads || {};
            document.querySelectorAll('.ed-file-input').forEach(function(input){
                input.addEventListener('change', function(){
                    const id = this.id.replace('ed-file-', '');
                    const nameSpan = document.getElementById('ed-file-name-' + id);
                    if (this.files && this.files.length > 0) {
                        // show multiple file names comma separated in the small label
                        const names = Array.from(this.files).map(f => f.name).join(', ');
                        nameSpan.textContent = names;

                        // Add selected files to pending queue for this document
                        window._ed_pendingUploads[id] = window._ed_pendingUploads[id] || [];
                        Array.from(this.files).forEach(function(f){
                            window._ed_pendingUploads[id].push(f);
                            // render a temporary list item in attachments list
                            const list = document.getElementById('ed-files-list-' + id);
                            if (list) {
                                const tempId = 'pending-' + Math.random().toString(36).slice(2,9);
                                const li = document.createElement('li');
                                li.id = tempId;
                                li.className = 'flex items-center justify-between pending-file';
                                const span = document.createElement('span');
                                span.className = 'text-gray-700';
                                span.textContent = f.name + ' (baru)';
                                const right = document.createElement('div');
                                right.className = 'flex items-center gap-2';
                                const remove = document.createElement('button');
                                remove.type = 'button';
                                remove.className = 'pending-remove-btn text-sm text-red-600';
                                remove.textContent = 'Batal';
                                remove.addEventListener('click', function(){
                                    // remove from pendingUploads and DOM
                                    const arr = window._ed_pendingUploads[id] || [];
                                    // remove first matching by name (best-effort)
                                    for (let i = 0; i < arr.length; i++) {
                                        if (arr[i].name === f.name && arr[i].size === f.size && arr[i].type === f.type) {
                                            arr.splice(i,1);
                                            break;
                                        }
                                    }
                                    if (li) li.remove();
                                    // clear small label if no pending left
                                    if (!(window._ed_pendingUploads[id] && window._ed_pendingUploads[id].length)) {
                                        nameSpan.textContent = '';
                                    }
                                });
                                right.appendChild(remove);
                                li.appendChild(span);
                                li.appendChild(right);
                                list.appendChild(li);
                            }
                        });

                        // reset input so selecting the same file again works
                        try { input.value = null; } catch(e){}
                    } else {
                        nameSpan.textContent = '';
                    }
                });
            });

            // helper: upload files (array of File) immediately for a document and append returned attachments to the list
            async function uploadFilesForDoc(id, filesArray) {
                if (!filesArray || filesArray.length === 0) return null;
                const form = new FormData();
                for (let i = 0; i < filesArray.length; i++) form.append('files[]', filesArray[i]);

                const upRes = await fetch(`/documents/${id}/files`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    body: form
                });
                if (!upRes.ok) {
                    const txt = await upRes.text().catch(()=>null);
                    throw new Error('Upload failed: ' + (txt || upRes.status));
                }
                const uploadResult = await upRes.json();

                // append returned files to the attachments list in the UI
                if (uploadResult && uploadResult.files && uploadResult.files.length) {
                    const list = document.getElementById('ed-files-list-' + id);
                    if (list) {
                        uploadResult.files.forEach(function(f){
                            // avoid duplicate if already present
                            if (document.getElementById('file-' + f.id)) return;
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
                            down.className = 'px-2 py-1 bg-blue-500 text-white rounded text-sm';
                            down.textContent = 'Download';
                            const del = document.createElement('button');
                            del.type = 'button';
                            del.dataset.fileId = f.id;
                            del.className = 'delete-file-btn hidden text-sm text-red-600';
                            del.textContent = 'Hapus';
                            right.appendChild(down);
                            right.appendChild(del);
                            li.appendChild(a);
                            li.appendChild(right);
                            list.appendChild(li);
                        });
                    }
                    // mark row as having attachments
                    const row = document.getElementById('ed-row-' + id);
                    if (row) row.classList.add('bg-green-50');
                }
                return uploadResult;
            }

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

            // (no immediate upload button in edit row; files are staged and uploaded on Save)

            // upload file if present, then PATCH link & notes in a single Save action
            // ed-save handler already handles PATCH; we'll extend it to upload first when file exists
        });
    })();
</script>
@endpush
