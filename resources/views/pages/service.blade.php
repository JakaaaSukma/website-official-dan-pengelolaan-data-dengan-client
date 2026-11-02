@php $seoPage = 'service'; @endphp
@extends('layouts.frontend')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-[#121824] via-[#1a2333] to-[#251014] text-white py-20 lg:py-24 overflow-hidden">
    <!-- Background glow & grid pattern -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#af101a_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-6">
            <a href="{{ route('index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">home</span>
                Beranda
            </a>
            <span class="text-slate-600">/</span>
            <span class="text-primary-300">Mitra & Klien Perusahaan</span>
        </nav>

        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-primary-200 text-xs font-bold mb-4 tracking-wide">
                <span class="material-symbols-outlined text-[16px]">handshake</span>
                REKANAN STRATEGIS INDUSTRI JEPANG & INDONESIA
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white mb-5 leading-tight font-headline">
                Mitra Perusahaan <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-rose-300 to-amber-200">LPK Gyokai</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-body">
                Daftar perusahaan rekanan dan asosiasi industri penerima tenaga kerja serta pemagang profesional dari LPK Gyokai Indonesia. Kami menjamin integritas, kesesuaian kualifikasi, dan kesinambungan kerja sama.
            </p>

            <!-- Status Indicator Guide -->
            <div class="flex flex-wrap items-center gap-3 mt-8 pt-6 border-t border-white/10 text-xs">
                <div class="flex items-center gap-2 bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 px-3.5 py-1.5 rounded-full font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Client Open : Aktif Rekrutmen & Bekerja Sama</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-500/15 border border-slate-500/30 text-slate-300 px-3.5 py-1.5 rounded-full font-semibold">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Client Close : Kuota Terpenuhi / Arsip Historis</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Area -->
<section class="py-16 lg:py-24 bg-surface-container-low min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

        <!-- SECTION 1: CLIENT OPEN (ACTIVE) -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 pb-4 border-b border-outline-variant/30">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Status Aktif
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-headline flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 text-[30px]">verified</span>
                        Mitra Perusahaan Aktif (Open)
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Perusahaan yang sedang aktif bermitra dan membuka kuota penempatan kerja/magang.</p>
                </div>
                <div class="text-xs font-semibold text-slate-500 bg-white px-3.5 py-2 rounded-xl shadow-xs border border-slate-200">
                    Total Aktif: <span class="font-bold text-slate-900 text-sm">{{ count($openClients) }}</span> Mitra
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($openClients as $client)
                    <div class="group bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-primary/40 transition-all duration-300 flex flex-col overflow-hidden">
                        <!-- Top status badge & Logo -->
                        <div class="relative bg-slate-50/70 p-6 flex items-center justify-center h-48 border-b border-slate-100 group-hover:bg-primary/5 transition-colors">
                            <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Open
                            </span>

                            @if($client->company_logo)
                                <img src="{{ Storage::url($client->company_logo) }}" 
                                     alt="{{ $client->company_name }}" 
                                     class="max-h-28 max-w-[85%] object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-2xs"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center text-slate-400\'><span class=\'material-symbols-outlined text-5xl\'>domain</span><p class=\'text-xs mt-1 font-semibold\'>{{ addslashes($client->company_name) }}</p></div>';">
                            @else
                                <div class="text-center text-slate-400">
                                    <span class="material-symbols-outlined text-5xl group-hover:text-primary transition-colors">apartment</span>
                                    <p class="text-xs mt-1 font-semibold text-slate-400">Logo Rekanan</p>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                                    {{ $client->company_name }}
                                </h3>
                                @if(!empty($client->about_company))
                                    <p class="text-xs text-slate-500 line-clamp-2 mt-1.5 leading-relaxed font-body">
                                        {{ Str::limit(strip_tags($client->about_company), 85) }}
                                    </p>
                                @endif
                            </div>

                            <!-- Action Collapses (Alpine.js-powered for clean animation) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100" x-data="{ tab: null }">
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button type="button"
                                            @click="tab = tab === 'about' ? null : 'about'"
                                            :class="tab === 'about' ? 'bg-primary text-white border-primary' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                                            class="inline-flex items-center justify-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg border transition-all">
                                        <span class="material-symbols-outlined text-[14px]">info</span>
                                        <span>Tentang</span>
                                    </button>

                                    <button type="button"
                                            @click="tab = tab === 'qual' ? null : 'qual'"
                                            :class="tab === 'qual' ? 'bg-primary text-white border-primary' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                                            class="inline-flex items-center justify-center gap-1 text-[11px] font-bold px-2.5 py-1.5 rounded-lg border transition-all">
                                        <span class="material-symbols-outlined text-[14px]">fact_check</span>
                                        <span>Kualifikasi</span>
                                    </button>
                                </div>

                                <!-- Accordion Content: Tentang -->
                                <div x-show="tab === 'about'" 
                                     x-cloak 
                                     x-collapse 
                                     class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs text-slate-700 space-y-2 mt-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <span>Profil Perusahaan</span>
                                        <button @click="tab = null" class="text-slate-400 hover:text-slate-600">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                    <p class="leading-relaxed whitespace-pre-line text-slate-600">
                                        {{ $client->about_company ?: 'Informasi profil perusahaan belum ditambahkan.' }}
                                    </p>
                                </div>

                                <!-- Accordion Content: Kualifikasi -->
                                <div x-show="tab === 'qual'" 
                                     x-cloak 
                                     x-collapse 
                                     class="p-3 bg-red-50/50 rounded-xl border border-red-100 text-xs text-slate-700 space-y-2 mt-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-primary uppercase tracking-wider">
                                        <span>Kriteria Kualifikasi</span>
                                        <button @click="tab = null" class="text-slate-400 hover:text-slate-600">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                    @php
                                        $qualifications = $client->qualifications;
                                        if (strpos($qualifications, "\n") !== false) {
                                            $qualArray = array_filter(array_map('trim', explode("\n", $qualifications)));
                                        } else {
                                            $clean = preg_replace('/\s+/', ' ', $qualifications);
                                            $qualArray = array_filter(array_map('trim', preg_split('/(?<=:)\s+|(?<=;)\s+|(?<=\.)\s+(?=[A-Z])/', $clean)));
                                        }
                                    @endphp
                                    <ul class="space-y-1.5 text-slate-600">
                                        @forelse($qualArray as $qual)
                                            @if(!empty(trim($qual)))
                                                <li class="flex items-start gap-1.5 leading-relaxed text-[11px]">
                                                    <span class="material-symbols-outlined text-[14px] text-emerald-600 shrink-0 mt-0.5">check_circle</span>
                                                    <span>{{ trim($qual) }}</span>
                                                </li>
                                            @endif
                                        @empty
                                            <li class="text-slate-400 text-center py-1">Tidak ada spesifikasi khusus tercantum.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                        <span class="material-symbols-outlined text-slate-300 text-6xl mb-2">business_center</span>
                        <h4 class="text-base font-bold text-slate-700">Belum Ada Klien Berstatus Open</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Saat ini semua rekrutmen mitra sedang dalam evaluasi atau persiapan kuota baru.</p>
                    </div>
                @endforelse
            </div>
        </div>


        <!-- SECTION 2: CLIENT CLOSE (ARCHIVED) -->
        <div class="pt-8 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 pb-4 border-b border-outline-variant/30">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600 bg-slate-100 px-3 py-1 rounded-full mb-2">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        Arsip / Selesai
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight font-headline flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-slate-500 text-[30px]">inventory_2</span>
                        Mitra Arsip / Riwayat Kerja Sama (Close)
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Daftar perusahaan rekanan dengan kuota yang telah terpenuhi atau selesai masa periode kerja sama.</p>
                </div>
                <div class="text-xs font-semibold text-slate-500 bg-white px-3.5 py-2 rounded-xl shadow-xs border border-slate-200">
                    Total Arsip: <span class="font-bold text-slate-900 text-sm">{{ count($closedClients) }}</span> Mitra
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($closedClients as $client)
                    <div class="group bg-white/70 hover:bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden opacity-85 hover:opacity-100">
                        <!-- Top status badge & Logo -->
                        <div class="relative bg-slate-100/60 p-6 flex items-center justify-center h-44 border-b border-slate-100 group-hover:bg-slate-50 transition-colors">
                            <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700 border border-slate-300 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Close
                            </span>

                            @if($client->company_logo)
                                <img src="{{ Storage::url($client->company_logo) }}" 
                                     alt="{{ $client->company_name }}" 
                                     class="max-h-24 max-w-[80%] object-contain grayscale contrast-75 group-hover:grayscale-0 group-hover:contrast-100 transition-all duration-300"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center text-slate-400\'><span class=\'material-symbols-outlined text-5xl\'>domain</span><p class=\'text-xs mt-1 font-semibold\'>{{ addslashes($client->company_name) }}</p></div>';">
                            @else
                                <div class="text-center text-slate-300">
                                    <span class="material-symbols-outlined text-5xl">apartment</span>
                                    <p class="text-xs mt-1 font-semibold text-slate-400">Logo Rekanan</p>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="font-bold text-sm text-slate-700 group-hover:text-slate-900 transition-colors leading-snug line-clamp-2">
                                    {{ $client->company_name }}
                                </h3>
                                @if(!empty($client->about_company))
                                    <p class="text-xs text-slate-400 line-clamp-2 mt-1 leading-relaxed font-body">
                                        {{ Str::limit(strip_tags($client->about_company), 80) }}
                                    </p>
                                @endif
                            </div>

                            <!-- Action Collapses (Alpine.js) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100" x-data="{ tab: null }">
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button type="button"
                                            @click="tab = tab === 'about' ? null : 'about'"
                                            :class="tab === 'about' ? 'bg-slate-700 text-white border-slate-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200'"
                                            class="inline-flex items-center justify-center gap-1 text-[11px] font-bold px-2 py-1.5 rounded-lg border transition-all">
                                        <span class="material-symbols-outlined text-[14px]">info</span>
                                        <span>Tentang</span>
                                    </button>

                                    <button type="button"
                                            @click="tab = tab === 'qual' ? null : 'qual'"
                                            :class="tab === 'qual' ? 'bg-slate-700 text-white border-slate-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200'"
                                            class="inline-flex items-center justify-center gap-1 text-[11px] font-bold px-2 py-1.5 rounded-lg border transition-all">
                                        <span class="material-symbols-outlined text-[14px]">fact_check</span>
                                        <span>Kualifikasi</span>
                                    </button>
                                </div>

                                <!-- Accordion Content: Tentang -->
                                <div x-show="tab === 'about'" 
                                     x-cloak 
                                     x-collapse 
                                     class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-2 mt-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <span>Profil Perusahaan</span>
                                        <button @click="tab = null" class="text-slate-400 hover:text-slate-600">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                    <p class="leading-relaxed whitespace-pre-line text-slate-500">
                                        {{ $client->about_company ?: 'Informasi profil perusahaan belum ditambahkan.' }}
                                    </p>
                                </div>

                                <!-- Accordion Content: Kualifikasi -->
                                <div x-show="tab === 'qual'" 
                                     x-cloak 
                                     x-collapse 
                                     class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-2 mt-2">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        <span>Kriteria Historis</span>
                                        <button @click="tab = null" class="text-slate-400 hover:text-slate-600">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                    @php
                                        $qualifications = $client->qualifications;
                                        if (strpos($qualifications, "\n") !== false) {
                                            $qualArray = array_filter(array_map('trim', explode("\n", $qualifications)));
                                        } else {
                                            $clean = preg_replace('/\s+/', ' ', $qualifications);
                                            $qualArray = array_filter(array_map('trim', preg_split('/(?<=:)\s+|(?<=;)\s+|(?<=\.)\s+(?=[A-Z])/', $clean)));
                                        }
                                    @endphp
                                    <ul class="space-y-1.5 text-slate-500">
                                        @forelse($qualArray as $qual)
                                            @if(!empty(trim($qual)))
                                                <li class="flex items-start gap-1.5 leading-relaxed text-[11px]">
                                                    <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0 mt-0.5">check</span>
                                                    <span>{{ trim($qual) }}</span>
                                                </li>
                                            @endif
                                        @empty
                                            <li class="text-slate-400 text-center py-1">Tidak ada spesifikasi khusus tercantum.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-2">folder_open</span>
                        <p class="text-xs text-slate-500">Tidak ada riwayat client berstatus close.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SECTION 3: CALL TO ACTION FOR PARTNERSHIP -->
        <div class="relative bg-gradient-to-r from-primary to-primary-container rounded-3xl p-8 sm:p-12 text-white shadow-xl overflow-hidden">
            <div class="absolute -bottom-16 -right-16 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-bold uppercase tracking-wider text-white mb-4">
                    <span class="material-symbols-outlined text-[14px]">corporate_fare</span>
                    Peluang Kemitraan Baru
                </span>
                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight font-headline mb-3 text-white">
                    Tertarik Menjadi Mitra Perusahaan Rekanan LPK Gyokai?
                </h3>
                <p class="text-white/85 text-sm sm:text-base leading-relaxed mb-6 font-body">
                    Dapatkan suplai tenaga kerja andal, terlatih bahasa Jepang bersertifikasi JLPT/JFT, disiplin etos kerja tinggi, serta dukungan legalitas menyeluruh dari tim kami.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="https://wa.me/6281215112025?text=Halo%20LPK%20Gyokai,%20kami%20tertarik%20untuk%20bermitra%20dalam%20penyediaan%20tenaga%20kerja/pemagangan." 
                       target="_blank" 
                       rel="noopener"
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white text-primary font-bold text-sm shadow-md hover:bg-slate-50 transition-all transform hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        Hubungi Tim Kerjasama
                    </a>
                    <a href="{{ route('our-services') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-sm border border-white/20 transition-all">
                        <span class="material-symbols-outlined text-[18px]">read_more</span>
                        Pelajari Layanan Kami
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
