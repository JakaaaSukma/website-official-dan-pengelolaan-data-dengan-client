@extends('layouts.gik-hrd')

@section('gik-search-bar')
<div class="relative">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    <input type="text" placeholder="Search resources..." class="w-full pl-12 pr-4 py-2.5 bg-gray-100 border border-gray-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
</div>
@endsection

@section('content')
@php
    $companyName = $company?->company_name ?? (auth()->user()->company?->company_name ?? 'Company A');
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">{{ $companyName }} Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Manage active interns and monitor contract statuses.</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-5">
        <!-- Monitoring Peserta Aktif -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gik-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <h2 class="text-xl font-bold text-gray-800">Monitoring Peserta Aktif</h2>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-50/60 text-blue-700 text-sm font-semibold">
                    {{ $activeCount }} Active
                </span>
            </div>

            <!-- ============== BARIS BARU: BATCH SELECTION COUNTER + ACTION DROPDOWN ============== -->
            <div id="batch-bar" class="hidden mb-4 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-50 via-fuchsia-50 to-pink-50 border-2 border-purple-200 shadow-sm animate-pulse-once">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-600 to-fuchsia-700 text-white flex items-center justify-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-purple-700 uppercase tracking-wider">Mode Batch Aktif</div>
                            <div class="text-base font-extrabold text-fuchsia-900"><span id="batch-count">0</span> peserta dipilih</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="relative group">
                            <button id="batch-action-btn" type="button" disabled class="px-4 py-2.5 bg-gradient-to-r from-gik-primary to-red-600 disabled:from-gray-300 disabled:to-gray-400 hover:from-gik-primary-dark hover:to-red-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                Tindakan Massal ▼
                            </button>
                            <div id="batch-action-dropdown" class="hidden absolute right-0 top-full mt-2 min-w-[240px] bg-white rounded-xl shadow-2xl border border-gray-100 z-50 py-2 overflow-hidden">
                                <a href="#" id="batch-extend-link" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-emerald-50 text-sm font-bold text-emerald-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Extend Kontrak Massal
                                </a>
                                <a href="#" id="batch-offboarding-link" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-orange-50 text-sm font-bold text-orange-700 border-t border-gray-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Off-boarding Massal
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="#" id="batch-suspend-link" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-purple-50 text-sm font-bold text-purple-800 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Ajukan Pemberhentian Sementara Massal
                                </a>
                                <a href="#" id="batch-violation-link" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-red-50 text-sm font-bold text-red-700 border-t border-gray-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Laporkan Pelanggaran Massal
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <button type="button" id="batch-view-summary" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 flex items-center gap-2.5 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat Ringkasan Dipilih
                                </button>
                            </div>
                        </div>
                        <button type="button" id="batch-clear-btn" class="px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-white hover:border-gray-300 hover:text-gray-800 transition">
                            ✕ Reset Pilihan
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 mb-4">
                <form id="interns-search-form" method="GET" class="flex items-center gap-3 flex-1 flex-wrap">
                    <div class="relative flex-1 min-w-[240px] max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search interns by name or ID..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                    </div>
                    <select name="department" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                        <option value="all">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d }}" {{ $deptFilter === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Apply</button>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200">
                <table class="w-full text-sm" id="interns-table">
                    <thead class="bg-[#f3f4f8] text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3.5 w-[50px] rounded-tl-2xl">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 rounded border-gray-300 text-gik-primary focus:ring-gik-primary">
                                    <span class="text-xs font-bold text-gray-500">ALL</span>
                                </label>
                            </th>
                            <th class="text-left px-5 py-3.5 font-semibold">Intern Details</th>
                            <th class="text-left px-5 py-3.5 font-semibold">Department & Role</th>
                            <th class="text-left px-5 py-3.5 font-semibold">Contract Progress</th>
                            <th class="text-right px-5 py-3.5 font-semibold w-[180px] rounded-tr-2xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($contracts as $c)
                            @php
                                $pct = $c->contractProgressPercentage();
                                $remaining = $c->contractRemainingLabel();
                                $endDate = $c->contract_end->format('M d, Y');
                                $days = $c->daysUntilExpiry();
                                $urgent = $days >= 0 && $days <= 14;
                                $isSuspendedDisabled = $c->is_currently_suspended || $c->has_pending_suspension_request;
                            @endphp
                            <tr class="{{ $urgent ? 'bg-amber-50/50 hover:bg-amber-50' : 'hover:bg-gray-50' }} intern-row" data-contract-id="{{ $c->id }}">
                                <td class="px-4 py-4 align-top">
                                    <input type="checkbox" class="intern-checkbox w-4 h-4 rounded border-gray-300 text-gik-primary focus:ring-gik-primary cursor-pointer" value="{{ $c->id }}" data-contract-id="{{ $c->id }}">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">
                                            {{ $c->initials }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">
                                                {{ $c->full_name }}
                                                @if($c->has_pending_suspension_request)
                                                    <span class="ml-1 inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] rounded-full bg-amber-100 text-amber-800 font-bold border border-amber-200">⏸ Menunggu Persetujuan</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs text-gray-500">{{ $c->employee_id ?? '—' }}</span>
                                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full {{ $c->status_badge_class }}">{{ $c->status_display_label }}</span>
                                            </div>
                                            @if($c->is_currently_suspended && $c->current_suspension_period_label)
                                                <div class="mt-1 text-[11px] font-semibold text-fuchsia-900 inline-flex items-center gap-1 bg-fuchsia-50 border border-fuchsia-100 px-2 py-0.5 rounded-md">
                                                    ⏸ {{ $c->current_suspension_period_label }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-800">{{ $c->department }}</div>
                                    <div class="text-xs text-gray-500">{{ $c->job_title }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-semibold {{ $urgent ? 'text-amber-700' : 'text-gray-700' }}">{{ $remaining }}</span>
                                        <span class="text-xs font-bold text-gray-700">{{ $pct }}%</span>
                                    </div>
                                    <div class="gik-progress w-full">
                                        <div class="gik-progress-bar {{ $c->progress_bar_class }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1.5">End: {{ $endDate }}</div>
                                </td>
                                <td class="px-5 py-4 text-right align-top">
                                    <div class="flex flex-col items-end gap-2">
                                        <button type="button" class="btn-view-detail inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg bg-gik-primary/10 text-gik-primary-dark hover:bg-gik-primary hover:text-white transition"
                                            data-id="{{ $c->id }}"
                                            data-nama="{{ $c->full_name }}"
                                            data-employee-id="{{ $c->employee_id ?? '—' }}"
                                            data-nik="{{ $c->national_id ?? '—' }}"
                                            data-phone="{{ $c->phone ?? '—' }}"
                                            data-email="{{ $c->email ?? '—' }}"
                                            data-posisi="{{ $c->job_title ?? '—' }}"
                                            data-dept="{{ $c->department ?? '—' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Kandidat
                                        </button>
                                        <div class="relative group inline-block">
                                            <button type="button" class="row-action-btn inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition group-hover:bg-gradient-to-r group-hover:from-gik-primary group-hover:to-red-600 group-hover:text-white group-hover:shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                Tindakan ▼
                                            </button>
                                            <div class="row-action-dropdown hidden absolute right-0 top-full mt-1.5 min-w-[230px] bg-white rounded-xl shadow-2xl border border-gray-100 z-40 py-2 overflow-hidden">
                                                <a href="{{ route('gik-hrd.quick.action', 'extend_contract') }}?ids={{ $c->id }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-emerald-50 text-sm font-bold text-emerald-800 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Perpanjang Kontrak
                                                </a>
                                                <a href="{{ route('gik-hrd.quick.action', 'off_boarding') }}?ids={{ $c->id }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-orange-50 text-sm font-bold text-orange-700 border-t border-gray-100 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                    Ajukan Off-boarding
                                                </a>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                @if($isSuspendedDisabled)
                                                    <span class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-gray-400 cursor-not-allowed" title="{{ $c->is_currently_suspended ? 'Masih dalam masa pemberhentian sementara s/d '.optional($c->current_suspension)->end_date->format('d M Y') : 'Sudah ada pengajuan pemberhentian sementara yang masih menunggu persetujuan' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Ajukan Suspend (disabled)
                                                    </span>
                                                @else
                                                    <a href="{{ route('gik-hrd.suspensions.create', $c) }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-purple-50 text-sm font-bold text-purple-800 transition">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Ajukan Pemberhentian Sementara
                                                    </a>
                                                @endif
                                                <a href="{{ route('gik-hrd.violations.create', $c) }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-red-50 text-sm font-bold text-red-700 border-t border-gray-100 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Laporkan Pelanggaran
                                                </a>
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <a href="{{ route('gik-hrd.employees.show', $c->id) }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 text-sm font-semibold text-gray-700 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Lihat Detail Lengkap
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-400">Tidak ada data peserta. Klik "New Intern/Employee" untuk menambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/60 flex items-center justify-between rounded-b-2xl">
                    <div class="text-sm text-gray-500">
                        Showing {{ $contracts->firstItem() ?? 0 }}–{{ $contracts->lastItem() ?? 0 }} of {{ $contracts->total() }} interns
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if($contracts->onFirstPage())
                            <span class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white text-gray-400">Prev</span>
                        @else
                            <a href="{{ $contracts->previousPageUrl() }}" class="page-nav-link px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white text-gray-700 hover:bg-gray-100">Prev</a>
                        @endif
                        @if($contracts->hasMorePages())
                            <a href="{{ $contracts->nextPageUrl() }}" class="page-nav-link px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white text-gray-700 hover:bg-gray-100">Next</a>
                        @else
                            <span class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg bg-white text-gray-400">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-5">
        <!-- Contract Expiring Soon -->
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-red-700 text-lg leading-tight">Contract Expiring Soon</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $expiringSoon->count() }} interns require immediate action before contract ends.</p>
                </div>
            </div>
            <div class="space-y-2.5">
                @forelse($expiringSoon as $exp)
                    @php
                        $days = $exp->daysUntilExpiry();
                        $endLabel = $days <= 0 ? 'Expired' : "Ends in {$days} day" . ($days !== 1 ? 's' : '');
                    @endphp
                    <div class="p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="font-semibold text-sm text-gray-800">{{ $exp->full_name }}</div>
                                <div class="text-xs {{ $days <= 14 ? 'text-amber-700 font-semibold' : 'text-gray-500' }}">{{ $endLabel }}</div>
                            </div>
                            <a href="{{ route('gik-hrd.employees.review-expiring', $exp->id) }}" class="text-xs font-bold text-gik-accent hover:text-gik-primary hover:underline px-2 py-1">Review</a>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl">
                        No contracts expiring soon.
                    </div>
                @endforelse
            </div>
            <a href="{{ route('gik-hrd.operations') }}" class="mt-4 w-full block text-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gik-accent hover:bg-gray-50 transition">
                View All Alerts
            </a>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div class="text-3xl font-extrabold text-gray-800 leading-none">{{ $newThisMonth }}</div>
                <div class="text-xs text-gray-500 mt-1.5">New this month</div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-3xl font-extrabold text-gray-800 leading-none">{{ $completedContracts }}</div>
                <div class="text-xs text-gray-500 mt-1.5">Completed contracts</div>
            </div>
        </div>

        <!-- Quick Links to Pusat Pengajuan / Hub -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-800">Quick Actions</h3>
                <a href="{{ route('gik-hrd.request-hub') }}" class="text-xs font-bold text-gik-primary hover:underline">Semua &rarr;</a>
            </div>
            <div class="space-y-2.5">
                <a href="{{ route('gik-hrd.add-contract.list') }}" class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-green-50 hover:border-green-200 transition group">
                    <div class="w-9 h-9 rounded-lg bg-green-100 text-green-700 flex items-center justify-center group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800 group-hover:text-green-800">Review Kandidat Baru</div>
                        <div class="text-[11px] text-gray-500 truncate">Proposal Add Contract dari Gyokai</div>
                    </div>
                </a>
                <a href="{{ route('gik-hrd.batch.select', 'extend_contract') }}" class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800 group-hover:text-blue-800">Extend Contract Batch</div>
                        <div class="text-[11px] text-gray-500 truncate">Perpanjang kontrak banyak peserta</div>
                    </div>
                </a>
                <a href="{{ route('gik-hrd.batch.select', 'off_boarding') }}" class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-red-50 hover:border-red-200 transition group">
                    <div class="w-9 h-9 rounded-lg bg-red-100 text-red-700 flex items-center justify-center group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800 group-hover:text-red-800">Off-boarding Batch</div>
                        <div class="text-[11px] text-gray-500 truncate">Pelepasan / akhir kontrak peserta</div>
                    </div>
                </a>
                <a href="{{ route('gik-hrd.operations') }}" class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center group-hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800">Riwayat & Status Request</div>
                        <div class="text-[11px] text-gray-500 truncate">Pantau status & persetujuan</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ============ MODAL VIEW DATA KANDIDAT ============ -->
<div id="view-candidate-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="view-candidate-overlay"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[85vh] overflow-hidden flex flex-col animate-slide-in">
        <div class="bg-gradient-to-r from-gik-primary-dark via-gik-primary to-red-700 px-6 py-5 text-white">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div id="candidate-avatar" class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm text-white text-lg font-bold flex items-center justify-center border-2 border-white/30 shadow-lg">XX</div>
                    <div>
                        <h3 id="candidate-nama" class="text-xl font-extrabold leading-tight">Nama Kandidat</h3>
                        <p class="text-xs text-white/80 mt-0.5" id="candidate-posisi-dept">Posisi • Departemen</p>
                    </div>
                </div>
                <button type="button" id="close-candidate-modal" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 gap-3">
                <div class="p-4 bg-gradient-to-br from-gray-50 to-white border border-gray-100 rounded-xl">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">👤 Nomor Induk Pekerja (ID Karyawan)</div>
                    <div class="text-base font-extrabold text-gray-800" id="candidate-employee-id">—</div>
                </div>
                <div class="p-4 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl">
                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1">🆔 NIK (Nomor Induk Kependudukan / NIM)</div>
                    <div class="text-base font-extrabold text-gray-800" id="candidate-nik">—</div>
                </div>
                <div class="p-4 bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-xl">
                    <div class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">📞 Nomor Handphone (WA)</div>
                    <div class="text-base font-extrabold text-gray-800 flex items-center gap-2">
                        <span id="candidate-phone">—</span>
                        <a id="candidate-phone-wa" href="#" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold shadow-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WA
                        </a>
                    </div>
                </div>
                <div class="p-4 bg-gradient-to-br from-purple-50 to-white border border-purple-100 rounded-xl">
                    <div class="text-[10px] font-bold text-purple-600 uppercase tracking-widest mb-1">✉️ Email Aktif</div>
                    <div class="text-base font-extrabold text-gray-800 break-all" id="candidate-email">—</div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex items-center justify-end gap-2">
            <button type="button" id="close-candidate-modal-2" class="px-4 py-2 text-sm border border-gray-200 rounded-xl hover:bg-white hover:border-gray-300 text-gray-600 font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ============ RINGKASAN PESERTA DIPILIH MODAL ============ -->
<div id="batch-summary-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="batch-summary-overlay"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-purple-700 to-fuchsia-800 px-6 py-5 text-white">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold leading-tight">Ringkasan Peserta Dipilih</h3>
                        <p class="text-xs text-white/80 mt-0.5"><span id="batch-summary-count">0</span> peserta akan dikenakan tindakan massal</p>
                    </div>
                </div>
                <button type="button" id="close-summary-modal" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="p-4 overflow-y-auto flex-1">
            <div id="batch-summary-list" class="space-y-2"></div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex items-center justify-between gap-2 flex-wrap">
            <button type="button" id="summary-clear-btn" class="px-3 py-2 text-xs border border-gray-200 rounded-lg bg-white hover:bg-gray-100 text-gray-600 font-bold transition">
                ✕ Reset Semua Pilihan
            </button>
            <button type="button" id="close-summary-modal-2" class="px-4 py-2 text-sm bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl font-bold shadow-sm transition">
                Tutup & Lanjutkan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
@keyframes slideInModal {
    from { transform: translateY(30px) scale(0.95); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
.animate-slide-in { animation: slideInModal 0.2s ease-out; }
@keyframes pulseOnce {
    0% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(168, 85, 247, 0); }
    100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0); }
}
.animate-pulse-once { animation: pulseOnce 1.5s ease-out; }
.intern-row.selected td { background-color: rgba(217, 70, 239, 0.05); }
</style>
<script>
(function() {
    const STORAGE_KEY = 'gik_selected_intern_ids_' + '{{ auth()->id() }}';

    function loadSelectedIds() {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            return new Set(raw ? JSON.parse(raw) : []);
        } catch(e) { return new Set(); }
    }
    function saveSelectedIds(set) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify([...set]));
    }
    function getInitials(name) {
        const parts = String(name || 'XX').trim().split(/\s+/).filter(Boolean);
        if (parts.length === 0) return 'XX';
        if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }

    let selected = loadSelectedIds();

    function refreshBatchBar() {
        const count = selected.size;
        const bar = document.getElementById('batch-bar');
        const counter = document.getElementById('batch-count');
        const btn = document.getElementById('batch-action-btn');
        if (count > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
            counter.textContent = count;
            btn.disabled = false;
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
            counter.textContent = 0;
            btn.disabled = true;
        }

        // Update URLs batch links — quick.action route tidak butuh parameter route (cuma ?ids= query string)
        const ids = [...selected].join(',');
        const suspendLink = document.getElementById('batch-suspend-link');
        const violationLink = document.getElementById('batch-violation-link');
        const extendLink = document.getElementById('batch-extend-link');
        const offLink = document.getElementById('batch-offboarding-link');
        if (ids) {
            suspendLink.href = "{{ route('gik-hrd.suspensions.create', 0) }}?ids=" + encodeURIComponent(ids);
            violationLink.href = "{{ route('gik-hrd.violations.create', 0) }}?ids=" + encodeURIComponent(ids);
            extendLink.href = "{{ route('gik-hrd.quick.action', 'extend_contract') }}?ids=" + encodeURIComponent(ids);
            offLink.href = "{{ route('gik-hrd.quick.action', 'off_boarding') }}?ids=" + encodeURIComponent(ids);
        } else {
            suspendLink.href = '#';
            violationLink.href = '#';
            extendLink.href = '#';
            offLink.href = '#';
        }
    }

    function applyCheckboxStates() {
        const boxes = document.querySelectorAll('.intern-checkbox');
        let allChecked = boxes.length > 0;
        boxes.forEach(b => {
            const id = b.value;
            const row = b.closest('.intern-row');
            if (selected.has(id)) {
                b.checked = true;
                if (row) row.classList.add('selected');
            } else {
                b.checked = false;
                if (row) row.classList.remove('selected');
            }
            if (!b.checked) allChecked = false;
        });
        document.getElementById('select-all-checkbox').checked = allChecked && boxes.length > 0;
    }

    function toggleId(id, add) {
        id = String(id);
        if (add) selected.add(id); else selected.delete(id);
        saveSelectedIds(selected);
        refreshBatchBar();
    }

    function clearAll() {
        selected.clear();
        saveSelectedIds(selected);
        applyCheckboxStates();
        refreshBatchBar();
    }

    // ===== Init =====
    document.addEventListener('DOMContentLoaded', function() {
        applyCheckboxStates();
        refreshBatchBar();

        // ===== Checkbox per row =====
        document.querySelectorAll('.intern-checkbox').forEach(box => {
            box.addEventListener('change', e => {
                toggleId(box.value, box.checked);
                const row = box.closest('.intern-row');
                if (row) {
                    if (box.checked) row.classList.add('selected'); else row.classList.remove('selected');
                }
                const boxes = document.querySelectorAll('.intern-checkbox');
                document.getElementById('select-all-checkbox').checked = [...boxes].every(b => b.checked) && boxes.length > 0;
            });
        });

        // ===== Select all =====
        document.getElementById('select-all-checkbox').addEventListener('change', e => {
            const isChecked = e.target.checked;
            document.querySelectorAll('.intern-checkbox').forEach(b => {
                b.checked = isChecked;
                toggleId(b.value, isChecked);
                const row = b.closest('.intern-row');
                if (row) { if (isChecked) row.classList.add('selected'); else row.classList.remove('selected'); }
            });
        });

        // ===== Batch clear button =====
        document.getElementById('batch-clear-btn').addEventListener('click', clearAll);

        // ===== Batch action dropdown show/hide =====
        const batchBtn = document.getElementById('batch-action-btn');
        const batchDrop = document.getElementById('batch-action-dropdown');
        batchBtn.addEventListener('click', e => {
            e.stopPropagation();
            batchDrop.classList.toggle('hidden');
            document.querySelectorAll('.row-action-dropdown').forEach(d => d.classList.add('hidden'));
        });
        document.addEventListener('click', e => {
            if (!batchDrop.contains(e.target) && e.target !== batchBtn) batchDrop.classList.add('hidden');
        });

        // ===== Row action dropdown show/hide =====
        document.querySelectorAll('.row-action-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                const dd = btn.parentElement.querySelector('.row-action-dropdown');
                const isHidden = dd.classList.contains('hidden');
                document.querySelectorAll('.row-action-dropdown').forEach(d => d.classList.add('hidden'));
                batchDrop.classList.add('hidden');
                if (isHidden) dd.classList.remove('hidden');
            });
        });
        document.addEventListener('click', () => {
            document.querySelectorAll('.row-action-dropdown').forEach(d => d.classList.add('hidden'));
        });

        // ===== Before search form submit / page navigation: state already saved via event listeners =====
        const searchForm = document.getElementById('interns-search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', () => {
                saveSelectedIds(selected); // redundant, tapi safety
            });
        }
        // Also intercept page link clicks for Prev/Next
        document.querySelectorAll('.page-nav-link').forEach(a => {
            a.addEventListener('click', () => saveSelectedIds(selected));
        });

        // ===== View Candidate Modal =====
        const modal = document.getElementById('view-candidate-modal');
        const overlay = document.getElementById('view-candidate-overlay');
        const closeBtns = [document.getElementById('close-candidate-modal'), document.getElementById('close-candidate-modal-2')];

        function openModal(data) {
            const avatar = document.getElementById('candidate-avatar');
            avatar.textContent = getInitials(data.nama);
            document.getElementById('candidate-nama').textContent = data.nama;
            document.getElementById('candidate-posisi-dept').textContent = (data.posisi || '—') + ' • ' + (data.dept || '—');
            document.getElementById('candidate-employee-id').textContent = data.employeeId || '—';
            document.getElementById('candidate-nik').textContent = data.nik || '—';
            document.getElementById('candidate-phone').textContent = data.phone || '—';
            document.getElementById('candidate-email').textContent = data.email || '—';
            const wa = document.getElementById('candidate-phone-wa');
            if (data.phone && data.phone !== '—') {
                const waNum = String(data.phone).replace(/\D/g, '').replace(/^0/, '62');
                wa.href = 'https://wa.me/' + waNum;
                wa.style.display = 'inline-flex';
            } else {
                wa.href = '#';
                wa.style.display = 'none';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
        closeBtns.forEach(b => b && b.addEventListener('click', closeModal));
        overlay && overlay.addEventListener('click', closeModal);

        document.querySelectorAll('.btn-view-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const d = btn.dataset;
                openModal({
                    nama: d.nama, posisi: d.posisi, dept: d.dept,
                    employeeId: d.employeeId, nik: d.nik, phone: d.phone, email: d.email
                });
            });
        });

        // ===== Batch Summary Modal =====
        const sumModal = document.getElementById('batch-summary-modal');
        const sumOverlay = document.getElementById('batch-summary-overlay');
        const sumClose = [document.getElementById('close-summary-modal'), document.getElementById('close-summary-modal-2'), document.getElementById('summary-clear-btn')];
        function openSummary() {
            const ids = [...selected];
            document.getElementById('batch-summary-count').textContent = ids.length;
            const list = document.getElementById('batch-summary-list');
            list.innerHTML = '';
            if (ids.length === 0) {
                list.innerHTML = '<div class="text-center py-6 text-gray-400 text-sm">Belum ada peserta yang dipilih.</div>';
            } else {
                ids.forEach((id, idx) => {
                    // Cari data dari row checkbox (jika tampil di halaman ini), atau tampilkan ID saja
                    let row = document.querySelector(`.intern-checkbox[value="${id}"]`)?.closest('.intern-row');
                    let nama = 'Peserta ID #' + id;
                    let empId = '';
                    if (row) {
                        nama = row.querySelector('.font-semibold')?.childNodes[0]?.nodeValue?.trim() || nama;
                        empId = row.querySelector('.text-gray-500')?.textContent?.trim() || '';
                    }
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between gap-3 p-3 border border-gray-100 rounded-xl bg-gradient-to-r from-white to-gray-50 hover:from-purple-50 hover:to-fuchsia-50 transition';
                    div.innerHTML = `
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">${idx + 1}</div>
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-gray-800 truncate">${nama}</div>
                                ${empId ? `<div class="text-[11px] text-gray-500 font-medium">${empId}</div>` : ''}
                            </div>
                        </div>
                        <button type="button" data-id="${id}" class="sum-uncheck-btn flex-shrink-0 w-7 h-7 rounded-full hover:bg-red-100 text-gray-400 hover:text-red-600 flex items-center justify-center transition" title="Hapus pilihan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    `;
                    list.appendChild(div);
                });
                // Bind uncheck buttons
                list.querySelectorAll('.sum-uncheck-btn').forEach(b => {
                    b.addEventListener('click', () => {
                        const id = b.dataset.id;
                        selected.delete(id);
                        saveSelectedIds(selected);
                        applyCheckboxStates();
                        refreshBatchBar();
                        openSummary(); // Re-render
                    });
                });
            }
            sumModal.classList.remove('hidden');
            sumModal.classList.add('flex');
        }
        function closeSummary() { sumModal.classList.add('hidden'); sumModal.classList.remove('flex'); }
        document.getElementById('summary-clear-btn').addEventListener('click', () => { clearAll(); closeSummary(); });
        sumClose.forEach(b => b && b.addEventListener('click', closeSummary));
        sumOverlay && sumOverlay.addEventListener('click', closeSummary);
        document.getElementById('batch-view-summary').addEventListener('click', e => { e.preventDefault(); openSummary(); });

        // ESC key tutup semua modal
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeModal();
                closeSummary();
                batchDrop.classList.add('hidden');
                document.querySelectorAll('.row-action-dropdown').forEach(d => d.classList.add('hidden'));
            }
        });
    });
})();
</script>
@endpush
