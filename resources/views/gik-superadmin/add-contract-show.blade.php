@extends(Auth::user()->isSuperAdminRecruitment() ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@section('content')
@php
    $isSuper = Auth::user()->isSuperAdminRecruitment();
    $isHrOwnCompany = !$isSuper && Auth::user()->company_id == $batch->company_id && $batch->status === 'submitted';
    $showForm = $isHrOwnCompany;

    $statusBadge = match($batch->status) {
        'submitted' => ['bg-amber-100', 'text-amber-700', 'SUBMITTED'],
        'reviewed' => ['bg-blue-100', 'text-blue-700', 'REVIEWED'],
        'completed' => ['bg-green-100', 'text-green-700', 'COMPLETED'],
        'draft' => ['bg-gray-100', 'text-gray-600', 'DRAFT'],
        default => ['bg-gray-100', 'text-gray-600', strtoupper($batch->status ?? 'UNKNOWN')],
    };
@endphp

<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">{{ $isSuper ? 'Super Admin Recruitment' : 'HR Company • Seleksi Kandidat' }}</div>
        <div class="flex flex-wrap items-center gap-3 mb-1">
            <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Pengajuan Kandidat #{{ $batch->batch_number }}</h1>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge[1] === 'text-amber-700' ? 'bg-amber-500' : ($statusBadge[1] === 'text-blue-700' ? 'bg-red-50/600' : 'bg-green-500') }}"></span>
                {{ $statusBadge[2] }}
            </span>
        </div>
        <p class="text-gray-500 text-sm mt-1">
            {{ $isSuper ? 'Lihat detail pengajuan beserta hasil seleksi dari HR Company.' : 'Lakukan seleksi kandidat (lolos/tidak lolos) lalu kirim hasil ke Super Admin.' }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($isSuper)
            <a href="{{ route('gik-superadmin.add-contract.list') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Pengajuan
            </a>
        @else
            <a href="{{ route('gik-hrd.add-contract.list') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h3 class="font-extrabold text-gray-800 text-lg">Informasi Batch</h3>
                <p class="text-xs text-gray-500 mt-0.5">Ringkasan data pengajuan kandidat.</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-gik-accent-light text-gik-accent flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Batch Number</div>
                <div class="text-sm font-extrabold text-gray-800 font-mono">{{ $batch->batch_number }}</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tujuan Company</div>
                <div class="text-sm font-extrabold text-gray-800">{{ optional($batch->company)->company_name ?? '-' }} <span class="text-gray-400 font-mono text-xs">({{ optional($batch->company)->company_code ?? '-' }})</span></div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Dibuat Oleh</div>
                <div class="text-sm font-semibold text-gray-800">{{ optional($batch->createdBy)->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tanggal Pengajuan</div>
                <div class="text-sm font-semibold text-gray-800">{{ $batch->created_at ? $batch->created_at->translatedFormat('d F Y • H:i') : '-' }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Catatan untuk HR Company</div>
                <div class="text-sm text-gray-700 p-3 rounded-xl bg-gray-50 border border-gray-100">
                    {{ $batch->notes ?? '— Tidak ada catatan —' }}
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-extrabold text-gray-800 mb-4">Ringkasan Seleksi</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kandidat</span>
                    <span class="text-2xl font-extrabold text-gik-primary">{{ $batch->candidates->count() }}</span>
                </div>
                <div class="w-full h-px bg-gray-100"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-green-700 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>Selected
                    </span>
                    <span class="text-xl font-extrabold text-green-700">{{ $batch->selected_count ?? $batch->candidates->where('status', 'selected')->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-red-700 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>Not Selected
                    </span>
                    <span class="text-xl font-extrabold text-red-700">{{ $batch->not_selected_count ?? $batch->candidates->where('status', 'not_selected')->count() }}</span>
                </div>
                @if($batch->reviewed_at || $batch->status !== 'submitted')
                    <div class="w-full h-px bg-gray-100"></div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Direspon pada</div>
                        <div class="text-xs font-semibold text-gray-700">{{ $batch->reviewed_at ? \Carbon\Carbon::parse($batch->reviewed_at)->translatedFormat('d F Y • H:i') : '—' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($showForm)
    <form method="POST" action="{{ route('gik-hrd.add-contract.submit-selection', $batch) }}" id="selectionForm">
    @csrf
@endif

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-7 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-extrabold text-gray-800 text-lg">Daftar Kandidat</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $showForm ? 'Centang kolom "Lolos Seleksi" untuk kandidat yang LOLOS. Uncheck untuk TIDAK LOLOS.' : ($isSuper ? 'Hasil seleksi final dari HR Company.' : 'Menunggu review oleh HR Company.') }}
            </p>
        </div>
        @if($showForm)
            <div class="flex items-center gap-2 text-xs">
                <button type="button" onclick="setAllCheckboxes(true)" class="px-3 py-1.5 border border-green-200 text-green-700 bg-green-50 hover:bg-green-100 rounded-lg font-bold transition">Pilih Semua Lolos</button>
                <button type="button" onclick="setAllCheckboxes(false)" class="px-3 py-1.5 border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 rounded-lg font-bold transition">Pilih Semua Tolak</button>
            </div>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    @if($showForm || $isSuper)
                        <th class="px-5 py-3.5 text-center w-24">Pilihan</th>
                    @endif
                    <th class="px-5 py-3.5 text-left">Nama Lengkap</th>
                    <th class="px-5 py-3.5 text-left">NIK</th>
                    <th class="px-5 py-3.5 text-left">Tgl Lahir</th>
                    <th class="px-5 py-3.5 text-left">Posisi</th>
                    <th class="px-5 py-3.5 text-left">Alamat</th>
                    <th class="px-5 py-3.5 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($batch->candidates as $cand)
                    @php
                        $candStatus = $cand->status ?? 'pending';
                        $isSelected = $candStatus === 'selected';
                        $isPending = $candStatus === 'pending';
                        $isRejected = $candStatus === 'not_selected';

                        $statusBadgeCand = match($candStatus) {
                            'selected' => ['bg-green-100', 'text-green-700', 'SELECTED • Lolos'],
                            'not_selected' => ['bg-red-100', 'text-red-700', 'NOT SELECTED'],
                            default => ['bg-gray-100', 'text-gray-600', 'PENDING'],
                        };

                        $nik = $cand->national_id ?? '';
                        if (strlen($nik) >= 10) {
                            $nikMasked = substr($nik, 0, 3) . str_repeat('*', max(0, strlen($nik) - 6)) . substr($nik, -3);
                        } else {
                            $nikMasked = $nik;
                        }
                    @endphp
                    <tr class="hover:bg-red-50/60/30 transition">
                        @if($showForm || $isSuper)
                            <td class="px-5 py-4 text-center align-middle">
                                @if($showForm)
                                    <div class="flex flex-col gap-2">
                                        <label class="inline-flex items-center cursor-pointer gap-2">
                                            <input type="radio" name="selections[{{ $cand->id }}]" value="selected"
                                                {{ $isPending || $isSelected ? 'checked' : '' }}
                                                class="w-4 h-4 text-green-600 focus:ring-green-500 cand-radio-lolos" required>
                                            <span class="text-xs font-bold text-green-700">Lolos</span>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer gap-2">
                                            <input type="radio" name="selections[{{ $cand->id }}]" value="not_selected"
                                                {{ $isRejected ? 'checked' : '' }}
                                                class="w-4 h-4 text-red-600 focus:ring-red-500 cand-radio-tolak" required>
                                            <span class="text-xs font-bold text-red-700">Tolak</span>
                                        </label>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-2 opacity-70">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" value="selected" {{ $isSelected ? 'checked' : '' }} disabled
                                                class="w-4 h-4 text-green-600 focus:ring-green-500">
                                            <span class="text-xs font-bold text-green-700">Lolos</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" value="not_selected" {{ $isRejected ? 'checked' : '' }} disabled
                                                class="w-4 h-4 text-red-600 focus:ring-red-500">
                                            <span class="text-xs font-bold text-red-700">Tolak</span>
                                        </label>
                                    </div>
                                @endif
                            </td>
                        @endif
                        <td class="px-5 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                                    {{ strtoupper(substr($cand->first_name ?? $cand->full_name ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">
                                        {{ trim(($cand->first_name ?? '') . ' ' . ($cand->last_name ?? '')) ?: ($cand->full_name ?? '-') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle font-mono text-xs text-gray-700">{{ $nikMasked ?: '-' }}</td>
                        <td class="px-5 py-4 align-middle text-xs text-gray-700">
                            {{ $cand->birth_date ? \Carbon\Carbon::parse($cand->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-gik-accent-light text-gik-accent text-xs font-bold">
                                {{ $cand->job_title ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 align-middle max-w-[260px]">
                            <div class="text-xs text-gray-600 leading-relaxed line-clamp-2">{{ $cand->address ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 align-middle">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $statusBadgeCand[0] }} {{ $statusBadgeCand[1] }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $isSelected ? 'bg-green-500' : ($isRejected ? 'bg-red-500' : 'bg-gray-400') }}
                                "></span>
                                {{ $statusBadgeCand[2] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($showForm)
        <div class="px-7 py-5 border-t border-gray-100 bg-gray-50/80 flex flex-wrap items-center justify-between gap-4 sticky bottom-0 z-10">
            <div class="text-xs text-gray-600">
                <span id="selectedCount" class="font-extrabold text-gik-primary text-base">{{ $batch->candidates->whereIn('status', ['pending', 'selected'])->count() }}</span>
                <span class="ml-1">kandidat dipilih LOLOS dari <span class="font-bold text-gray-700">{{ $batch->candidates->count() }}</span> total</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('gik-hrd.add-contract.list') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 rounded-xl text-sm font-bold transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition shadow-lg shadow-green-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Kirim Hasil Seleksi ke Super Admin (Tim Rekrutmen)
                </button>
            </div>
        </div>
    @endif
</div>

@if($showForm)
    </form>
@endif

@if($showForm)
<script>
function setAllCheckboxes(val) {
    if (val) {
        document.querySelectorAll('.cand-radio-lolos').forEach(r => { if (!r.disabled) r.checked = true; });
    } else {
        document.querySelectorAll('.cand-radio-tolak').forEach(r => { if (!r.disabled) r.checked = true; });
    }
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.cand-radio-lolos:checked:not(:disabled)').length;
    document.getElementById('selectedCount').textContent = count;
}

document.querySelectorAll('.cand-radio-lolos, .cand-radio-tolak').forEach(r => {
    r.addEventListener('change', updateSelectedCount);
});

document.getElementById('selectionForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.cand-radio-lolos:checked:not(:disabled)').length;
    const rejected = document.querySelectorAll('.cand-radio-tolak:checked:not(:disabled)').length;
    if (rejected > 0) {
        if (!confirm(`Anda memilih ${checked} LOLOS dan ${rejected} DITOLAK.\nKandidat tidak lolos akan disimpan sebagai arsip Not Selected.\n\nLanjutkan kirim hasil seleksi?`)) {
            e.preventDefault();
        }
    }
});
</script>
@endif
@endsection
