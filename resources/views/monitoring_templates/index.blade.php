@extends('layout.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Daftar Monitoring Template</h2>
        <a href="{{ route('monitoring-templates.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Tambah Template</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">No.</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Kategori</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Sub Kategori</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Ownership</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-t align-top">
                        <td class="py-2 px-4 text-sm text-gray-700 font-semibold">{{ $loop->iteration + $templates->firstItem() - 1 }}</td>
                        <td class="py-2 px-4">{{ $template->name }}</td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $template->category)) }}
                            </span>
                        </td>
                        <td class="py-2 px-4">{{ $template->sub_category ?? '-' }}</td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">
                                {{ ucfirst(str_replace('_', ' ', $template->ownership)) }}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            <div class="flex items-center space-x-3 whitespace-nowrap">
                                <a href="{{ route('monitoring-templates.edit', $template) }}" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 010 2.828l-9.192 9.192a1 1 0 01-.464.263l-4 1a1 1 0 01-1.213-1.213l1-4a1 1 0 01.263-.464l9.192-9.192a2 2 0 012.828 0z"/></svg>
                                </a>
                                <form action="{{ route('monitoring-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 000 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 5a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1zm4 0a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="py-4 px-4 text-center text-gray-500" colspan="6">Tidak ada template.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        @if(method_exists($templates, 'links'))
            {{ $templates->links('vendor.pagination.light') }}
        @endif
    </div>
</div>
@endsection
