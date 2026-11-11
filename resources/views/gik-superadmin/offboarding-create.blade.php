@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin • Operasional Siklus Kontrak</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Proses Off-boarding Langsung</h1>
        <p class="text-gray-500 text-sm mt-1">Pilih perusahaan dan peserta yang akan di-offboard secara langsung oleh Super Admin Rekrutmen.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.offboarding.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Off-boarding
        </a>
    </div>
</div>

<form method="POST" action="{{ route('gik-superadmin.offboarding.store') }}" id="offboardForm" class="space-y-6">
    @csrf

    <!-- Card 1: Pilih Perusahaan -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
        <h2 class="text-lg font-extrabold text-gray-800 mb-2">1. Pilih Perusahaan Mitra</h2>
        <p class="text-xs text-gray-500 mb-4">Pilih perusahaan tempat peserta saat ini ditempatkan untuk memuat daftar peserta aktif.</p>

        <div class="max-w-md">
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Perusahaan Klien <span class="text-red-500">*</span></label>
            <select name="company_id" id="companySelect" required onchange="window.location.href='{{ route('gik-superadmin.offboarding.create') }}?company_id=' + this.value" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">-- Pilih Perusahaan --</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ $selectedCompanyId == $c->id ? 'selected' : '' }}>
                        {{ $c->company_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedCompanyId)
        <!-- Card 2: Pilih Peserta yang Di-offboard -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-gray-800 mb-1">2. Pilih Peserta yang Akan Di-offboard</h2>
                    <p class="text-xs text-gray-500">Centang satu atau beberapa peserta yang masa kontraknya telah selesai atau dipulangkan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="rounded text-gik-primary focus:ring-0 w-4 h-4">
                        Pilih Semua ({{ $participants->count() }})
                    </label>
                </div>
            </div>

            @if($participants->isEmpty())
                <div class="py-12 text-center text-gray-500 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2 font-bold">ℹ️</div>
                    <p class="text-sm font-bold text-gray-700">Tidak ada peserta aktif di perusahaan ini.</p>
                    <p class="text-xs text-gray-400 mt-1">Seluruh peserta mungkin telah di-offboard atau belum ada data peserta terdaftar.</p>
                </div>
            @else
                <div class="overflow-x-auto border border-gray-100 rounded-2xl">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3 text-left w-12">Pilih</th>
                                <th class="px-5 py-3 text-left">Nama Peserta</th>
                                <th class="px-5 py-3 text-left">NIK / Employee ID</th>
                                <th class="px-5 py-3 text-left">Departemen / Posisi</th>
                                <th class="px-5 py-3 text-left">Akhir Kontrak</th>
                                <th class="px-5 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($participants as $p)
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-5 py-3.5">
                                        <input type="checkbox" name="contract_ids[]" value="{{ $p->id }}" class="participant-checkbox rounded text-gik-primary focus:ring-0 w-4 h-4 cursor-pointer" onchange="updateSelectedCount()">
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-gray-800">
                                        {{ $p->full_name }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-gray-600">
                                        {{ $p->national_id ?? $p->employee_id ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-gray-600">
                                        <div class="font-bold text-gray-700">{{ $p->job_title ?? '—' }}</div>
                                        <div class="text-gray-400">{{ $p->department ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-gray-700">
                                        {{ optional($p->contract_end)->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status_badge_class }}">
                                            {{ strtoupper($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-xs text-gray-500 font-semibold" id="selectionCountNotice">
                    Terpilih: 0 peserta
                </div>
            @endif
        </div>

        @if($participants->isNotEmpty())
            <!-- Card 3: Detail Tanggal & Alasan Off-boarding -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
                <h2 class="text-lg font-extrabold text-gray-800 mb-1">3. Tanggal Efektif & Alasan Off-boarding</h2>
                <p class="text-xs text-gray-500 mb-5">Informasi ini akan dicatat dalam histori operasional dan notifikasi ke HR Company.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Efektif Keluar <span class="text-red-500">*</span></label>
                        <input type="date" name="off_boarding_effective_date" value="{{ old('off_boarding_effective_date', now()->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <p class="text-[11px] text-gray-400 mt-1">Tanggal resmi peserta tidak lagi aktif bekerja di perusahaan ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori / Alasan Off-boarding <span class="text-red-500">*</span></label>
                        <select name="reason_preset" id="reasonPreset" onchange="applyReasonPreset(this.value)" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="">-- Pilih Kategori Alasan --</option>
                            <option value="Selesai Masa Kontrak Penuh">Selesai Masa Kontrak Penuh</option>
                            <option value="Pengunduran Diri (Resign) atas Permintaan Sendiri">Pengunduran Diri (Resign) atas Permintaan Sendiri</option>
                            <option value="Pemulangan / Terminasi akibat Pelanggaran Disiplin">Pemulangan / Terminasi akibat Pelanggaran Disiplin</option>
                            <option value="Alasan Kesehatan / Medis (Tidak Memungkinkan Melanjutkan)">Alasan Kesehatan / Medis (Tidak Memungkinkan Melanjutkan)</option>
                            <option value="Penutupan Proyek / Efisiensi Perusahaan Mitra">Penutupan Proyek / Efisiensi Perusahaan Mitra</option>
                            <option value="Lainnya">Lainnya (Tulis Manual)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Penjelasan Detail Alasan <span class="text-red-500">*</span></label>
                    <textarea name="off_boarding_reason" id="offBoardingReason" rows="3" required placeholder="Tuliskan alasan lengkap off-boarding..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('off_boarding_reason') }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan Internal Super Admin (Opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan operasional internal..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('gik-superadmin.offboarding.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memproses status off-boarding untuk peserta yang dipilih? Tindakan ini akan mengubah status peserta menjadi Off-boarded.')" class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-sm font-bold shadow-md shadow-orange-200 transition">
                        ✓ Eksekusi Off-boarding
                    </button>
                </div>
            </div>
        @endif
    @endif
</form>

@push('scripts')
<script>
function toggleSelectAll(master) {
    const cbs = document.querySelectorAll('.participant-checkbox');
    cbs.forEach(cb => cb.checked = master.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const total = document.querySelectorAll('.participant-checkbox').length;
    const checked = document.querySelectorAll('.participant-checkbox:checked').length;
    const notice = document.getElementById('selectionCountNotice');
    if (notice) {
        notice.innerText = `Terpilih: ${checked} dari ${total} peserta`;
    }
    const master = document.getElementById('selectAllCheckbox');
    if (master) {
        master.checked = (total > 0 && checked === total);
    }
}

function applyReasonPreset(val) {
    if (val && val !== 'Lainnya') {
        document.getElementById('offBoardingReason').value = val;
    }
}
</script>
@endpush
@endsection

