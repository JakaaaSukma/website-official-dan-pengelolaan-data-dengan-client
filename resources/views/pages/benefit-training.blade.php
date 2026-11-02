@php
    $seoPage = 'benefit-training';
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
                <a class="hover:text-primary transition-colors" href="{{ route('our-services') }}">Layanan</a>
                <span class="text-secondary/60">/</span>
                <span class="text-primary font-label-sm font-bold">Training Program</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">model_training</span>
                        Customized In-House Curriculum
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm font-bold">
                        Benefit <span class="text-primary">Training Program Development</span>
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Program pelatihan kejuruan &amp; soft skill yang dikembangkan khusus sesuai kebutuhan operasional perusahaan untuk menghasilkan SDM handal, terstandarisasi, dan selaras dengan budaya industri.
                    </p>
                </div>

                <!-- Trust Pill -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">workspace_premium</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">TNA Based</div>
                        <div class="font-body-sm text-body-sm text-secondary">Analisis Kebutuhan Nyata</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Benefits Grid -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-12">
        <div class="text-center max-w-2xl mx-auto mb-space-xl">
            <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-bold">Keunggulan Pelatihan</span>
            <h2 class="font-headline-lg text-headline-lg text-on-surface mt-space-xs font-bold">Mengapa In-House Training Gyokai?</h2>
            <p class="font-body-md text-body-md text-secondary mt-1">Metode pembekalan presisi yang fokus langsung pada pemecahan masalah operasional.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
            <!-- Benefit 1 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-primary-fixed text-primary flex items-center justify-center mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <span class="material-symbols-outlined text-[28px]">target</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Pelatihan Tepat Sasaran
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        Materi pelatihan disusun berbasis Training Need Analysis (TNA), sehingga setiap jam pelajaran relevan langsung dengan kebutuhan nyata mesin dan sistem perusahaan.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-primary">
                    <span>Relevansi Materi 100%</span>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center mb-space-md group-hover:bg-secondary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[28px]">trending_up</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Peningkatan Produktivitas Nyata
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        SDM yang terlatih berstandar manufaktur mampu bekerja lebih cekatan, meminimalisir reject rate (kecacatan produk), dan mendongkrak efisiensi output harian.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-secondary">
                    <span>Peningkatan Kualitas Output</span>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-primary-fixed text-primary flex items-center justify-center mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <span class="material-symbols-outlined text-[28px]">group_work</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Internalisasi Budaya Perusahaan
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        Menanamkan prinsip kedisiplinan kerja Jepang (Kaizen, 5S, Hou-Ren-So) sehingga karyawan baru memiliki rasa kepemilikan dan adaptasi lingkungan yang cepat.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-primary">
                    <span>Etos Kerja Kuat</span>
                </div>
            </div>

            <!-- Benefit 4 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center mb-space-md group-hover:bg-secondary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[28px]">savings</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Efisiensi Biaya Jangka Panjang
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        Modul pelatihan yang telah dikembangkan dapat direplikasi untuk angkatan-angkatan selanjutnya, menekan biaya per tenaga kerja secara signifikan.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-secondary">
                    <span>Investasi Berkelanjutan</span>
                </div>
            </div>

            <!-- Benefit 5 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-primary-fixed text-primary flex items-center justify-center mb-space-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <span class="material-symbols-outlined text-[28px]">fact_check</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Konsistensi Mutu Tenaga Kerja
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        Standar keahlian seluruh lini staf diseragamkan lewat sertifikasi kejuruan, menjaga keandalan proses produksi terbebas dari deviasi mutu personil.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-primary">
                    <span>Standar Terintegrasi</span>
                </div>
            </div>

            <!-- Benefit 6 -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-xl bg-secondary-fixed text-secondary flex items-center justify-center mb-space-md group-hover:bg-secondary group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[28px]">loyalty</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Retensi Karyawan Lebih Tinggi
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed">
                        Karyawan yang dibina melalui program pelatihan terarah merasa dihargai dan melihat jenjang karir yang jelas, sehingga angka perputaran tenaga kerja (turnover) menurun drastis.
                    </p>
                </div>
                <div class="pt-space-md mt-space-sm border-t border-surface-container-high/40 text-xs font-bold text-secondary">
                    <span>Loyalitas &amp; Retensi Tinggi</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Profile Download & Marketing CTA Section -->
    <section class="max-w-7xl mx-auto w-full px-gutter pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            
            <!-- Left 7 Cols: Download Form Card -->
            <div class="lg:col-span-7 bg-surface-container-lowest p-space-lg md:p-space-xl rounded-2xl shadow-sm border border-surface-container-high/60">
                <div class="border-b border-surface-container-high pb-4 mb-6">
                    <span class="inline-flex items-center gap-1 text-primary text-xs font-bold uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-[16px]">file_download</span>
                        Modul &amp; Proposal Pelatihan
                    </span>
                    <h3 class="font-headline-md text-headline-md text-on-surface font-bold">Unduh Company Profile &amp; Modul Training</h3>
                    <p class="text-body-md text-secondary mt-1">
                        Pelajari silabus pelatihan, fasilitas workshop praktek, dan rincian skema in-house training LPK GYOKAI.
                    </p>
                </div>

                <form id="companyProfileForm" method="POST" action="{{ route('company-profile.download') }}" class="flex flex-col gap-4">
                    @csrf
                    
                    @if (isset($errors) && $errors->any())
                        <div class="p-4 rounded-xl bg-error-container text-on-error-container text-sm">
                            <strong>Perhatian!</strong> Mohon lengkapi seluruh kolom dengan format yang benar.
                        </div>
                    @endif
                    @if (isset($errors) && $errors->has('pdf'))
                        <div class="p-4 rounded-xl bg-error-container text-on-error-container text-sm">
                            {{ $errors->first('pdf') }}
                        </div>
                    @endif

                    <!-- Nama Perusahaan -->
                    <div class="flex flex-col gap-1">
                        <label for="company_name" class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between">
                            <span>Nama Perusahaan <span class="text-primary">*</span></span>
                            <span class="text-xs text-secondary">PT / CV / Instansi</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]">corporate_fare</span>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name') }}" 
                                   placeholder="Contoh: PT Manufaktur Presisi Indonesia" 
                                   required minlength="2" maxlength="255" 
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface text-body-md border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"/>
                        </div>
                        @if(isset($errors) && $errors->has('company_name'))
                            <span class="text-xs text-error mt-0.5">{{ $errors->first('company_name') }}</span>
                        @endif
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="flex flex-col gap-1">
                        <label for="full_name" class="font-label-md text-label-md text-on-surface font-medium flex items-center justify-between">
                            <span>Nama Lengkap <span class="text-primary">*</span></span>
                            <span class="text-xs text-secondary">PIC Pengunduh</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]">person</span>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="{{ old('full_name') }}" 
                                   placeholder="Contoh: Hendra Wijaya" 
                                   required minlength="2" maxlength="255" 
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface text-body-md border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"/>
                        </div>
                        @if(isset($errors) && $errors->has('full_name'))
                            <span class="text-xs text-error mt-0.5">{{ $errors->first('full_name') }}</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Jabatan -->
                        <div class="flex flex-col gap-1">
                            <label for="position" class="font-label-md text-label-md text-on-surface font-medium">
                                Jabatan <span class="text-primary">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]">badge</span>
                                <input type="text" 
                                       id="position" 
                                       name="position" 
                                       value="{{ old('position') }}" 
                                       placeholder="HRD / Training Manager" 
                                       required minlength="2" maxlength="255" 
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface text-body-md border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"/>
                            </div>
                            @if(isset($errors) && $errors->has('position'))
                                <span class="text-xs text-error mt-0.5">{{ $errors->first('position') }}</span>
                            @endif
                        </div>

                        <!-- Kontak -->
                        <div class="flex flex-col gap-1">
                            <label for="contact" class="font-label-md text-label-md text-on-surface font-medium">
                                No. WhatsApp / Email <span class="text-primary">*</span>
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]">contact_mail</span>
                                <input type="text" 
                                       id="contact" 
                                       name="contact" 
                                       value="{{ old('contact') }}" 
                                       placeholder="08123456789 atau nama@pt.com" 
                                       required maxlength="255" 
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-surface-container-lowest text-on-surface text-body-md border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"/>
                            </div>
                            @if(isset($errors) && $errors->has('contact'))
                                <span class="text-xs text-error mt-0.5">{{ $errors->first('contact') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submitBtn" 
                            class="w-full mt-2 py-3.5 px-6 rounded-xl bg-primary hover:bg-tertiary text-on-primary font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-md transition-all font-bold cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        <span>Unduh Company Profile PDF</span>
                    </button>
                </form>
            </div>

            <!-- Right 5 Cols: Marketing Consultation & Direct Contact Card -->
            <div class="lg:col-span-5 flex flex-col gap-space-md">
                <div class="bg-gradient-to-br from-primary to-tertiary text-on-primary p-space-lg md:p-space-xl rounded-2xl shadow-md">
                    <span class="material-symbols-outlined text-[36px] mb-2">engineering</span>
                    <h3 class="font-headline-md text-headline-md text-on-primary font-bold mb-2">
                        Konsultasi Silabus &amp; Training
                    </h3>
                    <p class="text-body-md text-on-primary/90 text-sm leading-relaxed mb-6">
                        Diskusikan kebutuhan kurikulum kejuruan teknik, pelatihan CNC, pengelasan, atau kelas bahasa Jepang bersama tim instruktur LPK GYOKAI.
                    </p>

                    <div class="flex flex-col gap-3">
                        <a href="https://wa.me/6285710561815?text=Halo%20LPK%20Gyokai,%20saya%20tertarik%20dengan%20program%20Training%20Program%20Development%20dan%20ingin%20bekerja%20sama,%20apakah%20bisa%20berdiskusi?" 
                           target="_blank" 
                           class="w-full py-3 px-4 rounded-xl bg-[#25D366] hover:bg-[#20ba5a] text-white font-label-md font-bold shadow transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                            <span>Hubungi Marketing 1 (+62 857-1056-1815)</span>
                        </a>

                        <a href="https://wa.me/6285770809904?text=Halo%20LPK%20Gyokai,%20saya%20tertarik%20dengan%20program%20Training%20Program%20Development%20dan%20ingin%20bekerja%20sama,%20apakah%20bisa%20berdiskusi?" 
                           target="_blank" 
                           class="w-full py-3 px-4 rounded-xl bg-white text-on-surface hover:bg-surface-container-low font-label-md font-bold shadow transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-[#25D366]">chat</span>
                            <span>Hubungi Marketing 2 (+62 857-7080-9904)</span>
                        </a>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container-high/60">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-[22px] shrink-0 mt-0.5">verified_user</span>
                        <div>
                            <h4 class="font-label-md font-bold text-on-surface text-sm">Instruktur Bersertifikat BNSP</h4>
                            <p class="text-xs text-secondary mt-0.5">
                                Didampingi instruktur teknik senior pemegang lisensi asesor BNSP serta native speaker bahasa Jepang berpengalaman.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('companyProfileForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">refresh</span><span>Memproses Unduhan...</span>';

                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">check_circle</span><span>Download Dimulai!</span>';
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                }, 2000);
            });
        }
    });
</script>
@endsection