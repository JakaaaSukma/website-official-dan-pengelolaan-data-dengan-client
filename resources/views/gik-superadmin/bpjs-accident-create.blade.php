@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7">
    <a href="{{ route('gik-superadmin.bpjs.accidents') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center gap-1 mb-2">← Kembali ke Laporan Kecelakaan</a>
    <h1 class="text-2xl font-extrabold text-gray-900">Buat Laporan Kecelakaan Kerja</h1>
    <p class="text-gray-500 text-sm mt-1">Isi formulir berikut untuk melaporkan kecelakaan kerja peserta magang.</p>
</div>

@if(session('error'))
<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">❌ {{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <!-- Step 1: Select Company -->
            <form method="GET" class="mb-6 pb-6 border-b border-gray-100">
                <label class="block text-sm font-semibold text-gray-700 mb-2">1. Pilih Perusahaan</label>
                <div class="flex gap-3">
                    <select name="company_id" required class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200" onchange="this.form.submit()">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $selectedCompanyId == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedCompanyId)
            <!-- Step 2: Fill Form -->
            <form action="{{ route('gik-superadmin.bpjs.accidents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">2. Pilih Peserta Magang <span class="text-red-500">*</span></label>
                        <select name="hr_contract_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($contracts as $c)
                            <option value="{{ $c->id }}" {{ old('hr_contract_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->national_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" name="incident_date" value="{{ old('incident_date') }}" required max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Waktu Kejadian</label>
                            <input type="time" name="incident_time" value="{{ old('incident_time') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Kejadian <span class="text-red-500">*</span></label>
                        <input type="text" name="incident_location" value="{{ old('incident_location') }}" required placeholder="Contoh: Area Produksi Gedung A" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Kecelakaan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                                <input type="radio" name="accident_category" value="ringan" class="sr-only" {{ old('accident_category') == 'ringan' ? 'checked' : '' }} required>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">🟢 Ringan</p>
                                    <p class="mt-1 text-xs text-gray-500">Luka kecil, P3K cukup</p>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                                <input type="radio" name="accident_category" value="sedang" class="sr-only" {{ old('accident_category') == 'sedang' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">🟡 Sedang</p>
                                    <p class="mt-1 text-xs text-gray-500">Butuh perawatan medis</p>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300 has-[:checked]:border-red-500 has-[:checked]:ring-1 has-[:checked]:ring-red-500">
                                <input type="radio" name="accident_category" value="berat" class="sr-only" {{ old('accident_category') == 'berat' ? 'checked' : '' }}>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">🔴 Berat / Fatal</p>
                                    <p class="mt-1 text-xs text-gray-500">Rawat inap / Cacat / Kematian</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kronologi Kejadian <span class="text-red-500">*</span></label>
                        <textarea name="chronology" rows="4" required placeholder="Ceritakan secara detail bagaimana kecelakaan terjadi..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">{{ old('chronology') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tindakan Pertolongan Pertama (Opsional)</label>
                        <textarea name="action_taken" rows="2" placeholder="Tindakan yang sudah dilakukan pasca kejadian..." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">{{ old('action_taken') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dokumen Pendukung / Foto Kejadian</label>
                        <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-400 mt-1">Bisa unggah lebih dari satu file (Max 5MB per file).</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full px-4 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-md transition">Kirim Laporan Kecelakaan</button>
                    </div>
                </div>
            </form>
            @else
            <div class="text-center py-10 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-sm">Pilih perusahaan terlebih dahulu untuk memuat daftar peserta magang.</p>
            </div>
            @endif
        </div>
    </div>
    
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
            <h3 class="font-bold text-blue-900 mb-2 flex items-center gap-2">ℹ️ Informasi Penting</h3>
            <ul class="text-sm text-blue-800 space-y-2 list-disc pl-4">
                <li>Pastikan tanggal dan waktu kejadian diisi dengan akurat.</li>
                <li>Pilih kategori kecelakaan yang sesuai dengan dampak sebenarnya. Kategori <strong>Berat</strong> akan mendapatkan prioritas tinggi.</li>
                <li>Lampirkan bukti foto atau rekam medis jika tersedia untuk mempercepat proses klaim.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
