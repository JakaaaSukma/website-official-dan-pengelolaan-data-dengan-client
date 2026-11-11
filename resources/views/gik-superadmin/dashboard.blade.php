@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin Overview</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Control Center</h1>
        <p class="text-gray-500 text-sm mt-1">Global monitoring across Company A, B, C, and D tenants.</p>
    </div>
</div>

<!-- Top grid: Global monitoring (2 cols) + Inbox (1 col) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- LEFT 2 cols: Monitoring Global Peserta BESAR -->
    <div class="lg:col-span-2 rounded-3xl relative overflow-hidden bg-gradient-to-br from-[#0a1e55] via-[#0F2C6F] to-[#143784] text-white shadow-xl p-8">
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-white/5"></div>
        <div class="relative z-10">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/15 text-xs font-semibold text-white/80 mb-3">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                        LIVE DATA
                    </div>
                    <h2 class="text-lg font-bold text-white/90">Monitoring Global Peserta</h2>
                    <p class="text-sm text-white/60">Total active interns and employees across all client companies.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur border border-white/15 rounded-xl px-4 py-2.5">
                        <div class="w-9 h-9 rounded-lg bg-green-500/20 text-green-300 flex items-center justify-center font-extrabold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-widest text-white/60 font-bold">Growth</div>
                            <div class="text-lg font-extrabold leading-none text-white">+{{ $growthPct }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-end justify-between gap-10">
                <div>
                    <div class="text-[92px] leading-none font-extrabold tracking-tight">{{ $totalActive }}</div>
                    <div class="text-white/70 mt-2 text-sm font-semibold">Total Active Interns & Employees</div>
                </div>
                <div class="flex items-center gap-6 pb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="inline-block w-3.5 h-3.5 rounded-full bg-blue-300 ring-2 ring-white/40"></span>
                            <div class="text-xs font-bold uppercase tracking-wider text-white/70">On-Site</div>
                        </div>
                        <div class="text-3xl font-extrabold">{{ $onSite }}</div>
                    </div>
                    <div class="text-white/40 text-3xl font-light">|</div>
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="inline-block w-3.5 h-3.5 rounded-full bg-teal-300 ring-2 ring-white/40"></span>
                            <div class="text-xs font-bold uppercase tracking-wider text-white/70">Remote / Hybrid</div>
                        </div>
                        <div class="text-3xl font-extrabold">{{ $remote }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT 1 col: Inbox Pesan Request -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7 flex flex-col">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-gik-accent-light text-gik-accent flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-800 leading-tight">Inbox Pesan Request</h3>
                    <p class="text-xs text-gray-500">Latest from HRD Clients</p>
                </div>
            </div>
            @if($unreadCount > 0)
                <span class="text-xs font-extrabold text-white bg-red-500 rounded-full px-2.5 py-1 shadow-sm">{{ $unreadCount }} New</span>
            @endif
        </div>

        <div class="space-y-2.5 mb-4 flex-1 overflow-y-auto max-h-[360px]">
            @forelse($newMessages as $msg)
                <a href="{{ route('gik-superadmin.inbox.show', $msg) }}" class="block rounded-xl border border-gray-100 hover:border-gik-accent/40 hover:shadow-sm p-3.5 transition {{ $msg->status === 'unread' ? 'bg-red-50/60/50 border-blue-100' : 'bg-gray-50/30' }}">
                    <div class="flex items-start justify-between gap-3 mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-[10px] font-extrabold text-white">
                                {{ strtoupper(substr(optional($msg->company)->company_name ?? 'HRD', 0, 1)) }}
                            </span>
                            <div>
                                <div class="text-sm font-bold text-gray-800 leading-tight">{{ optional($msg->company)->company_name ?? 'System' }}</div>
                                <div class="text-[10px] text-gray-500 font-semibold">{{ $msg->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            @if($msg->status === 'unread')<span class="w-2 h-2 rounded-full bg-red-500"></span>@endif
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded
                                @if($msg->priority === 'urgent') bg-red-100 text-red-700
                                @elseif($msg->priority === 'high') bg-orange-100 text-orange-700
                                @elseif($msg->priority === 'low') bg-gray-100 text-gray-600
                                @else bg-blue-100 text-blue-700 @endif
                            ">{{ $msg->priority }}</span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-700 leading-snug line-clamp-2 pl-8">
                        <span class="font-semibold text-gray-900">{{ \Illuminate\Support\Str::limit($msg->subject ?? $msg->body, 50) }}</span>
                        {{ \Illuminate\Support\Str::limit($msg->body, 90) }}
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <div class="text-sm font-bold text-gray-700">All caught up!</div>
                    <div class="text-xs text-gray-500">No new messages from clients.</div>
                </div>
            @endforelse
        </div>

        <a href="{{ route('gik-superadmin.inbox') }}" class="w-full mt-auto inline-flex items-center justify-center gap-2 px-4 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold transition">
            Buka Semua Pesan Masuk (Inbox)
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>
</div>

<!-- Quick Actions Super Admin: Add New Contract + All Pengajuan + Off-boarding -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <a href="{{ route('gik-superadmin.add-contract.create') }}" class="group relative rounded-3xl overflow-hidden bg-gradient-to-br from-green-600 via-emerald-600 to-green-700 text-white shadow-lg shadow-emerald-200 p-6 hover:shadow-xl hover:-translate-y-0.5 transition-all">
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center group-hover:scale-110 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur text-[10px] font-bold uppercase tracking-wider">
                    NEW FLOW
                </div>
            </div>
            <h3 class="text-xl font-extrabold mb-1">Tambah Pengajuan Kandidat</h3>
            <p class="text-xs text-white/80 leading-relaxed mb-4">Pilih tenant tujuan, input kandidat baru via Excel/manual untuk direview HR.</p>
            <div class="inline-flex items-center gap-2 text-xs font-bold group-hover:gap-3 transition-all">
                Buat Pengajuan
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('gik-superadmin.add-contract.list') }}" class="group bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-200 hover:-translate-y-0.5 transition-all block">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center group-hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                MONITORING
            </div>
        </div>
        <h3 class="text-xl font-extrabold text-gray-800 mb-1">Daftar Pengajuan Kandidat</h3>
        <p class="text-xs text-gray-500 leading-relaxed mb-4">Monitoring semua batch seleksi kandidat yang dikirim ke HR Company.</p>
        <div class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 group-hover:gap-3 transition-all">
            Buka Daftar Batch
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </div>
    </a>

    <a href="{{ route('gik-superadmin.offboarding.index') }}" class="group bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-orange-200 hover:-translate-y-0.5 transition-all block">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center group-hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            @php
                $pendingOffCountDash = \App\Models\HrRequest::withoutTenant()->where('request_type', 'off_boarding')->whereIn('status', ['submitted', 'in_review'])->count();
            @endphp
            @if($pendingOffCountDash > 0)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500 text-white text-[10px] font-extrabold uppercase tracking-wider">
                    {{ $pendingOffCountDash }} PERLU REVIEW
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-[10px] font-bold uppercase tracking-wider text-orange-700">
                    TERMINASI
                </span>
            @endif
        </div>
        <h3 class="text-xl font-extrabold text-gray-800 mb-1">Manajemen Off-boarding</h3>
        <p class="text-xs text-gray-500 leading-relaxed mb-4">Review pengajuan pelepasan dari klien atau eksekusi off-boarding langsung.</p>
        <div class="inline-flex items-center gap-2 text-xs font-bold text-orange-700 group-hover:gap-3 transition-all">
            Buka Manajemen Off-board
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </div>
    </a>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-5 mb-8">
    <a href="{{ route('gik-superadmin.employees') }}" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-green-200 transition block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">New this month</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $stats['new'] ?? 0 }}</div>
            </div>
        </div>
    </a>
    <a href="{{ route('gik-superadmin.operations') }}?status=completed" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-blue-200 transition block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Completed</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $stats['completed'] ?? 0 }}</div>
            </div>
        </div>
    </a>
    <a href="{{ route('gik-superadmin.operations') }}?status=submitted" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-amber-200 transition block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Pending Requests</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $stats['pending'] ?? 0 }}</div>
            </div>
        </div>
    </a>
    <a href="{{ route('gik-superadmin.talents') }}" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-purple-200 transition block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Talent Pool</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $stats['talents'] ?? 0 }}</div>
            </div>
        </div>
    </a>
    <a href="{{ route('gik-superadmin.clients') }}" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-red-200 transition block">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 text-gik-primary-dark flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Active Clients</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $stats['clients'] ?? 0 }}</div>
            </div>
        </div>
    </a>
</div>

<!-- KLIEN: Client Account Management Table (big) -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-7 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-extrabold text-gray-800 text-lg">Kelola Login Client</h3>
            <p class="text-xs text-gray-500 mt-0.5">Manage HRD accounts for all tenants.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search client..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
            </div>
            <button onclick="document.getElementById('addClientModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Client
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5 text-left">Client Name</th>
                    <th class="px-6 py-3.5 text-left">Admin Account (HRD)</th>
                    <th class="px-6 py-3.5 text-left">Status</th>
                    <th class="px-6 py-3.5 text-left">Active Interns</th>
                    <th class="px-6 py-3.5 text-left">Plan</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clientAccounts as $acc)
                    <tr class="hover:bg-red-50/60/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-{{ ['indigo','purple','rose','blue','cyan','emerald'][$acc->id % 6] ?? 'indigo' }}-500 to-{{ ['indigo','purple','rose','blue','cyan','emerald'][$acc->id % 6] ?? 'indigo' }}-700 flex items-center justify-center text-white font-extrabold shadow-sm">
                                    {{ strtoupper(substr(optional($acc->company)->company_name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">{{ optional($acc->company)->company_name ?? 'Unknown Company' }}</div>
                                    <div class="text-xs text-gray-500">Tenant ID: C-{{ str_pad($acc->company_id ?? 0, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-700">{{ $acc->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $acc->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                @if($acc->account_status === 'active') bg-green-100 text-green-700
                                @elseif($acc->account_status === 'pending_review') bg-amber-100 text-amber-700
                                @else bg-gray-100 text-gray-600 @endif
                            ">
                                <span class="w-1.5 h-1.5 rounded-full
                                    @if($acc->account_status === 'active') bg-green-500
                                    @elseif($acc->account_status === 'pending_review') bg-amber-500
                                    @else bg-gray-400 @endif
                                "></span>
                                {{ strtoupper(str_replace('_', ' ', $acc->account_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 gik-progress"><div class="gik-progress-bar bg-gradient-to-r from-gik-accent to-gik-primary" style="width: {{ min(100, (optional($acc->company)->activeHrContractsCount() ?? 0) * 5) }}%"></div></div>
                                <div class="font-extrabold text-gray-800">{{ optional($acc->company)->activeHrContractsCount() ?? 0 }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">Enterprise</span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <button onclick='editClient(@json($acc->id), @json($acc->name), @json($acc->email), @json($acc->account_status), @json($acc->phone))' class="text-xs font-bold text-gik-accent hover:text-gik-primary hover:underline px-2 py-1 inline-flex items-center gap-1">
                                Edit
                            </button>
                            <span class="text-gray-300">•</span>
                            @if($acc->account_status === 'active')
                                <form method="POST" action="{{ route('gik-superadmin.clients.destroy', $acc) }}" onsubmit="return confirm('Suspend akun ini? Data historis tetap tersimpan.')" class="inline">@csrf @method('DELETE')
                                    <button class="text-xs font-bold text-red-600 hover:text-red-800 hover:underline px-2 py-1 inline-flex items-center gap-1">Suspend</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('gik-superadmin.clients.update', $acc) }}" class="inline">@csrf @method('PATCH')
                                    <input type="hidden" name="account_status" value="active">
                                    <button class="text-xs font-bold text-green-600 hover:text-green-800 hover:underline px-2 py-1 inline-flex items-center gap-1">Activate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-gray-500">No client accounts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clientAccounts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $clientAccounts->links() }}</div>
    @endif
</div>

<!-- Add Client Modal -->
<div id="addClientModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this) this.classList.add('hidden'), this.classList.remove('flex')">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
        <form method="POST" action="{{ route('gik-superadmin.clients.store') }}">
            @csrf
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-800 text-lg">+ Add New Client HRD Account</h3>
                <button type="button" onclick="document.getElementById('addClientModal').classList.add('hidden'), document.getElementById('addClientModal').classList.remove('flex')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">First Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Company</label>
                    <select name="company_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                        @foreach(\App\Models\Client::all() as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Email (Login)</label>
                    <input type="email" name="email" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Phone</label>
                        <input type="text" name="phone" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('addClientModal').classList.add('hidden'), document.getElementById('addClientModal').classList.remove('flex')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Client Modal -->
<div id="editClientModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this) this.classList.add('hidden'), this.classList.remove('flex')">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
        <form id="editClientForm" method="POST">
            @csrf @method('PATCH')
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-800 text-lg">Edit Client HRD Account</h3>
                <button type="button" onclick="document.getElementById('editClientModal').classList.add('hidden'), document.getElementById('editClientModal').classList.remove('flex')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Full Name</label>
                    <input type="text" id="editName" name="name" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Email</label>
                    <input type="email" id="editEmail" name="email" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Status</label>
                        <select id="editStatus" name="account_status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="active">Active</option>
                            <option value="pending_review">Pending Review</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">New Password (optional)</label>
                        <input type="password" name="password" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Phone</label>
                    <input type="text" id="editPhone" name="phone" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('editClientModal').classList.add('hidden'), document.getElementById('editClientModal').classList.remove('flex')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editClient(id, name, email, status, phone) {
    const f = document.getElementById('editClientForm');
    f.action = `/hr-superadmin/clients/${id}`;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editStatus').value = status;
    document.getElementById('editPhone').value = phone || '';
    const m = document.getElementById('editClientModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
</script>
@endsection
