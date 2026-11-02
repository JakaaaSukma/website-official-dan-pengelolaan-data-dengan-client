@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Dashboard Kecelakaan Kerja</h1>
        <p class="text-gray-500 text-sm mt-1">Rekapitulasi data kecelakaan kerja seluruh peserta magang perusahaan mitra.</p>
    </div>
    <form method="GET" class="flex items-center gap-2">
        <select name="year" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold bg-white shadow-sm focus:ring-2 focus:ring-blue-200">
            @for($y = now()->year + 1; $y >= now()->year - 4; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase">Total Kecelakaan</h3>
            <span class="p-2 bg-gray-50 rounded-lg text-gray-600">📉</span>
        </div>
        <div class="text-3xl font-extrabold text-gray-900">{{ array_sum($monthlyCounts) }}</div>
        <p class="text-xs text-gray-400 mt-1">Sepanjang tahun {{ $year }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase">Kategori Ringan</h3>
            <span class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">🟡</span>
        </div>
        <div class="text-3xl font-extrabold text-yellow-600">{{ array_sum($categoryBreakdown['ringan'] ?? []) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase">Kategori Sedang</h3>
            <span class="p-2 bg-orange-50 text-orange-600 rounded-lg">🟠</span>
        </div>
        <div class="text-3xl font-extrabold text-orange-600">{{ array_sum($categoryBreakdown['sedang'] ?? []) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-gray-500 uppercase">Kategori Berat</h3>
            <span class="p-2 bg-red-50 text-red-600 rounded-lg">🔴</span>
        </div>
        <div class="text-3xl font-extrabold text-red-600">{{ array_sum($categoryBreakdown['berat'] ?? []) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <!-- Chart Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-bold text-gray-900 mb-6">Trend Kecelakaan Kerja Tahun {{ $year }}</h3>
        
        <!-- Simplified Chart Display Using HTML/CSS for implementation without JS library dependencies -->
        <div class="flex items-end gap-2 h-64 mt-4 relative">
            @php $max = max(1, max($monthlyCounts)); @endphp
            <!-- Y-Axis markers -->
            <div class="absolute left-0 top-0 w-full h-full flex flex-col justify-between pointer-events-none text-[10px] text-gray-400">
                <div class="w-full border-t border-dashed border-gray-200"></div>
                <div class="w-full border-t border-dashed border-gray-200"></div>
                <div class="w-full border-t border-dashed border-gray-200"></div>
                <div class="w-full border-t border-solid border-gray-300"></div>
            </div>
            
            @foreach(range(1, 12) as $m)
            @php 
                $val = $monthlyCounts[$m] ?? 0; 
                $heightPercent = ($val / $max) * 100;
                $monthName = \Carbon\Carbon::create(null, $m)->translatedFormat('M');
            @endphp
            <div class="flex-1 flex flex-col items-center justify-end h-full z-10 group relative">
                @if($val > 0)
                <div class="absolute -top-6 text-xs font-bold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ $val }}</div>
                <div class="w-full max-w-[40px] bg-red-500 rounded-t-md hover:bg-red-600 transition-colors" style="height: {{ $heightPercent }}%;"></div>
                @endif
                <div class="w-full max-w-[40px] border-t border-gray-300"></div>
                <div class="text-xs text-gray-500 mt-2">{{ $monthName }}</div>
            </div>
            @endforeach
        </div>
    </div>

    </div>
</div>
@endsection
