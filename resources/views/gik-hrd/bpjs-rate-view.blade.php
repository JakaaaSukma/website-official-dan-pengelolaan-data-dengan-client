@extends('layouts.gik-hrd')

@section('content')
<div class="mb-7">
    <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Rate Iuran BPJS Perusahaan Anda</h1>
    <p class="text-gray-500 text-sm mt-1">Rate ini diatur oleh Super Admin dan berlaku untuk perhitungan iuran F2A.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    @if($rate)
    <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
        ✅ Rate aktif berlaku sejak <strong>{{ optional($rate->effective_from)->format('d M Y') }}</strong>
    </div>
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JKK (Jaminan Kecelakaan Kerja)</div>
                <div class="text-2xl font-extrabold text-gik-primary-dark">{{ $rate->jkk_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JKM (Jaminan Kematian)</div>
                <div class="text-2xl font-extrabold text-gik-primary-dark">{{ $rate->jkm_rate }}%</div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JHT Pemberi Kerja</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jht_employer_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JHT Tenaga Kerja</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jht_employee_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JP Pemberi Kerja</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jp_employer_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JP Tenaga Kerja</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jp_employee_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JKP Pemberi Kerja</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jkp_employer_rate }}%</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-xs text-gray-500 font-semibold uppercase mb-1">JKP Pemerintah</div>
                <div class="text-lg font-bold text-gray-700">{{ $rate->jkp_government_rate }}%</div>
            </div>
        </div>
    </div>
    @else
    <div class="px-4 py-8 text-center">
        <div class="text-4xl mb-3">⚠️</div>
        <div class="text-gray-600 font-semibold">Belum ada rate BPJS yang diatur untuk perusahaan Anda.</div>
        <div class="text-sm text-gray-400 mt-1">Silakan hubungi Super Admin untuk mengatur rate iuran.</div>
    </div>
    @endif
</div>
@endsection
