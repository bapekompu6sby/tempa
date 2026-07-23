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

            @if($headerItems->isNotEmpty())
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                <div class="font-semibold text-blue-800 mb-2">Keterangan Item Monitoring:</div>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-gray-700">
                    @foreach($headerItems as $index => $item)
                        <li class="flex items-start">
                            <strong class="text-blue-900 mr-2">{{ $index + 1 }}.</strong> 
                            <div>
                                {{ $item->name }}
                                @if($item->template && $item->template->sub_category)
                                    <span class="text-xs text-gray-500 italic block mt-0.5">({{ $item->template->sub_category }})</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="overflow-x-auto max-h-[65vh] border rounded-lg shadow-sm">
                <table class="min-w-full leading-normal relative">
                    <thead class="sticky top-0 z-30 bg-gray-100 border-b-2 border-gray-200 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-100 z-40 border-r border-gray-200">Peserta</th>
                            @foreach($headerItems as $index => $item)
                                <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-help border-l border-gray-200" title="{{ $item->name }}">
                                    {{ $index + 1 }}
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
                                <td class="px-4 py-2 text-sm font-semibold text-gray-700 align-middle">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-2 text-sm align-middle sticky left-0 bg-white group-hover:bg-gray-50 border-r border-gray-200 z-20 whitespace-nowrap transition-colors">
                                    <div class="font-bold text-gray-900 text-sm">{{ $aes->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">NIP: {{ $aes->nip ?? '-' }}</div>
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
                                            <input type="checkbox" name="items[{{ $item->id }}]" value="1" {{ $item->value ? 'checked' : '' }} class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 cursor-pointer shadow-sm hover:scale-110 transition-transform" title="{{ $item->name }}">
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
@endsection
