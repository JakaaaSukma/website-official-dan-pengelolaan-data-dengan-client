@extends('layouts.gik-superadmin')

@section('page-title', 'Manajemen Pemberhentian Sementara')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gik-primary-dark">⏸ Manajemen Pemberhentian Sementara</h2>
    <p class="text-gray-500 mt-1 text-sm">Review & setujui pengajuan pemberhentian sementara dari seluruh HR Company.</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('warning') }}
    </div>
@endif
@if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('info') }}
    </div>
@endif
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        <ul class="list-disc ml-4 space-y-1 text-sm">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-xl p-5 shadow-md relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                <span class="text-xs font-semibold uppercase tracking-wider opacity-90">Pending Approval</span>
            </div>
            <div class="text-3xl font-black">{{ $stats['pending_approval'] }}</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl p-5 shadow-md relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                <span class="text-xs font-semibold uppercase tracking-wider opacity-90">Under Review</span>
            </div>
            <div class="text-3xl font-black">{{ $stats['under_review'] }}</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-600 to-fuchsia-800 text-white rounded-xl p-5 shadow-md relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative">
            <span class="text-xs font-semibold uppercase tracking-wider opacity-90">Disetujui (Total)</span>
            <div class="text-3xl font-black mt-1">{{ $stats['approved'] }}</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-fuchsia-900 to-purple-950 text-white rounded-xl p-5 shadow-md relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative">
            <span class="text-xs font-semibold uppercase tracking-wider opacity-90">⏱ Aktif Berlangsung Hari Ini</span>
            <div class="text-3xl font-black mt-1">{{ $stats['active_today'] }}</div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-rose-700 text-white rounded-xl p-5 shadow-md relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative">
            <span class="text-xs font-semibold uppercase tracking-wider opacity-90">Ditolak</span>
            <div class="text-3xl font-black mt-1">{{ $stats['rejected'] }}</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-gik-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
        Filter Perusahaan (Tenant View)
    </h4>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('gik-superadmin.suspensions.index') }}" class="px-3 py-1.5 text-xs rounded-full border-2 {{ request('company_id') ? 'border-gray-200 text-gray-600 hover:bg-gray-50' : 'border-gik-primary bg-gik-primary text-white font-bold shadow-sm' }} transition-colors">
            🌐 Semua Perusahaan
        </a>
        @foreach($companies as $c)
            @php
                $cPending = $tenantStats->where('company_id', $c->id)->where('status', 'pending_approval')->sum('total') ?? 0;
                $cActive = $tenantStats->where('company_id', $c->id)->sum('total') ?? 0;
            @endphp
            <a href="{{ route('gik-superadmin.suspensions.index', ['company_id' => $c->id]) }}" class="px-3 py-1.5 text-xs rounded-full border-2 {{ request('company_id') == $c->id ? 'border-gik-primary bg-gik-primary/10 text-gik-primary-dark font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }} transition-colors flex items-center gap-2">
                <span class="font-medium truncate max-w-[200px]">{{ $c->company_name }}</span>
                <span class="inline-flex items-center gap-1">
                    @if($cPending > 0) <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-orange-500 text-white font-bold">{{ $cPending }} pending</span> @endif
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-gray-200 text-gray-700 font-semibold">{{ $cActive }}</span>
                </span>
            </a>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('gik-superadmin.suspensions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="hidden" name="company_id" value="{{ request('company_id') }}">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Nama Peserta</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none" placeholder="Ketik nama...">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Perusahaan</label>
            <select name="company_id_2" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none" onchange="if(this.value){document.querySelector('input[name=company_id]').value=this.value;this.form.submit();}">
                <option value="">-- Semua --</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @if(request('company_id') == $c->id) selected @endif>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
                <option value="">-- Semua Status --</option>
                @foreach($statusLabels as $k => $label)
                    <option value="{{ $k }}" @if(request('status') === $k) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tgl Submit Dari</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tgl Submit Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
        </div>
        <div class="md:col-span-5 flex gap-2 justify-end">
            <a href="{{ route('gik-superadmin.suspensions.index') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 font-medium">Reset</a>
            <button type="submit" class="px-5 py-2 text-sm bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg font-medium shadow-sm">
                🔍 Terapkan Filter
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-gik-primary-dark to-gik-primary text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Perusahaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Peserta</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Periode & Durasi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Progres</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Diajukan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $r)
                    @php
                        $progress = match($r->status){
                            'pending_approval' => 15,
                            'under_review' => 50,
                            'approved' => 100,
                            'rejected' => 100,
                            default => 0
                        };
                        $progressColor = match($r->status){
                            'pending_approval' => 'from-amber-400 to-orange-500',
                            'under_review' => 'from-blue-400 to-indigo-500',
                            'approved' => 'from-purple-500 to-fuchsia-700',
                            'rejected' => 'from-red-400 to-rose-600',
                            default => 'from-gray-300 to-gray-400'
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $r->request_number }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800 text-xs">{{ $r->company?->company_name ?? '-' }}</div>
                            <div class="text-[10px] text-gray-500">{{ $r->requestedBy?->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $r->intern?->full_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $r->intern?->position ?? '-' }}</div>
                            @if($r->intern?->is_currently_suspended)
                                <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] rounded-full bg-fuchsia-900 text-white font-bold shadow-sm">
                                    ⏸ SUSPEND AKTIF
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-gray-700">{{ $r->period_label }}</div>
                            <div class="text-[10px] text-gray-500 font-semibold">{{ $r->total_days }} hari</div>
                            @if($r->status === 'approved')
                                <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] rounded-full font-bold {{ $r->active_status_badge_class }}">{{ $r->active_status_text }}</span>
                                @if($r->days_remaining > 0 && $r->isCurrentlyActive())
                                    <div class="text-[10px] text-purple-700 mt-0.5">sisa {{ $r->days_remaining }} hari</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full {{ $r->status_badge_class }}">{{ $r->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 w-40">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] text-gray-500 font-semibold">{{ $progress }}%</span>
                            </div>
                            <div class="gik-progress h-2 w-full bg-gray-100 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-gradient-to-r {{ $progressColor }} rounded-full transition-all duration-700" style="width: {{ $progress }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ optional($r->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('gik-superadmin.suspensions.show', $r->id) }}" class="inline-block px-3 py-1.5 text-xs bg-gradient-to-r from-gik-primary to-red-600 hover:from-gik-primary-dark hover:to-red-700 text-white font-semibold rounded-md shadow-sm transition-all">
                                👁 Review & Update
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="mx-auto w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada pengajuan pemberhentian sementara yang sesuai filter.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->total() > $reports->perPage())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $reports->links() }}
        </div>
    @endif
</div>

@endsection
