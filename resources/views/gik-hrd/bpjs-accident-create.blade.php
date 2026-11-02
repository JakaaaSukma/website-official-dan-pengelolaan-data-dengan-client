@extends('layouts.gik-hrd')

@section('content')
<div class="mb-7">
    <a href="{{ route('gik-hrd.bpjs.accidents') }}" class="text-sm text-gray-500 hover:text-gik-primary transition">← Kembali ke Daftar Laporan</a>
    <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight mt-2">Laporan Kecelakaan Kerja</h1>
    <p class="text-gray-500 text-sm mt-1">Peserta: <strong>{{ $contract->full_name }}</strong></p>
</div>

@if($errors->any())
<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('gik-hrd.bpjs.accidents.store', $contract) }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kejadian <span class="text-red-500">*</span></label>
                <input type="date" name="incident_date" value="{{ old('incident_date') }}" required max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Waktu Kejadian</label>
                <input type="time" name="incident_time" value="{{ old('incident_time') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Kejadian <span class="text-red-500">*</span></label>
                <input type="text" name="incident_location" value="{{ old('incident_location') }}" required maxlength="255" placeholder="Contoh: Area produksi lantai 2, Pabrik A" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kronologi Kejadian <span class="text-red-500">*</span></label>
                <textarea name="chronology" required minlength="10" maxlength="5000" rows="5" placeholder="Jelaskan kronologi kejadian secara detail..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">{{ old('chronology') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Kecelakaan <span class="text-red-500">*</span></label>
                <select name="accident_category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="ringan" {{ old('accident_category') === 'ringan' ? 'selected' : '' }}>Ringan — Luka ringan, bisa langsung bekerja</option>
                    <option value="sedang" {{ old('accident_category') === 'sedang' ? 'selected' : '' }}>Sedang — Perlu perawatan medis, istirahat sementara</option>
                    <option value="berat" {{ old('accident_category') === 'berat' ? 'selected' : '' }}>Berat — Rawat inap / cacat / meninggal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Dokumen Pendukung (opsional)</label>
                <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                <p class="text-xs text-gray-400 mt-1">Foto, berita acara, surat keterangan medis. Maks 5MB/file.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tindakan / Penanganan yang Sudah Dilakukan</label>
                <textarea name="action_taken" rows="3" maxlength="3000" placeholder="Uraikan tindakan atau penanganan yang sudah dilakukan..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">{{ old('action_taken') }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-gik-primary to-red-600 text-white rounded-xl text-sm font-bold shadow-md hover:from-gik-primary-dark hover:to-red-700 transition">🚨 Kirim Laporan Kecelakaan</button>
            <a href="{{ route('gik-hrd.bpjs.accidents') }}" class="px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
