@extends('layouts.gik-hrd')

@section('content')
@php
    $isExtend = ($type === 'extend_contract');
    $submitColor = $isExtend
        ? 'bg-gik-primary hover:bg-gik-primary-dark shadow-blue-600/20'
        : 'bg-orange-500 hover:bg-orange-600 shadow-orange-500/20';
    $pageBadgeColor = $isExtend ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700';
    $pageAccent = $isExtend ? 'text-blue-700' : 'text-orange-600';
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <div class="text-xs {{ $pageAccent }} font-bold uppercase tracking-widest">
                HR Company • {{ $isExtend ? 'Contract Extension' : 'Employee Off-boarding' }}
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $pageBadgeColor }}">
                BATCH MODE
            </span>
        </div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">
            {{ $isExtend ? 'Extend Contract' : 'Off-boarding' }} — Pilih Peserta Aktif
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Pilih lebih dari 1 peserta sekaligus (batch). Hanya peserta aktif milik Company Anda yang tampil (Isolasi Data).
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-hrd.operations') }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Operations
        </a>
    </div>
</div>

<form method="GET" action="{{ route('gik-hrd.batch.select', $type) }}" class="mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[240px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search nama / NIK / posisi..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent text-sm">
        </div>
        @if($isExtend)
            <label class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="expiring" value="1" {{ request('expiring') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gik-primary">
                Hanya tampilkan kontrak < 30 hari
            </label>
        @endif
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-xs font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari
        </button>
        @if(request('search') || request('expiring'))
            <a href="{{ route('gik-hrd.batch.select', $type) }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-50 transition">Reset</a>
        @endif
    </div>
</form>

<form method="POST" action="{{ route('gik-hrd.batch.submit', $type) }}" id="batchForm">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-extrabold text-gray-800">{{ $isExtend ? 'Peserta Aktif — Perpanjang Kontrak' : 'Peserta Aktif — Proses Off-boarding' }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Centang baris untuk memilih. Input di kolom kanan <span class="font-bold text-red-500">hanya aktif</span> jika baris dicentang.
            </p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="batchTable">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3.5 text-center w-14">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="selectAll" class="w-5 h-5 rounded-md border-gray-300 text-gik-primary focus:ring-gik-accent cursor-pointer">
                        </label>
                    </th>
                    <th class="px-4 py-3.5 text-left">Nama</th>
                    <th class="px-4 py-3.5 text-left">NIK/NIP</th>
                    <th class="px-4 py-3.5 text-left">Tgl Lahir</th>
                    <th class="px-4 py-3.5 text-left">Awal Kontrak</th>
                    <th class="px-4 py-3.5 text-left">Habis Kontrak</th>
                    <th class="px-4 py-3.5 text-left">Alamat</th>
                    @if($isExtend)
                        <th class="px-4 py-3.5 text-left {{ $pageAccent }} bg-red-50/60/70 border-l border-blue-100">
                            <div class="font-extrabold">Perpanjang sampai</div>
                            <div class="font-normal opacity-70">(Habis Kontrak BARU)</div>
                        </th>
                        <th class="px-4 py-3.5 text-left {{ $pageAccent }} bg-red-50/60/70">Catatan</th>
                    @else
                        <th class="px-4 py-3.5 text-left {{ $pageAccent }} bg-orange-50/60 border-l border-orange-100">
                            <div class="font-extrabold">Tgl Efektif</div>
                            <div class="font-normal opacity-70">Off-boarding</div>
                        </th>
                        <th class="px-4 py-3.5 text-left {{ $pageAccent }} bg-orange-50/60">
                            <div class="font-extrabold">Alasan / Catatan</div>
                            <div class="font-normal opacity-70 text-red-500">(WAJIB)</div>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contracts as $c)
                    @php
                        $p = $c->participant ?? $c;
                        $fullName = trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: ($p->full_name ?? ($p->name ?? 'Peserta'));
                        $initials = strtoupper(collect(explode(' ', $fullName))->take(2)->map(fn($s) => substr($s,0,1))->implode(''));
                        $colors = ['indigo','purple','rose','blue','cyan','emerald','teal','amber'];
                        $colorIdx = ($c->id ?? 0) % count($colors);
                        $palette = $colors[$colorIdx];

                        $end = $c->contract_end ?? ($c->end_date ?? null);
                        $start = $c->contract_start ?? ($c->start_date ?? null);
                        $daysLeft = $end ? \Carbon\Carbon::parse($end)->diffInDays(now(), false) : 999;
                        $isExpiring = $daysLeft >= 0 && $daysLeft < 30;
                        $isOver = $daysLeft < 0;

                        $nik = $p->national_id ?? ($p->nik ?? ($p->nip ?? ''));
                        if (strlen($nik) >= 8) {
                            $nikMasked = substr($nik, 0, 3) . str_repeat('*', max(0, strlen($nik) - 6)) . substr($nik, -3);
                        } else {
                            $nikMasked = $nik;
                        }
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition batch-row" data-contract-id="{{ $c->id }}">
                        <td class="px-4 py-4 text-center align-middle">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="contract_ids[]" value="{{ $c->id }}" class="w-5 h-5 rounded-md border-gray-300 text-gik-primary focus:ring-gik-accent cursor-pointer row-check">
                            </label>
                            <input type="hidden" name="items[{{ $c->id }}][contract_id]" value="{{ $c->id }}" disabled class="ctrl-input">
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-{{ $palette }}-500 to-{{ $palette }}-700 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">{{ $fullName }}</div>
                                    <div class="text-[11px] text-gray-500 font-semibold">
                                        {{ $p->job_title ?? ($c->job_title ?? ($c->position ?? '-')) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle font-mono text-xs text-gray-700">{{ $nikMasked ?: '-' }}</td>
                        <td class="px-4 py-4 align-middle text-xs text-gray-700">
                            {{ $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-4 align-middle text-xs text-gray-700">
                            {{ $start ? \Carbon\Carbon::parse($start)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex flex-col gap-1">
                                <div class="text-xs font-semibold {{ $isOver ? 'text-red-700' : ($isExpiring ? 'text-amber-700' : 'text-gray-700') }}">
                                    {{ $end ? \Carbon\Carbon::parse($end)->translatedFormat('d M Y') : '-' }}
                                </div>
                                @if($end)
                                    @if($isOver)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-red-100 text-red-700 w-fit">
                                            EXPIRED {{ abs($daysLeft) }}d
                                        </span>
                                    @elseif($isExpiring)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-700 w-fit animate-pulse">
                                            EXPIRING {{ $daysLeft }}d
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-400 font-semibold">{{ $daysLeft }} hari lagi</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle max-w-[200px]">
                            <div class="text-xs text-gray-600 leading-relaxed line-clamp-2">{{ $p->address ?? '-' }}</div>
                        </td>
                        @if($isExtend)
                            <td class="px-4 py-4 align-middle bg-red-50/60/40 border-l border-blue-100">
                                <input type="date"
                                    name="items[{{ $c->id }}][new_contract_end]"
                                    disabled
                                    required
                                    class="ctrl-input w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition min-w-[150px]">
                            </td>
                            <td class="px-4 py-4 align-middle bg-red-50/60/40">
                                <textarea name="items[{{ $c->id }}][notes]" rows="1"
                                    disabled
                                    placeholder="Opsional: keterangan perpanjangan"
                                    class="ctrl-input w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition resize-y min-w-[180px]"></textarea>
                            </td>
                        @else
                            <td class="px-4 py-4 align-middle bg-orange-50/40 border-l border-orange-100">
                                <input type="date"
                                    name="items[{{ $c->id }}][off_boarding_effective_date]"
                                    disabled
                                    required
                                    min="{{ date('Y-m-d') }}"
                                    class="ctrl-input w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition min-w-[150px]">
                            </td>
                            <td class="px-4 py-4 align-middle bg-orange-50/40">
                                <textarea name="items[{{ $c->id }}][off_boarding_reason]" rows="1"
                                    disabled
                                    required
                                    placeholder="Mis: resign, habis kontrak tidak diperpanjang, pelanggaran dsb."
                                    class="ctrl-input w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition resize-y min-w-[220px]"></textarea>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $isExtend ? 9 : 9 }}" class="px-6 py-16 text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-700 mb-1">Tidak ada peserta aktif ditemukan.</h3>
                        <p class="text-sm text-gray-500">
                            {{ request('search') ? 'Coba ubah kata kunci pencarian.' : 'Data peserta aktif untuk Company Anda belum tersedia.' }}
                        </p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contracts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-gray-500">
                Showing {{ $contracts->firstItem() ?? 0 }}–{{ $contracts->lastItem() ?? 0 }} of {{ $contracts->total() }} peserta aktif
            </div>
            {{ $contracts->appends(request()->query())->links() }}
        </div>
    @elseif($contracts->count() > 0)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="text-xs text-gray-500">Showing 1–{{ $contracts->count() }} of {{ $contracts->count() }} peserta aktif</div>
        </div>
    @endif
</div>

<div class="sticky bottom-4 z-20 mt-6">
    <div class="bg-white rounded-2xl shadow-2xl border {{ $isExtend ? 'border-blue-200' : 'border-orange-200' }} p-4 md:p-5 flex flex-wrap items-center justify-between gap-4 backdrop-blur-sm">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl {{ $isExtend ? 'bg-red-50/60 border border-blue-200' : 'bg-orange-50 border border-orange-200' }}">
                <div class="w-11 h-11 rounded-full {{ $isExtend ? 'bg-blue-600' : 'bg-orange-500' }} text-white flex items-center justify-center text-lg font-extrabold shadow-sm" id="checkCountBig">0</div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Peserta Dipilih</div>
                    <div class="text-base font-extrabold {{ $pageAccent }}"><span id="checkCountText">0</span> orang</div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('gik-hrd.operations') }}" class="inline-flex items-center gap-2 px-5 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 rounded-xl text-sm font-bold transition">
                Cancel
            </a>
            <button type="submit" id="submitBtn" disabled
                class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-xl text-sm font-bold transition shadow-lg disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none {{ $submitColor }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Submit Request {{ $isExtend ? 'Perpanjang Kontrak' : 'Off-boarding' }}
            </button>
        </div>
    </div>
</div>
</form>

<script>
(function() {
    const rows = document.querySelectorAll('.batch-row');
    const selectAll = document.getElementById('selectAll');
    const submitBtn = document.getElementById('submitBtn');
    const countText = document.getElementById('checkCountText');
    const countBig = document.getElementById('checkCountBig');

    function refreshInputsForRow(tr, checked) {
        tr.querySelectorAll('.ctrl-input').forEach(inp => {
            inp.disabled = !checked;
            if (!checked && inp.tagName !== 'INPUT' || inp.type !== 'hidden') {
                if (inp.tagName === 'INPUT' || inp.tagName === 'TEXTAREA' || inp.tagName === 'SELECT') {
                    if (!inp.hasAttribute('name')) return;
                }
            }
        });
        tr.querySelectorAll('.row-check').forEach(cb => {
        });
    }

    function updateCounts() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        countText.textContent = checked;
        countBig.textContent = checked;
        submitBtn.disabled = checked === 0;

        const rowChecks = document.querySelectorAll('.row-check');
        if (rowChecks.length > 0) {
            selectAll.checked = checked === rowChecks.length;
            selectAll.indeterminate = (checked > 0 && checked < rowChecks.length);
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
    }

    rows.forEach(tr => {
        const cb = tr.querySelector('.row-check');
        if (cb) {
            cb.addEventListener('change', () => {
                refreshInputsForRow(tr, cb.checked);
                updateCounts();
            });
            refreshInputsForRow(tr, cb.checked);
        }
    });

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const val = selectAll.checked;
            document.querySelectorAll('.row-check').forEach(cb => {
                cb.checked = val;
                const tr = cb.closest('.batch-row');
                if (tr) refreshInputsForRow(tr, val);
            });
            updateCounts();
        });
    }

    document.getElementById('batchForm').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.row-check:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 peserta.');
            return;
        }
        let ok = true;
        checked.forEach(cb => {
            const tr = cb.closest('.batch-row');
            if (!tr) return;
            const reqInputs = tr.querySelectorAll('.ctrl-input[required]');
            reqInputs.forEach(inp => {
                if (!inp.value || String(inp.value).trim() === '') {
                    ok = false;
                    inp.focus();
                    inp.classList.add('border-red-500','ring-2','ring-red-200');
                }
            });
        });
        if (!ok) {
            e.preventDefault();
            alert('Lengkapi semua field wajib (required) pada baris yang dicentang.');
        } else {
            const jenisBatch = @json($isExtend ? 'Extend Contract' : 'Off-boarding');
            const tanya = confirm(`Anda akan submit ${jenisBatch} batch untuk ${checked.length} peserta. Lanjutkan?`);
            if (!tanya) e.preventDefault();
        }
    });

    updateCounts();
})();
</script>
@endsection
