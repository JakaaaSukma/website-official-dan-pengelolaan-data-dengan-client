@php
    $seoPage = 'contact';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Top Visual Accent / Breadcrumb Bar -->
    <section class="w-full bg-surface-container-low border-b border-surface-container py-space-md">
        <div class="max-w-7xl mx-auto px-gutter flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
            <nav aria-label="Breadcrumb" class="flex items-center gap-space-xs font-label-md text-label-md">
                <a class="text-secondary hover:text-primary transition-colors flex items-center gap-1" href="{{ route('index') }}">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    Beranda
                </a>
                <span class="text-outline-variant">/</span>
                <span class="text-primary font-bold">Kontak Kami</span>
            </nav>
            <div class="inline-flex items-center gap-space-xs self-start md:self-auto bg-surface-container-lowest px-space-sm py-1 rounded-full shadow-sm border border-surface-container-high/60">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="font-label-sm text-label-sm text-secondary">Layanan Responsif Cepat: Senin - Jumat 08.00 - 17.00 WIB</span>
            </div>
        </div>
    </section>

    <!-- Page Header Banner -->
    <section class="w-full bg-surface-container-lowest py-space-xl relative overflow-hidden border-b border-surface-container">
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-96 h-96 rounded-full bg-primary/5 pointer-events-none blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-gutter relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-space-xs px-space-sm py-1 rounded-lg bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-primary">support_agent</span>
                    Pusat Bantuan &amp; Konsultasi Resmi
                </div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm">
                    Hubungi <span class="text-primary">Kami</span>
                </h1>
                <p class="font-body-lg text-body-lg text-secondary leading-relaxed">
                    Kami siap membantu Anda. Silakan hubungi informasi kontak di bawah ini atau kirim pesan langsung melalui formulir konsultasi terpadu. Tim kami siap mendampingi kebutuhan pelatihan karir dan kerja sama industri Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content Grid (Contact Info Left + Form Right) -->
    <section class="w-full py-space-xl bg-background">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                
                <!-- Left Column: Detailed Contact Cards -->
                <div class="lg:col-span-5 flex flex-col gap-space-md">
                    <div class="mb-space-xs">
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-1 font-bold">Kanal Komunikasi</h2>
                        <p class="font-body-md text-body-md text-secondary">Pilih jalur komunikasi yang paling nyaman bagi Anda.</p>
                    </div>

                    <!-- Card 1: Telepon Kantor -->
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md border border-surface-container-high/60">
                        <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-[26px]">call</span>
                        </div>
                        <div class="flex-col flex flex-1">
                            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-semibold">Telepon Kantor</span>
                            <div class="flex flex-col my-0.5">
                                @if(config('gyokai.phone2'))
                                    <a class="font-headline-sm text-headline-sm text-on-surface hover:text-primary transition-colors font-bold" href="tel:{{ preg_replace('/[^0-9]/', '', config('gyokai.phone2')) }}">
                                        {{ config('gyokai.phone2') }}
                                    </a>
                                @endif
                                <a class="font-body-md text-body-md text-on-surface hover:text-primary transition-colors font-medium" href="tel:{{ preg_replace('/[^0-9]/', '', config('gyokai.phone')) }}">
                                    {{ config('gyokai.phone') }}
                                </a>
                            </div>
                            <span class="font-body-sm text-body-sm text-secondary flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                Jam operasional (08.00 - 17.00 WIB)
                            </span>
                        </div>
                    </div>

                    <!-- Card 2: Konsultasi WhatsApp -->
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md border border-surface-container-high/60">
                        <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-secondary text-[26px]">chat</span>
                        </div>
                        <div class="flex-col flex flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-semibold">Konsultasi WhatsApp</span>
                                <span class="font-label-sm text-label-sm bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold">Respon Cepat</span>
                            </div>
                            @php
                                $waNumber = preg_replace('/[^0-9]/', '', config('gyokai.phone'));
                                if (str_starts_with($waNumber, '0')) {
                                    $waNumber = '62' . substr($waNumber, 1);
                                }
                            @endphp
                            <a class="font-headline-sm text-headline-sm text-primary hover:underline my-0.5 font-bold" 
                               href="https://wa.me/{{ $waNumber }}?text=Halo%20LPK%20Gyokai,%20saya%20ingin%20konsultasi%20program%20pelatihan%20dan%20magang" 
                               target="_blank" rel="noopener noreferrer">
                                +{{ substr($waNumber, 0, 2) }} {{ substr($waNumber, 2, 3) }}-{{ substr($waNumber, 5, 4) }}-{{ substr($waNumber, 9) }}
                            </a>
                            <span class="font-body-sm text-body-sm text-secondary flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">headset_mic</span>
                                Layanan CS Interaktif (08.00 - 17.00 WIB)
                            </span>
                        </div>
                    </div>

                    <!-- Card 3: Email Resmi -->
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md border border-surface-container-high/60">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-surface text-[26px]">mail</span>
                        </div>
                        <div class="flex-col flex flex-1">
                            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-semibold">Email Informasi &amp; Kerjasama</span>
                            <div class="flex flex-col my-0.5">
                                <a class="font-label-md text-label-md text-on-surface hover:text-primary transition-colors font-medium" href="mailto:{{ config('gyokai.contact_email') }}">
                                    {{ config('gyokai.contact_email') }}
                                </a>
                                @if(config('gyokai.hrd_email'))
                                    <a class="font-label-md text-label-md text-on-surface hover:text-primary transition-colors font-medium" href="mailto:{{ config('gyokai.hrd_email') }}">
                                        {{ config('gyokai.hrd_email') }}
                                    </a>
                                @endif
                            </div>
                            <span class="font-body-sm text-body-sm text-secondary flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[14px]">mark_email_read</span>
                                Respon maksimal 1x24 jam kerja
                            </span>
                        </div>
                    </div>

                    <!-- Card 4: Alamat Training Center -->
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md border border-surface-container-high/60">
                        <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-[26px]">location_on</span>
                        </div>
                        <div class="flex-col flex flex-1">
                            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-semibold">Kantor &amp; Pusat Pelatihan</span>
                            <p class="font-body-md text-body-md text-on-surface font-bold mt-1">LPK GYOKAI Indonesia Kompeten</p>
                            <p class="font-body-sm text-body-sm text-secondary mt-0.5 leading-relaxed">{{ config('gyokai.address') }}</p>
                            <div class="mt-space-sm pt-space-xs flex items-center gap-space-sm">
                                <span class="font-label-sm text-label-sm text-primary flex items-center gap-1 font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">directions</span>
                                    Akses Strategis &amp; Terjangkau
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Legalitas & Sertifikasi Resmi -->
                    <div class="bg-gradient-to-br from-surface-container to-surface-container-high p-space-md rounded-xl shadow-sm border border-surface-container-high/60">
                        <div class="flex items-center gap-space-sm mb-space-xs">
                            <span class="material-symbols-outlined text-primary text-[22px]">verified</span>
                            <span class="font-label-md text-label-md text-on-surface uppercase tracking-wide font-bold">Legalitas Resmi Terakreditasi</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-secondary mb-space-sm">
                            Lembaga Pelatihan Kerja resmi dengan otorisasi penempatan dan izin operasional Kemnaker RI.
                        </p>
                        <div class="grid grid-cols-2 gap-space-xs">
                            <div class="bg-surface-container-lowest p-2 rounded-lg text-center border border-surface-container-high/40">
                                <span class="font-label-sm text-label-sm text-primary block font-bold">Kemnaker RI</span>
                                <span class="font-body-sm text-body-sm text-secondary text-[11px]">Terdaftar &amp; Terverifikasi</span>
                            </div>
                            <div class="bg-surface-container-lowest p-2 rounded-lg text-center border border-surface-container-high/40">
                                <span class="font-label-sm text-label-sm text-primary block font-bold">Akreditasi LA-LPK</span>
                                <span class="font-body-sm text-body-sm text-secondary text-[11px]">Standar Mutu A</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Consultation & Contact Form -->
                <div class="lg:col-span-7">
                    <div class="bg-surface-container-lowest p-space-lg md:p-space-xl rounded-xl shadow-sm border border-surface-container-high/60">
                        <div class="border-b border-surface-container-high pb-space-md mb-space-lg">
                            <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider block mb-1 font-bold">Konsultasi Terpadu</span>
                            <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Formulir Konsultasi &amp; Pertanyaan</h2>
                            <p class="font-body-md text-body-md text-secondary mt-1">
                                Kirim pesan langsung kepada konsultan pelatihan kami. Data Anda aman dan terlindungi.
                            </p>
                        </div>

                        <form class="flex flex-col gap-space-md" id="contactForm" onsubmit="event.preventDefault(); handleContactSubmit();">
                            <!-- Name Input -->
                            <div class="flex flex-col gap-1">
                                <label class="font-label-md text-label-md text-on-surface flex items-center justify-between font-medium" for="fullName">
                                    <span>Nama Lengkap <span class="text-primary">*</span></span>
                                    <span class="font-body-sm text-body-sm text-secondary">Sesuai KTP</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined text-secondary absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">person</span>
                                    <input class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface placeholder:text-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm transition-all text-body-md font-body-md border border-surface-container-high/60 focus:border-primary/40" 
                                           id="fullName" 
                                           placeholder="Contoh: Budi Santoso" 
                                           required 
                                           type="text"/>
                                </div>
                            </div>

                            <!-- Phone / WhatsApp & Email in 2 columns -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-md text-label-md text-on-surface flex items-center justify-between font-medium" for="phoneNumber">
                                        <span>No. WhatsApp <span class="text-primary">*</span></span>
                                        <span class="font-body-sm text-body-sm text-secondary">Aktif</span>
                                    </label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined text-secondary absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">phone_iphone</span>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface placeholder:text-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm transition-all text-body-md font-body-md border border-surface-container-high/60 focus:border-primary/40" 
                                               id="phoneNumber" 
                                               placeholder="Contoh: 081234567890" 
                                               required 
                                               type="tel"/>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-md text-label-md text-on-surface flex items-center justify-between font-medium" for="emailAddress">
                                        <span>Alamat Email <span class="text-primary">*</span></span>
                                        <span class="font-body-sm text-body-sm text-secondary">Valid</span>
                                    </label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined text-secondary absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">mail</span>
                                        <input class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface placeholder:text-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm transition-all text-body-md font-body-md border border-surface-container-high/60 focus:border-primary/40" 
                                               id="emailAddress" 
                                               placeholder="nama@email.com" 
                                               required 
                                               type="email"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Topic / Service Selection Dropdown -->
                            <div class="flex flex-col gap-1">
                                <label class="font-label-md text-label-md text-on-surface font-medium" for="topicOption">
                                    Topik Konsultasi / Minat Layanan <span class="text-primary">*</span>
                                </label>
                                <div class="relative">
                                    <span class="material-symbols-outlined text-secondary absolute left-3 top-1/2 -translate-y-1/2 text-[20px]">category</span>
                                    <select class="w-full pl-10 pr-10 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm transition-all appearance-none text-body-md font-body-md border border-surface-container-high/60 focus:border-primary/40" 
                                            id="topicOption" 
                                            required>
                                        <option disabled selected value="">Pilih salah satu program atau kebutuhan</option>
                                        <option value="Program Magang Jepang (Ginou Jisshuusei)">Pendaftaran Program Magang Jepang (Ginou Jisshuusei)</option>
                                        <option value="Program Pekerja Berketerampilan Khusus (SSW / Tokutei Ginou)">Pendaftaran Program Pekerja Berketerampilan Khusus (SSW)</option>
                                        <option value="Kemitraan Labor Supply & Rekrutmen Industri">Kemitraan Labor Supply &amp; Rekrutmen Industri</option>
                                        <option value="Training Program Development & In-House Training">Training Program Development &amp; In-House Training</option>
                                        <option value="Konsultasi Perizinan & Legalitas Ketenagakerjaan">Konsultasi Perizinan &amp; Legalitas Ketenagakerjaan</option>
                                        <option value="Pertanyaan Umum Lainnya">Pertanyaan Umum Lainnya</option>
                                    </select>
                                    <span class="material-symbols-outlined text-secondary absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]">expand_more</span>
                                </div>
                            </div>

                            <!-- Message Textarea -->
                            <div class="flex flex-col gap-1">
                                <label class="font-label-md text-label-md text-on-surface flex items-center justify-between font-medium" for="messageBody">
                                    <span>Isi Pesan / Rincian Kebutuhan <span class="text-primary">*</span></span>
                                    <span class="font-body-sm text-body-sm text-secondary">Rincian pertanyaan</span>
                                </label>
                                <textarea class="w-full p-3.5 rounded-lg bg-surface-container-lowest text-on-surface placeholder:text-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm transition-all text-body-md font-body-md resize-none border border-surface-container-high/60 focus:border-primary/40" 
                                          id="messageBody" 
                                          placeholder="Tuliskan pertanyaan, latar belakang pendidikan/kejuruan, atau jadwal temu yang diinginkan..." 
                                          required 
                                          rows="4"></textarea>
                            </div>

                            <!-- Agreement Checkbox -->
                            <div class="flex items-start gap-space-sm pt-space-xs">
                                <input class="mt-1 h-4 w-4 rounded text-primary focus:ring-primary/30 accent-primary cursor-pointer shrink-0" 
                                       id="privacyConsent" 
                                       required 
                                       type="checkbox"/>
                                <label class="font-body-sm text-body-sm text-secondary cursor-pointer leading-normal" for="privacyConsent">
                                    Saya menyetujui kebijakan privasi dan data kontak saya digunakan oleh Tim LPK GYOKAI untuk keperluan follow-up konsultasi serta penyampaian informasi resmi.
                                </label>
                            </div>

                            <!-- Feedback / Notification banner hidden by default -->
                            <div class="hidden bg-surface-container p-space-md rounded-lg flex items-center gap-space-sm border border-primary/20" id="formSuccessAlert">
                                <span class="material-symbols-outlined text-primary text-[28px]">task_alt</span>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface font-bold">Pesan Terkirim dengan Sukses!</p>
                                    <p class="font-body-sm text-body-sm text-secondary">Konsultan kami akan menghubungi Anda via WhatsApp atau Email maksimal dalam 1x24 jam kerja.</p>
                                </div>
                            </div>

                            <!-- Submit Buttons: Direct or WhatsApp -->
                            <div class="flex flex-col sm:flex-row gap-3 mt-space-sm">
                                <button class="flex-1 py-3 px-space-lg rounded-lg bg-primary hover:bg-tertiary text-on-primary font-headline-sm text-headline-sm flex items-center justify-center gap-space-sm shadow-md transition-all active:scale-[0.99] cursor-pointer font-bold" 
                                        id="submitBtn" 
                                        type="submit">
                                    <span>Kirim Pesan Sekarang</span>
                                    <span class="material-symbols-outlined text-[20px]">send</span>
                                </button>
                                <button type="button" 
                                        onclick="sendViaWhatsApp()" 
                                        class="py-3 px-space-lg rounded-lg bg-[#25D366] hover:bg-[#20ba5a] text-white font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.99] cursor-pointer font-bold">
                                    <span>Chat WA</span>
                                    <span class="material-symbols-outlined text-[20px]">chat</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Map & Directions Section -->
    <section class="w-full py-space-xl bg-surface-container-low border-y border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-space-lg gap-space-md">
                <div>
                    <div class="inline-flex items-center gap-space-xs px-space-sm py-0.5 rounded bg-surface-container text-secondary font-label-sm text-label-sm uppercase tracking-wide mb-1 font-semibold">
                        <span class="material-symbols-outlined text-primary text-[14px]">map</span>
                        Lokasi Kampus &amp; Diklat
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Peta Lokasi &amp; Pusat Pelatihan</h2>
                    <p class="font-body-md text-body-md text-secondary mt-1">Kunjungi fasilitas ruang belajar bahasa Jepang, workshop teknik kejuruan, dan asrama kami.</p>
                </div>
                <div class="flex items-center gap-space-sm shrink-0">
                    <a class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-tertiary transition-all shadow-sm font-bold" 
                       href="https://maps.google.com/?q={{ urlencode(config('gyokai.address')) }}" 
                       rel="noopener noreferrer" 
                       target="_blank">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                        Buka di Google Maps
                    </a>
                </div>
            </div>

            <!-- Map Display Container with Interactive Google Map Embed -->
            <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-surface-container-high/60">
                <!-- Map Embed Component -->
                <div class="lg:col-span-8 relative min-h-[380px] lg:min-h-[460px]">
                    <iframe class="w-full h-full min-h-[380px] lg:min-h-[460px] border-0" 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0182159658366!2d106.44066201183526!3d-6.261330461276567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e42065128d869a1%3A0xac3788bc22eaba78!2sSMK%20%26%20LPK%20Gyokai%20Indonesia!5e0!3m2!1sid!2sid!4v1739271072474!5m2!1sid!2sid" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"></iframe>

                    <!-- Floating Map Location Pin Card -->
                    <div class="absolute bottom-4 left-4 right-4 md:right-auto md:max-w-md bg-surface-container-lowest/95 backdrop-blur-md p-space-md rounded-xl shadow-lg flex items-start gap-space-sm border border-surface-container-high/60 pointer-events-none">
                        <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-on-primary text-[22px]">apartment</span>
                        </div>
                        <div>
                            <p class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">Kampus LPK GYOKAI</p>
                            <p class="font-body-sm text-body-sm text-secondary mt-0.5">{{ config('gyokai.address') }}</p>
                            <div class="flex items-center gap-space-sm mt-2">
                                <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-primary font-bold">
                                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                    Terbuka untuk Kunjungan &amp; Konsultasi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transportation & Navigation Guide -->
                <div class="lg:col-span-4 p-space-lg flex flex-col justify-between bg-surface-container-lowest border-t lg:border-t-0 lg:border-l border-surface-container-high/60">
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-space-sm font-bold">Petunjuk Akses Lokasi</h3>
                        <p class="font-body-sm text-body-sm text-secondary mb-space-md">
                            Akses kampus strategis dan dapat dijangkau menggunakan kendaraan pribadi maupun transportasi umum.
                        </p>
                        <div class="flex flex-col gap-space-sm">
                            <div class="flex items-start gap-space-sm p-space-sm rounded-lg bg-surface-container-low border border-surface-container-high/40">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">train</span>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface font-semibold">Stasiun KRL Terdekat</p>
                                    <p class="font-body-sm text-body-sm text-secondary">Akses terdekat dari Stasiun Tigaraksa / Cikoya, lanjutkan perjalanan singkat via transportasi lokal.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-space-sm p-space-sm rounded-lg bg-surface-container-low border border-surface-container-high/40">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">directions_car</span>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface font-semibold">Akses Tol &amp; Kendaraan</p>
                                    <p class="font-body-sm text-body-sm text-secondary">Keluar gerbang Tol Balaraja Barat / Timur. Tersedia area parkir yang memadai untuk mobil &amp; motor pengunjung.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-space-sm p-space-sm rounded-lg bg-surface-container-low border border-surface-container-high/40">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">domain</span>
                                <div>
                                    <p class="font-label-md text-label-md text-on-surface font-semibold">Area Kompleks Kampus</p>
                                    <p class="font-body-sm text-body-sm text-secondary">Berada dalam kawasan terpadu SMK &amp; LPK Gyokai Indonesia dengan fasilitas workshop lengkap.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-space-md pt-space-sm border-t border-surface-container-high">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-label-sm text-label-sm text-secondary uppercase font-semibold">Jadwal Tur Kampus</p>
                                <p class="font-body-sm text-body-sm text-on-surface font-bold">Senin - Sabtu (Reservasi H-1)</p>
                            </div>
                            <a class="px-space-sm py-1.5 rounded-lg bg-primary text-on-primary hover:bg-tertiary font-label-sm text-label-sm font-bold transition-colors" 
                               href="https://wa.me/{{ $waNumber }}?text=Halo%20LPK%20Gyokai,%20saya%20ingin%20jadwalkan%20kunjungan%20tur%20kampus" 
                               target="_blank">
                                Reservasi Tur →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reference Image Integration Panel (Visual Identity Context) -->
    <section class="w-full py-space-xl bg-surface-container-lowest">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-space-lg items-center">
                <div class="md:col-span-5 flex flex-col gap-space-sm">
                    <div class="inline-flex items-center gap-1.5 px-space-sm py-1 rounded bg-primary-fixed text-primary font-label-sm text-label-sm w-fit uppercase font-semibold">
                        <span class="material-symbols-outlined text-[16px]">domain</span>
                        Pusat Standarisasi Kejuruan
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">
                        Komitmen Pelayanan &amp; Transparansi Informasi
                    </h2>
                    <p class="font-body-md text-body-md text-secondary leading-relaxed">
                        LPK GYOKAI berpegang teguh pada etika keterbukaan, zero-illegal fee, serta komunikasi transparan antara calon peserta pelatihan, wali murid, dan perusahaan mitra penerima di Jepang.
                    </p>
                    <div class="flex flex-col gap-space-xs mt-space-xs">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">Konsultasi awal bebas biaya / gratis tanpa komitmen.</span>
                        </div>
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">Verifikasi dokumen dan kesiapan bahasa sebelum seleksi kerja.</span>
                        </div>
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">Pendampingan legalitas visa dan kontrak kerja resmi bilateral.</span>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-7">
                    <div class="relative rounded-xl overflow-hidden shadow-md group border border-surface-container-high/60">
                        <img alt="Gedung dan Kegiatan LPK Gyokai Indonesia Kompeten" 
                             class="w-full h-auto object-cover max-h-[420px] transition-transform duration-500 group-hover:scale-105" 
                             src="{{ \App\Models\SiteAsset::url('contact_campus_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBtN9U5FWdeqV6a6Hd6miwhDu5ykimTnheL51GK8hZkJhbq0lxNmhhiqmETduBM9IsIyviqKckUww7nDzIuug-39R1VXV4FxBNqFY9X5OJ50jIFi5-LrKtVLivJvuhAIp-6nX2e3Kh5gn1gcaT_XKD1F63iISptgi5drbt1mmXKrA0blPC1FBGaThCqSJhp6wtNJ0FjizmQe2SDzCfWrswoKTFE7vcS-h2MF2bHayS28iQuVUQbAh8w') }}"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-space-md">
                            <div class="text-white">
                                <p class="font-headline-sm text-headline-sm font-bold">Pusat Diklat Terpadu Bahasa &amp; Keterampilan Kerja</p>
                                <p class="font-body-sm text-body-sm text-white/80">Dokumentasi operasional resmi LPK GYOKAI Indonesia Kompeten.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Singkat Kontak (Accordion Interactive) -->
    <section class="w-full py-space-xl bg-background border-t border-surface-container">
        <div class="max-w-4xl mx-auto px-gutter">
            <div class="text-center mb-space-lg">
                <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider block mb-1 font-bold">Tanya Jawab Layanan</span>
                <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Pertanyaan Seputar Layanan &amp; Kunjungan</h2>
                <p class="font-body-md text-body-md text-secondary mt-1">Hal-hal yang sering ditanyakan sebelum menghubungi atau datang langsung.</p>
            </div>
            <div class="flex flex-col gap-space-sm" id="contactFaqAccordion">
                <!-- FAQ Item 1 -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-surface-container-high/60">
                    <button class="w-full text-left p-space-md flex items-center justify-between gap-space-md focus:outline-none" onclick="toggleContactFaq(1)" type="button">
                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Kapan jam operasional kantor &amp; layanan konsultasi tatap muka?</span>
                        <span class="material-symbols-outlined text-secondary transition-transform duration-300" id="faqIcon-1">expand_more</span>
                    </button>
                    <div class="hidden px-space-md pb-space-md pt-0 text-secondary font-body-md text-body-md leading-relaxed border-t border-surface-container-low mt-space-xs" id="faqContent-1">
                        Kantor kami beroperasi setiap hari Senin hingga Jumat pukul 08.00 - 17.00 WIB, serta hari Sabtu pukul 08.30 - 13.00 WIB. Konsultasi tatap muka atau tur fasilitas diklat sangat disarankan membuat janji temu terlebih dahulu melalui WhatsApp agar staf konsultan kami dapat mendampingi secara optimal.
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-surface-container-high/60">
                    <button class="w-full text-left p-space-md flex items-center justify-between gap-space-md focus:outline-none" onclick="toggleContactFaq(2)" type="button">
                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Apakah pendaftaran program magang dan pelatihan bisa dilakukan secara online?</span>
                        <span class="material-symbols-outlined text-secondary transition-transform duration-300" id="faqIcon-2">expand_more</span>
                    </button>
                    <div class="hidden px-space-md pb-space-md pt-0 text-secondary font-body-md text-body-md leading-relaxed border-t border-surface-container-low mt-space-xs" id="faqContent-2">
                        Ya, calon peserta dari luar domisili atau luar pulau dapat melakukan pendaftaran awal secara online melalui formulir kontak atau menu Lowongan Kerja pada website ini. Seleksi berkas administrasi dan wawancara awal dapat difasilitasi melalui telekonferensi video sebelum jadwal masuk asrama dimulai.
                    </div>
                </div>
                <!-- FAQ Item 3 -->
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-surface-container-high/60">
                    <button class="w-full text-left p-space-md flex items-center justify-between gap-space-md focus:outline-none" onclick="toggleContactFaq(3)" type="button">
                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Bagaimana prosedur kerja sama bagi mitra industri dan penyerap tenaga kerja?</span>
                        <span class="material-symbols-outlined text-secondary transition-transform duration-300" id="faqIcon-3">expand_more</span>
                    </button>
                    <div class="hidden px-space-md pb-space-md pt-0 text-secondary font-body-md text-body-md leading-relaxed border-t border-surface-container-low mt-space-xs" id="faqContent-3">
                        Bagi perusahaan manufaktur, konstruksi, perhotelan, maupun perawatan medis (Kaigo), Anda dapat memilih topik 'Kemitraan Labor Supply' pada formulir kontak di atas atau mengirimkan Terms of Reference via email resmi ke <a class="text-primary underline font-bold" href="mailto:{{ config('gyokai.contact_email') }}">{{ config('gyokai.contact_email') }}</a>. Tim Partnership kami akan membalas dengan profil kompetensi lulusan dan draf perjanjian kerja sama (MoU).
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Interactive Scripts -->
<script>
    function toggleContactFaq(id) {
        const content = document.getElementById('faqContent-' + id);
        const icon = document.getElementById('faqIcon-' + id);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function handleContactSubmit() {
        const btn = document.getElementById('submitBtn');
        const alertBox = document.getElementById('formSuccessAlert');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">refresh</span><span>Mengirim Pesan...</span>';
        
        setTimeout(function() {
            btn.disabled = false;
            btn.innerHTML = '<span>Kirim Pesan Sekarang</span><span class="material-symbols-outlined text-[20px]">send</span>';
            alertBox.classList.remove('hidden');
            document.getElementById('contactForm').reset();
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 800);
    }

    function sendViaWhatsApp() {
        const name = document.getElementById('fullName').value.trim();
        const phone = document.getElementById('phoneNumber').value.trim();
        const email = document.getElementById('emailAddress').value.trim();
        const topic = document.getElementById('topicOption').value;
        const msg = document.getElementById('messageBody').value.trim();

        if (!name || !msg) {
            alert('Mohon isi nama lengkap dan isi pesan terlebih dahulu sebelum mengirim via WhatsApp.');
            return;
        }

        const text = `Halo Admin LPK Gyokai,\n\nNama: ${name}\nNo. HP: ${phone || '-'}\nEmail: ${email || '-'}\nTopik: ${topic || 'Konsultasi'}\n\nPesan:\n${msg}`;
        const targetNumber = '{{ $waNumber }}';
        window.open(`https://wa.me/${targetNumber}?text=${encodeURIComponent(text)}`, '_blank');
    }
</script>
@endsection
