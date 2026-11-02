@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-8 flex flex-wrap items-start justify-between gap-5">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin Recruitment • Disiplin & Penegakan</div>
        <h1 class="text-[32px] font-extrabold text-gik-primary-dark leading-tight tracking-tight">Manajemen Pelanggaran Peserta</h1>
        <p class="text-gray-500 text-sm mt-1.5 max-w-xl">Kelola dan tindaklanjuti laporan pelanggaran dari seluruh HR Company. Pastikan setiap kasus mendapatkan solusi yang adil & terdokumentasi.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('gik-superadmin.employees') }}" class="px-5 py-3 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold transition">
            ← Ke Daftar Peserta
        </a>
    </div>
</div>

@php
$stats = [
    ['key' => 'pending_review', 'label' => 'Menunggu Tinjauan', 'icon' => '⏳', 'bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500'],
    ['key' => 'contacted', 'label' => 'Sudah Dihubungi', 'icon' => '📞', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'dot' => 'bg-blue-500'],
    ['key' => 'in_process', 'label' => 'Sedang Diproses', 'icon' => '⚙️', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'dot' => 'bg-orange-500'],
    ['key' => 'resolved', 'label' => 'Selesai', 'icon' => '✅', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500'],
    ['key' => 'rejected', 'label' => 'Ditolak', 'icon' => '⛔', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500'],
];
@endphp
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-7">
    @foreach($stats as $st)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden group hover:shadow-md transition">
        <div class="absolute right-[-12px] top-[-12px] w-16 h-16 {{ $st['bg'] }} rounded-full opacity-50 group-hover:scale-110 transition"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full {{ $st['dot'] }} animate-pulse"></span>
                <div class="text-[11px] font-extrabold uppercase text-gray-500 tracking-wide">{{ $st['label'] }}</div>
            </div>
            <div class="flex items-end justify-between">
                <div class="text-4xl font-black {{ $st['text'] }} leading-none">{{ $statusCounts[$st['key']] ?? 0 }}</div>
                <div class="text-2xl opacity-70">{{ $st['icon'] }}</div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 text-[11px] text-gray-400 flex items-center justify-between">
                <span>Total Laporan: <strong class="text-gray-600">{{ $statusCounts['total'] ?? 0 }}</strong></span>
                <a href="?status={{ $st['key'] }}" class="text-gik-accent hover:underline font-semibold">Lihat →</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div><strong>Error!</strong> {{ session('error') }}</div>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-7 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <form method="GET" class="space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[260px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama peserta, perusahaan, nomor laporan, atau deskripsi..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 text-sm bg-white">
                </div>
                <select name="status" class="px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-200 min-w-[180px]">
                    <option value="">Semua Status</option>
                    <option value="pending_review" {{ ($status ?? '') === 'pending_review' ? 'selected' : '' }}>⏳ Menunggu Tinjauan</option>
                    <option value="contacted" {{ ($status ?? '') === 'contacted' ? 'selected' : '' }}>📞 Sudah Dihubungi</option>
                    <option value="in_process" {{ ($status ?? '') === 'in_process' ? 'selected' : '' }}>⚙️ Sedang Diproses</option>
                    <option value="resolved" {{ ($status ?? '') === 'resolved' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>⛔ Ditolak</option>
                </select>
                <button type="submit" class="px-5 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold transition shadow-sm shadow-red-200">
                    🔍 Terapkan
                </button>
                <a href="{{ route('gik-superadmin.violations.index') }}" class="px-5 py-3 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl text-sm font-bold transition">
                    ↺ Reset
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-[11px] font-extrabold uppercase text-gray-500 tracking-widest px-1">Filter Perusahaan:</div>
                <a href="{{ request()->fullUrlWithQuery(['company_id' => 'all'] + array_diff_key(request()->query(), array_flip(['company_id']))) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ ($companyId ?? 'all') === 'all' ? 'bg-gik-primary text-white shadow-sm shadow-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    🌐 SEMUA PERUSAHAAN ({{ $statusCounts['total'] ?? 0 }})
                </a>
                @foreach($companies as $c)
                @php $cCount = $c->violationReports() ? ($c->violationReports()->withoutTenant()->count() ?? 0) : 0; @endphp
                @if($cCount > 0 || true)
                <a href="{{ request()->fullUrlWithQuery(['company_id' => $c->id] + array_diff_key(request()->query(), array_flip(['company_id']))) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ ($companyId ?? '') == $c->id ? 'bg-gik-primary text-white shadow-sm shadow-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ \Illuminate\Support\Str::limit($c->company_name, 20) }} <span class="opacity-75">({{ $cCount }})</span>
                </a>
                @endif
                @endforeach
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-500 uppercase">Rentang Kejadian:</span>
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-200">
                    <span class="text-gray-400 text-xs font-bold">sampai</span>
                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-200">
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-7 py-4 text-left font-bold">No. Laporan</th>
                    <th class="px-7 py-4 text-left font-bold">Peserta</th>
                    <th class="px-7 py-4 text-left font-bold">Perusahaan Pelapor</th>
                    <th class="px-7 py-4 text-left font-bold">Kejadian</th>
                    <th class="px-7 py-4 text-left font-bold">Ringkasan</th>
                    <th class="px-7 py-4 text-left font-bold">Status</th>
                    <th class="px-7 py-4 text-left font-bold">Progres</th>
                    <th class="px-7 py-4 text-right font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $rep)
                @php
                $progressPct = match($rep->status) {
                    'pending_review' => 10,
                    'contacted' => 35,
                    'in_process' => 65,
                    'resolved', 'rejected' => 100,
                    default => 0,
                };
                @endphp
                <tr class="hover:bg-red-50/40 transition group">
                    <td class="px-7 py-4 font-mono text-[12px] font-extrabold text-gik-primary-dark">{{ $rep->report_number }}</td>
                    <td class="px-7 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-[12px] font-extrabold shadow-sm group-hover:scale-110 transition">
                                {{ $rep->intern?->initials ?? '??' }}
                            </div>
                            <div>
                                <div class="font-extrabold text-gray-800 leading-tight">{{ $rep->intern?->full_name ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $rep->intern?->job_title ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-7 py-4">
                        <div class="text-xs font-bold text-gray-700">{{ \Illuminate\Support\Str::limit($rep->company?->company_name ?? '-', 24) }}</div>
                        <div class="text-[10px] text-gray-400">oleh {{ $rep->reportedBy?->name ?? '-' }}</div>
                    </td>
                    <td class="px-7 py-4 whitespace-nowrap">
                        <div class="text-[11px] font-bold text-gray-700">{{ optional($rep->incident_date)->format('d M Y') }}</div>
                        @if($rep->incident_time)<div class="text-[10px] text-gray-500">🕒 {{ substr($rep->incident_time, 0, 5) }}</div>@endif
                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $rep->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-7 py-4 max-w-[280px]">
                        <div class="text-xs text-gray-700 line-clamp-2 leading-snug">{{ \Illuminate\Support\Str::limit($rep->description, 110) }}</div>
                        @if($rep->evidence_file)
                        <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-700 rounded-md text-[9px] font-bold uppercase">📎 Ada bukti</div>
                        @endif
                    </td>
                    <td class="px-7 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold {{ $rep->status_badge_class }} shadow-sm">
                            {{ $rep->status_label }}
                        </span>
                    </td>
                    <td class="px-7 py-4 w-[140px]">
                        <div class="flex items-center justify-between text-[9px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">
                            <span>Progress</span><span class="text-gik-primary-dark">{{ $progressPct }}%</span>
                        </div>
                        <div class="gik-progress">
                            <div class="gik-progress-bar bg-gradient-to-r from-gik-primary to-red-400" style="width: {{ $progressPct }}%"></div>
                        </div>
                    </td>
                    <td class="px-7 py-4 text-right whitespace-nowrap">
                        <div class="inline-flex items-center gap-2 text-xs">
                            <a href="{{ route('gik-superadmin.violations.show', $rep) }}"
                                class="px-3 py-1.5 bg-gik-primary/10 text-gik-primary-dark hover:bg-gik-primary hover:text-white rounded-lg text-[11px] font-bold transition">
                                Detail & Tindak Lanjut →
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-7 py-16 text-center">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-extrabold text-gray-700 text-lg mb-1">Belum ada laporan pelanggaran masuk</h3>
                        <p class="text-sm text-gray-500 mb-4 max-w-md mx-auto">Laporan pelanggaran dari HR Company akan muncul di sini untuk ditindaklanjuti.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
        <div class="px-7 py-5 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex items-center justify-between rounded-b-2xl">
            <span class="font-semibold">Menampilkan {{ $reports->firstItem() ?? 0 }}–{{ $reports->lastItem() ?? 0 }} dari total {{ $reports->total() }} laporan pelanggaran</span>
            {{ $reports->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
