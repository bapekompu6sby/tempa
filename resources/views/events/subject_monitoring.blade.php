@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Monitoring Sikap: {{ $subject->name }}</h2>
            <p class="text-gray-600">Pelatihan: {{ $event->name }}</p>
        </div>
        <a href="{{ route('events.show', ['event' => $event, 'main_tab' => 'mata_pelatihan']) }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 font-medium">Kembali</a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            <span class="block sm:inline">{!! session('success') !!}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border-t-4 border-blue-600 p-6">
        <form method="POST" action="{{ route('events.saveSubjectMonitoring', ['event' => $event, 'subject' => $subject]) }}">
            @csrf
            
            <div class="flex justify-end mb-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700 shadow-md transition-colors">Simpan Progress</button>
            </div>

            @php
                $firstAes = $asnEventSubjects->first();
                $headerItems = $firstAes ? $monitoringItems->get($firstAes->aes_id, collect()) : collect();
            @endphp
            
            <style>
                .sticky-col {
                    position: -webkit-sticky !important;
                    position: sticky !important;
                    left: 0 !important;
                    z-index: 20 !important;
                    background-color: #f0f9ff !important; /* sky-50 */
                    box-shadow: 6px 0 6px -2px rgba(0, 0, 0, 0.08) !important;
                }
                .sticky-col-header {
                    position: -webkit-sticky !important;
                    position: sticky !important;
                    left: 0 !important;
                    z-index: 30 !important;
                    background-color: #e0f2fe !important; /* sky-100 */
                    box-shadow: 6px 0 6px -2px rgba(0, 0, 0, 0.08) !important;
                }
            </style>

            <div class="w-full overflow-x-auto border rounded-lg shadow-sm">
                <table class="min-w-full leading-normal border-separate border-spacing-0">
                    <thead class="bg-gray-100 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-r border-gray-200 align-bottom min-w-[250px] sticky-col-header">Peserta</th>
                            @foreach($headerItems as $index => $item)
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-l border-b-2 border-gray-200 min-w-[160px] max-w-[200px] align-bottom" title="{{ $item->name }}">
                                    <div class="line-clamp-4 leading-tight normal-case font-medium">{{ $item->name }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($asnEventSubjects as $aes)
                            @php
                                $items = $monitoringItems->get($aes->aes_id, collect());
                            @endphp
                            <tr class="group border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 text-sm align-middle group-hover:bg-gray-50 border-r border-gray-200 whitespace-nowrap transition-colors sticky-col">
                                    <div class="flex items-start">
                                        <div class="font-semibold text-gray-700 mr-3 w-5 mt-0.5 text-right">{{ $loop->iteration }}.</div>
                                        <div class="flex-grow flex justify-between items-center">
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ $aes->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">NIP: {{ $aes->nip ?? '-' }}</div>
                                            </div>
                                            <label class="flex flex-col items-center justify-center cursor-pointer ml-3 text-[10px] text-gray-500 hover:text-blue-600">
                                                <input type="checkbox" class="check-all-row w-3 h-3 cursor-pointer mb-1" data-row="{{ $aes->aes_id }}">
                                                <span>OK Semua</span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                @if($items->isEmpty())
                                    <td colspan="{{ $headerItems->count() ?: 1 }}" class="px-4 py-2 text-sm align-middle">
                                        <div class="p-2 bg-yellow-50 border border-yellow-200 rounded text-yellow-700 text-xs italic">
                                            Belum ada item monitoring sikap untuk peserta ini.
                                        </div>
                                    </td>
                                @else
                                    @foreach($items as $index => $item)
                                        <td class="px-2 py-2 text-center align-middle border-l border-gray-100 hover:bg-blue-100 transition-colors">
                                            <input type="hidden" name="all_item_ids[]" value="{{ $item->id }}">
                                            <input type="checkbox" name="items[{{ $item->id }}]" value="1" {{ $item->value ? 'checked' : '' }} class="item-checkbox w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer shadow-sm hover:scale-110 transition-transform" title="{{ $item->name }}" data-col="{{ $index }}" data-row="{{ $aes->aes_id }}">
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $headerItems->count() }}" class="px-5 py-8 text-center text-sm text-gray-500 bg-gray-50">
                                    Belum ada peserta yang terdaftar pada mata pelatihan ini.<br>
                                    <span class="text-xs text-gray-400">Silakan klik "Inisiasi Nilai Peserta" pada halaman Mata Pelatihan.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($asnEventSubjects->isNotEmpty())
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700 shadow-md transition-colors">Simpan Progress</button>
            </div>
            @endif
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowCheckboxes = document.querySelectorAll('.check-all-row');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const rowId = this.getAttribute('data-row');
            const isChecked = this.checked;
            document.querySelectorAll(`.item-checkbox[data-row="${rowId}"]`).forEach(item => {
                item.checked = isChecked;
            });
        });
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateRowCheckAllState();
        });
    });

    function updateRowCheckAllState() {
        rowCheckboxes.forEach(cb => {
            const rowId = cb.getAttribute('data-row');
            const items = document.querySelectorAll(`.item-checkbox[data-row="${rowId}"]`);
            if(items.length > 0) {
                const allChecked = Array.from(items).every(item => item.checked);
                const someChecked = Array.from(items).some(item => item.checked);
                cb.checked = allChecked;
                cb.indeterminate = !allChecked && someChecked;
            }
        });
    }

    // Initialize state
    updateRowCheckAllState();
});
</script>
@endsection

