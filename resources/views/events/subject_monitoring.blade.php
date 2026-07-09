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

            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal border rounded-lg">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peserta</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[300px]">Item Monitoring Sikap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asnEventSubjects as $aes)
                            @php
                                $items = $monitoringItems->get($aes->aes_id, collect());
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700 align-top">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-5 py-4 text-sm align-top">
                                    <div class="font-bold text-gray-900 text-base mb-1">{{ $aes->name }}</div>
                                    <div class="text-xs text-gray-500 bg-gray-100 inline-block px-2 py-1 rounded border">NIP: {{ $aes->nip ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm align-top">
                                    @if($items->isEmpty())
                                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-700 text-xs italic">
                                            Belum ada item monitoring sikap untuk peserta ini. Pastikan Anda telah klik "Generate Monitoring Items" di halaman sebelumnya.
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($items as $item)
                                                <input type="hidden" name="all_item_ids[]" value="{{ $item->id }}">
                                                <label class="flex items-start space-x-3 cursor-pointer p-3 rounded-lg bg-gray-50 hover:bg-blue-50 transition border border-gray-200 hover:border-blue-300">
                                                    <div class="flex items-center h-5 mt-0.5">
                                                        <input type="checkbox" name="items[{{ $item->id }}]" value="1" {{ $item->value ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer shadow-sm">
                                                    </div>
                                                    <div class="flex flex-col flex-grow">
                                                        <span class="text-sm font-semibold text-gray-800">{{ $item->name }}</span>
                                                        @if($item->template && $item->template->sub_category)
                                                            <span class="text-xs text-gray-500 mt-1">{{ $item->template->sub_category }}</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500 bg-gray-50">
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
@endsection
