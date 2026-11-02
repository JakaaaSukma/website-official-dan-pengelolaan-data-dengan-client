@php
    $seoPage = 'legalitas';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Top Hero & Breadcrumb Section -->
    <section class="relative w-full bg-surface-container-low overflow-hidden py-12 md:py-16 border-b border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-space-xs font-label-md text-label-md text-secondary mb-4">
                <a href="{{ route('index') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    Beranda
                </a>
                <span class="text-secondary/50">/</span>
                <span class="text-primary font-headline-sm text-label-md">Legalitas &amp; Sertifikasi</span>
            </nav>
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-3 font-bold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Standar Pelatihan &amp; Legalitas Kemenaker RI &amp; Regulasi Industri
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">
                        Legalitas &amp; <span class="text-primary">Sertifikasi</span>
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary mt-3 leading-relaxed">
                        Landasan hukum resmi, lisensi operasional, dan kepatuhan regulasi ketenagakerjaan internasional untuk menjamin keamanan masa depan karir setiap peserta di Jepang dan industri nasional.
                    </p>
                </div>
                <!-- Quick Summary Ribbon Box -->
                <div class="flex items-center gap-4 bg-surface-container-lowest p-4 rounded-xl shadow-sm self-start lg:self-auto border border-surface-container">
                    <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 shadow-md">
                        <span class="material-symbols-outlined text-[28px]">gavel</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">100% Legal</span>
                        <span class="font-body-sm text-body-sm text-secondary">Terdaftar di Kemenaker &amp; Kemenkumham RI</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Intro & Transparansi Lembaga -->
    <section class="w-full max-w-7xl mx-auto px-gutter py-8">
        <div class="bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-sm border border-surface-container">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-4">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-2 text-primary font-label-md text-label-md mb-1 font-bold">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        Transparansi &amp; Integritas Lembaga
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Dokumen Resmi dan Terpercaya</h2>
                    <p class="font-body-md text-body-md text-secondary mt-1 leading-relaxed">
                        Semua legalitas dan sertifikasi LPK GYOKAI Indonesia Kompeten telah diakui oleh instansi berwenang sebagai bukti kredibilitas, profesionalisme, dan kepatuhan hukum yang menyeluruh.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-2 px-3 py-2 bg-surface-container rounded-lg text-secondary font-label-sm text-label-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">verified</span>
                        Status: Terakreditasi Aktif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Legal & Certification Document Grid (Dynamic Data + Standard Curated Items) -->
    <section class="w-full max-w-7xl mx-auto px-gutter pb-16">
        @php
            $legalList = $legalitas ?? collect([]);
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="documentsGrid">
            @forelse($legalList as $item)
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
                <div class="col-span-full py-16 text-center bg-surface-container-lowest rounded-2xl border border-dashed border-surface-container">
                    <span class="material-symbols-outlined text-secondary text-5xl mb-2">policy</span>
                    <p class="text-sm font-semibold text-on-surface">Belum ada dokumen legalitas yang dipublikasikan.</p>
                    <p class="text-xs text-secondary mt-1">Dokumen legalitas dapat dikelola melalui Panel Admin.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Highlight Banner Section (100% Legal & Terpercaya) -->
    <section class="w-full max-w-7xl mx-auto px-gutter pb-16">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary to-primary-container text-on-primary p-8 md:p-12 shadow-xl">
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="max-w-2xl text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-on-primary font-label-sm text-label-sm mb-4 font-bold">
                        <span class="material-symbols-outlined text-[16px]">verified_user</span>
                        Jaminan Hukum &amp; Kepatuhan Regulasi
                    </div>
                    <h2 class="font-headline-xl text-headline-xl leading-tight font-bold">
                        100% Legal &amp; Terpercaya
                    </h2>
                    <p class="font-body-lg text-body-lg text-on-primary/90 mt-3 leading-relaxed">
                        Semua dokumen telah diverifikasi dan sesuai dengan peraturan ketenagakerjaan yang berlaku di Republik Indonesia dan regulasi program industri Jepang. Anda dapat bermitra tanpa keraguan.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                    <a href="{{ route('contact') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-lg bg-surface-container-lowest text-primary font-headline-sm hover:bg-white shadow-md transition-all text-center flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        Hubungi Tim Legalitas
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Document Detail Modal -->
<div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4" id="documentModal" onclick="closeDocModal()">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-2xl relative" onclick="event.stopPropagation()">
        <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface hover:bg-surface-container-high transition-colors" onclick="closeDocModal()" type="button">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">verified</span>
            </div>
            <div>
                <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-wider" id="modalIssuer">KEMNAKER RI</span>
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
</script>
@endpush
@endsection
