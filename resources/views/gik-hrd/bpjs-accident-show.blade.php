@extends('layouts.gik-hrd')

@section('content')
<div class="mb-7">
    <a href="{{ route('gik-hrd.bpjs.accidents') }}" class="text-sm text-gray-500 hover:text-gik-primary transition">← Kembali ke Daftar Laporan</a>
    <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight mt-2">Detail Laporan #{{ $report->report_number_display }}</h1>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Detail Utama -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-5">
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold {{ $report->category_badge_class }}">{{ $report->category_label }}</span>
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Peserta</div>
                <div class="text-sm font-bold text-gray-900">{{ $report->hrContract?->full_name ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $report->hrContract?->job_title ?? '' }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Tanggal & Waktu</div>
                <div class="text-sm font-bold text-gray-900">{{ $report->incident_date_time_label }}</div>
            </div>
            <div class="sm:col-span-2">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">Lokasi Kejadian</div>
                <div class="text-sm text-gray-900">{{ $report->incident_location }}</div>
            </div>
        </div>

        <div class="mb-6">
            <div class="text-xs text-gray-500 font-semibold uppercase mb-2">Kronologi Kejadian</div>
            <div class="text-sm text-gray-800 leading-relaxed bg-gray-50 rounded-xl p-4 whitespace-pre-wrap">{{ $report->chronology }}</div>
        </div>

        @if($report->action_taken)
        <div class="mb-6">
            <div class="text-xs text-gray-500 font-semibold uppercase mb-2">Tindakan / Penanganan</div>
            <div class="text-sm text-gray-800 leading-relaxed bg-blue-50 rounded-xl p-4 whitespace-pre-wrap">{{ $report->action_taken }}</div>
        </div>
        @endif

        @if($report->documents && count($report->documents) > 0)
        <div>
            <div class="text-xs text-gray-500 font-semibold uppercase mb-2">Dokumen Pendukung</div>
            <div class="flex flex-wrap gap-2">
                @foreach($report->documents as $doc)
                <a href="{{ Storage::url($doc) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-200 transition">
                    📎 {{ basename($doc) }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Info Sidebar -->
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs text-gray-500 font-semibold uppercase mb-3">Informasi Pelaporan</div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Laporan</span>
                    <span class="font-mono font-bold text-gray-800">{{ $report->report_number_display }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pelapor</span>
                    <span class="font-semibold text-gray-800">{{ $report->reportedByUser?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Perusahaan</span>
                    <span class="font-semibold text-gray-800">{{ $report->company?->company_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Dilaporkan pada</span>
                    <span class="text-gray-800">{{ optional($report->created_at)->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
