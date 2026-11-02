@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">BPJS Ketenagakerjaan</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola status BPJS dan nomor kepesertaan peserta magang aktif.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('gik-hrd.bpjs.accidents') }}" class="px-4 py-2.5 bg-amber-50 text-amber-800 rounded-xl text-sm font-semibold hover:bg-amber-100 transition">🚨 Laporan Kecelakaan</a>
        <a href="{{ route('gik-hrd.bpjs.f2a') }}" class="px-4 py-2.5 bg-gradient-to-r from-gik-primary to-red-600 text-white rounded-xl text-sm font-bold shadow-md hover:from-gik-primary-dark hover:to-red-700 transition">📄 Laporan F2A</a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">❌ {{ session('error') }}</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <!-- Filters -->
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[240px] max-w-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, nomor BPJS..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
        </div>
        <select name="bpjs_status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('bpjs_status') === 'aktif' ? 'selected' : '' }}>BPJS Aktif</option>
            <option value="tidak_aktif" {{ request('bpjs_status') === 'tidak_aktif' ? 'selected' : '' }}>BPJS Tidak Aktif</option>
            <option value="belum" {{ request('bpjs_status') === 'belum' ? 'selected' : '' }}>Belum Terdaftar</option>
        </select>
        <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Apply</button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">NIK</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">Tgl Mulai Magang</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wider">Status BPJS</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">No. BPJS</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">Tgl Kepesertaan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($contracts as $contract)
                @php $bpjs = $contract->bpjsKetenagakerjaan; @endphp
                <tr class="hover:bg-gray-50/50 transition" id="row-{{ $contract->id }}">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $contract->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $contract->national_id ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ optional($contract->contract_start)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($bpjs)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $bpjs->status_badge_class }}">
                                {{ $bpjs->status_label }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Belum Terdaftar</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $bpjs->bpjs_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $bpjs ? optional($bpjs->bpjs_join_date)->format('d M Y') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Tidak ada peserta magang aktif ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $contracts->withQueryString()->links() }}</div>
</div>
@endsection
