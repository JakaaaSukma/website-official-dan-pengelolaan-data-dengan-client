@php
    $seoPage = 'team';
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
                <span class="text-primary font-label-sm font-bold">Team Kami</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">groups</span>
                        Profesional &amp; Instruktur Berpengalaman
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm">
                        Struktur Tim &amp; Manajemen
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Mengenal pimpinan, manajemen eksekutif, dan tenaga ahli LPK GYOKAI yang berkomitmen membangun kompetensi sumber daya manusia unggul berstandar industri internasional.
                    </p>
                </div>

                <!-- Quick Stats Pill -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">badge</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">
                            {{ $teams->count() > 0 ? $teams->count() . ' Anggota' : 'Tenaga Ahli' }}
                        </div>
                        <div class="font-body-sm text-body-sm text-secondary">Tersertifikasi Resmi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $directors = $teams->where('category', 'director');
        $executives = $teams->where('category', 'executive');
        $marketing = $teams->where('category', 'marketing');
        $legal = $teams->where('category', 'legal');
        $hasDynamicData = $teams->isNotEmpty();
    @endphp

    <!-- Team Content Section -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-12 flex flex-col gap-16">

        @if($hasDynamicData)
            <!-- DYNAMIC TEAM DATA FROM DATABASE -->
            
            @if($directors->isNotEmpty())
            <!-- DIREKTUR & CEO -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">CEO &amp; DIREKSI</h2>
                        <p class="text-body-sm text-secondary">Pimpinan strategis arah kebijakan dan kualitas kelembagaan</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg justify-center">
                    @foreach($directors as $member)
                        <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col items-center p-6 text-center">
                            <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-surface-container mb-4 flex items-center justify-center relative">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" 
                                         alt="{{ $member->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-surface-container-low text-secondary">
                                        <span class="material-symbols-outlined text-[64px] text-primary/40">person</span>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded bg-primary text-on-primary text-xs font-bold uppercase shadow">
                                    Direksi
                                </div>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                                {{ $member->name }}
                            </h3>
                            <p class="font-body-md text-body-md text-secondary font-medium">
                                {{ $member->position }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($executives->isNotEmpty())
            <!-- EXECUTIVE & MANAGEMENT -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">EXECUTIVE &amp; MANAGEMENT TEAM</h2>
                        <p class="text-body-sm text-secondary">Operasional pendidikan, pelatihan kejuruan, dan standardisasi kurikulum</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-space-md">
                    @foreach($executives as $member)
                        <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col items-center p-5 text-center">
                            <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 flex items-center justify-center">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" 
                                         alt="{{ $member->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-surface-container-low text-secondary">
                                        <span class="material-symbols-outlined text-[48px] text-primary/40">person</span>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors line-clamp-1">
                                {{ $member->name }}
                            </h4>
                            <p class="text-body-sm text-secondary line-clamp-1">
                                {{ $member->position }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($marketing->isNotEmpty())
            <!-- MARKETING & BUSINESS DEVELOPMENT -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">MARKETING &amp; BUSINESS DEVELOPMENT</h2>
                        <p class="text-body-sm text-secondary">Hubungan kemitraan industri manufaktur dan asosiasi penerima di Jepang</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-space-md">
                    @foreach($marketing as $member)
                        <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col items-center p-5 text-center">
                            <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 flex items-center justify-center">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" 
                                         alt="{{ $member->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-surface-container-low text-secondary">
                                        <span class="material-symbols-outlined text-[48px] text-primary/40">person</span>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors line-clamp-1">
                                {{ $member->name }}
                            </h4>
                            <p class="text-body-sm text-secondary line-clamp-1">
                                {{ $member->position }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($legal->isNotEmpty())
            <!-- LEGAL TEAM -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">LEGAL &amp; COMPLIANCE TEAM</h2>
                        <p class="text-body-sm text-secondary">Kepatuhan regulasi ketenagakerjaan, perizinan visa, dan kontrak kerja resmi</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-space-md">
                    @foreach($legal as $member)
                        <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col items-center p-5 text-center">
                            <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 flex items-center justify-center">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" 
                                         alt="{{ $member->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-surface-container-low text-secondary">
                                        <span class="material-symbols-outlined text-[48px] text-primary/40">person</span>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors line-clamp-1">
                                {{ $member->name }}
                            </h4>
                            <p class="text-body-sm text-secondary line-clamp-1">
                                {{ $member->position }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        @else
            <!-- REFINED MODERN PLACEHOLDERS (KARENA DATA TEAM DATABASE KOSONG) -->
            
            <!-- Section 1: Pimpinan & Direksi Utama -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">CEO &amp; DIREKSI UTAMA</h2>
                        <p class="text-body-sm text-secondary">Pimpinan strategis pengawas mutu dan legalitas LPK GYOKAI</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg max-w-4xl mx-auto w-full">
                    <!-- Director 1 -->
                    <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col p-6 text-center">
                        <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-surface-container mb-4 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuDhq28-J17p7hP8LdKk_rJ6y345aT6t7jN6_Yy9r6_x2J4qFz2Q5K_xZ_wY7o0" 
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop&q=80';"
                                 alt="Direktur Utama LPK Gyokai"/>
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-lg bg-primary text-on-primary text-xs font-bold uppercase shadow">
                                Direktur Utama
                            </div>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Ir. H. Hendra Sukmana, M.T.
                        </h3>
                        <p class="font-label-md text-label-md text-secondary font-medium">
                            Direktur Utama / Chief Executive Officer
                        </p>
                        <p class="text-body-sm text-secondary/80 mt-2 text-xs">
                            Pengalaman 15+ tahun dalam supervisi kemitraan industri manufaktur otomotif Indonesia - Jepang.
                        </p>
                    </div>

                    <!-- Director 2 -->
                    <div class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col p-6 text-center">
                        <div class="w-full aspect-[4/5] rounded-xl overflow-hidden bg-surface-container mb-4 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&auto=format&fit=crop&q=80" 
                                 alt="Direktur Operasional LPK Gyokai"/>
                            <div class="absolute top-3 left-3 px-3 py-1 rounded-lg bg-secondary text-on-secondary text-xs font-bold uppercase shadow">
                                Direktur Diklat
                            </div>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Siti Rahmawati, S.Pd., M.M.
                        </h3>
                        <p class="font-label-md text-label-md text-secondary font-medium">
                            Direktur Operasional &amp; Kurikulum Pelatihan
                        </p>
                        <p class="text-body-sm text-secondary/80 mt-2 text-xs">
                            Pakar standardisasi kurikulum vokasi dan sertifikasi kebahasaan JLPT N4-N2 Kemnaker RI.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Executive & Manajemen Pelatihan -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">TIM MANAJEMEN &amp; INSTRUKTUR UTAMA</h2>
                        <p class="text-body-sm text-secondary">Tenaga pengajar native speaker, instruktur teknik bersertifikat BNSP, dan pembina kedisiplinan</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-space-md">
                    <!-- Member 1 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-4 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&auto=format&fit=crop&q=80" 
                                 alt="Kenji Takahashi"/>
                            <span class="absolute top-2 right-2 bg-primary/90 text-white text-[10px] font-bold px-2 py-0.5 rounded">Native</span>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Kenji Takahashi, B.A.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Head of Japanese Language &amp; Culture
                        </p>
                    </div>

                    <!-- Member 2 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-4 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&auto=format&fit=crop&q=80" 
                                 alt="Bambang Suryono"/>
                            <span class="absolute top-2 right-2 bg-secondary text-white text-[10px] font-bold px-2 py-0.5 rounded">BNSP Lead</span>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Bambang Suryono, S.T.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Instruktur Senior Mesin Manufaktur &amp; CNC
                        </p>
                    </div>

                    <!-- Member 3 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-4 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80" 
                                 alt="Nurul Azizah"/>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Nurul Azizah, S.Psi.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Koordinator Psikotes &amp; Konseling Karir
                        </p>
                    </div>

                    <!-- Member 4 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-4 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3 relative">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=500&auto=format&fit=crop&q=80" 
                                 alt="Agus Triyono"/>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Kapten (Purn) Agus Triyono
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Kepala Pembinaan Fisik, Mental &amp; Disiplin (FMD)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Legalitas & Kemitraan Industri -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                    <div class="w-2.5 h-7 bg-primary rounded-full"></div>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">LEGAL &amp; KEMITRAAN INTERNASIONAL</h2>
                        <p class="text-body-sm text-secondary">Pendampingan dokumen imigrasi, kontrak bilateral, dan sertifikasi penempatan</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-space-md">
                    <!-- Member 1 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-5 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=500&auto=format&fit=crop&q=80" 
                                 alt="Advokat Farhan"/>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Farhan Hidayat, S.H., M.Kn.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Legal &amp; Regulatory Compliance Specialist
                        </p>
                    </div>

                    <!-- Member 2 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-5 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=500&auto=format&fit=crop&q=80" 
                                 alt="Diana Kartika"/>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Diana Kartika, S.E.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Manager Kerjasama Industri &amp; Labor Supply
                        </p>
                    </div>

                    <!-- Member 3 -->
                    <div class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-surface-container-high/60 flex flex-col p-5 text-center">
                        <div class="w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container mb-3">
                            <img class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
                                 src="https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=500&auto=format&fit=crop&q=80" 
                                 alt="Reza Firmansyah"/>
                        </div>
                        <h4 class="font-label-md text-label-md text-on-surface font-bold mb-1 group-hover:text-primary transition-colors">
                            Reza Firmansyah, S.Kom.
                        </h4>
                        <p class="text-body-sm text-secondary text-xs">
                            Administrasi Penempatan &amp; Visa Hub
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- CTA Hubungi Tim Kami -->
        <div class="relative bg-surface-container-low rounded-2xl p-space-lg md:p-space-xl overflow-hidden shadow-sm border border-surface-container-high/60 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed text-primary font-label-sm text-label-sm font-bold uppercase mb-2">
                    <span class="material-symbols-outlined text-[16px]">handshake</span>
                    Kemitraan &amp; Konsultasi Karir
                </span>
                <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight mb-2">
                    Ingin Berdiskusi Langsung dengan Tim Kami?
                </h3>
                <p class="text-body-md text-secondary">
                    Tim konsultan dan instruktur LPK GYOKAI siap membantu menjawab kebutuhan seleksi magang ke Jepang maupun kerja sama penyerapan tenaga kerja industri Anda.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl bg-primary text-on-primary font-label-md font-bold hover:bg-tertiary transition-all shadow-md">
                    Hubungi Kami Sekarang
                </a>
            </div>
        </div>

    </section>
</div>
@endsection
