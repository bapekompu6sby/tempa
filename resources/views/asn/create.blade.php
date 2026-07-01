@extends('layout.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Tambah ASN</h2>
        <a href="{{ route('asn.index') }}" class="px-4 py-2 bg-gray-300 rounded text-sm">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow border-t-4 border-green-500">
        <form action="{{ route('asn.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pangkat/Golongan</label>
                    <input type="text" name="rank_grade" value="{{ old('rank_grade') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WA</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_city" value="{{ old('birth_city') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                        <option value="">- Pilih -</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                    <input type="text" name="latest_education" value="{{ old('latest_education') }}" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe ASN</label>
                    <select name="asn_type" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                        <option value="">- Pilih -</option>
                        <option value="pns" {{ old('asn_type') == 'pns' ? 'selected' : '' }}>PNS</option>
                        <option value="cpns" {{ old('asn_type') == 'cpns' ? 'selected' : '' }}>CPNS</option>
                        <option value="pppk" {{ old('asn_type') == 'pppk' ? 'selected' : '' }}>PPPK</option>
                        <option value="lainnya" {{ old('asn_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Instansi</label>
                    <select name="asn_source" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">
                        <option value="">- Pilih -</option>
                        <option value="pusat" {{ old('asn_source') == 'pusat' ? 'selected' : '' }}>Pusat</option>
                        <option value="daerah" {{ old('asn_source') == 'daerah' ? 'selected' : '' }}>Daerah</option>
                        <option value="lainnya" {{ old('asn_source') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
                <textarea name="office_address" rows="3" class="w-full border p-2 rounded focus:ring-green-500 focus:border-green-500">{{ old('office_address') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded font-semibold hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
