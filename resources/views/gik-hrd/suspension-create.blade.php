@extends('layouts.gik-hrd')

@section('page-title', $pageTitle ?? 'Ajukan Pemberhentian Sementara')

@section('content')
@php
    $batchIdsStr = isset($batchIds) && count($batchIds) ? implode(',', $batchIds) : '';
    $formAction = $isBatch
        ? route('gik-hrd.suspensions.store', 0)
        : route('gik-hrd.suspensions.store', $contract->id);
    $confirmText = $isBatch
        ? 'Apakah Anda yakin akan mengajukan pemberhentian sementara SECARA MASSAL untuk ' . count($selectedContracts) . ' peserta sekaligus? Periode dan alasan akan sama untuk semua peserta. Data yang disubmit tidak bisa diedit kembali.'
        : 'Apakah Anda yakin akan mengajukan pemberhentian sementara untuk ' . $contract->full_name . '? Data yang disubmit tidak bisa diedit kembali.';
@endphp

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gik-primary-dark">⏸ {{ $pageTitle ?? 'Ajukan Pemberhentian Sementara' }}</h2>
        <p class="text-gray-500 mt-1 text-sm">
            @if($isBatch)
                Mode <span class="font-bold text-fuchsia-800 bg-fuchsia-50 px-2 py-0.5 rounded">MASSAL</span> — Periode & alasan akan sama untuk semua <b>{{ count($selectedContracts) }} peserta</b>.
            @else
                Isi formulir di bawah untuk mengajukan pemberhentian sementara peserta magang.
            @endif
        </p>
    </div>
    <a href="{{ route('gik-hrd.suspensions.index') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">← Kembali</a>
</div>

@if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('warning') }}
    </div>
@endif
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        <ul class="list-disc ml-4 space-y-1 text-sm">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        @if($isBatch && $selectedContracts)
            <div class="bg-white rounded-xl shadow-sm border-2 border-fuchsia-200 p-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <h4 class="text-sm font-extrabold text-fuchsia-900 flex items-center gap-2">
                        <span class="w-8 h-8 bg-gradient-to-br from-purple-600 to-fuchsia-700 text-white rounded-lg flex items-center justify-center shadow-sm">📋</span>
                        Daftar Peserta dalam Pengajuan Massal <span class="text-fuchsia-700">({{ count($selectedContracts) }} orang)</span>
                    </h4>
                    <span class="text-[10px] font-bold text-fuchsia-700 bg-fuchsia-50 px-2.5 py-1 rounded-full border border-fuchsia-200">MODE MASSAL</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-[240px] overflow-y-auto pr-1">
                    @foreach($selectedContracts as $idx => $c)
                        <div class="flex items-center gap-2.5 p-2.5 bg-gradient-to-r from-purple-50/60 to-fuchsia-50/60 border border-fuchsia-100 rounded-lg hover:from-purple-50 hover:to-fuchsia-50 transition">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white flex items-center justify-center text-[11px] font-bold shadow-sm flex-shrink-0">{{ $idx + 1 }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs text-gray-800 truncate">{{ $c->full_name }}</div>
                                <div class="text-[10px] text-gray-500 truncate">{{ $c->employee_id ?? 'No ID' }} • {{ $c->job_title ?? $c->position ?? '-' }}</div>
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[9px] rounded-full {{ $c->status_badge_class }} flex-shrink-0">{{ $c->status_display_label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" id="suspension-form">
            @csrf
            @if($isBatch && $batchIdsStr)
                <input type="hidden" name="_batch_ids" value="{{ $batchIdsStr }}">
            @endif
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="border-b border-gray-100 pb-4 -mx-6 -mt-6 px-6 pt-6 bg-gradient-to-r from-purple-50 to-fuchsia-50/50 border-b-purple-100 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-600 text-white p-2.5 rounded-lg shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-purple-900">Form Pengajuan Pemberhentian Sementara</h3>
                            <p class="text-xs text-purple-700/80">Pemberhentian sementara biasanya diajukan karena kondisi perusahaan (bukan pelanggaran peserta).</p>
                        </div>
                    </div>
                </div>

                @if(!$isBatch && $contract)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap Peserta</label>
                        <div class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-semibold">{{ $contract->full_name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Posisi / Jabatan</label>
                        <div class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 font-semibold">{{ $contract->position ?? '-' }}</div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Mulai Pemberhentian <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none @error('start_date') border-red-400 ring-red-100 @enderror" required>
                        @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Selesai / Berakhir <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', today()->addDays(30)->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none @error('end_date') border-red-400 ring-red-100 @enderror" required>
                        @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pengaju</label>
                    <div class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                        <span class="font-semibold">{{ $u->name }}</span>
                        <span class="text-gray-400 mx-2">•</span>
                        <span class="text-xs text-gray-500">{{ $u->company?->company_name ?? '-' }}</span>
                        <span class="ml-2 inline-block px-2 py-0.5 text-[10px] bg-purple-100 text-purple-700 rounded-full font-semibold">AUTO-FILLED</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alasan Pemberhentian <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">min. 20 karakter</span></label>
                    <textarea name="reason" rows="6" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none @error('reason') border-red-400 ring-red-100 @enderror" placeholder="Contoh: Perusahaan sedang melakukan perampingan divisi operasional, sehingga perlu menunda aktivitas magang selama 1 bulan. Keputusan ini bukan karena kesalahan peserta, melainkan penyesuaian kebutuhan internal perusahaan. Setelah periode selesai, peserta diharapkan dapat kembali masuk seperti biasa." required>{{ old('reason') }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 ml-auto" id="reason-counter">0 / 20+ karakter</p>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs text-amber-800 space-y-1">
                            <p class="font-semibold">Catatan Penting Sebelum Submit:</p>
                            <ul class="list-disc ml-4 space-y-0.5">
                                <li>Pengajuan yang sudah disubmit <b>TIDAK DAPAT DIUBAH</b> oleh HRD (read-only), hanya bisa dilihat statusnya.</li>
                                <li>Setelah status menjadi <b>Disetujui</b>, peserta otomatis tampil berstatus <b>Diberhentikan Sementara</b> pada daftar peserta.</li>
                                <li>Jika <b>Ditolak</b>, peserta tetap berstatus Aktif seperti biasa. Anda akan mendapatkan notifikasi perubahan status via Inbox.</li>
                                <li>Setelah tanggal selesai terlewati, peserta <b>otomatis kembali Aktif</b> (tidak perlu submit kembali).</li>
                                @if($isBatch)
                                <li class="text-fuchsia-800 font-semibold bg-fuchsia-50 px-2 py-0.5 rounded mt-1">⚠️ Mode Massal: Periode & alasan akan diterapkan ke SEMUA {{ count($selectedContracts) }} peserta sekaligus.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 flex-wrap">
                    <a href="{{ route('gik-hrd.suspensions.index') }}" class="px-5 py-2.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">Batal</a>
                    <button type="submit" class="px-6 py-2.5 text-sm bg-purple-700 hover:bg-fuchsia-900 text-white rounded-lg font-semibold shadow-md transition-colors" onclick="return confirm('{{ $confirmText }}')">
                        📤 {{ $isBatch ? 'Submit Pengajuan Massal ('.count($selectedContracts).')' : 'Submit Pengajuan' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="space-y-5">
        @if(!$isBatch && $contract)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-gik-primary/10 text-gik-primary-dark rounded-full flex items-center justify-center text-xs">👤</span>
                Identitas Peserta Magang
            </h4>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Nama</div>
                    <div class="font-semibold text-gray-800">{{ $contract->full_name }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Posisi / Job Role</div>
                    <div class="text-gray-700">{{ $contract->position ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">NIM / NIK</div>
                    <div class="text-gray-700">{{ $contract->student_id_number ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Kampus / Sekolah</div>
                    <div class="text-gray-700">{{ $contract->institution_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Status Saat Ini</div>
                    <div><span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full {{ $contract->status_badge_class }}">{{ $contract->status_display_label }}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-purple-100 text-purple-800 rounded-full flex items-center justify-center text-xs">🕒</span>
                Riwayat Pengajuan Orang Ini
            </h4>
            @if($previousRequests && $previousRequests->count() > 0)
                <div class="space-y-3">
                    @foreach($previousRequests as $pr)
                        <div class="border-l-2 border-purple-200 pl-3 py-1">
                            <div class="flex items-center justify-between">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $pr->status_badge_class }}">{{ $pr->status_label }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $pr->request_number }}</span>
                            </div>
                            <div class="text-xs text-gray-700 mt-1 font-medium">{{ $pr->period_label }} ({{ $pr->total_days }} hari)</div>
                            <div class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">{{ $pr->reason }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ optional($pr->created_at)->format('d M Y') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-xs text-gray-400 italic py-4 text-center">Belum ada riwayat pengajuan pemberhentian sementara untuk peserta ini.</div>
            @endif
        </div>
        @else
        <div class="bg-gradient-to-br from-fuchsia-50 to-purple-50 rounded-xl shadow-sm border-2 border-fuchsia-200 p-5">
            <h4 class="text-sm font-extrabold text-fuchsia-900 mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-fuchsia-700 text-white rounded-full flex items-center justify-center shadow-sm">📊</span>
                Ringkasan Mode Massal
            </h4>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-center p-2.5 bg-white/60 rounded-lg border border-fuchsia-100">
                    <span class="text-xs text-gray-600">Total Peserta</span>
                    <span class="font-extrabold text-fuchsia-900 text-lg">{{ count($selectedContracts) }} 👥</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/60 rounded-lg border border-fuchsia-100">
                    <span class="text-xs text-gray-600">Pengaju</span>
                    <span class="font-bold text-gray-800 text-xs">{{ $u->name }}</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/60 rounded-lg border border-fuchsia-100">
                    <span class="text-xs text-gray-600">Perusahaan</span>
                    <span class="font-bold text-gray-800 text-xs">{{ $u->company?->company_name ?? '-' }}</span>
                </div>
                <div class="text-[10px] text-fuchsia-800 bg-white p-2.5 rounded-lg border border-fuchsia-100 mt-2 leading-relaxed">
                    💡 <b>Tips:</b> Periode tanggal & alasan pemberhentian akan SAMBA diisi untuk semua {{ count($selectedContracts) }} peserta. Setelah submit, setiap peserta akan memiliki nomor pengajuan masing-masing yang bisa dilacak secara individual.
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    const ta = document.querySelector('textarea[name="reason"]');
    const ctr = document.getElementById('reason-counter');
    if (ta && ctr) {
        const update = () => ctr.textContent = `${ta.value.length} / 20+ karakter`;
        update();
        ta.addEventListener('input', update);
    }
})();
</script>
@endpush
