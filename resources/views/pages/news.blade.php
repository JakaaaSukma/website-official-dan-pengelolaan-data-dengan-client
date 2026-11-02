@php
    $seoPage = 'news';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Top Breadcrumb & Page Banner Header -->
    <section class="relative bg-surface-container-lowest pt-8 pb-12 shadow-sm overflow-hidden border-b border-surface-container">
        <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-gutter relative z-10">
            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb" class="flex items-center gap-space-xs text-body-sm font-body-sm text-secondary mb-space-md">
                <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('index') }}">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    Beranda
                </a>
                <span class="text-secondary/60">/</span>
                <span class="text-primary font-label-sm font-semibold">Berita &amp; Informasi</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        Media &amp; Publikasi Resmi
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm">
                        Berita &amp; Informasi Terkini
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Kabar terbaru seputar program magang ke Jepang, pelatihan kejuruan vokasi, rekrutmen tenaga kerja industri, dan kegiatan kemitraan LPK GYOKAI.
                    </p>
                </div>

                <!-- Quick Stats Pill -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">newspaper</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">
                            {{ ($latest->count() + ($headline ? 1 : 0)) > 0 ? ($latest->count() + ($headline ? 1 : 0)) : '120+' }}
                        </div>
                        <div class="font-body-sm text-body-sm text-secondary">Artikel &amp; Rilis Resmi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Pills Bar & Search -->
    <section class="max-w-7xl mx-auto w-full px-gutter pt-8 pb-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-surface-container-lowest p-space-sm sm:p-space-md rounded-xl shadow-sm border border-surface-container-high/60">
            <!-- Filter Chips -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 scrollbar-none" id="filter-container">
                <button type="button" onclick="filterCategory('all', this)" class="category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm transition-all whitespace-nowrap">
                    Semua
                </button>
                <button type="button" onclick="filterCategory('Program Magang', this)" class="category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-all whitespace-nowrap">
                    Program Magang
                </button>
                <button type="button" onclick="filterCategory('Pelatihan Teknis', this)" class="category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-all whitespace-nowrap">
                    Pelatihan Teknis
                </button>
                <button type="button" onclick="filterCategory('Kerjasama Industri', this)" class="category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-all whitespace-nowrap">
                    Kerjasama Industri
                </button>
                <button type="button" onclick="filterCategory('Info Karir', this)" class="category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-all whitespace-nowrap">
                    Info Karir
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative w-full lg:w-80 shrink-0">
                <input id="newsSearchInput" 
                       type="text" 
                       oninput="searchNews(this.value)"
                       placeholder="Cari berita atau kegiatan..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low text-on-surface text-body-md font-body-md rounded-lg placeholder-secondary/60 focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all border border-transparent focus:border-primary/30" />
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[20px]">search</span>
            </div>
        </div>
    </section>

    <!-- Featured News / Hero Article Card -->
    @if($headline)
    <section class="max-w-7xl mx-auto w-full px-gutter py-6">
        <div class="group bg-surface-container-lowest rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col lg:flex-row border border-surface-container-high/50">
            <!-- Media Column -->
            <div class="relative w-full lg:w-7/12 min-h-[280px] sm:min-h-[380px] lg:min-h-full overflow-hidden bg-surface-container">
                @if($headline->thumbnail)
                    <img src="{{ Storage::url($headline->thumbnail) }}" 
                         alt="{{ $headline->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                @else
                    <img src="{{ asset('img/blog-1.png') }}" 
                         alt="{{ $headline->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent lg:hidden"></div>
                <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-on-primary text-label-sm font-label-sm tracking-wide shadow-md font-bold">
                    <span class="material-symbols-outlined text-[15px]">star</span>
                    BERITA UTAMA
                </div>
            </div>

            <!-- Content Column -->
            <div class="w-full lg:w-5/12 p-space-lg sm:p-space-xl flex flex-col justify-between">
                <div class="flex flex-col">
                    <div class="flex items-center gap-space-sm mb-space-sm flex-wrap">
                        <span class="px-2.5 py-1 rounded bg-primary-fixed text-on-primary-fixed-variant text-label-sm font-label-sm uppercase font-semibold">
                            Program Utama
                        </span>
                        <span class="text-secondary text-body-sm font-body-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            {{ \Carbon\Carbon::parse($headline->created_at)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight group-hover:text-primary transition-colors mb-space-md">
                        <a href="{{ !empty($headline->slug) ? route('news.detail', $headline->slug) : '#' }}">
                            {{ $headline->title }}
                        </a>
                    </h2>
                    <div class="flex items-center gap-space-md text-secondary text-body-sm font-body-sm pb-space-md mb-space-md bg-surface-container-low/50 px-3 py-2 rounded-lg">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                            Admin LPK
                        </span>
                        <span class="text-secondary/40">•</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-primary">visibility</span>
                            {{ number_format($headline->views ?? 0) }} Views
                        </span>
                        <span class="text-secondary/40">•</span>
                        <span class="text-secondary font-label-sm">Akreditasi Kemnaker RI</span>
                    </div>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed line-clamp-4 mb-space-lg">
                        {{ Str::limit(strip_tags($headline->content), 260) }}
                    </p>
                </div>
                <div class="pt-space-md flex items-center justify-between border-t border-surface-container-high/40">
                    @if(!empty($headline->slug))
                        <a href="{{ route('news.detail', $headline->slug) }}" class="inline-flex items-center gap-2 px-space-lg py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary hover:shadow transition-all group/btn">
                            Baca Selengkapnya
                            <span class="material-symbols-outlined text-[18px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    @else
                        <span class="text-secondary font-body-sm">Detail tidak tersedia</span>
                    @endif
                    <span class="text-label-sm font-label-sm text-secondary">Estimasi baca: 3 menit</span>
                </div>
            </div>
        </div>
    </section>
    @else
    <!-- Mockup Hero Featured News saat database belum memiliki headline -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-6">
        <div class="group bg-surface-container-lowest rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col lg:flex-row border border-surface-container-high/50">
            <!-- Media Column -->
            <div class="relative w-full lg:w-7/12 min-h-[280px] sm:min-h-[380px] lg:min-h-full overflow-hidden bg-surface-container">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuDs1buS24ajBXo1UR9AG-ggELWsTAvh0STbR-fdE4xYdIzkzWENLentdtJMWEjnKLH4b2EcDIss6NTWw4psk-5AbRhNyTYyzXl7ysI4yl9FGuBSTSM-wmrDQxzP-MqnsxpCFwPbQjCpg-I4BxjfESshIZuXsbCemMB1e8ZELBm9OIp8g3py7QRdk0OaR53RYgoEO-CJkQfujGstuZ1rT9z79X4CvLYmFB2P0a7ZDSA" 
                     alt="Pembukaan Program Magang Ke Jepang Angkatan Terbaru Resmi Dimulai"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent lg:hidden"></div>
                <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-on-primary text-label-sm font-label-sm tracking-wide shadow-md font-bold">
                    <span class="material-symbols-outlined text-[15px]">star</span>
                    BERITA UTAMA
                </div>
            </div>
            <!-- Content Column -->
            <div class="w-full lg:w-5/12 p-space-lg sm:p-space-xl flex flex-col justify-between">
                <div class="flex flex-col">
                    <div class="flex items-center gap-space-sm mb-space-sm flex-wrap">
                        <span class="px-2.5 py-1 rounded bg-primary-fixed text-on-primary-fixed-variant text-label-sm font-label-sm uppercase font-semibold">
                            Program Magang
                        </span>
                        <span class="text-secondary text-body-sm font-body-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            15 Mei 2025
                        </span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight group-hover:text-primary transition-colors mb-space-md">
                        Pembukaan Program Magang Ke Jepang Angkatan 2025 Resmi Dimulai
                    </h2>
                    <div class="flex items-center gap-space-md text-secondary text-body-sm font-body-sm pb-space-md mb-space-md bg-surface-container-low/50 px-3 py-2 rounded-lg">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                            Admin LPK
                        </span>
                        <span class="text-secondary/40">•</span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-primary">visibility</span>
                            1,420 Views
                        </span>
                        <span class="text-secondary/40">•</span>
                        <span class="text-secondary font-label-sm">Akreditasi Kemnaker RI</span>
                    </div>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed line-clamp-4 mb-space-lg">
                        LPK GYOKAI Indonesia Kompeten resmi membuka seleksi dan pelatihan intensif untuk angkatan 2025. Program ini memfasilitasi kandidat terpilih untuk penempatan di sektor manufaktur otomotif dan presisi di Tokyo dan Aichi Prefecture, Jepang dengan standar keselamatan kerja Kaizen dan remunerasi kompetitif.
                    </p>
                </div>
                <div class="pt-space-md flex items-center justify-between border-t border-surface-container-high/40">
                    <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 px-space-lg py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group/btn">
                        Lihat Program &amp; Seleksi
                        <span class="material-symbols-outlined text-[18px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                    <span class="text-label-sm font-label-sm text-secondary">Estimasi baca: 3 menit</span>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section: Berita Terbaru & Trending Split Layout -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl">
            <!-- Left / Main: Artikel Rilis & Grid (8 Columns) -->
            <div class="lg:col-span-8 flex flex-col">
                <div class="flex items-center justify-between mb-space-lg">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                        <h3 class="font-headline-md text-headline-md text-on-surface tracking-tight">Rilis Berita &amp; Artikel Terkini</h3>
                    </div>
                    <span id="newsCountDisplay" class="text-body-sm font-body-sm text-secondary hidden sm:inline-block">
                        Menampilkan berita terkini LPK Gyokai
                    </span>
                </div>

                <!-- Articles Grid (2 cols on md) -->
                <div id="newsCardsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
                    @if($latest && $latest->isNotEmpty())
                        @foreach($latest as $item)
                            <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                     data-title="{{ strtolower($item->title) }}"
                                     data-content="{{ strtolower(strip_tags($item->content)) }}"
                                     data-category="{{ $item->category->name ?? 'Kegiatan' }}">
                                <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                    @if($item->thumbnail)
                                        <img src="{{ Storage::url($item->thumbnail) }}" 
                                             alt="{{ $item->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                             loading="lazy" />
                                    @else
                                        <img src="{{ asset('img/blog-1.png') }}" 
                                             alt="{{ $item->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                             loading="lazy" />
                                    @endif
                                    <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary text-on-secondary text-label-sm font-label-sm shadow-sm font-semibold">
                                        {{ $item->category->name ?? 'Kegiatan' }}
                                    </div>
                                </div>
                                <div class="p-space-lg flex flex-col flex-1 justify-between">
                                    <div>
                                        <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                            <span class="material-symbols-outlined text-[15px]">event</span>
                                            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                                        </div>
                                        <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                            <a href="{{ !empty($item->slug) ? route('news.detail', $item->slug) : '#' }}">
                                                {{ $item->title }}
                                            </a>
                                        </h4>
                                        <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                            {{ Str::limit(strip_tags($item->content), 120) }}
                                        </p>
                                    </div>
                                    <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                        @if(!empty($item->slug))
                                            <a href="{{ route('news.detail', $item->slug) }}" class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:text-tertiary font-bold group/link">
                                                Baca Selengkapnya
                                                <span class="material-symbols-outlined text-[16px] group-hover/link:translate-x-1 transition-transform">chevron_right</span>
                                            </a>
                                        @else
                                            <span class="text-secondary text-body-sm">Baca Detail</span>
                                        @endif
                                        <span class="text-body-sm text-secondary/60">{{ number_format($item->views ?? 0) }} views</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @else
                        <!-- Redesign Mockup Articles when database is empty -->
                        <!-- Mockup 1 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="pelatihan operator mesin cnc dan pembekalan keselamatan kerja k3"
                                 data-content="peningkatan kompetensi praktis peserta program pemesinan manufaktur presisi"
                                 data-category="Pelatihan Teknis">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsLBoO3MDKt2n1ZDpTAEqRgbRAqgeY3vh3srt5op0agN9E1H5BIGrAXPZNgYzRxSl_4if3IH8dqoBbBhMs_9cd4AxJd2M-V5GLS7_vtb7hNU9iCFvs18LjogtqHh-68W_-7Y9jzIyLsOOvHBiIEfl-MJJ9iVrxszObo8PUOd-d_xcjt42EhgtmEgxJKFMOgOyCi35oHk9lUOKH0VwEjAY6ua8BEYE77G1wcNALSjE" 
                                     alt="Pelatihan Operator Mesin CNC dan Pembekalan Keselamatan Kerja K3"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary text-on-secondary text-label-sm font-label-sm shadow-sm font-semibold">
                                    Pelatihan Teknis
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        08 Mei 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Pelatihan Operator Mesin CNC dan Pembekalan Keselamatan Kerja K3
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Peningkatan kompetensi praktis peserta program pemesinan manufaktur presisi berstandar Japanese Industrial Standards (JIS).
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">850 views</span>
                                </div>
                            </div>
                        </article>

                        <!-- Mockup 2 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="penandatanganan mou strategis dengan konsorsium manufaktur otomotif"
                                 data-content="kemitraan strategis ini menjamin serapan kerja langsung sebanyak 120 lulusan"
                                 data-category="Kerjasama Industri">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlkt7NVB2ljTVmVJjYC1eMQQigHJvWrLAl4_hUJEl6kLuy10TrbPTTE-wUrqaoejoyS7jFxjSOibx2IBCgOJGakbq0NDUDxfV0DoARtPL-lAxVnKQTbfdS-WmJUIHpTBbhEXCmR1bCP8ewaHhSRoZVM1kWPOq4iRB4Ba82cEST6sSheNO_xmsyd1v-oQtPwHUKnZ0I0OJ6JAldlWW4TVMG6dflw5phCZWHIo3p91w" 
                                     alt="Penandatanganan MoU Strategis"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-primary text-on-primary text-label-sm font-label-sm shadow-sm font-semibold">
                                    Kerjasama Industri
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        02 Mei 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Penandatanganan MoU Strategis dengan Konsorsium Manufaktur Otomotif
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Kemitraan strategis ini menjamin serapan kerja langsung sebanyak 120 lulusan LPK GYOKAI per tahun pada rantai pasok industri.
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">1,120 views</span>
                                </div>
                            </div>
                        </article>

                        <!-- Mockup 3 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="kunjungan supervisi industri ke prefektur shizuoka jepang"
                                 data-content="monitoring kondisi kerja kepatuhan akomodasi dan kesejahteraan alumni"
                                 data-category="Program Magang">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBoHMtMsm2pE2kXep4SIoVRMjYgtVpJc7iWhnT71xvToxsvnTj7jB0LNswdORc1fbl09vHCF_JLvV2RYtaLk3-UCICUXRsbg-2UbciojmsDn89qtDge86Ds979BoibsKEeuzGyncotQe5K3Fu_FfXIDB8zMK0PZUrWCoer7EvLhA6t7R1C46SH-ukWTWxThVEykkSYUYFjkrBfN2LG_dS-WD1s8iVFEhLcuaEzsF_o" 
                                     alt="Kunjungan Supervisi Industri"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary text-on-secondary text-label-sm font-label-sm shadow-sm font-semibold">
                                    Program Magang
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        28 Apr 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Kunjungan Supervisi Industri ke Prefektur Shizuoka Jepang
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Monitoring berkala kondisi kerja, kepatuhan akomodasi, dan jaminan keselamatan peserta magang di Shizuoka.
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">940 views</span>
                                </div>
                            </div>
                        </article>

                        <!-- Mockup 4 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="sertifikasi kompetensi nasional bnsp batch iv tahun 2025 sukses"
                                 data-content="uji kompetensi skema pengelasan smaw gmaw dan perakitan mekanikal mencatat kelulusan 100 persen"
                                 data-category="Pelatihan Teknis">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuD94nJbjyy8iYGX09iH_Y9xdUOl8Slb505H01gCcdIznDDYdEkh3qKQX8f_ccQLdd_h48F-_ein5I6bcj_4h7UytnmDOrKynblTnUYrRGRmjLjaNT8EIGUt834Ac_xuciwXuvRVGZVK9DBmoBSE-Yv2ap8pIDgab_RBA6o1zBc3SeRT7T-QirjgnRJ_DWNyoRUMYvt95ZWoZSg2O34yRUkWIWPVXdvd5pjMBR4euOo" 
                                     alt="Sertifikasi Kompetensi Nasional BNSP"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm shadow-sm font-semibold">
                                    Pelatihan Teknis
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        24 Apr 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Sertifikasi Kompetensi Nasional BNSP Batch IV Berjalan Sukses
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Uji kompetensi skema pengelasan (SMAW/GMAW) dan perakitan mekanikal mencatat kelulusan 100% dari 45 peserta.
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">730 views</span>
                                </div>
                            </div>
                        </article>

                        <!-- Mockup 5 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="kegiatan outbound training dan penguatan karakter kedisiplinan siswa magang"
                                 data-content="membangun ketangguhan mental integritas kerja tim serta adaptasi budaya etika kerja jepang"
                                 data-category="Program Magang">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHd1vp6m-HLQuhCM_yamy6KOjIvIF1N3shn6b5iYpHRUwap9--1oortT3QfjK9WksyweN4lHepSb4-IvIlqv7l04qRBHXuVRXbUZ4wKEA514KGA2Bw-_mYf-2JRxuMFqRayMpjdF9IRkwW6MqVgmO7dbhBNaGwcynOV7IdmcEs0FQ2o43ApnxXeqxKuBZRiZVblaeEvbNf6QVDGeXNSCum8EkC9pywQwFFc3275R4" 
                                     alt="Outbound Training Kedisiplinan Siswa"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-surface-container-high text-on-surface text-label-sm font-label-sm shadow-sm font-semibold">
                                    Program Magang
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        19 Apr 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Kegiatan Outbound Training &amp; Penguatan Karakter Kedisiplinan
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Membangun ketangguhan mental, integritas kerja tim, serta budaya etika kerja Jepang (Hou-Ren-So) sebelum berangkat.
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">680 views</span>
                                </div>
                            </div>
                        </article>

                        <!-- Mockup 6 -->
                        <article class="news-item group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col border border-surface-container-high/50"
                                 data-title="tips lolos wawancara user perusahaan jepang kaisha bagi peserta baru"
                                 data-content="panduan praktis gestur tubuh etika salam ojigi dan cara menyampaikan motivasi kerja"
                                 data-category="Info Karir">
                            <div class="relative w-full h-48 overflow-hidden bg-surface-container">
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuDx-OA6wleAXIIZrRZJYyN_GdTYrXbCSdkN1eumQQqqAUZmY7ZdbRNyaQxdpq-d0jZBD7EucX-xaFSpqmn9f5NOfcPEWnkvMFITBnFrACAew32SFdbycghUwIG4nCtx8deraVBNFaamp6zVVNdcRqcVmCzw4Pe-KPhPHKfIgZ9UPcuyrK3SsX7eyn8idWZV00BwQUGBLOlpYIuii_9G5CkZiWSo8FXRkXXwynBFr9E" 
                                     alt="Tips Wawancara Perusahaan Jepang"/>
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-primary-fixed text-on-primary-fixed-variant text-label-sm font-label-sm shadow-sm font-semibold">
                                    Info Karir
                                </div>
                            </div>
                            <div class="p-space-lg flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center gap-space-xs text-secondary text-body-sm font-body-sm mb-space-xs">
                                        <span class="material-symbols-outlined text-[15px]">event</span>
                                        12 Apr 2025
                                    </div>
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 mb-space-sm font-bold">
                                        Tips Lolos Wawancara User Perusahaan Jepang (Kaisha)
                                    </h4>
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mb-space-md">
                                        Panduan praktis etika salam (Ojigi), gestur formal, dan penyampaian motivasi kerja meyakinkan di hadapan hiring manager.
                                    </p>
                                </div>
                                <div class="pt-space-sm flex items-center justify-between border-t border-surface-container-high/30">
                                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                                        Rilis Resmi
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                    <span class="text-body-sm text-secondary/60">1,580 views</span>
                                </div>
                            </div>
                        </article>
                    @endif
                </div>

                <!-- Empty Search Notification -->
                <div id="noResultsNews" class="hidden p-12 text-center bg-surface-container-lowest rounded-xl border border-surface-container-high mt-4">
                    <span class="material-symbols-outlined text-4xl text-secondary mb-2">search_off</span>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tidak ada berita ditemukan</h4>
                    <p class="text-body-md text-secondary mt-1">Coba kata kunci lain atau pilih kategori "Semua".</p>
                </div>
            </div>

            <!-- Right Sidebar: Trending Topics & Video Dokumentasi (4 Columns) -->
            <div class="lg:col-span-4 flex flex-col gap-space-xl">
                <!-- Trending News Card -->
                <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm border border-surface-container-high/60">
                    <div class="flex items-center gap-2 mb-space-md pb-space-sm border-b border-surface-container">
                        <span class="material-symbols-outlined text-primary text-[24px]">trending_up</span>
                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Berita Populer &amp; Trending</h4>
                    </div>

                    <div class="flex flex-col gap-space-md">
                        @if($trending && $trending->isNotEmpty())
                            @foreach($trending as $index => $item)
                                <a href="{{ !empty($item->slug) ? route('news.detail', $item->slug) : '#' }}" 
                                   class="group flex items-start gap-space-sm hover:bg-surface-container-low p-2 rounded-lg transition-colors">
                                    <span class="w-7 h-7 rounded-md {{ $index < 3 ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container-high text-secondary' }} flex items-center justify-center shrink-0 font-label-sm text-label-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex flex-col">
                                        <h5 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-tight">
                                            {{ $item->title }}
                                        </h5>
                                        <span class="text-body-sm text-secondary text-xs mt-1">{{ number_format($item->views ?? 0) }} views</span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <!-- Mockup Trending Items -->
                            <div class="flex flex-col gap-3">
                                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-container-low transition-colors">
                                    <span class="w-7 h-7 rounded-md bg-primary text-on-primary font-bold flex items-center justify-center shrink-0 font-label-sm text-xs">
                                        1
                                    </span>
                                    <div>
                                        <h5 class="font-label-md text-label-md text-on-surface leading-tight hover:text-primary transition-colors font-medium">
                                            Tips Lolos Wawancara User Perusahaan Jepang (Kaisha)
                                        </h5>
                                        <span class="text-xs text-secondary mt-1 block">1,580 views • Karir</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-container-low transition-colors">
                                    <span class="w-7 h-7 rounded-md bg-primary text-on-primary font-bold flex items-center justify-center shrink-0 font-label-sm text-xs">
                                        2
                                    </span>
                                    <div>
                                        <h5 class="font-label-md text-label-md text-on-surface leading-tight hover:text-primary transition-colors font-medium">
                                            Pembukaan Seleksi Magang Sektor Otomotif Angkatan 2025
                                        </h5>
                                        <span class="text-xs text-secondary mt-1 block">1,420 views • Magang</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-container-low transition-colors">
                                    <span class="w-7 h-7 rounded-md bg-primary text-on-primary font-bold flex items-center justify-center shrink-0 font-label-sm text-xs">
                                        3
                                    </span>
                                    <div>
                                        <h5 class="font-label-md text-label-md text-on-surface leading-tight hover:text-primary transition-colors font-medium">
                                            Penandatanganan MoU dengan Asosiasi Manufaktur Jawa Barat
                                        </h5>
                                        <span class="text-xs text-secondary mt-1 block">1,120 views • Industri</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-container-low transition-colors">
                                    <span class="w-7 h-7 rounded-md bg-surface-container-high text-secondary flex items-center justify-center shrink-0 font-label-sm text-xs font-semibold">
                                        4
                                    </span>
                                    <div>
                                        <h5 class="font-label-md text-label-md text-on-surface leading-tight hover:text-primary transition-colors font-medium">
                                            Kunjungan Supervisi Peserta Magang di Prefektur Shizuoka
                                        </h5>
                                        <span class="text-xs text-secondary mt-1 block">940 views • Supervisi</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Video Kegiatan Banner / Box -->
                <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm border border-surface-container-high/60">
                    <div class="flex items-center justify-between mb-space-md pb-space-sm border-b border-surface-container">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[24px]">smart_display</span>
                            <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Video Kegiatan</h4>
                        </div>
                        <span class="font-label-sm text-label-sm text-secondary">Terbaru</span>
                    </div>

                    @php
                        $firstVideo = ($videos ?? collect([]))->first();
                    @endphp

                    @if($firstVideo)
                        <div class="relative rounded-xl overflow-hidden shadow group cursor-pointer aspect-video bg-on-surface flex items-center justify-center"
                             onclick="openNewsVideoModal('{{ $firstVideo->video_id ? 'https://www.youtube.com/embed/'.$firstVideo->video_id.'?autoplay=1' : ($firstVideo->youtube_link ? 'https://www.youtube.com/embed/'.$firstVideo->youtube_link.'?autoplay=1' : '') }}')">
                            <img src="{{ $firstVideo->thumbnail_url ?: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBf0jZtRFhu3mxkFmC1TnwuTPfO3jMDR0nrisPJHLUL5bwwWdIlUyx33qmVx1DnePXxLxCuyZW-vPm92JuCXE88ZadV27A_PS4GN9SB81F62enbLv48Ek-6fdfmypwKXsq9ZUEXDG0sbRLUXt-eaPHBRuqvVVAqACQhjCAt9btlOUZQLG89RsYrd51v4_grdmhOilMPrHJJHBCXgYs7b_uot_asE2QnB7q_DR_1hYw' }}" 
                                 alt="{{ $firstVideo->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-80" />
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="font-headline-sm text-body-md font-bold text-on-surface mt-3 leading-snug line-clamp-2">
                            {{ $firstVideo->title }}
                        </h5>
                    @else
                        <!-- Fallback Sample Video -->
                        <div class="relative rounded-xl overflow-hidden shadow group cursor-pointer aspect-video bg-on-surface flex items-center justify-center"
                             onclick="openNewsVideoModal('https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1')">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBf0jZtRFhu3mxkFmC1TnwuTPfO3jMDR0nrisPJHLUL5bwwWdIlUyx33qmVx1DnePXxLxCuyZW-vPm92JuCXE88ZadV27A_PS4GN9SB81F62enbLv48Ek-6fdfmypwKXsq9ZUEXDG0sbRLUXt-eaPHBRuqvVVAqACQhjCAt9btlOUZQLG89RsYrd51v4_grdmhOilMPrHJJHBCXgYs7b_uot_asE2QnB7q_DR_1hYw" 
                                 alt="Video Kegiatan LPK Gyokai" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-80" />
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="font-headline-sm text-body-md font-bold text-on-surface mt-3 leading-snug">
                            Dokumentasi Pelatihan &amp; Seleksi Calon Siswa Magang LPK GYOKAI
                        </h5>
                        <p class="text-body-sm text-secondary text-xs mt-1">Official LPK Gyokai Channel</p>
                    @endif
                </div>


            </div>
        </div>
    </section>

    <!-- Section: Berlangganan Newsletter & Info Lowongan Cepat -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-12">
        <div class="relative bg-surface-container-low rounded-xl p-space-lg md:p-space-xl overflow-hidden shadow-sm border border-surface-container-high/60">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-space-lg">
                <!-- Text details -->
                <div class="max-w-2xl text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-highest text-on-surface font-label-sm text-label-sm mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">notifications_active</span>
                        Update Seleksi &amp; Lowongan Terbaru
                    </div>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-xs font-bold">
                        Dapatkan Info Jadwal Seleksi Langsung ke Email Anda
                    </h3>
                    <p class="font-body-md text-body-md text-secondary">
                        Bergabunglah dengan ribuan calon peserta magang dan tenaga profesional. Kami mengirimkan notifikasi resmi pembukaan kelas diklat, jadwal wawancara user Jepang, dan tips karir setiap minggu tanpa spam.
                    </p>
                </div>
                <!-- Form Subscription -->
                <div class="w-full lg:w-auto shrink-0">
                    <form class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 max-w-md mx-auto" onsubmit="event.preventDefault(); alert('Terima kasih! Email Anda telah terdaftar untuk menerima update informasi LPK GYOKAI.'); this.reset();">
                        <div class="relative flex-1">
                            <input class="w-full pl-10 pr-4 py-3 bg-surface-container-lowest text-on-surface text-body-md font-body-md rounded-lg shadow-sm placeholder-secondary/60 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all border border-surface-container-high/60" 
                                   placeholder="Masukkan alamat email aktif" 
                                   required 
                                   type="email"/>
                            <span class="material-symbols-outlined absolute left-3 top-3 text-secondary text-[20px]">mail</span>
                        </div>
                        <button class="px-space-lg py-3 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-md transition-all whitespace-nowrap flex items-center justify-center gap-2 font-bold" type="submit">
                            <span>Berlangganan</span>
                            <span class="material-symbols-outlined text-[18px]">send</span>
                        </button>
                    </form>
                    <div class="mt-2 text-center lg:text-left flex items-center justify-center lg:justify-start gap-2 text-body-sm font-body-sm text-secondary text-xs">
                        <span class="material-symbols-outlined text-[15px] text-primary">verified_user</span>
                        <span>Data Anda terlindungi dan tidak akan diperjualbelikan.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Video Modal Container -->
<div id="newsVideoModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="relative w-full max-w-4xl bg-surface-container-lowest rounded-2xl overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-surface-container">
            <h4 class="font-headline-sm text-on-surface font-bold">Dokumentasi Video Kegiatan</h4>
            <button onclick="closeNewsVideoModal()" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="aspect-video w-full bg-black">
            <iframe id="newsVideoFrame" class="w-full h-full" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<!-- Client-side Search and Filter Script -->
<script>
    let currentFilter = 'all';
    let currentSearch = '';

    function filterCategory(category, buttonElement) {
        currentFilter = category;
        
        // Update active pill button styling
        const buttons = document.querySelectorAll('.category-btn');
        buttons.forEach(btn => {
            btn.className = "category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface transition-all whitespace-nowrap";
        });
        buttonElement.className = "category-btn px-space-md py-2 rounded-lg font-label-md text-label-md bg-primary text-on-primary shadow-sm transition-all whitespace-nowrap";

        applyFilterAndSearch();
    }

    function searchNews(keyword) {
        currentSearch = keyword.toLowerCase().trim();
        applyFilterAndSearch();
    }

    function applyFilterAndSearch() {
        const articles = document.querySelectorAll('.news-item');
        let visibleCount = 0;

        articles.forEach(article => {
            const title = article.getAttribute('data-title') || '';
            const content = article.getAttribute('data-content') || '';
            const category = article.getAttribute('data-category') || '';

            const matchesCategory = (currentFilter === 'all') || (category.toLowerCase() === currentFilter.toLowerCase());
            const matchesSearch = !currentSearch || title.includes(currentSearch) || content.includes(currentSearch);

            if (matchesCategory && matchesSearch) {
                article.style.display = 'flex';
                visibleCount++;
            } else {
                article.style.display = 'none';
            }
        });

        // Show/hide no results banner
        const noResults = document.getElementById('noResultsNews');
        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
        }

        const countDisplay = document.getElementById('newsCountDisplay');
        if (countDisplay) {
            countDisplay.innerText = `Menampilkan ${visibleCount} artikel`;
        }
    }

    function openNewsVideoModal(url) {
        if (!url) return;
        const modal = document.getElementById('newsVideoModal');
        const frame = document.getElementById('newsVideoFrame');
        frame.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeNewsVideoModal() {
        const modal = document.getElementById('newsVideoModal');
        const frame = document.getElementById('newsVideoFrame');
        frame.src = '';
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.getElementById('newsVideoModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeNewsVideoModal();
        }
    });
</script>
@endsection
