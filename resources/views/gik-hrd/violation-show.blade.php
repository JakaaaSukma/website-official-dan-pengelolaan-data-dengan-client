@extends('layouts.gik-hrd')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-start gap-3">
        <a href="{{ route('gik-hrd.violations.index') }}" class="p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[11px] font-bold font-mono tracking-wide">
                    {{ $report->report_number }}
                </span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-extrabold {{ $report->status_badge_class }}">
                    {{ $report->status_label }}
                </span>
            </div>
            <h1 class="text-[28px] font-extrabold text-gik-primary-dark leading-tight">Detail Laporan Pelanggaran</h1>
            <p class="text-gray-500 text-sm mt-1">Diajukan oleh Anda pada {{ $report->created_at->translatedFormat('l, d F Y H:i') }} WIB</p>
        </div>
    </div>
    <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-bold uppercase tracking-wider">
        🔒 Hanya Dapat Dilihat (Read-Only)
    </span>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-gik-primary-dark flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-lg font-extrabold text-gray-900">Uraian Pelanggaran</h2>
            </div>

            <div class="grid sm:grid-cols-2 gap-5 mb-6 p-4 bg-gray-50 rounded-xl">
                <div>
                    <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">Tanggal Kejadian</div>
                    <div class="text-sm font-semibold text-gray-800">{{ optional($report->incident_date)->translatedFormat('l, d F Y') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">Jam Kejadian</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $report->incident_time ? substr($report->incident_time, 0, 5) . ' WIB' : '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">Dilaporkan Oleh</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $report->reportedBy?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-1">Perusahaan</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $report->company?->company_name ?? '-' }}</div>
                </div>
            </div>

            <div class="mb-5">
                <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-2">Deskripsi Pelanggaran</div>
                <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-4 bg-red-50/50 border border-red-100 rounded-xl">
                    {!! nl2br(e($report->description)) !!}
                </div>
            </div>

            @if($report->proposed_sanction)
            <div class="mb-5">
                <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-2">Sanksi Yang Diusulkan Perusahaan</div>
                <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-4 bg-amber-50 border border-amber-100 rounded-xl">
                    {!! nl2br(e($report->proposed_sanction)) !!}
                </div>
            </div>
            @endif

            @if($report->evidence_file)
            <div>
                <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-2">Lampiran Bukti</div>
                <a href="{{ Storage::disk('public')->url($report->evidence_file) }}" target="_blank"
                    class="inline-flex items-center gap-2.5 px-5 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold transition shadow-sm shadow-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Lihat Lampiran Bukti
                </a>
            </div>
            @endif

            @if($report->resolution_notes && ($report->status === 'resolved' || $report->status === 'rejected'))
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider mb-2">
                    📝 Catatan {{ $report->status === 'resolved' ? 'Penyelesaian' : 'Penolakan' }} dari Tim Rekrutmen
                </div>
                <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap p-4 bg-{{ $report->status === 'resolved' ? 'green' : 'red' }}-50 border border-{{ $report->status === 'resolved' ? 'green' : 'red' }}-100 rounded-xl">
                    {!! nl2br(e($report->resolution_notes)) !!}
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-lg font-extrabold text-gray-900">Timeline Penanganan Laporan</h2>
                <span class="ml-auto text-xs text-gray-400 font-medium">{{ $report->statusLogs->count() }} peristiwa</span>
            </div>

            <div class="relative">
                <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200 border-l border-dashed border-gray-300"></div>
                <div class="space-y-6">
                    @forelse($report->statusLogs->sortByDesc('created_at') as $log)
                    <div class="relative pl-14">
                        <div class="absolute left-0 top-1 w-11 h-11 rounded-full bg-white border-4 border-gray-200 flex items-center justify-center shadow-sm">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-gik-primary to-red-600 shadow-sm"></span>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 hover:bg-white hover:shadow-sm transition">
                            <div class="flex items-start justify-between gap-3 flex-wrap mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $log->new_status_badge_class }}">
                                        {{ $log->new_status_label }}
                                    </span>
                                    @if($log->old_status && $log->old_status !== $log->new_status)
                                    <span class="text-[10px] text-gray-400 font-mono flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12l4-4m-4 4l4 4"/></svg>
                                        {{ $log->old_status_label }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-500 font-medium">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                            </div>
                            @if($log->notes)
                            <div class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $log->notes }}</div>
                            @else
                            <div class="text-[12px] text-gray-500 italic">Tidak ada catatan tambahan</div>
                            @endif
                            <div class="text-[11px] text-gray-400 mt-3 pt-3 border-t border-gray-100 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span><strong>{{ $log->updatedBy?->name ?? 'Sistem' }}</strong> • {{ $log->updatedBy?->isSuperAdminRecruitment() ? 'Tim Rekrutmen (SA)' : 'HR Company' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="pl-14 text-center text-sm text-gray-400 py-8 italic">Belum ada riwayat penanganan</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 mb-4 text-base">Data Peserta</h3>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-lg font-extrabold shadow-md">
                    {{ $report->intern?->initials ?? '??' }}
                </div>
                <div>
                    <div class="font-extrabold text-gray-900">{{ $report->intern?->full_name ?? '-' }}</div>
                    <div class="text-[11px] text-gray-500 font-mono">{{ $report->intern?->employee_id ?? '—' }}</div>
                </div>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-start"><span class="text-gray-500">Jabatan</span><span class="font-semibold text-gray-800 text-right max-w-[55%]">{{ $report->intern?->job_title }}</span></div>
                <div class="flex justify-between items-start"><span class="text-gray-500">Departemen</span><span class="font-semibold text-gray-800 text-right max-w-[55%]">{{ $report->intern?->department }}</span></div>
                <div class="flex justify-between items-start"><span class="text-gray-500">Status Kerja</span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $report->intern?->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $report->intern?->status ?? '-')) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 mb-4 text-base">Ringkasan Proses</h3>
            <div class="space-y-3.5">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-green-50 border border-green-100">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold text-green-800">Laporan Diajukan</div>
                        <div class="text-[10px] text-green-600">{{ $report->created_at->translatedFormat('d M Y H:i') }}</div>
                    </div>
                </div>

                @php $steps = ['contacted' => 'Dihubungi SA', 'in_process' => 'Diproses', 'resolved' => 'Selesai', 'rejected' => 'Ditolak'];
                      $currentIdx = array_search($report->status, array_keys($steps)) ?? -1;
                @endphp
                @foreach(['contacted' => 'Menunggu dihubungi Tim Rekrutmen', 'in_process' => 'Dalam proses tindak lanjut', 'resolved' => 'Selesai ditangani'] as $key => $label)
                @php
                    $reached = in_array($report->status, array_slice(array_keys($steps), 0, array_search($key, array_keys($steps)) + 1)) ||
                              ($key === 'resolved' && $report->status === 'resolved');
                    if ($key === 'resolved' && $report->status === 'rejected') $reached = false;
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $reached ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100 opacity-60' }}">
                    <div class="w-8 h-8 rounded-full {{ $reached ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center flex-shrink-0">
                        @if($reached)
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><circle cx="12" cy="12" r="4"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-extrabold {{ $reached ? 'text-green-800' : 'text-gray-500' }}">{{ $label }}</div>
                        @if($report->status === $key || ($key === 'in_process' && $report->status === 'contacted' && false) || ($key === 'resolved' && $report->status === 'resolved'))
                        <div class="text-[10px] {{ $reached ? 'text-green-600' : 'text-gray-400' }}">{{ $report->handled_at?->translatedFormat('d M Y H:i') ?? 'Dalam proses' }}</div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($report->status === 'rejected')
                <div class="flex items-center gap-3 p-3 rounded-xl bg-red-50 border border-red-100">
                    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-extrabold text-red-800">Laporan Ditolak</div>
                        <div class="text-[10px] text-red-600">{{ $report->handled_at?->translatedFormat('d M Y H:i') ?? '' }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 mb-4 text-base">Ditangani Oleh</h3>
            @if($report->handledBy)
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#991B1B] to-[#B91C1C] flex items-center justify-center text-white text-sm font-extrabold shadow-sm">
                    {{ strtoupper(substr($report->handledBy->name, 0, 2)) }}
                </div>
                <div>
                    <div class="font-extrabold text-gray-900 text-sm">{{ $report->handledBy->name }}</div>
                    <div class="text-[10px] text-gik-primary-dark font-bold uppercase">Tim Rekrutmen • Super Admin</div>
                    @if($report->handled_at)
                    <div class="text-[10px] text-gray-400 mt-0.5">Terakhir update: {{ $report->handled_at->translatedFormat('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>
            @else
            <div class="text-[12px] text-gray-500 italic py-2.5">Belum ada Tim Rekrutmen yang menindaklanjuti laporan ini.</div>
            @endif
        </div>
    </div>
</div>
@endsection
