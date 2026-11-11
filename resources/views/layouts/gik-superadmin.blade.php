<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Super Admin Control Center' }} — Gyokai Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- TomSelect CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        'gik-primary': '#E53E3E',
                        'gik-primary-dark': '#991B1B',
                        'gik-accent': '#F97316',
                        'gik-accent-light': '#FEE2E2',
                        'gik-surface': '#FEF2F2',
                        'gik-sidebar': '#B91C1C',
                        'gik-sidebar-hover': '#DC2626',
                    },
                    boxShadow: {
                        'card-sm': '0 2px 4px rgba(229,62,62,0.05)'
                    }
                }
            }
        }
    </script>
    <style>
        .gik-sidebar-link {
            transition: all .15s ease;
        }
        .gik-sidebar-link.active {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border-left: 3px solid #fff;
            font-weight: 700;
        }
        .gik-sidebar-link:hover {
            background: rgba(255,255,255,0.04);
            color: #fff;
        }
        .gik-progress {
            height: 8px;
            background: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
        }
        .gik-progress-bar {
            height: 100%;
            border-radius: 9999px;
            transition: width .3s ease;
        }
        .gik-progress-thick {
            height: 12px;
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .gik-notif-item:hover .gik-notif-action { opacity: 1; }
        
        @media (max-width: 768px) {
            /* MOBILE APP SPECIFIC: Convert Tables to Cards (No Horizontal Scroll) */
            .overflow-x-auto { overflow-x: hidden !important; }
            table { min-width: 0 !important; width: 100% !important; display: block !important; }
            thead { display: none !important; } /* Hide headers */
            tbody { display: block !important; width: 100% !important; }
            tbody tr { 
                display: flex !important; 
                flex-direction: column !important; 
                background: #ffffff !important; 
                border-radius: 16px !important; 
                box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; 
                border: 1px solid #e5e7eb !important;
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
            tbody td {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                padding: 0.75rem 0 !important;
                border: none !important;
                border-bottom: 1px dashed #e2e8f0 !important;
                width: 100% !important;
                text-align: left !important;
            }
            tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem !important;
                justify-content: center !important; /* Center buttons */
            }
            tbody td:first-child {
                padding-top: 0 !important;
                font-weight: 800 !important;
            }
            
            /* Fix filters and headers */
            .flex.flex-wrap { 
                flex-wrap: nowrap !important; 
                overflow-x: auto !important; 
                padding-bottom: 0.5rem !important; 
                -webkit-overflow-scrolling: touch; 
                scrollbar-width: none; 
            }
            .flex.flex-wrap::-webkit-scrollbar { display: none; }
            
            .flex.items-center.justify-between.mb-4,
            .flex.items-center.justify-between.mb-5,
            .flex.items-center.justify-between.mb-6,
            .flex.items-center.justify-between.mb-8 {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }
            .flex.items-center.justify-between.mb-4 > div,
            .flex.items-center.justify-between.mb-5 > div,
            .flex.items-center.justify-between.mb-6 > div,
            .flex.items-center.justify-between.mb-8 > div {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-[#FEF2F2] font-sans antialiased min-h-screen text-gray-800">
<div class="flex min-h-screen">
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="document.getElementById('mobileSidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebarOverlay').classList.toggle('hidden');"></div>

    <!-- Sidebar -->
    <aside id="mobileSidebar" class="fixed left-0 top-0 h-screen w-[260px] bg-gradient-to-b from-[#991B1B] via-[#B91C1C] to-[#DC2626] text-white flex flex-col z-30 shadow-xl transition-transform duration-300 -translate-x-full md:translate-x-0">
        <div class="px-5 pt-5 pb-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center ring-2 ring-white/30 shadow-lg overflow-hidden shrink-0">
                    <img src="{{ asset('logo/Gyokai.png') }}" alt="Gyokai Indonesia Kompeten" class="w-[130%] h-[130%] object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[15px] font-extrabold leading-none tracking-tight truncate">Gyokai Connect</div>
                    <div class="text-[11px] text-white/75 font-semibold tracking-wide uppercase mt-1.5">Super Admin Rekrutmen</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $sMenu = request()->routeIs('gik-superadmin.dashboard*') ? 'dashboard'
                    : (request()->routeIs('gik-superadmin.clients*') ? 'clients'
                    : (request()->routeIs('gik-superadmin.talents*') || request()->routeIs('gik-superadmin.client-needs*') ? 'talents'
                    : (request()->routeIs('gik-superadmin.inbox*') ? 'inbox'
                    : (request()->routeIs('gik-superadmin.offboarding*') ? 'offboarding'
                    : (request()->routeIs('gik-superadmin.employees*') ? 'employees'
                    : (request()->routeIs('gik-superadmin.candidates*') ? 'candidates'
                    : (request()->routeIs('gik-superadmin.operations*') || request()->routeIs('gik-superadmin.requests*') || request()->routeIs('gik-superadmin.add-contract*') ? 'operations'
                    : (request()->routeIs('gik-superadmin.profile*') ? 'profile'
                    : (request()->routeIs('gik-superadmin.activity*') ? 'activity-logs' : '')))))))));
            @endphp
            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white/40 mb-1">Overview</div>
            <a href="{{ route('gik-superadmin.dashboard') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='dashboard'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Control Center
                </div>
            </a>
            <a href="{{ route('gik-superadmin.clients') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='clients'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Client Account Mgmt
                </div>
            </a>

            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white/40 mb-1 mt-3">Talent Pipeline</div>
            <a href="{{ route('gik-superadmin.add-contract.create') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ ($sMenu??'')==='add-contract-create'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Add New Contract
                </div>
            </a>
            <a href="{{ route('gik-superadmin.add-contract.list') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ ($sMenu??'')==='add-contract-list'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Daftar Pengajuan Kandidat
                </div>
            </a>
            <a href="{{ route('gik-superadmin.talents') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ ($sMenu??'')==='talents'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Talent DB & Matching
                </div>
            </a>
            <a href="{{ route('gik-superadmin.operations') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ $sMenu==='operations'?'active':'' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Monitoring Request & Operasi
                    </div>
                    @php
                        $pendingAllReqsCount = \App\Models\HrRequest::withoutTenant()
                            ->whereIn('status', ['submitted', 'in_review'])
                            ->count();
                    @endphp
                    @if($pendingAllReqsCount > 0)
                        <span class="text-[10px] font-extrabold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full px-2 py-0.5 min-w-[22px] text-center shadow-sm">{{ $pendingAllReqsCount }}</span>
                    @endif
                </div>
            </a>

            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white/40 mb-1 mt-3">Tenant Data</div>
            <a href="{{ route('gik-superadmin.employees') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='employees'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    All Active Participants
                </div>
            </a>
            <a href="{{ route('gik-superadmin.offboarding.index') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ $sMenu==='offboarding'?'active':'' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Manajemen Off-boarding
                    </div>
                    @php
                        $pendingOffCount = \App\Models\HrRequest::withoutTenant()
                            ->where('request_type', 'off_boarding')
                            ->whereIn('status', ['submitted', 'in_review'])
                            ->count();
                    @endphp
                    @if($pendingOffCount > 0)
                        <span class="text-[10px] font-extrabold text-white bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-2 py-0.5 min-w-[22px] text-center shadow-sm">{{ $pendingOffCount }}</span>
                    @endif
                </div>
            </a>
            <a href="{{ route('gik-superadmin.candidates') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='candidates'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    All Company Candidates
                </div>
            </a>

            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white/40 mb-1 mt-3">Communications</div>
            <a href="{{ route('gik-superadmin.violations.index') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ ($sMenu ?? '') === 'violations' ? 'active' : '' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Manajemen Pelanggaran
                    </div>
                    @php $saVioCount = \App\Models\HrViolationReport::withoutTenant()->where('status', 'pending_review')->count(); @endphp
                    @if($saVioCount > 0)<span class="text-[10px] font-extrabold text-white bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-1.5 py-0.5 min-w-[22px] text-center shadow-sm shadow-red-900/30">{{ $saVioCount }}</span>@endif
                </div>
            </a>
            <a href="{{ route('gik-superadmin.suspensions.index') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ ($sMenu ?? '') === 'suspensions' ? 'active' : '' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pemberhentian Sementara
                    </div>
                    @php $saSusCount = \App\Models\HrSuspensionRequest::withoutTenant()->where('status', 'pending_approval')->count(); @endphp
                    @if($saSusCount > 0)<span class="text-[10px] font-extrabold text-white bg-gradient-to-r from-purple-600 to-fuchsia-700 rounded-full px-1.5 py-0.5 min-w-[22px] text-center shadow-sm shadow-purple-900/30">{{ $saSusCount }}</span>@endif
                </div>
            </a>
            <div class="space-y-0.5">
                <a href="{{ route('gik-superadmin.bpjs.index') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ request()->routeIs('gik-superadmin.bpjs.index') || request()->routeIs('gik-superadmin.bpjs.rate-settings*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        BPJS Ketenagakerjaan
                    </div>
                </a>
                <div class="ml-8 pl-3 border-l border-white/20 space-y-0.5 mt-1 pb-2">
                    <a href="{{ route('gik-superadmin.bpjs.f2a') }}" class="gik-sidebar-link block px-3 py-2.5 rounded-lg text-[13px] text-white/60 hover:text-white {{ request()->routeIs('gik-superadmin.bpjs.f2a*') ? 'active' : '' }}">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Pelaporan F2A
                        </div>
                    </a>
                    <a href="{{ route('gik-superadmin.bpjs.accidents') }}" class="gik-sidebar-link block px-3 py-2.5 rounded-lg text-[13px] text-white/60 hover:text-white {{ request()->routeIs('gik-superadmin.bpjs.accidents*') ? 'active' : '' }}">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Laporan Kecelakaan Kerja
                        </div>
                    </a>
                </div>
            </div>
            <a href="{{ route('gik-superadmin.inbox') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 relative {{ $sMenu==='inbox'?'active':'' }}">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Client Inbox
                    </div>
                    @if(($unread = \App\Models\HrMessage::where(function($q){ $q->where('to_user_id', auth()->id())->orWhereNull('to_user_id'); })->where('status', 'unread')->count()) > 0)
                        <span class="text-[10px] font-extrabold text-white bg-red-500 rounded-full px-1.5 py-0.5 min-w-[20px] text-center">{{ $unread }}</span>
                    @endif
                </div>
            </a>

            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white/40 mb-1 mt-3">Account & Security</div>
            <a href="{{ route('gik-superadmin.activity-logs') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='activity-logs'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Log Aktivitas Sistem
                </div>
            </a>
            <a href="{{ route('gik-superadmin.profile.change-password') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ $sMenu==='profile'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ubah Password SA
                </div>
            </a>
            <a href="{{ route('gik-superadmin.panduan') }}" class="gik-sidebar-link block px-3.5 py-3 rounded-lg text-[14px] text-white/75 pl-5 {{ ($sMenu??'')==='panduan'?'active':'' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    📘 Panduan Penggunaan
                </div>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white text-xs font-extrabold ring-2 ring-white/20">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="min-w-0 flex-1">
                    <div class="text-[13px] font-bold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-white/60 uppercase font-semibold tracking-wider">Super Admin</div>
                </div>
            </div>
            <div class="space-y-1 px-2 mb-3">
                <a href="{{ route('gik-superadmin.profile.change-password') }}" class="block px-2 py-2 rounded-lg text-[12px] text-white/75 hover:bg-white/10 hover:text-white transition flex items-center gap-2.5 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Change Password
                </a>
                <a href="{{ route('gik-superadmin.activity-logs') }}" class="block px-2 py-2 rounded-lg text-[12px] text-white/75 hover:bg-white/10 hover:text-white transition flex items-center gap-2.5 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    My Activity Logs
                </a>
            </div>
            <form method="POST" action="{{ route('company.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-[13px] text-white/70 hover:bg-white/10 hover:text-white transition flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="md:ml-[260px] flex-1 flex flex-col min-h-screen w-full overflow-x-hidden">
        <!-- Top Nav -->
        <header class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm border-b border-gray-200/70">
            <div class="px-4 md:px-8 py-3.5 flex items-center gap-3 md:gap-5">
                <!-- Hamburger Menu (Mobile Only) -->
                <button type="button" onclick="document.getElementById('mobileSidebar').classList.toggle('-translate-x-full'); document.getElementById('sidebarOverlay').classList.toggle('hidden');" class="md:hidden p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2 min-w-0">
                    <h1 class="text-xs md:text-sm font-extrabold text-gik-primary-dark uppercase tracking-wide truncate">GYOKAI CONNECT <span class="hidden md:inline">• SUPER ADMIN</span></h1>
                    <span class="hidden md:inline-block px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-red-100 text-red-800 ring-1 ring-red-200">CROSS-TENANT</span>
                </div>

                <div class="relative flex-1 max-w-md ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" placeholder="Search talents, clients, requests... (⌘K)" class="w-full pl-10 pr-4 py-2 bg-gray-100/80 border-transparent rounded-xl text-sm focus:outline-none focus:bg-white focus:border-gik-accent focus:ring-2 focus:ring-red-200 transition">
                </div>

                <div class="ml-auto flex items-center gap-3">
                    <div class="relative" id="saNotificationWrap">
                        <button type="button" onclick="document.getElementById('saNotifDropdown').classList.toggle('hidden')" class="relative w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if(($unreadGlobal = \App\Models\HrMessage::where(function($q){ $q->where('to_user_id', auth()->id())->orWhereNull('to_user_id'); })->where('status', 'unread')->count()) > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-extrabold flex items-center justify-center ring-2 ring-white">{{ $unreadGlobal }}</span>
                            @endif
                        </button>
                        @php
                          $topNotifs = \App\Models\HrMessage::with(['fromUser', 'company'])->where(function($q){ $q->where('to_user_id', auth()->id())->orWhereNull('to_user_id'); })->orderByDesc('created_at')->limit(5)->get();
                          $unreadTotal = \App\Models\HrMessage::where(function($q){ $q->where('to_user_id', auth()->id())->orWhereNull('to_user_id'); })->where('status', 'unread')->count();
                        @endphp
                        <div id="saNotifDropdown" class="hidden absolute right-0 mt-2 w-[380px] rounded-2xl shadow-2xl border border-gray-100 bg-white overflow-hidden z-50">
                            <div class="px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-rose-50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-extrabold uppercase tracking-wider text-gik-primary">🔔 Notifikasi Super Admin</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 font-semibold">{{ $unreadTotal }} pesan belum dibaca</div>
                                </div>
                                <a href="{{ route('gik-superadmin.inbox') }}" class="text-[11px] font-extrabold text-gik-primary hover:text-blue-800 px-2.5 py-1 rounded-lg bg-white border border-blue-100 shadow-sm transition">Lihat Semua →</a>
                            </div>
                            <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-50">
                                @forelse($topNotifs as $n)
                                <a href="{{ route('gik-superadmin.inbox.show', $n) }}" class="gik-notif-item block px-4 py-3 hover:bg-red-50/60 transition relative">
                                    @if($n->status === 'unread')<span class="absolute left-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-red-500"></span>@endif
                                    <div class="flex items-start gap-3 pl-3">
                                        <div class="flex-shrink-0 w-9 h-9 rounded-lg {{ $n->priority === 'urgent' ? 'bg-red-100 text-red-600' : ($n->priority === 'high' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-gik-accent') }} flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="text-[12px] font-extrabold text-gray-800 truncate">{{ $n->subject }}</div>
                                                <span class="text-[10px] font-bold text-gray-400 whitespace-nowrap">{{ $n->created_at?->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-[11px] text-gray-500 line-clamp-2 leading-snug mt-0.5">{{ \Illuminate\Support\Str::limit(strip_tags($n->body), 110) }}</div>
                                            <div class="mt-1.5 flex items-center gap-2">
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $n->status_badge_class }}">{{ $n->status }}</span>
                                                @if($n->company)<span class="text-[9px] font-bold text-gray-500">🏢 {{ \Illuminate\Support\Str::limit($n->company->company_name, 20) }}</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="px-5 py-12 text-center text-gray-400 text-[12px] font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" class="w-9 h-9 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Belum ada notifikasi saat ini.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <a href="#" class="hidden md:inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-gik-primary px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Help & Support
                    </a>
                    <div class="h-7 w-px bg-gray-200 mx-1"></div>
                    <div class="flex items-center gap-2.5 pl-1 pr-2 py-1 rounded-full hover:bg-gray-100 cursor-pointer transition">
                        <div class="w-8.5 h-8.5 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-xs font-extrabold shadow-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="hidden md:block">
                            <div class="text-[12px] font-bold leading-tight text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-gray-500 leading-tight">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 px-4 md:px-8 py-5 md:py-7 overflow-x-hidden">
            @if(session('success'))
                <div class="mb-5 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm text-green-700 font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm text-red-700 font-medium">{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-bold text-red-800 text-sm">Validation Errors</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script>
window.addEventListener('click', function(e){
  const wrap = document.getElementById('saNotificationWrap');
  const drop = document.getElementById('saNotifDropdown');
  if (wrap && drop && !wrap.contains(e.target)) drop.classList.add('hidden');
});

// Initialize Tom Select on all <select> elements
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select').forEach(function(el) {
        // Skip if it already has tom-select or is marked ignore
        if (el.classList.contains('tomselected') || el.classList.contains('no-select2')) return;
        new TomSelect(el, {
            create: false,
            sortField: null,
            placeholder: el.getAttribute('placeholder') || 'Pilih opsi...'
        });
    });
});
// Make all tables responsive on mobile
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('table').forEach(function(table) {
        if (!table.parentElement.classList.contains('overflow-x-auto')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'overflow-x-auto w-full pb-2 -mx-4 px-4 md:mx-0 md:px-0';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
