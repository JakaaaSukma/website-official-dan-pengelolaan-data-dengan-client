@php
    $seoPage = 'fasilitas';
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
                <span class="text-secondary">Pages</span>
                <span class="text-secondary/60">/</span>
                <span class="text-primary font-label-sm font-bold">Fasilitas &amp; Galeri</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">apartment</span>
                        Infrastruktur &amp; Sarana Pelatihan
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm font-bold">
                        Fasilitas &amp; Galeri Kegiatan
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Sarana pembelajaran terintegrasi standar Japanese Industrial Standards (JIS), ruang kelas multimedia ber-AC, laboratorium bahasa, workshop kejuruan manufaktur, dan asrama disiplin.
                    </p>
                </div>

                <!-- Quick Stats Pill -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">domain</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">
                            Standar JIS
                        </div>
                        <div class="font-body-sm text-body-sm text-secondary">Terakreditasi Lengkap</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nav Filter Tabs -->
    <section class="max-w-7xl mx-auto w-full px-gutter pt-8">
        <div class="flex items-center gap-2 border-b border-surface-container pb-4">
            <button type="button" 
                    onclick="switchSection('fasilitas', this)" 
                    class="tab-btn px-5 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary font-bold shadow-sm transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">engineering</span>
                <span>Fasilitas Pelatihan</span>
            </button>
            <button type="button" 
                    onclick="switchSection('galeri', this)" 
                    class="tab-btn px-5 py-2.5 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface font-semibold transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">photo_library</span>
                <span>Galeri Foto &amp; Kegiatan</span>
            </button>
        </div>
    </section>

    <!-- Section 1: Fasilitas Utama -->
    <section id="section-fasilitas" class="max-w-7xl mx-auto w-full px-gutter py-8">
        <div class="flex items-center justify-between mb-space-lg">
            <div class="flex items-center gap-space-sm">
                <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">Fasilitas Kampus &amp; Workshop Diklat</h2>
            </div>
            <span class="text-body-sm font-body-sm text-secondary hidden sm:inline-block">Sarana modern penunjang kesiapan kerja</span>
        </div>

        @if($facilities && $facilities->isNotEmpty())
            <!-- Dynamic Data from DB -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
                @foreach($facilities as $facility)
                    <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                        <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                             onclick="openImageModal('{{ Storage::url($facility->photo) }}', '{{ $facility->name }}')">
                            @if($facility->photo)
                                <img src="{{ Storage::url($facility->photo) }}" 
                                     alt="{{ $facility->name ?? 'Fasilitas' }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     loading="lazy"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-surface-container text-secondary">
                                    <span class="material-symbols-outlined text-5xl text-secondary/40">image</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                </span>
                            </div>
                        </div>
                        <div class="p-space-lg flex flex-col flex-1 justify-between">
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                    {{ $facility->name }}
                                </h3>
                                @if($facility->description)
                                    <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                        {{ $facility->description }}
                                    </p>
                                @endif
                            </div>
                            <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                                <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">verified</span>
                                    Fasilitas Resmi
                                </span>
                                <button type="button" 
                                        onclick="openImageModal('{{ Storage::url($facility->photo) }}', '{{ $facility->name }}')"
                                        class="text-xs font-bold text-secondary hover:text-primary transition-colors">
                                    Lihat Foto Penuh →
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Standard Refined Facility Mockup Items -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
                <!-- Fasilitas 1 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80', 'Workshop Mesin Bubut & CNC Manufaktur')">
                        <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80" 
                             alt="Workshop Mesin Bubut & CNC Manufaktur" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-primary text-on-primary text-label-sm font-label-sm shadow font-bold">
                            Workshop Teknik
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Workshop Mesin Bubut &amp; CNC Presisi
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Dilengkapi mesin bubut konvensional dan simulator CNC modern untuk pembekalan praktikum manufaktur berstandar Japanese Industrial Standards (JIS).
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Standar Industri
                            </span>
                            <span class="text-xs font-bold text-secondary">Kapasitas 40 Siswa</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas 2 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&auto=format&fit=crop&q=80', 'Ruang Kelas Bahasa Jepang Ber-AC & Multimedia')">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&auto=format&fit=crop&q=80" 
                             alt="Ruang Kelas Bahasa Jepang Ber-AC & Multimedia" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary text-on-secondary text-label-sm font-label-sm shadow font-bold">
                            Ruang Teori
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Ruang Kelas Bahasa &amp; Budaya Jepang
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Ruang kelas berpendingin udara (AC) dengan perangkat proyektor pintar dan audio listening intensif untuk persiapan ujian JLPT &amp; JFT-Basic.
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Multimedia AC
                            </span>
                            <span class="text-xs font-bold text-secondary">Audio Listening Hub</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas 3 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800&auto=format&fit=crop&q=80', 'Laboratorium Komputer & Simulasi Tes Digital')">
                        <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800&auto=format&fit=crop&q=80" 
                             alt="Laboratorium Komputer & Simulasi CBT" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm shadow font-bold">
                            Lab Komputer
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Laboratorium CBT &amp; Simulasi Ujian
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Dilengkapi puluhan unit komputer berkecepatan tinggi untuk pelaksanaan tryout daring, tes psikometri, dan wawancara online via telekonferensi dengan user Jepang.
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                High-Speed Lan
                            </span>
                            <span class="text-xs font-bold text-secondary">50 PC Terhubung</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas 4 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&auto=format&fit=crop&q=80', 'Asrama Siswa Bersih & Berdisiplin')">
                        <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&auto=format&fit=crop&q=80" 
                             alt="Asrama Siswa LPK Gyokai" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-surface-container-high text-on-surface text-label-sm font-label-sm shadow font-bold">
                            Asrama Diklat
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Asrama Siswa (Dormitory) Berdisiplin
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Hunian asrama terpisah putra dan putri dengan tata tertib ketat 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) untuk membentuk kemandirian sebelum berangkat.
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Budaya 5S Kaizen
                            </span>
                            <span class="text-xs font-bold text-secondary">Aman &amp; Nyaman</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas 5 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&auto=format&fit=crop&q=80', 'Lapangan Latihan Fisik & Baris Berbaris')">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&auto=format&fit=crop&q=80" 
                             alt="Lapangan Olahraga & Pembinaan Fisik" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-primary-fixed text-on-primary-fixed-variant text-label-sm font-label-sm shadow font-bold">
                            Fasilitas Fisik
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Lapangan Latihan Fisik &amp; FMD
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Area terbuka luas untuk senam pagi, latihan ketahanan stamina, pembinaan baris-berbaris (PBB), dan penguatan kedisiplinan kerja tim.
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Pembinaan Mental
                            </span>
                            <span class="text-xs font-bold text-secondary">Outdoor Area</span>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas 6 -->
                <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col">
                    <div class="relative w-full h-64 overflow-hidden bg-surface-container cursor-pointer"
                         onclick="openImageModal('https://images.unsplash.com/photo-1517502884422-41eaead166d4?w=800&auto=format&fit=crop&q=80', 'Auditorium Serbaguna & Ruang Wawancara Kerja')">
                        <img src="https://images.unsplash.com/photo-1517502884422-41eaead166d4?w=800&auto=format&fit=crop&q=80" 
                             alt="Auditorium Serbaguna" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"/>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-secondary text-on-secondary text-label-sm font-label-sm shadow font-bold">
                            Aula Pertemuan
                        </div>
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="w-10 h-10 rounded-full bg-white/90 text-primary flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-space-lg flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                Aula Pertemuan &amp; Ruang Interview User
                            </h3>
                            <p class="font-body-md text-body-md text-secondary line-clamp-2 leading-relaxed mt-2">
                                Tempat pelaksanaan orientasi awal, simulasi wawancara formal, upacara pelepasan kontingen magang ke Jepang, dan seminar kemitraan industri.
                            </p>
                        </div>
                        <div class="pt-space-md mt-space-sm flex items-center justify-between border-t border-surface-container-high/40">
                            <span class="text-xs text-primary font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Ruang Serbaguna
                            </span>
                            <span class="text-xs font-bold text-secondary">Kapasitas 150 Orang</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Section 2: Galeri Foto & Kegiatan -->
    <section id="section-galeri" class="max-w-7xl mx-auto w-full px-gutter py-8">
        <div class="flex items-center justify-between mb-space-lg">
            <div class="flex items-center gap-space-sm">
                <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">Dokumentasi &amp; Galeri Aktivitas</h2>
            </div>
            <span class="text-body-sm font-body-sm text-secondary hidden sm:inline-block">Momen pelatihan, pelepasan magang, dan seleksi industri</span>
        </div>

        @if($galeri && $galeri->isNotEmpty())
            <!-- Dynamic Galeri from DB -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-space-md">
                @foreach($galeri as $item)
                    @if($item->photo)
                        <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                             onclick="openImageModal('{{ Storage::url($item->photo) }}', 'Dokumentasi Kegiatan LPK Gyokai')">
                            <img src="{{ Storage::url($item->photo) }}" 
                                 alt="Galeri Kegiatan" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                 loading="lazy"/>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                <span class="text-white text-xs font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    Lihat Foto
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <!-- Sample Refined Gallery Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-space-md">
                <!-- Photo 1 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuDs1buS24ajBXo1UR9AG-ggELWsTAvh0STbR-fdE4xYdIzkzWENLentdtJMWEjnKLH4b2EcDIss6NTWw4psk-5AbRhNyTYyzXl7ysI4yl9FGuBSTSM-wmrDQxzP-MqnsxpCFwPbQjCpg-I4BxjfESshIZuXsbCemMB1e8ZELBm9OIp8g3py7QRdk0OaR53RYgoEO-CJkQfujGstuZ1rT9z79X4CvLYmFB2P0a7ZDSA', 'Upacara Pelepasan Peserta Magang Jepang')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDs1buS24ajBXo1UR9AG-ggELWsTAvh0STbR-fdE4xYdIzkzWENLentdtJMWEjnKLH4b2EcDIss6NTWw4psk-5AbRhNyTYyzXl7ysI4yl9FGuBSTSM-wmrDQxzP-MqnsxpCFwPbQjCpg-I4BxjfESshIZuXsbCemMB1e8ZELBm9OIp8g3py7QRdk0OaR53RYgoEO-CJkQfujGstuZ1rT9z79X4CvLYmFB2P0a7ZDSA" 
                         alt="Upacara Pelepasan Peserta Magang" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Pelepasan Magang Ke Jepang</span>
                    </div>
                </div>

                <!-- Photo 2 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuCsLBoO3MDKt2n1ZDpTAEqRgbRAqgeY3vh3srt5op0agN9E1H5BIGrAXPZNgYzRxSl_4if3IH8dqoBbBhMs_9cd4AxJd2M-V5GLS7_vtb7hNU9iCFvs18LjogtqHh-68W_-7Y9jzIyLsOOvHBiIEfl-MJJ9iVrxszObo8PUOd-d_xcjt42EhgtmEgxJKFMOgOyCi35oHk9lUOKH0VwEjAY6ua8BEYE77G1wcNALSjE', 'Praktik Bubut & K3 Manufaktur')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsLBoO3MDKt2n1ZDpTAEqRgbRAqgeY3vh3srt5op0agN9E1H5BIGrAXPZNgYzRxSl_4if3IH8dqoBbBhMs_9cd4AxJd2M-V5GLS7_vtb7hNU9iCFvs18LjogtqHh-68W_-7Y9jzIyLsOOvHBiIEfl-MJJ9iVrxszObo8PUOd-d_xcjt42EhgtmEgxJKFMOgOyCi35oHk9lUOKH0VwEjAY6ua8BEYE77G1wcNALSjE" 
                         alt="Praktik Bubut & K3" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Praktikum Mesin Presisi</span>
                    </div>
                </div>

                <!-- Photo 3 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuBlkt7NVB2ljTVmVJjYC1eMQQigHJvWrLAl4_hUJEl6kLuy10TrbPTTE-wUrqaoejoyS7jFxjSOibx2IBCgOJGakbq0NDUDxfV0DoARtPL-lAxVnKQTbfdS-WmJUIHpTBbhEXCmR1bCP8ewaHhSRoZVM1kWPOq4iRB4Ba82cEST6sSheNO_xmsyd1v-oQtPwHUKnZ0I0OJ6JAldlWW4TVMG6dflw5phCZWHIo3p91w', 'Penandatanganan Kerjasama Industri')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlkt7NVB2ljTVmVJjYC1eMQQigHJvWrLAl4_hUJEl6kLuy10TrbPTTE-wUrqaoejoyS7jFxjSOibx2IBCgOJGakbq0NDUDxfV0DoARtPL-lAxVnKQTbfdS-WmJUIHpTBbhEXCmR1bCP8ewaHhSRoZVM1kWPOq4iRB4Ba82cEST6sSheNO_xmsyd1v-oQtPwHUKnZ0I0OJ6JAldlWW4TVMG6dflw5phCZWHIo3p91w" 
                         alt="Penandatanganan MoU" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">MoU Konsorsium Industri</span>
                    </div>
                </div>

                <!-- Photo 4 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuD94nJbjyy8iYGX09iH_Y9xdUOl8Slb505H01gCcdIznDDYdEkh3qKQX8f_ccQLdd_h48F-_ein5I6bcj_4h7UytnmDOrKynblTnUYrRGRmjLjaNT8EIGUt834Ac_xuciwXuvRVGZVK9DBmoBSE-Yv2ap8pIDgab_RBA6o1zBc3SeRT7T-QirjgnRJ_DWNyoRUMYvt95ZWoZSg2O34yRUkWIWPVXdvd5pjMBR4euOo', 'Uji Kompetensi Nasional BNSP')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuD94nJbjyy8iYGX09iH_Y9xdUOl8Slb505H01gCcdIznDDYdEkh3qKQX8f_ccQLdd_h48F-_ein5I6bcj_4h7UytnmDOrKynblTnUYrRGRmjLjaNT8EIGUt834Ac_xuciwXuvRVGZVK9DBmoBSE-Yv2ap8pIDgab_RBA6o1zBc3SeRT7T-QirjgnRJ_DWNyoRUMYvt95ZWoZSg2O34yRUkWIWPVXdvd5pjMBR4euOo" 
                         alt="Uji Kompetensi BNSP" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Sertifikasi BNSP Nasional</span>
                    </div>
                </div>

                <!-- Photo 5 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuCHd1vp6m-HLQuhCM_yamy6KOjIvIF1N3shn6b5iYpHRUwap9--1oortT3QfjK9WksyweN4lHepSb4-IvIlqv7l04qRBHXuVRXbUZ4wKEA514KGA2Bw-_mYf-2JRxuMFqRayMpjdF9IRkwW6MqVgmO7dbhBNaGwcynOV7IdmcEs0FQ2o43ApnxXeqxKuBZRiZVblaeEvbNf6QVDGeXNSCum8EkC9pywQwFFc3275R4', 'Pelatihan Fisik & Mental Disiplin')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHd1vp6m-HLQuhCM_yamy6KOjIvIF1N3shn6b5iYpHRUwap9--1oortT3QfjK9WksyweN4lHepSb4-IvIlqv7l04qRBHXuVRXbUZ4wKEA514KGA2Bw-_mYf-2JRxuMFqRayMpjdF9IRkwW6MqVgmO7dbhBNaGwcynOV7IdmcEs0FQ2o43ApnxXeqxKuBZRiZVblaeEvbNf6QVDGeXNSCum8EkC9pywQwFFc3275R4" 
                         alt="Latihan Fisik & Disiplin" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Latihan Fisik &amp; Disiplin FMD</span>
                    </div>
                </div>

                <!-- Photo 6 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuDx-OA6wleAXIIZrRZJYyN_GdTYrXbCSdkN1eumQQqqAUZmY7ZdbRNyaQxdpq-d0jZBD7EucX-xaFSpqmn9f5NOfcPEWnkvMFITBnFrACAew32SFdbycghUwIG4nCtx8deraVBNFaamp6zVVNdcRqcVmCzw4Pe-KPhPHKfIgZ9UPcuyrK3SsX7eyn8idWZV00BwQUGBLOlpYIuii_9G5CkZiWSo8FXRkXXwynBFr9E', 'Simulasi Wawancara User Jepang')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDx-OA6wleAXIIZrRZJYyN_GdTYrXbCSdkN1eumQQqqAUZmY7ZdbRNyaQxdpq-d0jZBD7EucX-xaFSpqmn9f5NOfcPEWnkvMFITBnFrACAew32SFdbycghUwIG4nCtx8deraVBNFaamp6zVVNdcRqcVmCzw4Pe-KPhPHKfIgZ9UPcuyrK3SsX7eyn8idWZV00BwQUGBLOlpYIuii_9G5CkZiWSo8FXRkXXwynBFr9E" 
                         alt="Simulasi Wawancara Kaisha" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Wawancara User Kaisha</span>
                    </div>
                </div>

                <!-- Photo 7 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuBoHMtMsm2pE2kXep4SIoVRMjYgtVpJc7iWhnT71xvToxsvnTj7jB0LNswdORc1fbl09vHCF_JLvV2RYtaLk3-UCICUXRsbg-2UbciojmsDn89qtDge86Ds979BoibsKEeuzGyncotQe5K3Fu_FfXIDB8zMK0PZUrWCoer7EvLhA6t7R1C46SH-ukWTWxThVEykkSYUYFjkrBfN2LG_dS-WD1s8iVFEhLcuaEzsF_o', 'Supervisi Industri Shizuoka Jepang')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBoHMtMsm2pE2kXep4SIoVRMjYgtVpJc7iWhnT71xvToxsvnTj7jB0LNswdORc1fbl09vHCF_JLvV2RYtaLk3-UCICUXRsbg-2UbciojmsDn89qtDge86Ds979BoibsKEeuzGyncotQe5K3Fu_FfXIDB8zMK0PZUrWCoer7EvLhA6t7R1C46SH-ukWTWxThVEykkSYUYFjkrBfN2LG_dS-WD1s8iVFEhLcuaEzsF_o" 
                         alt="Supervisi Pabrik Jepang" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Supervisi Pabrik Shizuoka</span>
                    </div>
                </div>

                <!-- Photo 8 -->
                <div class="group relative aspect-square rounded-xl overflow-hidden bg-surface-container shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer border border-surface-container-high/60"
                     onclick="openImageModal('https://lh3.googleusercontent.com/aida-public/AB6AXuBtN9U5FWdeqV6a6Hd6miwhDu5ykimTnheL51GK8hZkJhbq0lxNmhhiqmETduBM9IsIyviqKckUww7nDzIuug-39R1VXV4FxBNqFY9X5OJ50jIFi5-LrKtVLivJvuhAIp-6nX2e3Kh5gn1gcaT_XKD1F63iISptgi5drbt1mmXKrA0blPC1FBGaThCqSJhp6wtNJ0FjizmQe2SDzCfWrswoKTFE7vcS-h2MF2bHayS28iQuVUQbAh8w', 'Gedung Kampus Terpadu LPK Gyokai')">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtN9U5FWdeqV6a6Hd6miwhDu5ykimTnheL51GK8hZkJhbq0lxNmhhiqmETduBM9IsIyviqKckUww7nDzIuug-39R1VXV4FxBNqFY9X5OJ50jIFi5-LrKtVLivJvuhAIp-6nX2e3Kh5gn1gcaT_XKD1F63iISptgi5drbt1mmXKrA0blPC1FBGaThCqSJhp6wtNJ0FjizmQe2SDzCfWrswoKTFE7vcS-h2MF2bHayS28iQuVUQbAh8w" 
                         alt="Gedung Kampus LPK Gyokai" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <span class="text-white text-xs font-bold">Kampus Diklat Terpadu</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Visit Campus Callout -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-12">
        <div class="relative bg-surface-container-low rounded-2xl p-space-lg md:p-space-xl overflow-hidden shadow-sm border border-surface-container-high/60 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed text-primary font-label-sm text-label-sm font-bold uppercase mb-2">
                    <span class="material-symbols-outlined text-[16px]">map</span>
                    Tur Fasilitas &amp; Konsultasi Langsung
                </span>
                <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight mb-2">
                    Ingin Melihat Fasilitas Kami Secara Langsung?
                </h3>
                <p class="text-body-md text-secondary">
                    Kami membuka jadwal kunjungan kampus dan tur workshop bagi calon peserta, orang tua murid, dan perwakilan perusahaan mitra penerima.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl bg-primary text-on-primary font-label-md font-bold hover:bg-tertiary transition-all shadow-md">
                    Jadwalkan Kunjungan Kampus
                </a>
            </div>
        </div>
    </section>
</div>

<!-- Image Lightbox Modal -->
<div id="facilityImageModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="relative max-w-4xl w-full bg-surface-container-lowest rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-surface-container">
            <h4 id="lightboxTitle" class="font-headline-sm text-on-surface font-bold text-base truncate pr-4">Preview Gambar</h4>
            <button onclick="closeImageModal()" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="max-h-[75vh] w-full bg-black/95 flex items-center justify-center overflow-hidden p-2">
            <img id="lightboxImg" src="" alt="Preview" class="max-h-[70vh] max-w-full object-contain rounded-lg"/>
        </div>
    </div>
</div>

<script>
    function openImageModal(src, title) {
        if (!src) return;
        const modal = document.getElementById('facilityImageModal');
        const img = document.getElementById('lightboxImg');
        const titleEl = document.getElementById('lightboxTitle');
        img.src = src;
        titleEl.innerText = title || 'Dokumentasi Fasilitas';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeImageModal() {
        const modal = document.getElementById('facilityImageModal');
        const img = document.getElementById('lightboxImg');
        img.src = '';
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.getElementById('facilityImageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    function switchSection(target, btn) {
        const secFasilitas = document.getElementById('section-fasilitas');
        const secGaleri = document.getElementById('section-galeri');
        const buttons = document.querySelectorAll('.tab-btn');

        buttons.forEach(b => {
            b.className = "tab-btn px-5 py-2.5 rounded-lg font-label-md text-label-md bg-surface-container-low text-secondary hover:bg-surface-container hover:text-on-surface font-semibold transition-all flex items-center gap-2";
        });
        btn.className = "tab-btn px-5 py-2.5 rounded-lg font-label-md text-label-md bg-primary text-on-primary font-bold shadow-sm transition-all flex items-center gap-2";

        if (target === 'fasilitas') {
            secFasilitas.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            secGaleri.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
</script>
@endsection
