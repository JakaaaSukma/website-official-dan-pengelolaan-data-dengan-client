@extends('layouts.gik-hrd')

@section('page-title', $pageTitle ?? 'Quick Action')

@section('content')
@php
    $isExtend = $type === 'extend_contract';
    $isOff = $type === 'off_boarding';
    $totalPeserta = $contracts->count();
    $formAction = route('gik-hrd.quick.submit', $type);

    $gradientHeader = $isExtend
        ? 'from-emerald-50 to-green-50/60 border-b-emerald-100'
        : 'from-orange-50 to-amber-50/60 border-b-orange-100';
    $iconBg = $isExtend ? 'bg-emerald-600' : 'bg-orange-600';
    $btnGradient = $isExtend
        ? 'bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 shadow-emerald-200'
        : 'bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 shadow-orange-200';
    $iconTitle = $isExtend ? '📈' : '🚪';
    $typeBadgeColor = $isExtend
        ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
        : 'bg-orange-50 text-orange-800 border-orange-200';
    $typeChipsColor = $isExtend
        ? 'from-emerald-500 to-green-600'
        : 'from-orange-500 to-amber-600';
    $typeSidebar = $isExtend
        ? 'from-green-50 to-emerald-50 border-emerald-200'
        : 'from-amber-50 to-orange-50 border-orange-200';
    $typeSidebarText = $isExtend ? 'text-emerald-900' : 'text-orange-900';
@endphp

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 border {{ $typeBadgeColor }} rounded-lg text-xs font-bold uppercase tracking-wide mb-1">
            {{ $iconTitle }} {{ $typeLabel }}
            @if($isBatch)<span class="bg-white/80 px-2 py-0.5 rounded-full border {{ $typeBadgeColor }} text-[10px]">MASSAL {{ $totalPeserta }} ORANG</span>@endif
        </div>
        <h2 class="text-2xl font-bold {{ $isExtend ? 'text-emerald-800' : 'text-orange-800' }} leading-tight">{{ $pageTitle }}</h2>
        <p class="text-gray-500 mt-1 text-sm">
            @if($isExtend)
                Ajukan perpanjangan masa kontrak peserta magang / karyawan. Setelah disetujui Super Admin, masa kontrak otomatis diperbarui.
            @else
                Ajukan pengakhiran kerja (Off-boarding) peserta. Setelah disetujui, status peserta otomatis berubah menjadi Off-boarded.
            @endif
        </p>
    </div>
    <a href="{{ route('gik-hrd.operations') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 font-medium flex-shrink-0">← Kembali</a>
</div>

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-5 shadow-sm">{{ session('error') }}</div>
@endif
@if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg mb-5 shadow-sm">{{ session('warning') }}</div>
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
        {{-- SECTION: DAFTAR PESERTA TERPUKUL --}}
        <div class="bg-white rounded-xl shadow-sm border-2 {{ $isExtend ? 'border-emerald-200' : 'border-orange-200' }} p-5">
            <div class="flex items-start justify-between gap-4 mb-4 flex-wrap">
                <h4 class="text-sm font-extrabold {{ $isExtend ? 'text-emerald-900' : 'text-orange-900' }} flex items-center gap-2">
                    <span class="w-9 h-9 bg-gradient-to-br {{ $typeChipsColor }} text-white rounded-xl flex items-center justify-center shadow-sm">📋</span>
                    @if($isBatch) Daftar {{ $totalPeserta }} Peserta Dalam Pengajuan @else Identitas Peserta @endif
                </h4>
                @if($isBatch)
                    <span class="text-[10px] font-bold {{ $isExtend ? 'text-emerald-700 bg-emerald-50' : 'text-orange-700 bg-orange-50' }} px-2.5 py-1 rounded-full border {{ $isExtend ? 'border-emerald-200' : 'border-orange-200' }}">
                        {{ $isExtend ? '1 Request Extend = '.$totalPeserta.' item' : '1 Request Off = '.$totalPeserta.' item' }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 max-h-[280px] overflow-y-auto pr-1.5">
                @foreach($contracts as $idx => $c)
                    @php
                        $endDate = $c->contract_end ? \Carbon\Carbon::parse($c->contract_end)->format('d M Y') : '-';
                        $isExpiringSoon = $c->contract_end && \Carbon\Carbon::parse($c->contract_end)->diffInDays(now(), false) <= 30 && \Carbon\Carbon::parse($c->contract_end)->isFuture();
                        $isExpired = $c->contract_end && \Carbon\Carbon::parse($c->contract_end)->isPast();
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl border {{ $isExtend ? 'bg-gradient-to-r from-emerald-50/60 to-green-50/60 border-emerald-100 hover:from-emerald-50 hover:to-green-50' : 'bg-gradient-to-r from-orange-50/60 to-amber-50/60 border-orange-100 hover:from-orange-50 hover:to-amber-50' }} transition">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $typeChipsColor }} text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">{{ $idx + 1 }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-sm text-gray-800 truncate">{{ $c->full_name }}</div>
                            <div class="text-[11px] text-gray-500 truncate">{{ $c->employee_id ?? '—' }} • {{ $c->job_title ?? $c->position ?? '-' }}</div>
                            <div class="text-[10px] mt-0.5 font-mono">
                                <span class="text-gray-400">Kontrak s/d:</span>
                                <span class="{{ $isExpired ? 'text-red-600 font-bold' : ($isExpiringSoon ? 'text-amber-600 font-semibold' : 'text-gray-600') }}">
                                    {{ $endDate }}
                                    @if($isExtend && $isExpired) <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold">EXPIRED</span>@endif
                                    @if($isExtend && $isExpiringSoon && !$isExpired) <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-bold">SEGERA HABIS</span>@endif
                                </span>
                            </div>
                        </div>
                        <span class="inline-block px-2 py-0.5 text-[9px] rounded-full {{ $c->status_badge_class }} flex-shrink-0">{{ $c->status_display_label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ $formAction }}" id="quick-action-form">
            @csrf
            <input type="hidden" name="_batch_ids" value="{{ $batchIdsStr }}">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div class="border-b border-gray-100 pb-4 -mx-6 -mt-6 px-6 pt-6 bg-gradient-to-r {{ $gradientHeader }} rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="{{ $iconBg }} text-white p-2.5 rounded-lg shadow-sm">
                            @if($isExtend)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold {{ $isExtend ? 'text-emerald-900' : 'text-orange-900' }}">
                                Form Pengajuan {{ $typeLabel }}
                            </h3>
                            <p class="text-xs {{ $isExtend ? 'text-emerald-700/80' : 'text-orange-700/80' }}">
                                @if($isExtend)
                                    Isi tanggal perpanjangan kontrak baru. HR Company bisa mengajukan 1 tanggal sama untuk semua (Global) atau beda per orang.
                                @else
                                    Isi tanggal efektif off-boarding dan alasan pengakhiran. Bisa pakai catatan SAMA untuk semua atau per individu.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ===== MODE TOGGLE (hanya jika BATCH > 1 orang) ===== --}}
                @if($isBatch)
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-800 flex items-center gap-2 flex-wrap">
                                <span>📝 Mode Pengisian:</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Pilih apakah ingin memakai {{ $isExtend ? 'tanggal + catatan SAMA untuk semua' : 'tanggal + alasan SAMA untuk semua' }}, atau isi data terpisah per orang.</div>
                        </div>
                        <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-white shadow-sm flex-shrink-0" role="tablist">
                            <label class="cursor-pointer px-4 py-2 text-xs font-bold rounded-md transition mode-tab" data-mode="global" id="tab-global">
                                ✅ Keseluruhan (Global)
                            </label>
                            <label class="cursor-pointer px-4 py-2 text-xs font-bold rounded-md transition mode-tab" data-mode="per_person" id="tab-per_person">
                                🧑 Per Individu
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="mode" id="input-mode" value="global">
                </div>
                @else
                    {{-- Single mode: paksa global --}}
                    <input type="hidden" name="mode" value="global">
                @endif

                {{-- ===== PANEL GLOBAL ===== --}}
                <div id="panel-global" class="space-y-5">
                    @if($isExtend)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    📅 Perpanjang Sampai (Habis Kontrak BARU) <span class="text-red-500">*</span>
                                </label>
                                @php
                                    $minDate = \Carbon\Carbon::today()->format('Y-m-d');
                                    $defaultExtend = \Carbon\Carbon::today()->addMonths(6)->format('Y-m-d');
                                @endphp
                                <input type="date" name="global_new_contract_end" id="g-extend-date"
                                    value="{{ old('global_new_contract_end', $defaultExtend) }}"
                                    min="{{ $minDate }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none @error('global_new_contract_end') border-red-400 ring-red-100 @enderror"
                                    {{ $isBatch ? 'data-mode-field="global"' : '' }}>
                                @error('global_new_contract_end')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <div class="text-[10px] text-gray-400 mt-1">Min. tanggal hari ini. Default 6 bulan ke depan.</div>
                            </div>
                            <div class="md:col-span-1 hidden"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                📝 Catatan / Alasan Perpanjangan <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea name="global_notes" id="g-extend-notes" rows="4"
                                placeholder="Contoh: Performa bagus, perusahaan masih membutuhkan tenaga kerja di divisi ini. Perpanjangan dilakukan selama 6 bulan kedepan dengan evaluasi di pertengahan periode."
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none @error('global_notes') border-red-400 ring-red-100 @enderror"
                                {{ $isBatch ? 'data-mode-field="global"' : '' }}>{{ old('global_notes') }}</textarea>
                            @error('global_notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            <div class="text-[10px] text-gray-400 mt-1">Maks. 1000 karakter.</div>
                        </div>
                    @else
                        {{-- OFF-BOARDING GLOBAL --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    📅 Tgl Efektif Off-boarding <span class="text-red-500">*</span>
                                </label>
                                @php
                                    $minOffDate = \Carbon\Carbon::today()->format('Y-m-d');
                                    $defaultOff = \Carbon\Carbon::today()->addDays(7)->format('Y-m-d');
                                @endphp
                                <input type="date" name="global_off_date" id="g-off-date"
                                    value="{{ old('global_off_date', $defaultOff) }}"
                                    min="{{ $minOffDate }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none @error('global_off_date') border-red-400 ring-red-100 @enderror"
                                    {{ $isBatch ? 'data-mode-field="global"' : '' }}>
                                @error('global_off_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                <div class="text-[10px] text-gray-400 mt-1">Min. H+1 dari hari ini (default 7 hari ke depan).</div>
                            </div>
                            <div class="hidden md:block"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                📝 Alasan / Catatan Pengakhiran <span class="text-red-500">*</span>
                            </label>
                            <textarea name="global_off_reason" id="g-off-reason" rows="5" minlength="5" maxlength="1000" required
                                placeholder="Contoh: Berakhirnya masa proyek divisi Operasional. Perusahaan mengucapkan terima kasih atas kinerja selama ini, dilaksanakan dengan mekanisme PHK yang baik dan sesuai peraturan ketenagakerjaan yang berlaku (Pasal 156 UU Ketenagakerjaan)."
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none @error('global_off_reason') border-red-400 ring-red-100 @enderror"
                                {{ $isBatch ? 'data-mode-field="global"' : '' }}>{{ old('global_off_reason') }}</textarea>
                            @error('global_off_reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            <div class="flex justify-between mt-1">
                                <div class="text-[10px] text-gray-400">Min. 5 karakter, maks. 1000 karakter.</div>
                                <div class="text-[10px] text-gray-400 font-mono" id="off-reason-counter">0 / 1000</div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ===== PANEL PER PERSON (HANYA JIKA BATCH > 1) ===== --}}
                @if($isBatch)
                <div id="panel-per_person" class="space-y-3 hidden">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <div>
                            <div class="text-xs font-bold text-gray-800">📝 Isi data terpisah per orang:</div>
                            <div class="text-[10px] text-gray-500">Klik tiap row untuk expand. Semua kolom wajib diisi (tanda *).</div>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $totalPeserta }} item</span>
                    </div>

                    @foreach($contracts as $idx => $c)
                        @php
                            $cId = $c->id;
                            $defaultPersonExtend = \Carbon\Carbon::parse($c->contract_end ?? 'now')->addMonths(6)->format('Y-m-d');
                            $defaultPersonOff = \Carbon\Carbon::today()->addDays(7)->format('Y-m-d');
                        @endphp
                        <div class="border-2 {{ $isExtend ? 'border-emerald-100' : 'border-orange-100' }} rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center gap-3 mb-3 pb-2 border-b border-gray-100">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $typeChipsColor }} text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">#{{ $idx + 1 }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-sm text-gray-800">{{ $c->full_name }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $c->employee_id ?? '—' }} • {{ $c->job_title ?? '-' }}</div>
                                </div>
                                <span class="text-[10px] text-gray-400 font-mono">ID #{{ $cId }}</span>
                            </div>

                            @if($isExtend)
                                <input type="hidden" name="items[{{ $idx }}][contract_id]" value="{{ $cId }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[11px] font-bold text-gray-700 mb-1 block">
                                            📅 Perpanjang Sampai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $idx }}][new_contract_end]"
                                            value="{{ old('items.'.$idx.'.new_contract_end', $defaultPersonExtend) }}"
                                            min="{{ $minDate }}"
                                            data-mode-field="per_person"
                                            class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none per-person-field">
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-bold text-gray-700 mb-1 block">
                                            📝 Catatan <span class="text-gray-400 font-normal">(Opsional)</span>
                                        </label>
                                        <input type="text" name="items[{{ $idx }}][notes]"
                                            value="{{ old('items.'.$idx.'.notes') }}"
                                            placeholder="Catatan khusus untuk orang ini (jika ada)..."
                                            data-mode-field="per_person"
                                            class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none per-person-field">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="items[{{ $idx }}][contract_id]" value="{{ $cId }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[11px] font-bold text-gray-700 mb-1 block">
                                            📅 Tgl Efektif <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="items[{{ $idx }}][off_boarding_effective_date]"
                                            value="{{ old('items.'.$idx.'.off_boarding_effective_date', $defaultPersonOff) }}"
                                            min="{{ $minOffDate }}"
                                            data-mode-field="per_person"
                                            class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none per-person-field">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-[11px] font-bold text-gray-700 mb-1 block">
                                            📝 Alasan Pengakhiran <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="items[{{ $idx }}][off_boarding_reason]" rows="2" minlength="5" maxlength="1000"
                                            placeholder="Alasan khusus per orang..."
                                            data-mode-field="per_person"
                                            class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none resize-none per-person-field">{{ old('items.'.$idx.'.off_boarding_reason') }}</textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif

                {{-- ===== WARNING CARD ===== --}}
                <div class="bg-{{ $isExtend ? 'emerald' : 'amber' }}-50 border border-{{ $isExtend ? 'emerald' : 'amber' }}-100 rounded-xl p-4">
                    <div class="flex gap-3 items-start">
                        <svg class="w-5 h-5 text-{{ $isExtend ? 'emerald' : 'amber' }}-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs {{ $isExtend ? 'text-emerald-800' : 'text-amber-800' }} space-y-1">
                            <p class="font-bold">Sebelum Submit:</p>
                            <ul class="list-disc ml-4 space-y-0.5">
                                @if($isExtend)
                                    <li>Request Extend Contract akan otomatis <b>dikirim ke Super Admin Rekrutmen</b> untuk review & persetujuan.</li>
                                    <li>Jika disetujui (Approved), <b>tanggal berakhir kontrak peserta otomatis diperbarui</b> & status menjadi <b>Extended</b>.</li>
                                    <li>Jika ditolak (Rejected), tanggal kontrak peserta <b>tetap seperti semula</b>.</li>
                                @else
                                    <li>Request Off-boarding dikirim ke Super Admin untuk review & persetujuan.</li>
                                    <li>Jika disetujui, <b>status peserta otomatis berubah menjadi Off-boarded</b> pada tanggal efektif yang ditentukan.</li>
                                    <li>Data peserta yang off-boarded <b>tidak terhapus</b> (tetap tersimpan untuk kebutuhan laporan & audit).</li>
                                @endif
                                @if($isBatch)
                                <li class="font-semibold bg-white/50 px-2 py-0.5 rounded mt-1">
                                    ⚠️ Mode Massal: 1 Request Nomor (EXT-/OFF-) berisi {{ $totalPeserta }} item peserta. Super Admin akan approve/ reject SEMUA item sekaligus dalam 1 keputusan.
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 flex-wrap border-t border-gray-100 -mx-6 px-6">
                    <a href="{{ route('gik-hrd.operations') }}" class="px-5 py-2.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">Batal</a>
                    <button type="submit" id="btn-submit"
                        class="px-6 py-2.5 text-sm text-white rounded-lg font-bold shadow-md hover:shadow-xl transition-colors {{ $btnGradient }}"
                        onclick="return confirmSubmit()">
                        {{ $iconTitle }} {{ $isBatch ? 'Kirim Request MASSAL ('.$totalPeserta.')' : 'Kirim Request' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== SIDEBAR ===== --}}
    <div class="space-y-5">
        <div class="bg-gradient-to-br {{ $typeSidebar }} rounded-xl shadow-sm border-2 p-5">
            <h4 class="text-sm font-extrabold {{ $typeSidebarText }} mb-4 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-full {{ $iconBg }} text-white flex items-center justify-center shadow-sm">📊</span>
                Ringkasan Pengajuan
            </h4>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-center p-2.5 bg-white/70 rounded-lg border border-white/60">
                    <span class="text-xs text-gray-600">Jenis Request</span>
                    <span class="font-bold {{ $typeSidebarText }} text-xs">{{ $typeLabel }}</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/70 rounded-lg border border-white/60">
                    <span class="text-xs text-gray-600">Jumlah Peserta</span>
                    <span class="font-extrabold {{ $typeSidebarText }} text-lg">{{ $totalPeserta }} 👥</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/70 rounded-lg border border-white/60">
                    <span class="text-xs text-gray-600">Pengaju</span>
                    <span class="font-bold text-gray-800 text-xs truncate max-w-[55%]">{{ auth()->user()->name }}</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/70 rounded-lg border border-white/60">
                    <span class="text-xs text-gray-600">Perusahaan</span>
                    <span class="font-bold text-gray-800 text-xs truncate max-w-[55%]">{{ auth()->user()->company?->company_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center p-2.5 bg-white/70 rounded-lg border border-white/60">
                    <span class="text-xs text-gray-600">Nomor Request</span>
                    <span class="text-[10px] font-mono text-gray-500">Generate otomatis</span>
                </div>
            </div>
            <div class="text-[11px] {{ $typeSidebarText }} bg-white/70 p-3 rounded-lg border border-white/60 mt-3 leading-relaxed">
                💡 <b>Tips:</b> {{ $isExtend ? 'Jika hampir semua peserta perpanjang tanggal SAMA, gunakan Mode GLOBAL lebih cepat. Hanya klik Per Individu jika ada orang yang beda tanggal.' : 'Gunakan Mode Global jika alasan pengakhiran SAMA (contoh: end of mass project). Gunakan Per Individu jika masing-masing orang punya alasan pengakhiran beda (contoh: resign vs end kontrak vs PHK).' }}
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-extrabold text-gray-900 mb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-gradient-to-br from-gik-primary to-red-600 text-white rounded-full flex items-center justify-center shadow-sm text-[11px]">🔗</span>
                Akses Cepat Menu Lain
            </h4>
            <div class="space-y-2">
                <a href="{{ route('gik-hrd.suspensions.index') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-fuchsia-50 border border-transparent hover:border-fuchsia-100 transition text-xs font-medium text-gray-700 hover:text-fuchsia-800">
                    <span class="w-6 h-6 bg-fuchsia-100 text-fuchsia-700 rounded-md flex items-center justify-center text-[10px]">⏸</span>
                    Pemberhentian Sementara
                </a>
                <a href="{{ route('gik-hrd.violations.index') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100 transition text-xs font-medium text-gray-700 hover:text-red-800">
                    <span class="w-6 h-6 bg-red-100 text-red-700 rounded-md flex items-center justify-center text-[10px]">🚨</span>
                    Laporan Pelanggaran
                </a>
                <a href="{{ route('gik-hrd.operations') }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-gik-primary/5 border border-transparent hover:border-red-100 transition text-xs font-medium text-gray-700 hover:text-gik-primary-dark">
                    <span class="w-6 h-6 bg-gik-primary/10 text-gik-primary-dark rounded-md flex items-center justify-center text-[10px]">⚙️</span>
                    Operations Center
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    // ===== MODE TAB TOGGLE (BATCH SAJA) =====
    @if($isBatch)
    const inputMode = document.getElementById('input-mode');
    const tabGlobal = document.getElementById('tab-global');
    const tabPer = document.getElementById('tab-per_person');
    const panelGlobal = document.getElementById('panel-global');
    const panelPer = document.getElementById('panel-per_person');
    const activeBtnClass = ['bg-gradient-to-r', 'from-gik-primary', 'to-red-600', 'text-white', 'shadow-md', 'shadow-red-200'];
    const inactiveBtnClass = ['text-gray-500', 'hover:bg-gray-50'];

    function setMode(mode){
        inputMode.value = mode;
        if(mode === 'global'){
            tabGlobal.classList.add(...activeBtnClass);
            tabGlobal.classList.remove(...inactiveBtnClass);
            tabPer.classList.remove(...activeBtnClass);
            tabPer.classList.add(...inactiveBtnClass);
            panelGlobal.classList.remove('hidden');
            panelPer.classList.add('hidden');
            document.querySelectorAll('[data-mode-field="global"]').forEach(el => {
                if(el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT'){
                    if(el.hasAttribute('required-fallback')) el.setAttribute('required', 'required');
                }
            });
            document.querySelectorAll('[data-mode-field="per_person"]').forEach(el => {
                if(el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT'){
                    el.removeAttribute('required');
                }
            });
        } else {
            tabPer.classList.add(...activeBtnClass);
            tabPer.classList.remove(...inactiveBtnClass);
            tabGlobal.classList.remove(...activeBtnClass);
            tabGlobal.classList.add(...inactiveBtnClass);
            panelGlobal.classList.add('hidden');
            panelPer.classList.remove('hidden');
            document.querySelectorAll('[data-mode-field="global"]').forEach(el => {
                if(el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT'){
                    el.removeAttribute('required');
                }
            });
            document.querySelectorAll('.per-person-field').forEach(el => {
                if(el.name && el.name.includes('new_contract_end') || el.name && el.name.includes('off_boarding_effective_date') || el.name && el.name.includes('off_boarding_reason')){
                    el.setAttribute('required', 'required');
                }
            });
        }
    }
    tabGlobal.addEventListener('click', () => setMode('global'));
    tabPer.addEventListener('click', () => setMode('per_person'));
    setMode('global'); // default
    @endif

    // ===== OFF REASON COUNTER =====
    const offReason = document.getElementById('g-off-reason');
    const offCounter = document.getElementById('off-reason-counter');
    if(offReason && offCounter){
        const MAX = 1000;
        function updateCount(){
            const len = offReason.value.length;
            offCounter.textContent = `${len} / ${MAX}`;
            offCounter.classList.toggle('text-red-600', len > MAX);
            offCounter.classList.toggle('font-bold', len > MAX * 0.9);
        }
        offReason.addEventListener('input', updateCount);
        updateCount();
    }

    // ===== CONFIRM SUBMIT =====
    window.confirmSubmit = function(){
        @php
            $confirmMsg = $isBatch
                ? ($isExtend
                    ? '⚠️ Anda yakin akan MENGIRIM REQUEST EXTEND KONTRAK MASSAL untuk '.$totalPeserta.' peserta sekaligus? Nomor request akan tergenerate otomatis & dikirim ke Super Admin untuk persetujuan.'
                    : '⚠️ Anda yakin akan MENGIRIM REQUEST OFF-BOARDING MASSAL untuk '.$totalPeserta.' peserta sekaligus? Setelah disetujui, status SEMUA peserta akan otomatis OFF-BOARDED.')
                : ($isExtend
                    ? 'Apakah Anda yakin akan mengajukan Extend Contract untuk peserta ini? Setelah dikirim, request akan menunggu review Super Admin Rekrutmen.'
                    : 'Apakah Anda yakin akan mengajukan Off-boarding untuk peserta ini? Pastikan tanggal efektif & alasan sudah benar.');
        @endphp
        return confirm(`{!! addslashes($confirmMsg) !!}`);
    };
})();
</script>
@endpush
