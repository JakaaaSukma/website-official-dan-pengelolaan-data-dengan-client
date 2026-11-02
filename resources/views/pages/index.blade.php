@php
    $seoPage = 'home';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- HERO SECTION -->
    <section class="relative w-full overflow-hidden bg-surface-container-lowest">
        <div class="relative max-w-7xl mx-auto px-gutter py-space-xl lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
                <!-- Text Column -->
                <div class="lg:col-span-7 flex flex-col items-start z-10">
                    <div class="inline-flex items-center gap-space-xs px-space-md py-space-xs rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-md text-label-md mb-space-md shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        LPK GYOKAI INDONESIA KOMPETEN
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-tight mb-space-md">
                        Mencetak Tenaga Kerja <span class="text-primary underline decoration-primary/20 decoration-wavy decoration-2">Terampil</span> dan Kompeten
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary max-w-2xl mb-space-xl">
                        Bersama Industri Membangun Masa Depan. Program pemagangan industri (paid internship), pelatihan kejuruan berstandar Jepang, dan sertifikasi profesi resmi sesuai kebutuhan pasar kerja nasional & internasional.
                    </p>
                    <div class="flex flex-wrap items-center gap-space-md w-full sm:w-auto">
                        <a href="{{ route('our-services') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-space-xs px-space-lg py-3.5 rounded-lg bg-primary hover:bg-tertiary text-on-primary font-headline-sm text-headline-sm transition-all shadow-md hover:shadow-lg">
                            <span>Lihat Layanan</span>
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-space-xs px-space-lg py-3.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-headline-sm text-headline-sm transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[20px] text-primary">chat</span>
                            <span>Hubungi Kami</span>
                        </a>
                    </div>
                    <!-- Trust Sub-badge -->
                    <div class="mt-space-xl pt-space-lg flex items-center gap-space-lg">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-fixed font-label-md text-label-md shadow-sm ring-2 ring-surface-container-lowest">JP</div>
                            <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-on-primary-fixed font-label-md text-label-md shadow-sm ring-2 ring-surface-container-lowest">ID</div>
                            <div class="w-10 h-10 rounded-full bg-surface-container-highest flex items-center justify-center text-secondary font-label-md text-label-md shadow-sm ring-2 ring-surface-container-lowest">+5k</div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-label-md text-label-md text-on-surface font-bold">Terakreditasi Resmi Kemnaker RI</span>
                            <span class="font-body-sm text-body-sm text-secondary">Standar Kaizen &amp; Kurikulum Industri Jepang</span>
                        </div>
                    </div>
                </div>
                <!-- Image Visual Column -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary-fixed/40 via-surface-container to-transparent rounded-full filter blur-2xl -z-10"></div>
                    <div class="relative w-full max-w-md lg:max-w-none rounded-2xl overflow-hidden shadow-xl bg-surface-container-low">
                        <picture>
                            @if(file_exists(public_path('img/jelas.webp')))
                                <source srcset="{{ asset('img/jelas.webp') }}" type="image/webp">
                            @endif
                            <img class="w-full h-[460px] lg:h-[520px] object-cover object-top" 
                                 src="{{ \App\Models\SiteAsset::url('home_hero_banner', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCo42P0IykCE784Q7B8Y5mrt9TAvdE-bOgnytGE8jsLtpFD_Djdv9TDw9vV7jQ_oac9TAhXp7EFnMDLz-_j8IWejkG1AoeWZBOo5IXtzdQc8UIbjOMzEvMPid4urCpJpd8kwcfHjCxKgZzu-gE41QorDUx_zfgSIiGSmlFkSfioeQzmbJDU0W0-tF-V3RKFMzwT2YR1q-TH8vUNLKalc8Bj-wS54MYjFzyFRN61zhM') }}" 
                                 alt="Peserta Pelatihan LPK Gyokai Indonesia Kompeten"
                                 loading="eager" />
                        </picture>
                        <div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-space-lg left-space-lg right-space-lg p-space-md rounded-xl bg-surface-container-lowest/90 backdrop-blur-md shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-label-sm text-label-sm text-primary uppercase tracking-wider font-bold">Status Pelatihan</p>
                                    <p class="font-headline-sm text-headline-sm text-on-surface">Siap Penempatan Industri</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
                                    <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK STATS RIBBON -->
        <div class="w-full bg-primary-container text-on-primary-container shadow-md">
            <div class="max-w-7xl mx-auto px-gutter py-space-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg divide-y md:divide-y-0 md:divide-x divide-on-primary-container/20">
                    <div class="flex items-center gap-space-md pt-space-sm md:pt-0">
                        <div class="w-14 h-14 rounded-xl bg-on-primary-container/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-primary-container text-[32px]">calendar_today</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline-xl text-headline-xl leading-none font-extrabold">2017</span>
                            <span class="font-body-md text-body-md text-on-primary-container/90 mt-1">Tahun Berdiri &amp; Dedikasi</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-md pt-space-md md:pt-0 md:pl-space-lg">
                        <div class="w-14 h-14 rounded-xl bg-on-primary-container/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-primary-container text-[32px]">school</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline-xl text-headline-xl leading-none font-extrabold">5000+</span>
                            <span class="font-body-md text-body-md text-on-primary-container/90 mt-1">Alumni &amp; Peserta Pelatihan</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-space-md pt-space-md md:pt-0 md:pl-space-lg">
                        <div class="w-14 h-14 rounded-xl bg-on-primary-container/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-primary-container text-[32px]">apartment</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline-xl text-headline-xl leading-none font-extrabold">50+</span>
                            <span class="font-body-md text-body-md text-on-primary-container/90 mt-1">Mitra Industri Nasional &amp; Jepang</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CLIENTS / MITRA PERUSAHAAN (DYNAMIC MARQUEE) -->
    @php
        $clientsCollection = $clients ?? collect([]);
    @endphp
    @if($clientsCollection->isNotEmpty())
    <section class="w-full py-12 bg-surface-container-low border-b border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter mb-6 text-center">
            <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest font-bold">Kemitraan Industri</span>
            <h2 class="font-headline-md text-headline-md text-on-surface mt-1">Perusahaan Mitra Yang Bekerjasama</h2>
        </div>
        <div class="client-marquee py-2">
            <div class="marquee-track">
                @foreach($clientsCollection->concat($clientsCollection) as $client)
                    <div class="bg-surface-container-lowest px-6 py-4 rounded-xl shadow-sm border border-surface-container-high/60 flex items-center justify-center shrink-0 min-w-[170px] h-20">
                        @if($client->company_logo)
                            <img src="{{ Storage::url($client->company_logo) }}" 
                                 alt="Logo {{ $client->company_name }}" 
                                 class="max-h-12 max-w-[130px] object-contain" 
                                 loading="lazy"/>
                        @else
                            <span class="font-headline-sm text-body-md font-bold text-on-surface text-center">{{ $client->company_name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- LAYANAN KAMI SECTION -->
    <section class="w-full py-20 bg-background">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="text-center max-w-2xl mx-auto mb-space-xl">
                <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-bold">Kompetensi Terpadu</span>
                <h2 class="font-headline-lg text-headline-lg text-on-surface mt-space-xs">Layanan Kami</h2>
                <p class="font-body-lg text-body-lg text-secondary mt-space-xs">
                    Solusi terpadu dan terpercaya untuk akselerasi pengembangan SDM serta pemenuhan tenaga kerja industri.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-lg">
                <!-- Card 1: Pemagangan -->
                <div class="group flex flex-col justify-between p-space-lg rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-transparent hover:border-primary/20">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[30px]">engineering</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-xs">Program Pemagangan</h3>
                        <p class="font-body-md text-body-md text-secondary mb-space-md leading-relaxed">
                            Program magang industri terstruktur sesuai Permenaker No. 6/2020 untuk mencetak SDM kompeten dan hemat biaya operasional.
                        </p>
                    </div>
                    <a href="{{ route('benefit-pemagangan') }}" class="inline-flex items-center gap-space-xs font-label-md text-label-md text-primary font-bold group-hover:translate-x-1 transition-transform">
                        <span>Selengkapnya</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 2: Labor Supply -->
                <div class="group flex flex-col justify-between p-space-lg rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-transparent hover:border-primary/20">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[30px]">groups</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-xs">Labor Supply</h3>
                        <p class="font-body-md text-body-md text-secondary mb-space-md leading-relaxed">
                            Penyediaan tenaga kerja lepas yang telah dilatih soft skill & hard skill siap pakai untuk kebutuhan operasional manufaktur.
                        </p>
                    </div>
                    <a href="{{ route('benefit-labor-supply') }}" class="inline-flex items-center gap-space-xs font-label-md text-label-md text-primary font-bold group-hover:translate-x-1 transition-transform">
                        <span>Selengkapnya</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 3: Training Program -->
                <div class="group flex flex-col justify-between p-space-lg rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-transparent hover:border-primary/20">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[30px]">model_training</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-xs">Training Development</h3>
                        <p class="font-body-md text-body-md text-secondary mb-space-md leading-relaxed">
                            Perancangan modul pelatihan kejuruan spesifik yang disesuaikan dengan SOP manufaktur dan standar mutu internasional.
                        </p>
                    </div>
                    <a href="{{ route('benefit-training') }}" class="inline-flex items-center gap-space-xs font-label-md text-label-md text-primary font-bold group-hover:translate-x-1 transition-transform">
                        <span>Selengkapnya</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>

                <!-- Card 4: Outsourcing -->
                <div class="group flex flex-col justify-between p-space-lg rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-transparent hover:border-primary/20">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-secondary-fixed flex items-center justify-center text-secondary mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[30px]">handshake</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-xs">Outsourcing Tenaga Kerja</h3>
                        <p class="font-body-md text-body-md text-secondary mb-space-md leading-relaxed">
                            Solusi alih daya profesional, patuh hukum UU Ketenagakerjaan, efisien, dan transparan untuk operasional perusahaan Anda.
                        </p>
                    </div>
                    <a href="{{ route('benefit-outsourcing') }}" class="inline-flex items-center gap-space-xs font-label-md text-label-md text-primary font-bold group-hover:translate-x-1 transition-transform">
                        <span>Selengkapnya</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- BANNER HIGHLIGHT & KEUNGGULAN SECTION -->
    <section class="w-full py-16 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-stretch">
                <!-- Left Side: Pusat Konsultasi Pemagangan Card -->
                <div class="lg:col-span-5 flex flex-col justify-between p-space-xl rounded-2xl bg-primary text-on-primary shadow-xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-[240px]">gavel</span>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-space-xs px-space-sm py-1 rounded-md bg-on-primary/10 text-on-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-md">
                            <span class="material-symbols-outlined text-[16px]">verified_user</span>
                            Efisiensi & Legalitas
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-primary mb-space-md leading-tight">
                            Solusi Biaya SDM & Legalitas Aman
                        </h3>
                        <p class="font-body-lg text-body-lg text-on-primary/90 mb-space-lg leading-relaxed">
                            Program pemagangan mengacu Permenaker No. 6 Tahun 2020. Membantu perusahaan menghemat biaya rekrutmen hingga 75-80% dengan kepatuhan hukum terjamin.
                        </p>
                    </div>
                    <div>
                        @php
                            $phone = preg_replace('/[^0-9]/', '', config('gyokai.phone', '082128496168'));
                            if (substr($phone, 0, 2) !== '62' && substr($phone, 0, 1) !== '0') {
                                $phone = '62' . ltrim($phone, '0');
                            } elseif (substr($phone, 0, 1) === '0') {
                                $phone = '62' . substr($phone, 1);
                            }
                            $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . urlencode('Halo LPK Gyokai, saya ingin konsultasi tentang Program Pemagangan dan Layanan Industri');
                        @endphp
                        <a href="{{ $whatsappUrl }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-space-xs px-space-lg py-3 rounded-lg bg-surface-container-lowest text-primary font-headline-sm text-headline-sm hover:bg-surface-container transition-all shadow-md">
                            <span>Konsultasi Gratis via WhatsApp</span>
                            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Keunggulan Kami List -->
                <div class="lg:col-span-7 flex flex-col justify-center p-space-xl rounded-2xl bg-surface-container-lowest shadow-md">
                    <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-bold mb-space-xs">Standar Kualitas</span>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface mb-space-lg">Keunggulan LPK GYOKAI</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-lg">
                        <div class="flex items-start gap-space-md">
                            <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">badge</span>
                            </div>
                            <div>
                                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-1">Tenaga Kerja Terlatih</h4>
                                <p class="font-body-md text-body-md text-secondary">Bersertifikasi resmi BNSP dan dibekali etika kerja standar industri Jepang (Kaizen & 5S).</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-md">
                            <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">route</span>
                            </div>
                            <div>
                                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-1">Pendampingan Penuh</h4>
                                <p class="font-body-md text-body-md text-secondary">Bimbingan intensif dari seleksi berkas, tes medis, pembinaan fisik, hingga evaluasi penempatan.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-md">
                            <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                            </div>
                            <div>
                                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-1">Mitra Industri Luas</h4>
                                <p class="font-body-md text-body-md text-secondary">Bekerjasama dengan lebih dari 50 perusahaan manufaktur terkemuka nasional & asosiasi Jepang.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-md">
                            <div class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">policy</span>
                            </div>
                            <div>
                                <h4 class="font-headline-sm text-headline-sm text-on-surface mb-1">Jaminan Legalitas</h4>
                                <p class="font-body-md text-body-md text-secondary">Memiliki izin operasional Kemnaker RI, akreditasi resmi LA-LPK, dan audit berkala yang transparan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BERITA TERBARU & VIDEO DOKUMENTASI SECTION (DYNAMIC DATA) -->
    <section class="w-full py-20 bg-background">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-space-xl">
                <div>
                    <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-bold">Informasi &amp; Kegiatan</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface mt-space-xs">Aktivitas Terkini</h2>
                </div>
                <a href="{{ route('news') }}" class="inline-flex items-center gap-space-xs font-label-md text-label-md text-primary font-bold hover:underline mt-space-sm md:mt-0">
                    <span>Lihat Semua Berita</span>
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
                <!-- News Column (7 Cols) -->
                <div class="lg:col-span-7 flex flex-col gap-space-md">
                    <div class="flex items-center justify-between mb-space-xs">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Berita Terbaru</h3>
                    </div>

                    @php
                        $newsList = ($latest ?? collect([]))->take(3);
                    @endphp

                    @if($newsList->isNotEmpty())
                        @foreach($newsList as $newsItem)
                            <a href="{{ !empty($newsItem->slug) ? route('news.detail', $newsItem->slug) : '#' }}" 
                               class="group flex flex-col sm:flex-row items-center gap-space-md p-space-md rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow border border-surface-container-high/40">
                                <div class="w-full sm:w-44 h-32 rounded-lg overflow-hidden shrink-0 bg-surface-container">
                                    @if($newsItem->thumbnail)
                                        <img src="{{ Storage::url($newsItem->thumbnail) }}" 
                                             alt="{{ $newsItem->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy"/>
                                    @else
                                        <img src="{{ asset('img/blog-1.png') }}" 
                                             alt="{{ $newsItem->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy"/>
                                    @endif
                                </div>
                                <div class="flex flex-col justify-between py-1 w-full">
                                    <div>
                                        <span class="inline-block px-space-xs py-0.5 rounded bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm mb-space-xs">
                                            {{ $newsItem->category->name ?? 'Kegiatan' }}
                                        </span>
                                        <h4 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            {{ $newsItem->title }}
                                        </h4>
                                    </div>
                                    <div class="flex items-center gap-space-xs text-secondary font-body-sm text-body-sm mt-space-sm">
                                        <span class="material-symbols-outlined text-[16px]">event</span>
                                        <span>{{ \Carbon\Carbon::parse($newsItem->created_at)->translatedFormat('d F Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <!-- Fallback Static Items if no news exists yet -->
                        <div class="p-8 text-center bg-surface-container-lowest rounded-xl border border-surface-container text-secondary">
                            Belum ada berita terbaru yang dipublikasikan.
                        </div>
                    @endif
                </div>

                <!-- Video Column (5 Cols) -->
                <div class="lg:col-span-5 flex flex-col">
                    <div class="flex items-center justify-between mb-space-md">
                        <h3 class="font-headline-md text-headline-md text-on-surface">Video Dokumentasi</h3>
                        <span class="font-label-sm text-label-sm text-secondary">HD Quality</span>
                    </div>

                    @php
                        $featuredVideo = isset($videos) ? $videos->first() : null;
                    @endphp

                    @if($featuredVideo)
                        <div class="relative w-full rounded-2xl overflow-hidden shadow-lg bg-on-surface group cursor-pointer aspect-video flex items-center justify-center"
                             onclick="openVideoModal('{{ $featuredVideo->video_id ? 'https://www.youtube.com/embed/'.$featuredVideo->video_id.'?autoplay=1' : $featuredVideo->embed_url }}')">
                            <img class="w-full h-full object-cover opacity-85 group-hover:opacity-75 transition-opacity" 
                                 src="{{ $featuredVideo->thumbnail_url ?: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBf0jZtRFhu3mxkFmC1TnwuTPfO3jMDR0nrisPJHLUL5bwwWdIlUyx33qmVx1DnePXxLxCuyZW-vPm92JuCXE88ZadV27A_PS4GN9SB81F62enbLv48Ek-6fdfmypwKXsq9ZUEXDG0sbRLUXt-eaPHBRuqvVVAqACQhjCAt9btlOUZQLG89RsYrd51v4_grdmhOilMPrHJJHBCXgYs7b_uot_asE2QnB7q_DR_1hYw' }}" 
                                 alt="{{ $featuredVideo->title }}"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/90 via-transparent to-transparent"></div>
                            <!-- Play Button -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                </div>
                                <span class="mt-space-sm font-label-md text-label-md text-on-primary font-bold">Putar Video Kegiatan</span>
                            </div>
                            <!-- Video Info -->
                            <div class="absolute bottom-space-md left-space-md right-space-md z-10 flex items-center justify-between text-on-primary">
                                <div class="pr-4">
                                    <p class="font-headline-sm text-headline-sm text-on-primary leading-tight line-clamp-1">{{ $featuredVideo->title }}</p>
                                    <p class="font-body-sm text-body-sm text-on-primary/80 line-clamp-1">Dokumentasi Pelatihan LPK GYOKAI</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Default Video Card -->
                        <div class="relative w-full rounded-2xl overflow-hidden shadow-lg bg-on-surface group cursor-pointer aspect-video flex items-center justify-center"
                             onclick="openVideoModal('https://www.youtube.com/embed/dQw4w9WgXcQ')">
                            <img class="w-full h-full object-cover opacity-85 group-hover:opacity-75 transition-opacity" 
                                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuBf0jZtRFhu3mxkFmC1TnwuTPfO3jMDR0nrisPJHLUL5bwwWdIlUyx33qmVx1DnePXxLxCuyZW-vPm92JuCXE88ZadV27A_PS4GN9SB81F62enbLv48Ek-6fdfmypwKXsq9ZUEXDG0sbRLUXt-eaPHBRuqvVVAqACQhjCAt9btlOUZQLG89RsYrd51v4_grdmhOilMPrHJJHBCXgYs7b_uot_asE2QnB7q_DR_1hYw" 
                                 alt="Video Kegiatan LPK Gyokai"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/90 via-transparent to-transparent"></div>
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-[36px]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                </div>
                                <span class="mt-space-sm font-label-md text-label-md text-on-primary font-bold">Putar Video Kegiatan</span>
                            </div>
                        </div>
                    @endif

                    <!-- Video Highlights Under Card -->
                    <div class="mt-space-md p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-md border border-surface-container">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-lowest text-primary flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-[24px]">live_tv</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline-sm text-headline-sm text-on-surface">Kunjungan Virtual Fasilitas</span>
                            <span class="font-body-sm text-body-sm text-secondary">Lihat standar asrama, bengkel kerja mesin, dan ruang kelas interaktif kami.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION BANNER (BOTTOM) -->
    <section class="w-full bg-primary-container text-on-primary-container py-space-xl">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-space-lg text-center lg:text-left">
                <div class="max-w-3xl">
                    <h2 class="font-headline-lg text-headline-lg text-on-primary leading-snug">
                        Ingin Mendaftar dan Informasi Lebih Lengkap?
                    </h2>
                    <p class="font-body-lg text-body-lg text-on-primary/90 mt-space-xs">
                        Hubungi kami sekarang untuk konsultasi program magang, kemitraan penyaluran kerja industri, atau jadwal seleksi peserta.
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-space-md">
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center gap-space-xs px-space-xl py-3.5 rounded-lg bg-surface-container-lowest text-primary font-headline-sm text-headline-sm hover:bg-surface-container transition-all shadow-lg hover:shadow-xl">
                        <span class="material-symbols-outlined text-[22px]">contact_support</span>
                        <span>Hubungi Kami</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Video -->
<div id="videoModal" class="video-modal" onclick="closeVideoModal()">
    <div class="relative w-full max-w-4xl bg-black rounded-2xl overflow-hidden shadow-2xl p-2" onclick="event.stopPropagation()">
        <button class="absolute top-4 right-4 z-20 text-white bg-black/60 hover:bg-black p-2 rounded-full leading-none flex items-center justify-center" onclick="closeVideoModal()">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
        <div class="aspect-video w-full">
            <iframe id="videoModalIframe" class="w-full h-full rounded-xl" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openVideoModal(url) {
        if (!url) return;
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoModalIframe');
        iframe.src = url;
        modal.classList.add('active');
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('videoModalIframe');
        iframe.src = '';
        modal.classList.remove('active');
    }
</script>
@endpush
