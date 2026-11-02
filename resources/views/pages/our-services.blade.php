@php
    $seoPage = 'our-services';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Sub-Header / Breadcrumb Banner -->
    <section class="w-full bg-surface-container-high py-space-lg border-b border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col md:flex-row md:items-center md:justify-between gap-space-xs">
            <div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Layanan Kami</h1>
                <div class="flex items-center gap-space-xs text-secondary mt-space-xs">
                    <a href="{{ route('index') }}" class="font-body-sm text-body-sm hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">home</span> Beranda
                    </a>
                    <span class="text-secondary font-body-sm text-body-sm">/</span>
                    <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider font-bold">Layanan</span>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-space-sm bg-surface-container-lowest px-space-md py-space-xs rounded-lg shadow-sm border border-surface-container">
                <span class="material-symbols-outlined text-primary text-[20px]">verified</span>
                <span class="font-label-sm text-label-sm text-on-surface">Standar Pelatihan Resmi Kemnaker RI &amp; Industri Jepang</span>
            </div>
        </div>
    </section>

    <!-- Section Lead Intro -->
    <section class="w-full py-space-xl bg-surface-container-lowest">
        <div class="max-w-4xl mx-auto px-gutter text-center">
            <span class="inline-flex items-center gap-space-xs px-space-sm py-space-xs rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm uppercase tracking-wider mb-space-sm font-bold">
                <span class="material-symbols-outlined text-[14px]">tune</span>
                Pilar Program Unggulan
            </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-snug font-bold">
                Solusi Lengkap untuk <span class="text-primary">Pengembangan SDM</span>
            </h2>
            <p class="mt-space-sm font-body-lg text-body-lg text-secondary leading-relaxed">
                Kami menyediakan berbagai layanan profesional terpadu untuk mendukung kebutuhan tenaga kerja siap pakai, kepatuhan legal ketenagakerjaan, dan pengembangan kompetensi kejuruan berstandar industri modern Indonesia dan Jepang.
            </p>
        </div>
    </section>

    <!-- Detailed Services (Alternating Bento-style Grid) -->
    <section class="w-full pb-space-xl bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col gap-space-xl">
            <!-- Service 1: Program Pemagangan -->
            <article class="bg-surface-container-low rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow grid grid-cols-1 lg:grid-cols-12 items-stretch border border-surface-container">
                <div class="lg:col-span-5 relative min-h-[260px] lg:min-h-full">
                    <img class="absolute inset-0 w-full h-full object-cover" 
                         src="{{ \App\Models\SiteAsset::url('service_program_01_pemagangan', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCL3k_h4fy111ZYeqIid5Ia0PqVJG4tLyI_jhVgPPhs8SPywBdkfkA8rWyxcx8KuRl27syN462hTHGwg3t8QrZhGzM1manEsb_7yi0oyQQQlyvfwLdA3IYcmICJobl6BZitCgbuSv5myY-YPhawiHhltKVaGsT18Tgh0xqULBJGPcWr6iqZrUum0YwNFcshtoyW-nwBCiMT-r_mSdKp02mKKdoajRXxWBVlOjjZ7SE') }}" 
                         alt="Program Pemagangan Industri LPK Gyokai"
                         loading="lazy"/>
                    <div class="absolute top-space-md left-space-md bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-wider px-space-sm py-space-xs rounded-lg shadow-sm font-bold">
                        Program 01
                    </div>
                </div>
                <div class="lg:col-span-7 p-space-lg lg:p-space-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-space-xs text-secondary font-label-sm text-label-sm mb-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">precision_manufacturing</span>
                            <span class="font-bold">STANDAR OPERASIONAL KAISHA</span>
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-sm font-bold">
                            Program Pemagangan (Paid Internship)
                        </h3>
                        <p class="font-body-md text-body-md text-secondary leading-relaxed mb-space-md">
                            Implementasi PERMENAKERTRANS No. 6 Tahun 2020. Program pemagangan industri terstruktur untuk membantu perusahaan mendapatkan tenaga kerja terampil yang siap kerja dengan efisiensi biaya rekrutmen hingga 75-80%.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm mb-space-lg">
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Sesuai Permenaker No. 6 Tahun 2020</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Kurikulum Etika Kerja Jepang & 5S</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Efisiensi Biaya (Saving Cost) SDM</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Karakter & Disiplin Fisik Tinggi</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('benefit-pemagangan') }}" class="inline-flex items-center gap-space-xs px-space-md py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group">
                            <span>Lihat Benefit Pemagangan</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </article>

            <!-- Service 2: Labor Supply -->
            <article class="bg-surface-container-low rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow grid grid-cols-1 lg:grid-cols-12 items-stretch border border-surface-container">
                <div class="lg:col-span-7 p-space-lg lg:p-space-xl flex flex-col justify-between order-2 lg:order-1">
                    <div>
                        <div class="flex items-center gap-space-xs text-secondary font-label-sm text-label-sm mb-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">group_add</span>
                            <span class="font-bold">PENYALURAN TENAGA KERJA INDUSTRI</span>
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-sm font-bold">
                            Labor Supply
                        </h3>
                        <p class="font-body-md text-body-md text-secondary leading-relaxed mb-space-md">
                            Penyediaan dan penyaluran tenaga kerja lepas terlatih, disiplin, berintegritas, dan telah dibekali hard & soft skill yang disesuaikan secara cepat dengan kebutuhan operasional industri manufaktur, pergudangan, dan logistik.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm mb-space-lg">
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Skrining Ketat Medis &amp; Psikotes</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Kepatuhan Regulasi Ketenagakerjaan</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Penyaluran Tenaga Cepat Sesuai Demand</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Garansi Penggantian Personel</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('benefit-labor-supply') }}" class="inline-flex items-center gap-space-xs px-space-md py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group">
                            <span>Lihat Benefit Labor Supply</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 relative min-h-[260px] lg:min-h-full order-1 lg:order-2">
                    <img class="absolute inset-0 w-full h-full object-cover" 
                         src="{{ \App\Models\SiteAsset::url('service_program_02_labor_supply', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAk5oD3txynGBBO-DcG64KZcaPh-Yj5qAGDUAd0y6BjgSYh8rPhODHV_dOIhQkBGdjjailWZzf6YU_q-cUMl7onxjWsS2TOSYm0rUbzaRjJOtl92tWaTiD4YyuCvUyR0ksVY1mo6hlvWTDQISOsFKQ73edpGl6lbEGASYtdvxr12q7i5zKaKNQeRvrauCMCgxEs3rFBoKskHsIYmzZCHIYhPn-MM7zSoFcIL01b258') }}" 
                         alt="Labor Supply Tenaga Kerja Industri"
                         loading="lazy"/>
                    <div class="absolute top-space-md right-space-md bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-wider px-space-sm py-space-xs rounded-lg shadow-sm font-bold">
                        Program 02
                    </div>
                </div>
            </article>

            <!-- Service 3: Training Program Development -->
            <article class="bg-surface-container-low rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow grid grid-cols-1 lg:grid-cols-12 items-stretch border border-surface-container">
                <div class="lg:col-span-5 relative min-h-[260px] lg:min-h-full">
                    <img class="absolute inset-0 w-full h-full object-cover" 
                         src="{{ \App\Models\SiteAsset::url('service_program_03_training', 'https://lh3.googleusercontent.com/aida-public/AB6AXuB51jX4bMCWQadg-3QBweUeXNa7JH4EyWpnTb8X3AV9ujSRuLbXT34UJ1jRIoASffSzo0g8lYQTo5fP81ML8FYAJpxhsjvalfSRUfbM__5zkHfZzjOvdHlAArp_xYMYd6qd4-c06VB_MLDb0YUkj096uPqwoNiiyooQF1OXR6heo3tpQgxVLo-rSdiZCjYyGkoXyTuJoqUh_yzlXlGWHyXouZl5jkQ2q9N35GRG7pk') }}" 
                         alt="Training Program Development Kurikulum Khusus"
                         loading="lazy"/>
                    <div class="absolute top-space-md left-space-md bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-wider px-space-sm py-space-xs rounded-lg shadow-sm font-bold">
                        Program 03
                    </div>
                </div>
                <div class="lg:col-span-7 p-space-lg lg:p-space-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-space-xs text-secondary font-label-sm text-label-sm mb-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">developer_board</span>
                            <span class="font-bold">KURIKULUM KUSTOM &amp; IN-HOUSE</span>
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-sm font-bold">
                            Training Development
                        </h3>
                        <p class="font-body-md text-body-md text-secondary leading-relaxed mb-space-md">
                            Desain kurikulum dan pelatihan kejuruan yang dirancang secara khusus untuk menghasilkan SDM dengan kompetensi presisi sesuai Standard Operating Procedure (SOP) dan ekspektasi mutu mitra industri.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm mb-space-lg">
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Analisis Kebutuhan Pelatihan (TNA)</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Modul Standar Mutu ISO &amp; Kaizen</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">In-House Training di Lokasi Mitra</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Evaluasi Berkala Pre/Post Test</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('benefit-training') }}" class="inline-flex items-center gap-space-xs px-space-md py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group">
                            <span>Lihat Benefit Training</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </article>

            <!-- Service 4: Outsourcing -->
            <article class="bg-surface-container-low rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow grid grid-cols-1 lg:grid-cols-12 items-stretch border border-surface-container">
                <div class="lg:col-span-7 p-space-lg lg:p-space-xl flex flex-col justify-between order-2 lg:order-1">
                    <div>
                        <div class="flex items-center gap-space-xs text-secondary font-label-sm text-label-sm mb-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">badge</span>
                            <span class="font-bold">EFISIENSI OPERASIONAL KORPORAT</span>
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-sm font-bold">
                            Outsourcing Tenaga Kerja
                        </h3>
                        <p class="font-body-md text-body-md text-secondary leading-relaxed mb-space-md">
                            Solusi pengelolaan tenaga kerja alih daya profesional yang fleksibel, akuntabel, sepenuhnya mematuhi regulasi UU Ketenagakerjaan (cleaning service, security service, produksi), bebas risiko administratif.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm mb-space-lg">
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Manajemen Payroll &amp; BPJS Terpadu</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Supervisi &amp; Evaluasi Kinerja Rutin</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Pengurangan Risiko Hubungan Industrial</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">check_circle</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-medium">Dukungan Pergantian Personel Cepat</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('benefit-outsourcing') }}" class="inline-flex items-center gap-space-xs px-space-md py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group">
                            <span>Lihat Benefit Outsourcing</span>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 relative min-h-[260px] lg:min-h-full order-1 lg:order-2">
                    <img class="absolute inset-0 w-full h-full object-cover" 
                         src="{{ \App\Models\SiteAsset::url('service_program_04_outsourcing', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBFk9-mBrkvrCzCprIA8BrD_-W-jDhJV98cE4tsMTkpCfpPXvuFX47S_D_ywl7hQcE-t4qyUCLzKKmlD7Aq4N0-YUXuI-Qh4ji0HG8x83NuTMhJ6nIQIuKw5uVpy_o9IcKMeBtiLpLMAFX3d_6OKQ0GVfGtOgosRbj2yYg-LrXq6p-H1bk8qcMVIfO07P1SaX0rLwg5iPrZcwGLoJzQ74PPC_9oMTl3Uk3e6fsqUD4') }}" 
                         alt="Layanan Outsourcing Profesional"
                         loading="lazy"/>
                    <div class="absolute top-space-md right-space-md bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-wider px-space-sm py-space-xs rounded-lg shadow-sm font-bold">
                        Program 04
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Legal Support / Pusat Bantuan Hukum Gyokai Banner -->
    <section class="w-full pb-space-xl bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="bg-surface-container rounded-xl p-space-lg lg:p-space-xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-space-lg border border-surface-container-high/60">
                <div class="flex items-start gap-space-md">
                    <div class="w-14 h-14 rounded-xl bg-primary text-on-primary flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[32px]">gavel</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-xs">
                            <h3 class="font-headline-md text-headline-md text-on-surface tracking-tight font-bold">Pusat Bantuan Hukum Gyokai</h3>
                            <span class="px-space-xs py-0.5 rounded bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm font-bold">Advokasi Resmi</span>
                        </div>
                        <p class="mt-space-xs font-body-md text-body-md text-secondary max-w-2xl leading-relaxed">
                            Memberikan pendampingan hukum, kepastian kontrak kerja, mediasi industrial, dan konsultasi regulasi ketenagakerjaan bagi semua mitra LPK Gyokai Indonesia Kompeten dan peserta magang.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 w-full md:w-auto">
                    <a href="{{ route('contact') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-space-xs px-space-lg py-space-sm rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary shadow-sm transition-all group">
                        <span>Konsultasi Regulasi</span>
                        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section class="w-full bg-primary-container text-on-primary-container py-space-xl">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col md:flex-row items-center justify-between gap-space-lg">
            <div class="flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-full bg-on-primary-container/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-on-primary-container text-[28px]">support_agent</span>
                </div>
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-primary-container tracking-tight font-bold">Butuh Solusi Tenaga Kerja?</h3>
                    <p class="font-body-md text-body-md text-on-primary-container/90 mt-0.5">Konsultasikan kebutuhan spesifik perusahaan Anda bersama konsultan ahli kami sekarang.</p>
                </div>
            </div>
            <div class="shrink-0 w-full md:w-auto">
                <a href="{{ route('contact') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-space-xs px-space-lg py-space-sm rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md hover:bg-surface-container-high transition-all shadow-sm font-bold">
                    <span>Konsultasi Sekarang</span>
                    <span class="material-symbols-outlined text-primary text-[18px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection