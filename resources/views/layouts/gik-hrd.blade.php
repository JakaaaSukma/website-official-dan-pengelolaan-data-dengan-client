<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Gyokai Connect' }} - Company HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- TomSelect CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'gik-primary': '#E53E3E',
                        'gik-primary-dark': '#991B1B',
                        'gik-accent': '#F97316',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .gik-sidebar-link.active { background-color: #FEE2E2; border-right: 4px solid #E53E3E; color: #991B1B; font-weight: 700; }
        .gik-sidebar-link { border-right: 4px solid transparent; transition: all .15s; }
        .gik-sidebar-link:hover { background-color: #FEF2F2; }
        .gik-progress { height: 8px; border-radius: 9999px; overflow: hidden; background: #e5e7eb; }
        .gik-progress-bar { height: 100%; border-radius: 9999px; transition: width .3s; }
        
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
<body class="bg-[#FEF2F2] min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar Overlay -->
    <div id="hrdSidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="document.getElementById('hrdMobileSidebar').classList.toggle('-translate-x-full'); document.getElementById('hrdSidebarOverlay').classList.toggle('hidden');"></div>

    <!-- SIDEBAR -->
    <aside id="hrdMobileSidebar" class="w-[260px] bg-white border-r border-gray-200 flex flex-col fixed h-screen z-30 transition-transform duration-300 -translate-x-full md:translate-x-0">
        <div class="px-5 py-5 border-b border-gray-100 bg-gradient-to-r from-white to-red-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center ring-2 ring-red-100 shadow-md shadow-red-50 overflow-hidden shrink-0">
                    <img src="{{ asset('logo/Gyokai.png') }}" alt="Gyokai Indonesia Kompeten" class="w-[130%] h-[130%] object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-extrabold text-gik-primary-dark text-[15px] leading-tight truncate">Gyokai Connect</div>
                    <div class="text-xs text-gray-500 mt-0.5">Company HR Portal</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-5 px-2 space-y-1 overflow-y-auto">
            @php
                $r = request()->route()?->getName() ?? '';
                $user = auth()->user();
                $initials = strtoupper(substr($user->name, 0, 1) . (explode(' ', $user->name)[1][0] ?? ''));
            @endphp
            <a href="{{ route('gik-hrd.dashboard') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('gik-hrd.talents') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.talents') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Candidate Database
                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold tracking-wide uppercase bg-gradient-to-r from-red-100 to-rose-100 text-red-700 ring-1 ring-red-200">Talent</span>
            </a>
            <a href="{{ route('gik-hrd.client-needs.create') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.client-needs') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Pengajuan Tenaga Ahli
                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold tracking-wide uppercase bg-gradient-to-r from-orange-100 to-red-100 text-orange-700 ring-1 ring-orange-200">URGENT</span>
            </a>
            <a href="{{ route('gik-hrd.request-hub') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.request-hub') || str_contains($r, 'gik-hrd.requests.') || str_contains($r, 'gik-hrd.add-contract') || str_contains($r, 'gik-hrd.batch') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Pusat Pengajuan (Hub)
            </a>
            <div class="ml-4 pl-2 border-l border-gray-100 space-y-0.5 my-1.5">
                <a href="{{ route('gik-hrd.add-contract.list') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.add-contract') ? 'active' : '' }} flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-500 rounded-l-md hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Review Kandidat Baru
                </a>
                <a href="{{ route('gik-hrd.batch.select', 'extend_contract') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.batch') && request('type') !== 'off_boarding' ? 'active' : '' }} flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-500 rounded-l-md hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Extend Contract
                </a>
                <a href="{{ route('gik-hrd.batch.select', 'off_boarding') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.batch') && request('type') === 'off_boarding' ? 'active' : '' }} flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-500 rounded-l-md hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Off-boarding
                </a>
            </div>
            <a href="{{ route('gik-hrd.operations') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.operations') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Riwayat & Status Request
            </a>

            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 mb-1 mt-2">Compliance & BPJS</div>
            <div class="space-y-0.5">
                <a href="{{ route('gik-hrd.bpjs.index') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.bpjs.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    BPJS Ketenagakerjaan
                </a>
                <div class="ml-4 pl-2 border-l border-gray-100 space-y-0.5 my-1.5">
                    <a href="{{ route('gik-hrd.bpjs.f2a') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.bpjs.f2a') ? 'active' : '' }} flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-500 rounded-l-md hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Pelaporan F2A
                    </a>
                    <a href="{{ route('gik-hrd.bpjs.accidents') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.bpjs.accidents') ? 'active' : '' }} flex items-center gap-2.5 px-3 py-2 text-[13px] text-gray-500 rounded-l-md hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Laporan Kecelakaan Kerja
                    </a>
                </div>
            </div>

            <a href="{{ route('gik-hrd.violations.index') }}" class="gik-sidebar-link {{ ($sMenu ?? '') === 'violations' ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Laporan Pelanggaran
                @php $hrdVioCount = \App\Models\HrViolationReport::where('company_id', auth()->user()->company_id)->whereIn('status', ['pending_review', 'contacted', 'in_process'])->count(); @endphp
                @if($hrdVioCount > 0)<span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-red-50 text-red-700 ring-1 ring-red-200">{{ $hrdVioCount }} Aktif</span>@endif
            </a>

            <a href="{{ route('gik-hrd.suspensions.index') }}" class="gik-sidebar-link {{ ($sMenu ?? '') === 'suspensions' ? 'active' : '' }} flex items-center gap-3 px-4 py-3 text-sm text-gray-600 rounded-l-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pemberhentian Sementara
                @php $hrdSusCount = \App\Models\HrSuspensionRequest::where('company_id', auth()->user()->company_id)->whereIn('status', ['pending_approval', 'under_review'])->count(); @endphp
                @if($hrdSusCount > 0)<span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-purple-50 text-purple-700 ring-1 ring-purple-200">{{ $hrdSusCount }} Aktif</span>@endif
            </a>

            <div class="mt-4 mx-3 pt-3 border-t border-gray-100">
                <div class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400">Account & Security</div>
                <a href="{{ route('gik-hrd.activity-logs') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.activity') ? 'active' : '' }} flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-600 rounded-l-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Log Aktivitas Saya
                </a>
                <a href="{{ route('gik-hrd.profile.change-password') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.profile.change-password') ? 'active' : '' }} flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-600 rounded-l-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ubah Password
                </a>
                <a href="{{ route('gik-hrd.messages') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.messages') ? 'active' : '' }} flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-600 rounded-l-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Kotak Pesan & Notif
                    @if(($hrdUnread = \App\Models\HrMessage::where('company_id', auth()->user()->company_id)->where('status', 'unread')->count()) > 0)
                      <span class="ml-auto text-[10px] font-extrabold text-white bg-red-500 rounded-full px-1.5 py-0.5 min-w-[18px] text-center">{{ $hrdUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('gik-hrd.panduan') }}" class="gik-sidebar-link {{ str_contains($r, 'gik-hrd.panduan') ? 'active' : '' }} flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-600 rounded-l-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px] text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    📘 Panduan Penggunaan
                </a>
            </div>
        </nav>

        <div class="p-3 border-t border-gray-100">
            <div class="px-3 py-2 mb-1">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-red-200">
                        {{ $initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500">HR Specialist</div>
                    </div>
                </div>
                <div class="mt-2 space-y-0.5">
                    <a href="{{ route('gik-hrd.profile.change-password') }}" class="block px-2 py-1.5 rounded-md text-[11px] text-gray-600 hover:bg-red-50 hover:text-gik-primary transition font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Change Password
                    </a>
                    <a href="{{ route('gik-hrd.activity-logs') }}" class="block px-2 py-1.5 rounded-md text-[11px] text-gray-600 hover:bg-amber-50 hover:text-amber-700 transition font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Activity Logs
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('company.logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="md:ml-[260px] flex-1 flex flex-col min-h-screen w-full overflow-x-hidden">
        <!-- TOP BAR -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
            <div class="flex items-center px-4 md:px-8 py-3 gap-3 md:gap-5">
                <!-- Hamburger Menu (Mobile Only) -->
                <button type="button" onclick="document.getElementById('hrdMobileSidebar').classList.toggle('-translate-x-full'); document.getElementById('hrdSidebarOverlay').classList.toggle('hidden');" class="md:hidden p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="text-lg md:text-xl font-extrabold text-gik-primary-dark tracking-tight truncate min-w-0">Gyokai Connect</div>
                <div class="hidden md:block flex-1 max-w-xl ml-4">
                    @yield('gik-search-bar')
                    @if(!View::hasSection('gik-search-bar'))
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" placeholder="Search candidates..." class="w-full pl-12 pr-4 py-2.5 bg-gray-100 border border-gray-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <div class="relative" id="hrdNotificationWrap">
                        <button type="button" onclick="document.getElementById('hrdNotifDropdown').classList.toggle('hidden')" class="relative p-2.5 text-gray-500 hover:bg-gray-100 rounded-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if(($hrdTopUnread = \App\Models\HrMessage::where('company_id', auth()->user()->company_id)->where('status', 'unread')->count()) > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-extrabold flex items-center justify-center ring-2 ring-white">{{ $hrdTopUnread }}</span>
                            @endif
                        </button>
                        @php
                          $hrdTopNotifs = \App\Models\HrMessage::with(['fromUser'])->where('company_id', auth()->user()->company_id)->orderByDesc('created_at')->limit(5)->get();
                          $hrdTopTotal = \App\Models\HrMessage::where('company_id', auth()->user()->company_id)->where('status', 'unread')->count();
                        @endphp
                        <div id="hrdNotifDropdown" class="hidden absolute right-0 mt-2 w-[370px] rounded-2xl shadow-2xl border border-gray-100 bg-white overflow-hidden z-50">
                            <div class="px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-rose-50 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-extrabold uppercase tracking-wider text-gik-primary-dark">🔔 Notifikasi HR Company</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 font-semibold">{{ $hrdTopTotal }} pesan belum dibaca</div>
                                </div>
                                <a href="{{ route('gik-hrd.messages') }}" class="text-[11px] font-extrabold text-gik-primary hover:text-gik-primary-dark px-2.5 py-1 rounded-lg bg-white border border-red-100 shadow-sm transition">Lihat Semua →</a>
                            </div>
                            <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-50">
                                @forelse($hrdTopNotifs as $n)
                                <a href="{{ route('gik-hrd.messages') }}" class="block px-4 py-3 hover:bg-red-50/50 transition relative">
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
                                            <div class="text-[11px] text-gray-500 line-clamp-2 leading-snug mt-0.5">{{ \Illuminate\Support\Str::limit(strip_tags($n->body), 100) }}</div>
                                            <div class="mt-1.5 flex items-center gap-2">
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $n->status_badge_class }}">{{ $n->status }}</span>
                                                <span class="text-[9px] font-bold text-gray-500">📬 {{ $n->message_type_label }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="px-5 py-12 text-center text-gray-400 text-[12px] font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" class="w-9 h-9 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Belum ada notifikasi. Anda hari ini.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <button class="p-2.5 text-gray-500 hover:bg-gray-100 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    <div class="w-px h-7 bg-gray-200 mx-2"></div>
                    <a href="{{ route('contact') }}" target="_blank" class="text-sm font-medium text-gray-600 hover:text-gik-accent px-3 py-1.5 rounded-lg hover:bg-gray-50">Support</a>
                    <a href="#" class="text-sm font-semibold text-gik-accent hover:text-gik-primary px-3 py-1.5 rounded-lg hover:bg-red-50">Profile</a>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-xs font-bold border-2 border-white shadow-sm">
                        {{ $initials }}
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 px-4 md:px-8 py-5 md:py-7 overflow-x-hidden">
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script>
window.addEventListener('click', function(e){
  const wrap = document.getElementById('hrdNotificationWrap');
  const drop = document.getElementById('hrdNotifDropdown');
  if (wrap && drop && !wrap.contains(e.target)) drop.classList.add('hidden');
});

// Initialize Tom Select on all <select> elements
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select').forEach(function(el) {
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
