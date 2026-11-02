@php
    $seoPage = 'about';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Top Breadcrumb & Page Banner -->
    <section class="w-full bg-surface-container-low py-space-lg border-b border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col md:flex-row md:items-center md:justify-between gap-space-sm">
            <div class="flex flex-col">
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Tentang Kami</h1>
                <p class="font-body-sm text-body-sm text-secondary">Mengenal dedikasi, rekam jejak, dan standar mutu pelatihan kerja LPK GYOKAI.</p>
            </div>
            <nav aria-label="Breadcrumb" class="flex items-center gap-space-xs text-body-sm">
                <a href="{{ route('index') }}" class="text-secondary hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    Beranda
                </a>
                <span class="text-secondary">/</span>
                <span class="text-primary font-label-md">Tentang Kami</span>
            </nav>
        </div>
    </section>

    <!-- Profil LPK GYOKAI Section (2 Columns) -->
    <section class="w-full py-space-xl bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
            <!-- Left Column: Copywriting -->
            <div class="lg:col-span-6 flex flex-col items-start gap-space-md">
                <div class="inline-flex items-center gap-space-xs px-space-sm py-space-xs rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Profil LPK GYOKAI
                </div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface leading-tight">
                    Mencetak Tenaga Kerja Berkualitas Global Sejak <span class="text-primary font-bold">2017</span>
                </h2>
                <p class="font-body-lg text-body-lg text-secondary leading-relaxed">
                    LPK GYOKAI Indonesia Kompeten berdiri sejak 2017 dengan komitmen kokoh untuk mencetak tenaga kerja terampil, profesional, berintegritas, dan berdaya saing global. Kami hadir sebagai mitra strategis industri manufaktur, otomotif, pengelasan, dan permesinan presisi baik di dalam negeri maupun ke Jepang.
                </p>
                <p class="font-body-md text-body-md text-secondary leading-relaxed">
                    Melalui kurikulum disiplin Kaizen (改善) berstandar industri internasional, penguasaan bahasa Jepang intensif (N4/N3), dan legalitas perizinan resmi Kemenaker RI, setiap lulusan dipersiapkan secara komprehensif dari keterampilan teknis hingga kesiapan mental kerja.
                </p>
                <div class="grid grid-cols-2 gap-space-md w-full pt-space-xs">
                    <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container">
                        <span class="font-headline-md text-headline-md text-primary block font-bold">100%</span>
                        <span class="font-body-sm text-body-sm text-secondary">Kepatuhan Regulasi &amp; Legalitas Resmi</span>
                    </div>
                    <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container">
                        <span class="font-headline-md text-headline-md text-primary block font-bold">ISO 9001</span>
                        <span class="font-body-sm text-body-sm text-secondary">Sistem Manajemen Mutu Pelatihan</span>
                    </div>
                </div>
                <a href="{{ route('legalitas') }}" 
                   class="mt-space-sm inline-flex items-center gap-space-xs px-space-lg py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-md transition-all">
                    <span>Lihat Legalitas Lengkap</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <!-- Right Column: Visual Showcase -->
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-2xl overflow-hidden shadow-xl bg-surface-container-high aspect-[4/3] group">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                         src="{{ \App\Models\SiteAsset::url('about_hero_banner', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAjFmEu6JRxY1lfdWwoUbqTT0bOAbORVdHPRdtlTv9dub0e1VcGm5v_Ee09M1TKVcz7JXeYn2lSyFUZK0nFa3lLn1n1sH1vrPK2k2s2-CvKjnFanF6UufPJ7cJRik4l8biMhUoaqrvtYffBWDSKOQbYHlPOB7VdabG1GgzwY3zMZxdMxRKpQDgEhiTa4eRhdcf0SgQvP2_yUbaefjMiwQmB6BzA0a-vmhT3m_1aZKA') }}" 
                         alt="Gedung Workshop dan Pusat Pelatihan LPK GYOKAI"
                         loading="lazy"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-on-surface/80 via-transparent to-transparent flex items-end p-space-lg">
                        <div class="flex items-center gap-space-sm bg-surface-container-lowest/95 backdrop-blur-sm p-space-sm rounded-xl shadow-lg">
                            <span class="material-symbols-outlined text-primary text-[28px]">verified_user</span>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface leading-tight font-bold">Gedung Pusat Pelatihan LPK GYOKAI</p>
                                <p class="font-body-sm text-body-sm text-secondary">Workshop Teknik &amp; Laboratorium Bahasa Jepang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Perjalanan Kami (Timeline Milestone 2017 - 2025) -->
    <section class="w-full py-space-xl bg-surface-container-low border-y border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col gap-space-lg">
            <div class="text-center max-w-2xl mx-auto flex flex-col items-center gap-space-xs">
                <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest font-bold">Milestone Rekam Jejak</span>
                <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Perjalanan Kami</h2>
                <p class="font-body-md text-body-md text-secondary">Pertumbuhan konsisten LPK GYOKAI dalam menjembatani generasi muda Indonesia dengan pasar industri modern.</p>
            </div>

            <!-- Timeline Grid -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-space-md relative pt-space-md">
                <!-- Step 1: 2017 -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-xs relative hover:-translate-y-1 transition-transform border border-surface-container-high/60">
                    <div class="flex items-center justify-between">
                        <span class="font-headline-md text-headline-md text-primary font-bold">2017</span>
                        <div class="w-3 h-3 rounded-full bg-primary ring-4 ring-primary-fixed"></div>
                    </div>
                    <h3 class="font-label-md text-label-md text-on-surface font-bold">Pendirian &amp; Izin Resmi</h3>
                    <p class="font-body-sm text-body-sm text-secondary leading-relaxed">Didirikan oleh Bpk. Teguh Imam Pambudi, SE & Ibu Anita Sandra Dewi, SH dengan izin resmi Disnaker.</p>
                </div>

                <!-- Step 2: 2020 -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-xs relative hover:-translate-y-1 transition-transform border border-surface-container-high/60">
                    <div class="flex items-center justify-between">
                        <span class="font-headline-md text-headline-md text-primary font-bold">2020</span>
                        <div class="w-3 h-3 rounded-full bg-primary ring-4 ring-primary-fixed"></div>
                    </div>
                    <h3 class="font-label-md text-label-md text-on-surface font-bold">Penghargaan Disnakertrans</h3>
                    <p class="font-body-sm text-body-sm text-secondary leading-relaxed">Meraih piagam penghargaan resmi atas peran aktif menurunkan angka pengangguran di Banten.</p>
                </div>

                <!-- Step 3: 2021 -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-xs relative hover:-translate-y-1 transition-transform border border-surface-container-high/60">
                    <div class="flex items-center justify-between">
                        <span class="font-headline-md text-headline-md text-primary font-bold">2021</span>
                        <div class="w-3 h-3 rounded-full bg-primary ring-4 ring-primary-fixed"></div>
                    </div>
                    <h3 class="font-label-md text-label-md text-on-surface font-bold">Akreditasi & Digitalisasi</h3>
                    <p class="font-body-sm text-body-sm text-secondary leading-relaxed">Akreditasi LA-LPK Kemnaker, lisensi TUK BNSP, dan implementasi sistem pendaftaran online terpadu.</p>
                </div>

                <!-- Step 4: 2022 -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-xs relative hover:-translate-y-1 transition-transform border border-surface-container-high/60">
                    <div class="flex items-center justify-between">
                        <span class="font-headline-md text-headline-md text-primary font-bold">2022</span>
                        <div class="w-3 h-3 rounded-full bg-primary ring-4 ring-primary-fixed"></div>
                    </div>
                    <h3 class="font-label-md text-label-md text-on-surface font-bold">ISO 9001:2015</h3>
                    <p class="font-body-sm text-body-sm text-secondary leading-relaxed">Standarisasi mutu manajemen pelatihan ISO 9001:2015 dan seluruh master trainer tersertifikasi.</p>
                </div>

                <!-- Step 5: 2025 -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-xs relative hover:-translate-y-1 transition-transform border border-surface-container-high/60">
                    <div class="flex items-center justify-between">
                        <span class="font-headline-md text-headline-md text-primary font-bold">2025</span>
                        <div class="w-3 h-3 rounded-full bg-primary ring-4 ring-primary-fixed"></div>
                    </div>
                    <h3 class="font-label-md text-label-md text-on-surface font-bold">Ekspansi Global</h3>
                    <p class="font-body-sm text-body-sm text-secondary leading-relaxed">Kerjasama 50+ konsorsium industri otomotif/manufaktur nasional & program magang/SSW ke Jepang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nilai-Nilai Kami (Core Values 4 Column Grid) -->
    <section class="w-full py-space-xl bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col gap-space-lg">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest font-bold">Fondasi Keberhasilan</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Nilai-Nilai Kami</h2>
                </div>
                <p class="font-body-md text-body-md text-secondary max-w-md">Prinsip dasar yang mengarahkan setiap proses pengajaran, kedisiplinan fisik, etos kerja, dan relasi mitra bisnis.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
                <!-- Value 1 -->
                <div class="p-space-lg bg-surface-container-low rounded-xl flex flex-col gap-space-sm hover:shadow-md transition-shadow border border-surface-container">
                    <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-xs">
                        <span class="material-symbols-outlined text-[28px]">school</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Pendidikan</h3>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">Meningkatkan kualitas SDM secara berkesinambungan melalui metode pelatihan berbasis kompetensi kerja riil.</p>
                </div>
                <!-- Value 2 -->
                <div class="p-space-lg bg-surface-container-low rounded-xl flex flex-col gap-space-sm hover:shadow-md transition-shadow border border-surface-container">
                    <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-xs">
                        <span class="material-symbols-outlined text-[28px]">verified</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Integritas</h3>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">Menjunjung tinggi kejujuran, disiplin waktu, etika profesional, dan transparansi standar industri.</p>
                </div>
                <!-- Value 3 -->
                <div class="p-space-lg bg-surface-container-low rounded-xl flex flex-col gap-space-sm hover:shadow-md transition-shadow border border-surface-container">
                    <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-xs">
                        <span class="material-symbols-outlined text-[28px]">handshake</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Komitmen</h3>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">Memberikan dedikasi dan pendampingan terbaik bagi peserta didik hingga sukses penempatan di perusahaan.</p>
                </div>
                <!-- Value 4 -->
                <div class="p-space-lg bg-surface-container-low rounded-xl flex flex-col gap-space-sm hover:shadow-md transition-shadow border border-surface-container">
                    <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary mb-space-xs">
                        <span class="material-symbols-outlined text-[28px]">workspace_premium</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Kualitas</h3>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">Konsisten menjalankan kaizen demi kesempurnaan materi ajar, instruktur tersertifikasi, dan kepuasan industri.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Legalitas & Sertifikasi Preview Section -->
    <section class="w-full py-space-xl bg-surface-container-low border-y border-surface-container" id="legalitas">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col gap-space-lg">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest font-bold">Kepatuhan Regulasi Penuh</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Legalitas &amp; Sertifikasi</h2>
                </div>
                <a href="{{ route('legalitas') }}" class="inline-flex items-center gap-space-xs text-primary font-label-md font-bold hover:underline">
                    <span>Lihat Semua Dokumen Legal</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <!-- Dynamic Badges/Document Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="documentsGrid">
                @forelse($legalitas ?? [] as $item)
                    <div class="doc-card flex flex-col bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all overflow-hidden group border border-surface-container">
                        <div class="relative bg-gradient-to-b from-surface-container to-surface-container-low p-5 flex flex-col items-center justify-center min-h-[190px] overflow-hidden">
                            @if($item->document)
                                <img src="{{ Storage::url($item->document) }}" 
                                     alt="Dokumen {{ $item->name }}" 
                                     class="max-h-[150px] w-auto object-contain rounded shadow-sm group-hover:scale-105 transition-transform"
                                     loading="lazy"/>
                            @else
                                <div class="w-full bg-surface-container-lowest rounded-lg p-4 shadow-sm flex flex-col items-center text-center relative">
                                    <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary mb-2">
                                        <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                                    </div>
                                    <span class="font-headline-sm text-[13px] text-on-surface uppercase tracking-tight font-bold">LPK GYOKAI</span>
                                    <span class="font-label-sm text-[10px] text-secondary">DOKUMEN RESMI</span>
                                    <div class="mt-2 w-16 h-1 rounded bg-primary"></div>
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded bg-primary text-on-primary font-label-sm text-[10px] uppercase tracking-wider shadow-sm font-bold">
                                RESMI
                            </span>
                        </div>
                        <div class="p-5 flex flex-col flex-1 justify-between">
                            <div>
                                <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-wider">LEGALITAS TERVERIFIKASI</span>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface mt-1 group-hover:text-primary transition-colors line-clamp-2 font-bold">
                                    {{ $item->name }}
                                </h3>
                                <p class="font-body-sm text-body-sm text-secondary mt-2 line-clamp-2">
                                    Dokumen sertifikat resmi legalitas dan kepatuhan hukum LPK Gyokai Indonesia Kompeten.
                                </p>
                            </div>
                            <div class="mt-5">
                                @if($item->document)
                                    <button type="button" 
                                            onclick="openDocModal('{{ addslashes($item->name) }}', 'LPK GYOKAI INDONESIA', 'Dokumen Terverifikasi', 'Terdaftar Aktif', 'Berlaku', '{{ Storage::url($item->document) }}')" 
                                            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-surface-container text-on-surface hover:bg-primary hover:text-on-primary font-label-md text-label-md transition-all font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        Lihat Sertifikat
                                    </button>
                                @else
                                    <button type="button" 
                                            onclick="openDocModal('{{ addslashes($item->name) }}', 'LPK GYOKAI INDONESIA', 'SK Legalitas', 'Aktif', 'Berlaku', '')" 
                                            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-surface-container text-on-surface hover:bg-primary hover:text-on-primary font-label-md text-label-md transition-all font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        Lihat Detail
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-surface-container-lowest rounded-2xl border border-dashed border-surface-container">
                        <span class="material-symbols-outlined text-secondary text-5xl mb-2">policy</span>
                        <p class="text-sm font-semibold text-on-surface">Belum ada dokumen legalitas yang dipublikasikan.</p>
                        <p class="text-xs text-secondary mt-1">Dokumen legalitas dapat dikelola melalui Panel Admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Red Metric Counter Ribbon -->
    <section class="w-full bg-primary py-space-lg text-on-primary shadow-md">
        <div class="max-w-7xl mx-auto px-gutter grid grid-cols-1 md:grid-cols-3 gap-space-md items-center text-center">
            <!-- Stat 1 -->
            <div class="flex items-center justify-center gap-space-md py-space-sm md:border-r border-on-primary/20">
                <span class="material-symbols-outlined text-[44px]">calendar_today</span>
                <div class="text-left">
                    <span class="font-headline-xl text-headline-xl block leading-none font-extrabold">2017</span>
                    <span class="font-label-md text-label-md opacity-90 uppercase tracking-wider">Tahun Berdiri</span>
                </div>
            </div>
            <!-- Stat 2 -->
            <div class="flex items-center justify-center gap-space-md py-space-sm md:border-r border-on-primary/20">
                <span class="material-symbols-outlined text-[44px]">group</span>
                <div class="text-left">
                    <span class="font-headline-xl text-headline-xl block leading-none font-extrabold">5000+</span>
                    <span class="font-label-md text-label-md opacity-90 uppercase tracking-wider">Alumni &amp; Peserta</span>
                </div>
            </div>
            <!-- Stat 3 -->
            <div class="flex items-center justify-center gap-space-md py-space-sm">
                <span class="material-symbols-outlined text-[44px]">domain</span>
                <div class="text-left">
                    <span class="font-headline-xl text-headline-xl block leading-none font-extrabold">50+</span>
                    <span class="font-label-md text-label-md opacity-90 uppercase tracking-wider">Mitra Industri</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA & Kontak Direct -->
    <section class="w-full py-16 bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="p-8 md:p-12 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold">Siap Bekerjasama dengan LPK GYOKAI?</h3>
                    <p class="font-body-lg text-body-lg text-secondary mt-1">Konsultasikan kebutuhan pelatihan karyawan atau pemagangan industri dengan tim ahli kami.</p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-lg bg-primary hover:bg-tertiary text-on-primary font-headline-sm transition-all shadow-md">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        <span>Hubungi Kami</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Interactive Document Preview Modal -->
<div id="documentModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto border border-surface-container">
        <button onclick="closeDocModal()" class="absolute top-4 right-4 text-secondary hover:text-on-surface p-1 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors" type="button">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">verified</span>
            </div>
            <div>
                <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-wider" id="modalIssuer">LPK GYOKAI INDONESIA</span>
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold" id="modalTitle">Judul Dokumen</h3>
            </div>
        </div>
        <div id="modalImgContainer" class="hidden mb-4 rounded-xl overflow-hidden max-h-[300px] border border-surface-container">
            <img id="modalImg" src="" alt="Dokumen" class="w-full h-full object-contain bg-surface-container-low" />
        </div>
        <div class="space-y-2 bg-surface-container-low rounded-xl p-4 my-4 font-body-sm text-body-sm border border-surface-container">
            <div class="flex justify-between items-center py-1 border-b border-surface-container/60">
                <span class="text-secondary">Nomor Registrasi / SK:</span>
                <span class="font-medium text-on-surface text-right" id="modalReg">-</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-surface-container/60">
                <span class="text-secondary">Status Predikat:</span>
                <span class="font-medium text-primary text-right" id="modalStatus">-</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-secondary">Masa Berlaku:</span>
                <span class="font-medium text-on-surface text-right" id="modalPeriod">-</span>
            </div>
        </div>
        <p class="font-body-md text-body-md text-secondary mb-6 leading-relaxed" id="modalDesc">
            Deskripsi keterangan dokumen resmi.
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('contact') }}" class="flex-1 py-3 px-4 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary transition-all text-center flex items-center justify-center gap-2 font-bold">
                <span class="material-symbols-outlined text-[18px]">contact_support</span>
                Konsultasi Legalitas
            </a>
            <button class="py-3 px-4 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-all" onclick="closeDocModal()" type="button">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDocModal(title, issuer, reg, status, period, imgSrc, desc) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalIssuer').innerText = issuer || 'LPK GYOKAI INDONESIA';
        document.getElementById('modalReg').innerText = reg || '-';
        document.getElementById('modalStatus').innerText = status || 'Aktif';
        document.getElementById('modalPeriod').innerText = period || '-';
        document.getElementById('modalDesc').innerText = desc || 'Dokumen resmi legalitas dan kepatuhan hukum LPK Gyokai Indonesia Kompeten.';

        const imgContainer = document.getElementById('modalImgContainer');
        const img = document.getElementById('modalImg');
        if (imgSrc) {
            img.src = imgSrc;
            imgContainer.classList.remove('hidden');
        } else {
            imgContainer.classList.add('hidden');
            img.src = '';
        }

        document.getElementById('documentModal').classList.remove('hidden');
    }

    function closeDocModal() {
        document.getElementById('documentModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDocModal();
        }
    });

    document.getElementById('documentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDocModal();
        }
    });
</script>
@endpush
@endsection
