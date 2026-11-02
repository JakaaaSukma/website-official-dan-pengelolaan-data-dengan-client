<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#ffffff"/>
    <link rel="icon" href="{{ asset('logo/Gyokai.png') }}" type="image/png"/>

    <!-- Google Web Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- SEO Component -->
    @include('components.seo-head', [
        'page' => $seoPage ?? 'home',
        'title' => $pageTitle ?? null,
        'description' => $pageDescription ?? null,
        'jobs' => $jobVacancies ?? null,
        'news' => $latest ?? null,
        'canonical' => $canonical ?? null,
    ])

    <!-- Google tag (gtag.js) -->
    @if(config('gyokai.ga_tracking_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('gyokai.ga_tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('gyokai.ga_tracking_id') }}');
    </script>
    @endif

    <!-- Google Ads (AdSense) -->
    @if(config('gyokai.google_ads_client'))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('gyokai.google_ads_client') }}" crossorigin="anonymous"></script>
    @endif

    <!-- Tailwind CSS (CDN with Custom Design System Config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary-fixed": "#ffdad6",
                    "surface-container-low": "#f2f4f6",
                    "secondary": "#545f73",
                    "on-tertiary-fixed": "#410002",
                    "surface-container-high": "#e6e8ea",
                    "secondary-fixed": "#d8e3fb",
                    "on-tertiary-container": "#fff1f0",
                    "on-secondary-fixed": "#111c2d",
                    "background": "#f7f9fb",
                    "error-container": "#ffdad6",
                    "on-primary-fixed-variant": "#930010",
                    "on-secondary-fixed-variant": "#3c475a",
                    "tertiary-container": "#d42c2b",
                    "surface-container-highest": "#e0e3e5",
                    "on-surface": "#191c1e",
                    "on-secondary": "#ffffff",
                    "outline": "#8f6f6c",
                    "inverse-on-surface": "#eff1f3",
                    "surface-container-lowest": "#ffffff",
                    "on-secondary-container": "#586377",
                    "on-background": "#191c1e",
                    "error": "#ba1a1a",
                    "inverse-primary": "#ffb3ac",
                    "surface-dim": "#d8dadc",
                    "secondary-container": "#d5e0f8",
                    "on-tertiary-fixed-variant": "#93000d",
                    "on-error-container": "#93000a",
                    "outline-variant": "#e4beba",
                    "inverse-surface": "#2d3133",
                    "surface-container": "#eceef0",
                    "surface-bright": "#f7f9fb",
                    "surface-variant": "#e0e3e5",
                    "on-tertiary": "#ffffff",
                    "tertiary": "#b00b16",
                    "tertiary-fixed": "#ffdad6",
                    "on-surface-variant": "#5b403d",
                    "primary-container": "#d32f2f",
                    "primary": "#af101a",
                    "on-primary": "#ffffff",
                    "primary-fixed-dim": "#ffb3ac",
                    "tertiary-fixed-dim": "#ffb4ac",
                    "secondary-fixed-dim": "#bcc7de",
                    "surface": "#f7f9fb",
                    "surface-tint": "#ba1a20",
                    "on-primary-container": "#fff2f0",
                    "on-primary-fixed": "#410003",
                    "on-error": "#ffffff"
                },
                borderRadius: {
                    DEFAULT: "0.125rem",
                    lg: "0.25rem",
                    xl: "0.5rem",
                    full: "0.75rem"
                },
                spacing: {
                    "space-xs": "0.25rem",
                    "space-sm": "0.5rem",
                    "space-md": "1rem",
                    "space-lg": "1.5rem",
                    "space-xl": "2.5rem",
                    "margin": "2rem",
                    "margin-mobile": "1rem",
                    "gutter": "1.5rem",
                    "gutter-mobile": "1rem"
                },
                fontFamily: {
                    "headline-xl": ["Plus Jakarta Sans", "sans-serif"],
                    "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
                    "headline-md": ["Plus Jakarta Sans", "sans-serif"],
                    "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
                    "body-lg": ["Inter", "sans-serif"],
                    "body-md": ["Inter", "sans-serif"],
                    "body-sm": ["Inter", "sans-serif"],
                    "label-md": ["Plus Jakarta Sans", "sans-serif"],
                    "label-sm": ["Plus Jakarta Sans", "sans-serif"]
                },
                fontSize: {
                    "headline-xl": ["44px", { lineHeight: "54px", fontWeight: "800" }],
                    "headline-xl-mobile": ["30px", { lineHeight: "38px", fontWeight: "800" }],
                    "headline-lg": ["32px", { lineHeight: "40px", fontWeight: "700" }],
                    "headline-lg-mobile": ["24px", { lineHeight: "32px", fontWeight: "700" }],
                    "headline-md": ["24px", { lineHeight: "32px", fontWeight: "700" }],
                    "headline-sm": ["18px", { lineHeight: "26px", fontWeight: "600" }],
                    "body-lg": ["16px", { lineHeight: "26px", fontWeight: "400" }],
                    "body-md": ["14px", { lineHeight: "22px", fontWeight: "400" }],
                    "body-sm": ["12px", { lineHeight: "18px", fontWeight: "400" }],
                    "label-md": ["14px", { lineHeight: "20px", fontWeight: "600" }],
                    "label-sm": ["11px", { lineHeight: "16px", fontWeight: "700" }]
                }
            }
        }
    };
    </script>

    <style>
        @layer base {
            html, body {
                margin: 0;
                padding: 0;
            }
            body {
                overscroll-behavior: none;
            }
            main > :first-child {
                margin-top: 0 !important;
            }
            main > :last-child {
                margin-bottom: 0 !important;
            }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        /* Marquee styles */
        .client-marquee {
            position: relative;
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }
        .marquee-track {
            display: flex;
            gap: 2rem;
            width: max-content;
            animation: marquee 35s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }
        .marquee-reverse {
            animation: marquee-rev 35s linear infinite;
        }
        .marquee-reverse:hover {
            animation-play-state: paused;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes marquee-rev {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0); }
        }
        /* Modal for video */
        .video-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.85);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .video-modal.active {
            display: flex;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased flex flex-col min-h-screen">
    @php
        $currentRoute = request()->route() ? request()->route()->getName() : '';
    @endphp

    <!-- Top Navigation Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface-container-lowest/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.06)]">
        <div class="h-20 max-w-7xl mx-auto px-gutter flex items-center justify-between gap-space-md">
            <!-- Brand Logo -->
            <a href="{{ route('index') }}" class="flex items-center gap-space-sm shrink-0">
                <img alt="Logo LPK GYOKAI" class="h-10 w-auto object-contain" src="{{ asset('img/jelas.png') }}"/>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary tracking-tight leading-none uppercase">LPK GYOKAI</span>
                    <span class="font-label-sm text-label-sm text-secondary tracking-wider uppercase leading-tight">INDONESIA KOMPETEN</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden xl:flex items-center gap-1">
                <a href="{{ route('index') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ $currentRoute === 'index' ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Beranda
                </a>
                <a href="{{ route('about') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ $currentRoute === 'about' ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Tentang Kami
                </a>
                <a href="{{ route('our-services') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ in_array($currentRoute, ['our-services', 'benefit-pemagangan', 'benefit-labor-supply', 'benefit-training', 'benefit-outsourcing']) ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Layanan
                </a>
                <a href="{{ route('service') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ $currentRoute === 'service' ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Clients
                </a>
                <a href="{{ route('blog') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ $currentRoute === 'blog' ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Lowongan Kerja
                </a>
                <a href="{{ route('news') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ in_array($currentRoute, ['news', 'news.detail']) ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Berita
                </a>

                <!-- Dropdown Pages -->
                <div class="relative group" id="pages-dropdown-container">
                    <button type="button" 
                            class="flex items-center gap-1 px-space-sm py-space-xs transition-colors rounded-lg {{ in_array($currentRoute, ['team', 'fasilitas', 'legalitas', 'upload', 'lamaran', 'sipeduli']) ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                        <span>Pages</span>
                        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:rotate-180">expand_more</span>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="absolute left-0 top-full pt-1.5 hidden group-hover:block w-52 z-50">
                        <div class="bg-surface-container-lowest rounded-xl shadow-xl border border-surface-container-high/60 py-2 overflow-hidden flex flex-col">
                            <a href="{{ route('team') }}" 
                               class="px-4 py-2 text-sm font-label-md {{ $currentRoute === 'team' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-low' }} transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">groups</span>
                                <span>Team</span>
                            </a>
                            <a href="{{ route('fasilitas') }}" 
                               class="px-4 py-2 text-sm font-label-md {{ $currentRoute === 'fasilitas' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-low' }} transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">apartment</span>
                                <span>Fasilitas &amp; Galeri</span>
                            </a>
                            <a href="{{ route('legalitas') }}" 
                               class="px-4 py-2 text-sm font-label-md {{ $currentRoute === 'legalitas' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-low' }} transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">verified_user</span>
                                <span>Legalitas</span>
                            </a>
                            <a href="{{ route('upload') }}" 
                               class="px-4 py-2 text-sm font-label-md {{ $currentRoute === 'upload' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-low' }} transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">upload_file</span>
                                <span>Upload</span>
                            </a>
                            <a href="{{ route('lamaran') }}" 
                               class="px-4 py-2 text-sm font-label-md {{ $currentRoute === 'lamaran' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-low' }} transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">assignment_ind</span>
                                <span>Pelamaran Kerja</span>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('contact') }}" 
                   class="px-space-sm py-space-xs transition-colors rounded-lg {{ $currentRoute === 'contact' ? 'bg-primary-container text-on-primary-container font-headline-sm' : 'font-label-md text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                    Kontak
                </a>
            </nav>

            <!-- Actions & Mobile Menu Toggle -->
            <div class="flex items-center gap-space-sm shrink-0">
                <a href="{{ route('contact') }}" 
                   class="hidden sm:inline-flex items-center justify-center px-space-md py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary transition-all shadow-sm">
                    Hubungi Kami
                </a>
                <!-- Mobile Menu Button -->
                <button type="button" 
                        onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" 
                        class="xl:hidden p-2 rounded-lg text-on-surface hover:bg-surface-container"
                        aria-label="Toggle Menu">
                    <span class="material-symbols-outlined text-[26px]">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-nav" class="hidden xl:hidden bg-surface-container-lowest border-t border-surface-container px-gutter py-space-md shadow-lg">
            <nav class="flex flex-col gap-1">
                <a href="{{ route('index') }}" class="px-space-md py-2.5 rounded-lg {{ $currentRoute === 'index' ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Beranda</a>
                <a href="{{ route('about') }}" class="px-space-md py-2.5 rounded-lg {{ $currentRoute === 'about' ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Tentang Kami</a>
                <a href="{{ route('our-services') }}" class="px-space-md py-2.5 rounded-lg {{ in_array($currentRoute, ['our-services', 'benefit-pemagangan', 'benefit-labor-supply', 'benefit-training', 'benefit-outsourcing']) ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Layanan</a>
                <a href="{{ route('service') }}" class="px-space-md py-2.5 rounded-lg {{ $currentRoute === 'service' ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Clients</a>
                <a href="{{ route('blog') }}" class="px-space-md py-2.5 rounded-lg {{ $currentRoute === 'blog' ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Lowongan Kerja</a>
                <a href="{{ route('news') }}" class="px-space-md py-2.5 rounded-lg {{ in_array($currentRoute, ['news', 'news.detail']) ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Berita</a>
                
                <!-- Mobile Pages Accordion -->
                <div class="flex flex-col">
                    <button type="button" 
                            onclick="document.getElementById('mobile-pages-sub').classList.toggle('hidden'); document.getElementById('mobile-pages-icon').classList.toggle('rotate-180');" 
                            class="flex items-center justify-between px-space-md py-2.5 rounded-lg {{ in_array($currentRoute, ['team', 'fasilitas', 'legalitas', 'upload', 'lamaran']) ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                        <span>Pages</span>
                        <span id="mobile-pages-icon" class="material-symbols-outlined text-[20px] transition-transform">expand_more</span>
                    </button>
                    <div id="mobile-pages-sub" class="{{ in_array($currentRoute, ['team', 'fasilitas', 'legalitas', 'upload', 'lamaran']) ? '' : 'hidden' }} flex flex-col pl-4 py-1 gap-1 border-l-2 border-primary/20 ml-4 my-1">
                        <a href="{{ route('team') }}" class="px-3 py-2 rounded-lg text-sm {{ $currentRoute === 'team' ? 'bg-primary text-white font-bold' : 'text-secondary hover:text-on-surface' }}">Team</a>
                        <a href="{{ route('fasilitas') }}" class="px-3 py-2 rounded-lg text-sm {{ $currentRoute === 'fasilitas' ? 'bg-primary text-white font-bold' : 'text-secondary hover:text-on-surface' }}">Fasilitas &amp; Galeri</a>
                        <a href="{{ route('legalitas') }}" class="px-3 py-2 rounded-lg text-sm {{ $currentRoute === 'legalitas' ? 'bg-primary text-white font-bold' : 'text-secondary hover:text-on-surface' }}">Legalitas</a>
                        <a href="{{ route('upload') }}" class="px-3 py-2 rounded-lg text-sm {{ $currentRoute === 'upload' ? 'bg-primary text-white font-bold' : 'text-secondary hover:text-on-surface' }}">Upload</a>
                        <a href="{{ route('lamaran') }}" class="px-3 py-2 rounded-lg text-sm {{ $currentRoute === 'lamaran' ? 'bg-primary text-white font-bold' : 'text-secondary hover:text-on-surface' }}">Pelamaran Kerja</a>
                    </div>
                </div>

                <a href="{{ route('contact') }}" class="px-space-md py-2.5 rounded-lg {{ $currentRoute === 'contact' ? 'bg-primary text-white font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">Kontak</a>
                <div class="pt-2 border-t border-surface-container mt-2">
                    <a href="{{ route('contact') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-primary text-white font-bold text-center">Hubungi Kami</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full pt-20 bg-background flex-grow">
        @yield('content')
    </main>

    <!-- Modern Redesigned Footer -->
    <footer class="w-full bg-surface-container-lowest shadow-[0_-1px_6px_rgba(0,0,0,0.03)] border-t border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter pt-space-xl pb-space-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-lg mb-space-xl">
                <!-- Column 1: Brand Info -->
                <div class="flex flex-col gap-space-sm">
                    <div class="flex items-center gap-space-xs">
                        <img alt="Logo LPK GYOKAI" class="h-8 w-auto object-contain" src="{{ asset('img/jelas.png') }}"/>
                        <span class="font-headline-md text-headline-md text-primary tracking-tight uppercase">LPK GYOKAI</span>
                    </div>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">
                        Mencetak Tenaga Kerja Terampil dan Kompeten berstandar industri internasional dan regulasi ketenagakerjaan resmi.
                    </p>
                    <div class="mt-space-sm inline-flex items-center gap-space-xs px-space-sm py-space-xs rounded-lg bg-surface-container text-on-surface font-label-sm text-label-sm w-fit">
                        <span class="material-symbols-outlined text-primary text-[16px]">verified</span>
                        Terakreditasi Resmi Kemnaker RI
                    </div>
                </div>

                <!-- Column 2: Menu Utama -->
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Menu Utama</h3>
                    <ul class="flex flex-col gap-2">
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('index') }}">Beranda</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('about') }}">Tentang Kami</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('our-services') }}">Layanan</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('legalitas') }}">Legalitas</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('blog') }}">Lowongan Kerja</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('news') }}">Berita</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('contact') }}">Kontak</a></li>
                    </ul>
                </div>

                <!-- Column 3: Layanan Kami -->
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Layanan Kami</h3>
                    <ul class="flex flex-col gap-2">
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('benefit-pemagangan') }}">Program Pemagangan</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('benefit-labor-supply') }}">Labor Supply</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('benefit-training') }}">Training Program Development</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('benefit-outsourcing') }}">Outsourcing Tenaga Kerja</a></li>
                        <li><a class="font-body-md text-body-md text-secondary hover:text-primary transition-colors" href="{{ route('fasilitas') }}">Fasilitas & Galeri</a></li>
                    </ul>
                </div>

                <!-- Column 4: Kontak Kami -->
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Kontak Kami</h3>
                    <div class="flex flex-col gap-space-sm text-body-md text-secondary">
                        <div class="flex items-start gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">location_on</span>
                            <span>{{ config('gyokai.address', 'Jl. Raya Serang Km 14, Cikupa, Tangerang, Banten') }}</span>
                        </div>
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[20px] shrink-0">call</span>
                            <a href="tel:{{ config('gyokai.phone', '082128496168') }}" class="hover:text-primary transition-colors">{{ config('gyokai.phone', '082128496168') }}</a>
                        </div>
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[20px] shrink-0">mail</span>
                            <a href="mailto:{{ config('gyokai.contact_email', 'info@gyokai.co.id') }}" class="hover:text-primary transition-colors">{{ config('gyokai.contact_email', 'info@gyokai.co.id') }}</a>
                        </div>
                        <!-- Social Media -->
                        <div class="flex items-center gap-3 mt-2">
                            @if(config('gyokai.instagram_url'))
                            <a href="{{ config('gyokai.instagram_url') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors" title="Instagram">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            @endif
                            @if(config('gyokai.facebook_url'))
                            <a href="{{ config('gyokai.facebook_url') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors" title="Facebook">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.7 5H18V0h-3.808C10.597 0 9 1.583 9 4.615V8z"/></svg>
                            </a>
                            @endif
                            @if(config('gyokai.linkedin_url'))
                            <a href="{{ config('gyokai.linkedin_url') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors" title="LinkedIn">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright bar -->
            <div class="pt-space-md bg-surface-container-low rounded-lg px-space-md flex flex-col md:flex-row items-center justify-between gap-space-sm text-center md:text-left">
                <p class="font-body-sm text-body-sm text-secondary">
                    &copy; {{ date('Y') }} {{ config('app.name', 'LPK GYOKAI INDONESIA KOMPETEN') }}. All rights reserved.
                </p>
                <div class="flex items-center gap-space-md font-body-sm text-body-sm text-secondary">
                    <a class="hover:text-primary transition-colors" href="{{ route('legalitas') }}">Legalitas & Perizinan</a>
                    <span>|</span>
                    <a class="hover:text-primary transition-colors" href="{{ route('contact') }}">Pusat Bantuan</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
