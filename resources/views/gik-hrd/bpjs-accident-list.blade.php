@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Laporan Kecelakaan Kerja</h1>
        <p class="text-gray-500 text-sm mt-1">Riwayat pelaporan kecelakaan kerja peserta magang perusahaan Anda.</p>
    </div>
    <a href="{{ route('gik-hrd.bpjs.accidents.dashboard') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
        📊 Lihat Statistik
    </a>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif

<!-- Status Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="text-2xl font-extrabold text-amber-600">{{ $statusCounts['dilaporkan'] }}</div>
        <div class="text-xs text-gray-500 font-semibold mt-1">Dilaporkan</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="text-2xl font-extrabold text-blue-600">{{ $statusCounts['dalam_proses'] }}</div>
        <div class="text-xs text-gray-500 font-semibold mt-1">Dalam Proses</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="text-2xl font-extrabold text-green-600">{{ $statusCounts['selesai'] }}</div>
        <div class="text-xs text-gray-500 font-semibold mt-1">Selesai</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="text-2xl font-extrabold text-purple-600">{{ $statusCounts['klaim_diajukan'] }}</div>
        <div class="text-xs text-gray-500 font-semibold mt-1">Klaim Diajukan</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari lokasi, kronologi, nama peserta..." class="w-full pl-4 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
        </div>
        <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="">Semua Status</option>
            <option value="dilaporkan" {{ request('status') === 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
            <option value="dalam_proses" {{ request('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="klaim_diajukan" {{ request('status') === 'klaim_diajukan' ? 'selected' : '' }}>Klaim Diajukan</option>
        </select>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
        <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Laporan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $report->report_number_display }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $report->hrContract?->full_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $report->incident_date_time_label }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">{{ $report->incident_location }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $report->category_badge_class }}">{{ $report->category_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('gik-hrd.bpjs.accidents.show', $report) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Detail →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada laporan kecelakaan kerja.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $reports->withQueryString()->links() }}</div>
</div>
@endsection
