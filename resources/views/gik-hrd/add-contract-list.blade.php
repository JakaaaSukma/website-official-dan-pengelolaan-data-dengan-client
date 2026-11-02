@extends(($isSuperAdmin ?? false) ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@section('content')
@php
    $curStatus = request('status', 'all');
    $counts = $batchCounts ?? ['all' => 0, 'submitted' => 0, 'reviewed' => 0, 'completed' => 0, 'draft' => 0];
    $isSA = ($isSuperAdmin ?? false);
    $rPrefix = $isSA ? 'gik-superadmin.' : 'gik-hrd.';
    $dashRoute = route($rPrefix . 'dashboard');
    $listRoute = route($rPrefix . 'add-contract.list');
    $showRoutePrefix = $rPrefix . 'add-contract.show';
    $pageSubtitle = $isSA
        ? 'Monitoring semua pengajuan kandidat baru (Add New Contract) untuk semua tenant Company A, B, C, D.'
        : 'Daftar pengajuan dari Tim Rekrutmen Super Admin untuk diseleksi.';
    $pageEyebrow = $isSA ? 'Super Admin • Semua Tenant' : 'HR Company • Seleksi';
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">{{ $pageEyebrow }}</div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Pengajuan Kandidat Baru</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $pageSubtitle }}</p>
    </div>
    <div class="flex items-center gap-3">
        @if($isSA)
            <a href="{{ route('gik-superadmin.add-contract.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-semibold transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add New Contract
            </a>
        @endif
        <a href="{{ $dashRoute }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Total Batch</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $counts['all'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Butuh Seleksi</div>
                <div class="text-2xl font-extrabold text-amber-700 leading-tight">{{ $counts['submitted'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Direview</div>
                <div class="text-2xl font-extrabold text-blue-700 leading-tight">{{ $counts['reviewed'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Selesai</div>
                <div class="text-2xl font-extrabold text-green-700 leading-tight">{{ $counts['completed'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="flex flex-wrap items-center gap-1 px-4 pt-4 border-b border-gray-100">
        @php
            $tabs = [
                'all' => ['label' => 'All', 'count' => $counts['all'] ?? 0],
                'submitted' => ['label' => 'Submitted', 'count' => $counts['submitted'] ?? 0],
                'reviewed' => ['label' => 'Reviewed', 'count' => $counts['reviewed'] ?? 0],
                'completed' => ['label' => 'Completed', 'count' => $counts['completed'] ?? 0],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            @php
                $active = ($curStatus === $key) || ($curStatus === '' && $key === 'all');
                $tabRouteParams = ($key === 'all') ? [] : ['status' => $key];
                $tabRoute = route($rPrefix . 'add-contract.list', $tabRouteParams);
            @endphp
            <a href="{{ $tabRoute }}" class="px-5 py-3 font-bold text-sm border-b-2 transition
                {{ $active ? 'border-gik-primary text-gik-primary' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                {{ $tab['label'] }}
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-extrabold
                    {{ $active ? 'bg-gik-accent-light text-gik-accent' : 'bg-gray-100 text-gray-600' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-3 px-6 py-4 border-b border-gray-100">
        <div class="relative flex-1 min-w-[220px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <form method="GET" action="{{ $listRoute }}" class="contents">
                @if($curStatus && $curStatus !== 'all')
                    <input type="hidden" name="status" value="{{ $curStatus }}">
                @endif
                <input type="text" name="q" value="{{ request('q', '') }}" placeholder="Search Batch ID, catatan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5 text-left">Batch ID</th>
                    <th class="px-6 py-3.5 text-left">Dikirim Pada</th>
                    <th class="px-6 py-3.5 text-left">Dari (Super Admin)</th>
                    <th class="px-6 py-3.5 text-left">Catatan</th>
                    <th class="px-6 py-3.5 text-left">Total Kandidat</th>
                    <th class="px-6 py-3.5 text-left">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($batches as $b)
                    @php
                        $st = $b->status ?? 'draft';
                        $statusBadgeClass = match($st) {
                            'completed', 'reviewed' => ['bg-green-100', 'text-green-700'],
                            'submitted', 'draft' => ['bg-amber-100', 'text-amber-700'],
                            default => ['bg-gray-100', 'text-gray-600'],
                        };
                        $selectedCount = $b->selected_count ?? ($b->candidates ? $b->candidates->where('status', 'selected')->count() : 0);
                        $notSelectedCount = $b->not_selected_count ?? ($b->candidates ? $b->candidates->where('status', 'not_selected')->count() : 0);
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gik-primary">{{ $b->batch_number }}</td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold text-gray-700">{{ $b->created_at ? $b->created_at->translatedFormat('d M Y') : '-' }}</div>
                            <div class="text-[10px] text-gray-500 font-semibold">{{ $b->created_at ? $b->created_at->translatedFormat('H:i') : '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-[10px] font-extrabold">
                                    {{ strtoupper(substr(optional($b->createdBy)->name ?? 'SA', 0, 1)) }}
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ optional($b->createdBy)->name ?? 'Super Admin' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-[240px]">
                            <div class="text-xs text-gray-600 line-clamp-1">{{ $b->notes ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="text-lg font-extrabold text-gray-800">{{ $b->candidates ? $b->candidates->count() : 0 }}</div>
                                @if(in_array($st, ['reviewed','completed']))
                                    <div class="text-[10px] font-bold space-x-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-green-100 text-green-700">✓{{ $selectedCount }}</span>
                                        <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700">✗{{ $notSelectedCount }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $statusBadgeClass[0] }} {{ $statusBadgeClass[1] }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ in_array($st, ['completed','reviewed']) ? 'bg-green-500' : 'bg-amber-500' }}
                                "></span>
                                {{ strtoupper($st) }}
                            </span>
                            @if($st === 'submitted')
                                <span class="ml-1.5 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-red-100 text-red-600 animate-pulse">ACTION NEEDED</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route($showRoutePrefix, $b) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2
                                {{ $st === 'submitted'
                                    ? 'bg-gik-primary hover:bg-gik-primary-dark text-white shadow-sm'
                                    : 'border border-gray-300 text-gray-700 bg-white hover:bg-gray-50'
                                }} rounded-lg text-xs font-bold transition">
                                @if($st === 'submitted')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Seleksi Sekarang
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Review Detail
                                @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-700 mb-1">Belum ada pengajuan kandidat dari Super Admin saat ini.</h3>
                        <p class="text-sm text-gray-500 mb-3">Pengajuan baru akan muncul di halaman ini ketika dikirim oleh Tim Rekrutmen.</p>
                        <a href="{{ $dashRoute }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gik-primary text-white rounded-lg text-sm font-semibold hover:bg-gik-primary-dark transition">Kembali ke Dashboard</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($batches->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-gray-500">
                Showing {{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} batches
            </div>
            {{ $batches->appends(request()->query())->links() }}
        </div>
    @elseif($batches->count() > 0)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="text-xs text-gray-500">Showing 1–{{ $batches->count() }} of {{ $batches->count() }} batches</div>
        </div>
    @endif
</div>
@endsection
