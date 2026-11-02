@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div class="flex items-start gap-3">
        <a href="{{ route('gik-superadmin.violations.index') }}" class="p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#991B1B]/10 text-[#991B1B] rounded-full text-[11px] font-extrabold font-mono tracking-wide shadow-sm">
                    🔴 {{ $report->report_number }}
                </span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold {{ $report->status_badge_class }} shadow-sm">
                    {{ $report->status_label }}
                </span>
                @if($report->evidence_file)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-bold">📎 Lampiran Tersedia</span>
                @endif
            </div>
            <h1 class="text-[30px] font-black text-gik-primary-dark leading-tight tracking-tight">Tinjauan & Tindak Lanjut Laporan Pelanggaran</h1>
            <p class="text-gray-500 text-sm mt-1.5">Diajukan oleh <strong>{{ $report->reportedBy?->name ?? '-' }}</strong> dari {{ $report->company?->company_name ?? '-' }} • {{ $report->created_at->translatedFormat('l, d F Y • H:i') }} WIB</p>
        </div>
    </div>
    <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs">
        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
        <span class="font-bold text-gray-700">Status Saat Ini:</span>
        <span class="font-extrabold text-gik-primary-dark uppercase tracking-wider">{{ $report->status_label }}</span>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3 shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3 shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div><strong>Error!</strong> {{ session('error') }}</div>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
    <div class="font-extrabold text-sm mb-2">⚠️ Kesalahan input:</div>
    <ul class="text-xs list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-7 py-5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-white flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gik-primary/10 text-gik-primary-dark flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-black text-gray-900">Detail Pelaporan Pelanggaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi yang diserahkan oleh HR Company Pelapor</p>
                </div>
                @if($report->handledBy)
                <div class="text-right">
                    <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Ditangani Oleh</div>
                    <div class="text-sm font-extrabold text-gik-primary-dark">{{ $report->handledBy->name }}</div>
                </div>
                @endif
            </div>
            <div class="p-7 space-y-6">
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-extrabold mb-1">Tanggal Kejadian</div>
                        <div class="text-sm font-black text-gray-800">{{ optional($report->incident_date)->translatedFormat('d M Y') ?? '-' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-extrabold mb-1">Jam Kejadian</div>
                        <div class="text-sm font-black text-gray-800">{{ $report->incident_time ? substr($report->incident_time, 0, 5) . ' WIB' : '-' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-extrabold mb-1">Perusahaan</div>
                        <div class="text-sm font-black text-gray-800 truncate" title="{{ $report->company?->company_name }}">{{ \Illuminate\Support\Str::limit($report->company?->company_name ?? '-', 18) }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="text-[10px] uppercase tracking-widest text-gray-500 font-extrabold mb-1">Pelapor</div>
                        <div class="text-sm font-black text-gray-800">{{ $report->reportedBy?->name ?? '-' }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-[11px] uppercase tracking-widest text-gray-500 font-extrabold mb-2.5 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-gik-primary rounded-full inline-block"></span>
                        Uraian / Deskripsi Pelanggaran
                    </div>
                    <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-5 bg-red-50/60 border-l-4 border-gik-primary rounded-r-xl">
                        {!! nl2br(e($report->description)) !!}
                    </div>
                </div>

                @if($report->proposed_sanction)
                <div>
                    <div class="text-[11px] uppercase tracking-widest text-gray-500 font-extrabold mb-2.5 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-amber-500 rounded-full inline-block"></span>
                        Sanksi Yang Diusulkan Oleh Perusahaan
                    </div>
                    <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-5 bg-amber-50/70 border-l-4 border-amber-500 rounded-r-xl">
                        {!! nl2br(e($report->proposed_sanction)) !!}
                    </div>
                </div>
                @endif

                @if($report->evidence_file)
                <div>
                    <div class="text-[11px] uppercase tracking-widest text-gray-500 font-extrabold mb-2.5 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-blue-500 rounded-full inline-block"></span>
                        Lampiran Bukti Pelanggaran
                    </div>
                    <a href="{{ Storage::disk('public')->url($report->evidence_file) }}" target="_blank"
                        class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 border border-blue-200 rounded-xl shadow-sm transition group">
                        <div class="w-11 h-11 rounded-xl bg-blue-500 text-white flex items-center justify-center group-hover:scale-110 transition shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-extrabold text-blue-900">Lihat / Unduh Bukti Pelanggaran</div>
                            <div class="text-[11px] text-blue-700 font-medium">Klik untuk membuka di tab baru • {{ pathinfo($report->evidence_file, PATHINFO_EXTENSION) }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 text-blue-500 ml-auto group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-700 flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-black text-gray-900">Update Status Penanganan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perubahan status akan dicatat sebagai riwayat & notifikasi otomatis terkirim ke HR Company pelapor.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('gik-superadmin.violations.update-status', $report) }}" class="space-y-5">
                @csrf
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm font-extrabold text-gray-800 mb-2 block flex items-center gap-2">
                            Status Penanganan Baru <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required class="w-full px-4 py-3.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary cursor-pointer font-semibold">
                            @foreach([
                                'pending_review' => ['label' => '⏳ Menunggu Tinjauan', 'class' => 'text-amber-700'],
                                'contacted' => ['label' => '📞 Sudah Dihubungi Perusahaan / Peserta', 'class' => 'text-blue-700'],
                                'in_process' => ['label' => '⚙️ Sedang Dalam Proses Tindak Lanjut', 'class' => 'text-orange-700'],
                                'resolved' => ['label' => '✅ Selesai / Terselesaikan', 'class' => 'text-green-700'],
                                'rejected' => ['label' => '⛔ Ditolak (Laporan Tidak Valid)', 'class' => 'text-red-700'],
                            ] as $val => $opt)
                            <option value="{{ $val }}" {{ ($report->status ?? '') === $val ? 'selected' : '' }} class="{{ $opt['class'] }} font-semibold">
                                {{ $opt['label'] }}
                            </option>
                            @endforeach
                        </select>
                        <div class="text-[10px] text-gray-400 mt-1.5 font-medium">Status saat ini: <span class="font-extrabold text-gray-600 uppercase">{{ $report->status_label }}</span></div>
                    </div>
                    <div>
                        <label class="text-sm font-extrabold text-gray-800 mb-2 block flex items-center gap-2">
                            Penanggung Jawab
                        </label>
                        <div class="w-full px-4 py-3.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-700 font-bold flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#991B1B] to-[#DC2626] flex items-center justify-center text-white text-[10px] font-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-gray-800 leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] text-gik-primary-dark font-extrabold uppercase tracking-wide">Super Admin Rekrutmen</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-extrabold text-gray-800 mb-2 block flex items-center gap-2">
                        Catatan / Alasan Perubahan Status
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-700 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            ⚠️ Wajib diisi untuk status Selesai / Ditolak
                        </span>
                    </label>
                    <textarea name="notes" rows="4" maxlength="3000" placeholder="Tuliskan catatan tindak lanjut, hasil komunikasi, keputusan yang diambil, alasan penolakan, instruksi ke pihak perusahaan / peserta, dll. Catatan ini akan terlihat oleh HR Company pelapor."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-gik-primary resize-none @error('notes') border-red-300 bg-red-50 @enderror">{{ old('notes') }}</textarea>
                    <div class="flex items-center justify-between text-[10px] text-gray-400 mt-1.5 font-medium">
                        <span>Catatan akan menjadi bagian dari timeline audit trail.</span>
                        <span class="font-mono font-bold">{{ strlen(old('notes') ?? '') }} / 3000 karakter</span>
                    </div>
                    @error('notes')<div class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</div>@enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-5 border-t border-gray-100">
                    <div class="flex items-start gap-2.5 max-w-md p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="text-lg leading-none">🔔</div>
                        <div class="text-[11px] text-amber-800 leading-relaxed font-medium">
                            <strong class="font-extrabold">Notifikasi otomatis</strong> akan dikirim ke HR Company pelapor (<strong>{{ $report->reportedBy?->name ?? '-' }}</strong>)
                            @if($report->company?->company_name) di {{ $report->company->company_name }} @endif
                            beserta catatan Anda di atas, sesaat setelah tombol Submit ditekan.
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('gik-superadmin.violations.index') }}" class="px-5 py-3 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-sm font-bold transition">
                            Kembali
                        </a>
                        <button type="submit"
                            onclick="return confirm('⚠️ Anda akan memperbarui status laporan ini. Tindakan ini akan tersimpan di riwayat audit & mengirim notifikasi ke HR Company. Lanjutkan?')"
                            class="px-7 py-3 bg-gradient-to-r from-gik-primary via-[#B91C1C] to-gik-primary-dark hover:shadow-lg hover:shadow-red-200 text-white rounded-xl font-black tracking-wide transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Simpan & Perbarui Status
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-100 to-pink-100 text-purple-700 flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-gray-900">Timeline Riwayat Penanganan</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Audit trail lengkap perubahan status beserta aktor & catatan.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-[11px] font-extrabold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $report->statusLogs->count() }} Entri
                </span>
            </div>

            <div class="relative">
                <div class="absolute left-[22px] top-0 bottom-0 w-1 bg-gradient-to-b from-red-300 via-gray-200 to-gray-100 rounded-full border-l border-dashed border-red-200/50"></div>
                <div class="space-y-5">
                    @forelse($report->statusLogs->sortByDesc('created_at')->values() as $idx => $log)
                    <div class="relative pl-16">
                        @php
                        $statusColor = match($log->new_status) {
                            'pending_review' => 'from-amber-400 to-amber-600',
                            'contacted' => 'from-blue-400 to-blue-600',
                            'in_process' => 'from-orange-400 to-orange-600',
                            'resolved' => 'from-green-400 to-green-600',
                            'rejected' => 'from-red-400 to-red-600',
                            default => 'from-gray-400 to-gray-600',
                        };
                        @endphp
                        <div class="absolute left-0 top-0.5">
                            <div class="w-12 h-12 rounded-full bg-white border-[3px] border-gray-200 flex items-center justify-center shadow-md z-10">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-br {{ $statusColor }} shadow-inner ring-2 ring-white"></div>
                            </div>
                        </div>
                        <div class="p-5 bg-gradient-to-br from-gray-50 to-white border border-gray-100 rounded-2xl hover:shadow-md hover:-translate-y-0.5 transition relative overflow-hidden">
                            @if($idx === 0)
                            <div class="absolute top-3 right-4 inline-flex items-center gap-1 px-2.5 py-0.5 bg-gik-primary text-white rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm">
                                ⭐ Terbaru
                            </div>
                            @endif
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold {{ $log->new_status_badge_class }} shadow-sm">
                                        {{ $log->new_status_label }}
                                    </span>
                                    @if($log->old_status && $log->old_status !== $log->new_status)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 font-bold font-mono">
                                        <span class="px-2 py-0.5 bg-gray-100 rounded-md line-through opacity-70">{{ $log->old_status_label }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] font-extrabold text-gik-primary-dark">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold font-mono">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                </div>
                            </div>

                            @if($log->notes)
                            <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                {!! nl2br(e($log->notes)) !!}
                            </div>
                            @else
                            <div class="text-[12px] text-gray-400 italic py-2 px-3 bg-white/60 rounded-lg border border-dashed border-gray-200 inline-block">
                                Tidak ada catatan tambahan dari pihak {{ $log->updatedBy?->isSuperAdminRecruitment() ? 'Tim Rekrutmen' : 'HR Company' }}.
                            </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#991B1B] to-[#DC2626] flex items-center justify-center text-white text-[10px] font-black shadow-sm">
                                        {{ $log->updatedBy ? strtoupper(substr($log->updatedBy->name, 0, 2)) : 'SY' }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-extrabold text-gray-800">{{ $log->updatedBy?->name ?? 'Sistem Otomatis' }}</div>
                                        <div class="text-[10px] font-bold uppercase tracking-wide {{ $log->updatedBy?->isSuperAdminRecruitment() ? 'text-gik-primary-dark' : 'text-green-700' }}">
                                            {{ $log->updatedBy?->isSuperAdminRecruitment() ? 'Super Admin Rekrutmen • Gyokai' : ($log->updatedBy?->isHrdCompany() ? 'HR Company Pelapor' : 'Sistem') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[10px] font-mono font-extrabold text-gray-400 uppercase">
                                    Log-{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="pl-16 py-12 text-center border-2 border-dashed border-gray-200 rounded-2xl">
                        <div class="text-4xl mb-3 opacity-40">📋</div>
                        <div class="text-sm text-gray-400 italic font-medium">Belum ada riwayat penanganan</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 overflow-hidden relative">
            <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-gradient-to-br from-red-100 to-orange-50 opacity-70"></div>
            <h3 class="relative text-sm font-black uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                <span class="w-1 h-5 rounded-full bg-gradient-to-b from-gik-primary to-orange-400 inline-block"></span>
                Identitas Peserta Terlapor
            </h3>
            <div class="relative flex items-center gap-4 mb-5 pb-5 border-b border-gray-100">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-red-500 via-[#991B1B] to-[#7F1D1D] flex items-center justify-center text-white text-2xl font-black shadow-xl shadow-red-200 ring-4 ring-white">
                    {{ $report->intern?->initials ?? '??' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-lg font-black text-gray-900 leading-tight truncate">{{ $report->intern?->full_name ?? '-' }}</div>
                    <div class="text-[11px] font-bold text-gik-primary-dark uppercase tracking-wide mt-0.5">{{ $report->intern?->employee_id ?? 'Belum ada ID' }}</div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <span class="inline-flex px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md text-[10px] font-bold">{{ $report->intern?->department ?? 'Departemen' }}</span>
                        <span class="inline-flex px-2 py-0.5 bg-red-50 text-red-700 rounded-md text-[10px] font-bold">{{ $report->intern?->job_title ?? 'Jabatan' }}</span>
                    </div>
                </div>
            </div>
            <div class="relative space-y-3 text-sm">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-500 text-xs font-bold uppercase">Status Kerja</span>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black {{ $report->intern?->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $report->intern?->status ?? '-')) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-500 text-xs font-bold uppercase">Kontrak</span>
                    <span class="text-gray-800 text-xs font-extrabold">{{ strtoupper($report->intern?->contract_type ?? '-') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-500 text-xs font-bold uppercase">TMT Awal</span>
                    <span class="text-gray-800 text-xs font-extrabold">{{ optional($report->intern?->contract_start)->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl {{ $report->intern?->isExpiringSoon(30) ? 'bg-red-50 border border-red-100' : 'bg-gray-50' }}">
                    <span class="text-gray-500 text-xs font-bold uppercase">Berakhir</span>
                    <span class="text-xs font-extrabold {{ $report->intern?->isExpiringSoon(30) ? 'text-red-700' : 'text-gray-800' }}">{{ optional($report->intern?->contract_end)->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="pt-2">
                    <a href="{{ route('gik-superadmin.employees') }}" class="text-[11px] font-extrabold text-gik-accent hover:text-gik-primary hover:underline inline-flex items-center gap-1">
                        Lihat data lengkap peserta →
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 overflow-hidden relative">
            <div class="absolute -left-10 -top-10 w-32 h-32 rounded-full bg-gradient-to-br from-blue-50 to-indigo-50 opacity-70"></div>
            <h3 class="relative text-sm font-black uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                <span class="w-1 h-5 rounded-full bg-gradient-to-b from-blue-400 to-indigo-400 inline-block"></span>
                HR Company Pelapor
            </h3>
            <div class="relative">
                <div class="text-base font-black text-gray-900 leading-snug mb-3">{{ $report->company?->company_name ?? '-' }}</div>
                <div class="flex items-center gap-3 p-3.5 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl mb-4">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[10px] uppercase font-black tracking-wider text-green-700">Kontak Pelapor</div>
                        <div class="text-sm font-black text-gray-900 truncate">{{ $report->reportedBy?->name ?? '-' }}</div>
                        <div class="text-[11px] text-gray-600 truncate font-medium">{{ $report->reportedBy?->email ?? 'Tidak ada email' }}</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    @if($report->company?->company_phone)
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <span class="text-gray-700 font-semibold">{{ $report->company->company_phone }}</span>
                    </div>
                    @endif
                    @if($report->company?->company_email)
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="text-gray-700 font-semibold truncate">{{ $report->company->company_email }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                <span class="w-1 h-5 rounded-full bg-gradient-to-b from-green-400 to-teal-400 inline-block"></span>
                Ringkasan Langkah Progres
            </h3>
            @php
            $flow = [
                ['key' => 'pending_review', 'icon' => '📝', 'title' => 'Laporan Masuk', 'desc' => 'Laporan diterima & menunggu review SA'],
                ['key' => 'contacted', 'icon' => '📞', 'title' => 'Telah Dihubungi', 'desc' => 'Koordinasi dengan pihak terkait'],
                ['key' => 'in_process', 'icon' => '⚖️', 'title' => 'Proses Mediasi', 'desc' => 'Proses investigasi & keputusan'],
                ['key' => 'closed', 'icon' => '🎯', 'title' => 'Keputusan Final', 'desc' => 'Selesai ditangani / ditolak'],
            ];
            $reachedIdx = match($report->status) {
                'pending_review' => 0,
                'contacted' => 1,
                'in_process' => 2,
                'resolved', 'rejected' => 3,
                default => -1,
            };
            @endphp
            <div class="space-y-0 relative">
                <div class="absolute left-[15px] top-8 bottom-8 w-0.5 bg-gray-200 border-l border-dashed border-gray-300"></div>
                @foreach($flow as $i => $step)
                @php
                $done = $i <= $reachedIdx;
                $isFinal = $i === 3 && in_array($report->status, ['resolved', 'rejected']);
                @endphp
                <div class="relative pl-10 pb-5 last:pb-0">
                    <div class="absolute left-0 top-0 w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $done ? ($isFinal ? 'bg-gradient-to-br from-green-500 to-emerald-600 border-green-400 text-white' : 'bg-gradient-to-br from-gik-primary to-red-600 border-red-400 text-white') : 'bg-white border-gray-300 text-gray-400' }} shadow-sm">
                        @if($done)
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><circle cx="12" cy="12" r="3"/></svg>
                        @endif
                    </div>
                    <div class="flex items-start gap-2 flex-wrap">
                        <span class="text-lg leading-none">{{ $step['icon'] }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-black {{ $done ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['title'] }}</div>
                            <div class="text-[11px] {{ $done ? 'text-gray-600' : 'text-gray-400' }} font-medium leading-snug mt-0.5">{{ $step['desc'] }}</div>
                            @if($isFinal)
                            <div class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-extrabold {{ $report->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                Hasil: {{ strtoupper($report->status_label) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
