@extends('layouts.gik-hrd')

@section('content')
@php
    $batchIdsStr = isset($batchIds) && count($batchIds) ? implode(',', $batchIds) : '';
    $formAction = $isBatch
        ? route('gik-hrd.violations.store', 0)
        : route('gik-hrd.violations.store', $contract);
    $backRoute = route('gik-hrd.violations.index');
@endphp

<div class="mb-5 flex items-center gap-3 flex-wrap">
    <a href="{{ $backRoute }}" class="p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="min-w-0">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-gik-primary-dark rounded-lg text-xs font-bold uppercase tracking-wide mb-1 flex-wrap">
            @if($isBatch)
                🚨 LAPORAN PELANGGARAN MASSAL <span class="bg-gik-primary text-white px-2 py-0.5 rounded-full text-[10px]">{{ count($selectedContracts) }} ORANG</span>
            @else
                🚨 Form Pelaporan Pelanggaran
            @endif
        </div>
        <h1 class="text-[26px] font-bold text-gik-primary-dark leading-tight truncate">
            @if($isBatch)
                Laporkan Pelanggaran Massal
            @else
                Laporkan Pelanggaran: {{ $contract->full_name }}
            @endif
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            @if($isBatch)
                Mode <b>MASSAL</b> — Kronologi, tanggal, dan sanksi usulan akan sama untuk semua <b>{{ count($selectedContracts) }} peserta</b>. Bukti lampiran juga akan dishare ke semua laporan.
            @else
                Form ini akan dikirim ke Tim Rekrutmen Gyokai Connect untuk ditindaklanjuti.
            @endif
        </p>
    </div>
</div>

@if(session('error'))
<div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div><strong>Error!</strong> {{ session('error') }}</div>
</div>
@endif

@if($errors->any())
<div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
    <div class="font-bold text-sm mb-2">⚠️ Ada kesalahan input, mohon perbaiki:</div>
    <ul class="text-xs list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        @if($isBatch && $selectedContracts)
            <div class="bg-white rounded-2xl shadow-sm border-2 border-red-200 p-6">
                <div class="flex items-start justify-between gap-4 mb-4 flex-wrap">
                    <h3 class="text-sm font-extrabold text-red-900 flex items-center gap-2.5">
                        <span class="w-9 h-9 bg-gradient-to-br from-gik-primary to-red-700 text-white rounded-xl flex items-center justify-center shadow-md">📋</span>
                        Daftar Peserta Akan Dilaporkan <span class="text-red-700">({{ count($selectedContracts) }} orang)</span>
                    </h3>
                    <span class="text-[10px] font-bold text-red-700 bg-red-50 px-3 py-1 rounded-full border border-red-200">⚠️ SATU LAPORAN PER ORANG</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 max-h-[260px] overflow-y-auto pr-1.5">
                    @foreach($selectedContracts as $idx => $c)
                        <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-red-50/50 to-orange-50/50 border border-red-100 rounded-xl hover:from-red-50 hover:to-orange-50 transition">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">{{ $idx + 1 }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-sm text-gray-800 truncate">{{ $c->full_name }}</div>
                                <div class="text-[11px] text-gray-500 truncate">{{ $c->employee_id ?? 'No ID' }} • {{ $c->department ?? '—' }}</div>
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[9px] rounded-full {{ $c->status_badge_class }} flex-shrink-0">{{ $c->status_display_label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7 space-y-5">
            @csrf
            @if($isBatch && $batchIdsStr)
                <input type="hidden" name="_batch_ids" value="{{ $batchIdsStr }}">
            @endif

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-bold text-gray-800 mb-2 block">Tanggal Kejadian <span class="text-red-500">*</span></label>
                    <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 @error('incident_date') border-red-300 bg-red-50 @enderror">
                    <div class="text-[11px] text-gray-400 mt-1">Tidak boleh lebih dari tanggal hari ini</div>
                    @error('incident_date')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="text-sm font-bold text-gray-800 mb-2 block">Jam Kejadian</label>
                    <input type="time" name="incident_time" value="{{ old('incident_time') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 @error('incident_time') border-red-300 bg-red-50 @enderror">
                    <div class="text-[11px] text-gray-400 mt-1">Opsional, isi jika waktu kejadian diketahui pasti</div>
                    @error('incident_time')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-bold text-gray-800 mb-2 block">Jenis & Uraian Pelanggaran <span class="text-red-500">*</span></label>
                <textarea name="description" rows="6" required minlength="10" maxlength="5000" placeholder="Contoh: Terlambat masuk kerja sebanyak 5x dalam 1 minggu terakhir tanpa pemberitahuan / izin. Atau: Terjadi pelanggaran kode etik perusahaan terkait penggunaan fasilitas..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 resize-none @error('description') border-red-300 bg-red-50 @enderror">{{ old('description') }}</textarea>
                <div class="text-[11px] text-gray-400 mt-1">Minimal 10 karakter, jelas dan spesifik. Jelaskan kronologi, frekuensi, dan dampaknya. @if($isBatch)<span class="text-red-600 font-semibold">Uraian ini akan digunakan untuk semua {{ count($selectedContracts) }} peserta.</span>@endif</div>
                @error('description')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-bold text-gray-800 mb-2 block">Sanksi yang Diusulkan Perusahaan</label>
                <textarea name="proposed_sanction" rows="3" maxlength="2000" placeholder="Contoh: Teguran tertulis / Pemotongan insentif / Pembinaan / Pemanggilan orang tua / Sanksi administratif lainnya..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 resize-none @error('proposed_sanction') border-red-300 bg-red-50 @enderror">{{ old('proposed_sanction') }}</textarea>
                <div class="text-[11px] text-gray-400 mt-1">Opsional. Sanksi final ditentukan oleh Tim Rekrutmen Gyokai Connect setelah koordinasi.</div>
                @error('proposed_sanction')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="text-sm font-bold text-gray-800 mb-2 block">Lampiran Bukti (Opsional)</label>
                <div class="border-2 border-dashed border-gray-200 hover:border-red-300 rounded-xl p-5 transition">
                    <input type="file" name="evidence_file" accept="image/*,.pdf,.doc,.docx"
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-gik-primary-dark hover:file:bg-red-100 cursor-pointer">
                </div>
                <div class="text-[11px] text-gray-400 mt-1">Format: JPG, JPEG, PNG, PDF, DOC, DOCX. Maks. 5 MB. @if($isBatch)<span class="text-red-600 font-semibold">File bukti yang sama akan dilampirkan ke semua laporan.</span>@endif</div>
                @error('evidence_file')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            @if($isBatch)
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex gap-3 items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <div class="text-xs text-red-800 font-semibold leading-relaxed">
                            ⚠️ PERINGATAN MODE MASSAL: Data tanggal, uraian, sanksi usulan, dan bukti akan <b>DITERAPKAN KE SEMUA {{ count($selectedContracts) }} PESERTA</b> secara otomatis. Akan dibuat {{ count($selectedContracts) }} nomor laporan terpisah (1 orang = 1 nomor laporan) agar setiap peserta memiliki timeline penanganan masing-masing.
                        </div>
                    </div>
                </div>
            @endif

            <div class="pt-3 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100">
                <div class="text-[11px] text-gray-400 min-w-0 flex-1">
                    ✅ Laporan otomatis tercatat atas nama <strong>{{ auth()->user()->name }}</strong> dari <strong>{{ auth()->user()->company?->company_name ?? 'Perusahaan Anda' }}</strong>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ $backRoute }}" class="px-5 py-3 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-sm font-bold transition">Batal</a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-gik-primary to-red-600 hover:from-gik-primary-dark hover:to-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 hover:shadow-xl transition flex items-center gap-2" @if($isBatch) onclick="return confirm('⚠️ Anda yakin akan MELAPORKAN SECARA MASSAL {{ count($selectedContracts) }} peserta sekaligus? Kronologi & bukti akan sama untuk semua orang. Akan dibuat {{ count($selectedContracts) }} nomor laporan terpisah.')" @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        @if($isBatch)
                            🚨 Kirim Laporan MASSAL ({{ count($selectedContracts) }})
                        @else
                            Kirim Laporan ke Tim Rekrutmen
                        @endif
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="space-y-5">
        @if(!$isBatch && $contract)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-extrabold text-gray-900 mb-4 text-lg">Data Peserta</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-xl font-extrabold shadow-md">
                    {{ $contract->initials }}
                </div>
                <div>
                    <div class="font-extrabold text-gray-900 text-base">{{ $contract->full_name }}</div>
                    <div class="text-xs text-gray-500 font-mono">{{ $contract->employee_id ?? '—' }}</div>
                </div>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Departemen</span><span class="font-semibold text-gray-800">{{ $contract->department }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Jabatan</span><span class="font-semibold text-gray-800">{{ $contract->job_title }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tipe Kontrak</span><span class="font-semibold text-gray-800 uppercase text-xs">{{ $contract->contract_type }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kontrak Berakhir</span><span class="font-semibold {{ $contract->isExpiringSoon(30) ? 'text-amber-700' : 'text-gray-800' }}">{{ optional($contract->contract_end)->format('d M Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $contract->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $contract->status)) }}</span></div>
            </div>
        </div>

        @if($existingReports && $existingReports->count() > 0)
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
            <h3 class="font-extrabold text-red-700 mb-4 text-base flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Laporan Peserta Ini (Perusahaan Anda)
            </h3>
            <div class="space-y-3">
                @foreach($existingReports as $ex)
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <div class="text-[10px] font-mono text-gray-500 font-bold">{{ $ex->report_number }}</div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold {{ $ex->status_badge_class }}">{{ $ex->status_label }}</span>
                    </div>
                    <div class="text-xs text-gray-700 line-clamp-2">{{ \Illuminate\Support\Str::limit($ex->description, 80) }}</div>
                    <div class="text-[10px] text-gray-400 mt-1">{{ optional($ex->incident_date)->format('d M Y') }} • {{ $ex->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @else
        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-sm border-2 border-red-200 p-6">
            <h3 class="font-extrabold text-red-900 mb-4 text-base flex items-center gap-2.5">
                <span class="w-9 h-9 bg-red-700 text-white rounded-full flex items-center justify-center shadow-md">📊</span>
                Ringkasan Laporan Massal
            </h3>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-center p-3 bg-white/70 rounded-xl border border-red-100">
                    <span class="text-xs text-gray-600">Total Peserta Dilaporkan</span>
                    <span class="font-extrabold text-red-800 text-xl">{{ count($selectedContracts) }} 👥</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-white/70 rounded-xl border border-red-100">
                    <span class="text-xs text-gray-600">Total Laporan Dibuat</span>
                    <span class="font-bold text-gray-800 text-sm">{{ count($selectedContracts) }} laporan (1 per orang)</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-white/70 rounded-xl border border-red-100">
                    <span class="text-xs text-gray-600">Pelapor</span>
                    <span class="font-bold text-gray-800 text-xs">{{ auth()->user()->name }}</span>
                </div>
                <div class="text-[11px] text-red-800 bg-white p-3 rounded-xl border border-red-100 mt-2 leading-relaxed">
                    💡 <b>Info:</b> Sistem akan membuat {{ count($selectedContracts) }} laporan terpisah otomatis (1 peserta = 1 nomor laporan). Urutan kronologi, tanggal, lampiran bukti, dan sanksi usulan akan SAMBA untuk semua.
                </div>
            </div>
        </div>
        @endif

        <div class="bg-gradient-to-br from-amber-50 to-red-50 rounded-2xl border border-amber-100 p-6">
            <h4 class="font-extrabold text-amber-800 mb-2 text-sm">📋 Catatan Penting</h4>
            <ul class="text-[11px] text-amber-700 space-y-1.5 list-disc list-inside font-medium leading-relaxed">
                <li>Laporan Anda bersifat resmi & langsung ditindaklanjuti oleh Tim Rekrutmen Gyokai</li>
                <li>Setelah submit, laporan tidak bisa di-edit. Jika perlu koreksi, hubungi Super Admin.</li>
                <li>Status progres penanganan bisa dipantau pada menu Laporan Pelanggaran ini</li>
                <li>Anda akan menerima notifikasi otomatis ketika status laporan diperbarui</li>
            </ul>
        </div>
    </div>
</div>
@endsection
