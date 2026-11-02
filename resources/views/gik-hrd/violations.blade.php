@extends('layouts.gik-hrd')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">HR Company • Disiplin Peserta</div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Laporan Pelanggaran Peserta</h1>
        <p class="text-gray-500 text-sm mt-1">Pantau status laporan pelanggaran yang Anda ajukan untuk peserta magang di perusahaan Anda.</p>
    </div>
    <a href="{{ route('gik-hrd.operations') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm shadow-red-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Lihat Daftar Peserta
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-xs font-bold uppercase text-gray-500">Menunggu Tinjauan</div>
        </div>
        <div class="text-3xl font-extrabold text-amber-700">{{ $statusCounts['pending_review'] ?? 0 }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="text-xs font-bold uppercase text-gray-500">Sedang Diproses</div>
        </div>
        <div class="text-3xl font-extrabold text-orange-700">{{ $statusCounts['in_process'] ?? 0 }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-xs font-bold uppercase text-gray-500">Selesai Ditangani</div>
        </div>
        <div class="text-3xl font-extrabold text-green-700">{{ $statusCounts['resolved'] ?? 0 }}</div>
    </div>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div><strong>Error!</strong> {{ session('error') }}</div>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
        <form method="GET" class="flex items-center gap-3 flex-1 flex-wrap w-full">
            <div class="relative flex-1 min-w-[220px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama peserta, ID, atau deskripsi pelanggaran..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 text-sm">
            </div>
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-200">
                <option value="">Semua Status</option>
                <option value="pending_review" {{ ($status ?? '') === 'pending_review' ? 'selected' : '' }}>Menunggu Tinjauan</option>
                <option value="contacted" {{ ($status ?? '') === 'contacted' ? 'selected' : '' }}>Sudah Dihubungi</option>
                <option value="in_process" {{ ($status ?? '') === 'in_process' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="resolved" {{ ($status ?? '') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <div class="flex items-center gap-2">
                <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" title="Dari Tanggal" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="to_date" value="{{ $toDate ?? '' }}" title="Sampai Tanggal" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">Terapkan</button>
            <a href="{{ route('gik-hrd.violations.index') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg text-sm font-semibold transition">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5 text-left">No. Laporan</th>
                    <th class="px-6 py-3.5 text-left">Peserta</th>
                    <th class="px-6 py-3.5 text-left">Kejadian</th>
                    <th class="px-6 py-3.5 text-left">Pelanggaran</th>
                    <th class="px-6 py-3.5 text-left">Status</th>
                    <th class="px-6 py-3.5 text-left">Dilaporkan</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $rep)
                <tr class="hover:bg-red-50/50 transition">
                    <td class="px-6 py-4 font-mono text-xs font-bold text-gik-primary-dark">{{ $rep->report_number }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-[11px] font-extrabold shadow-sm">
                                {{ $rep->intern?->initials ?? '??' }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 leading-tight">{{ $rep->intern?->full_name ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $rep->intern?->job_title ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-semibold text-gray-700">{{ optional($rep->incident_date)->format('d M Y') }}</div>
                        @if($rep->incident_time)<div class="text-[11px] text-gray-500">Jam {{ substr($rep->incident_time, 0, 5) }}</div>@endif
                    </td>
                    <td class="px-6 py-4 max-w-[280px]">
                        <div class="text-xs text-gray-700 line-clamp-2 leading-snug">{{ \Illuminate\Support\Str::limit($rep->description, 100) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rep->status_badge_class }}">{{ $rep->status_label }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        <div>{{ $rep->created_at->diffForHumans() }}</div>
                        <div class="text-[10px] text-gray-400">oleh {{ $rep->reportedBy?->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('gik-hrd.violations.show', $rep) }}" class="text-xs font-semibold text-gik-accent hover:text-gik-primary hover:underline px-2">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-700 mb-1">Belum ada laporan pelanggaran</h3>
                        <p class="text-sm text-gray-500 mb-3">Laporkan pelanggaran melalui tombol "Laporkan Pelanggaran" pada daftar peserta.</p>
                        <a href="{{ route('gik-hrd.operations') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition">Ke Daftar Peserta</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex items-center justify-between rounded-b-2xl">
            <span>Menampilkan {{ $reports->firstItem() ?? 0 }}–{{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} laporan</span>
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
