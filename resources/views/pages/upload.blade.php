@php
    $seoPage = 'upload';
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
                <span class="text-primary font-label-sm font-bold">Upload &amp; Pendaftaran</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">upload_file</span>
                        Portal Berkas &amp; Dokumen Pendaftaran
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm font-bold">
                        Bergabung Bersama <span class="text-primary">LPK GYOKAI</span>
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Mulai langkah karir profesional Anda menuju Jepang. Pilih salah satu tahapan pendaftaran terpadu di bawah ini untuk memulai proses administrasi dan seleksi.
                    </p>
                </div>

                <!-- Trust Badge -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">verified</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">3 Langkah</div>
                        <div class="font-body-sm text-body-sm text-secondary">Proses Cepat &amp; Resmi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Step Cards Section -->
    <section class="max-w-7xl mx-auto w-full px-gutter py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg">
            
            <!-- Step Card 1: Instagram -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888] text-white flex items-center justify-center mb-space-md shadow-md group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">camera_alt</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed-variant text-[11px] font-bold uppercase">Langkah 1</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Follow Media Sosial
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed mb-space-md">
                        Ikuti Instagram resmi kami untuk mendapatkan informasi lowongan terbaru, pengumuman hasil seleksi wawancara user, dan tips seputar kehidupan di Jepang.
                    </p>
                </div>
                <button type="button" 
                        onclick="followInstagram()" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-[#f09433] via-[#dc2743] to-[#bc1888] text-white font-label-md font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    <span>Follow @gyokaiindonesiakompeten</span>
                </button>
            </div>

            <!-- Step Card 2: Formulir Pendaftaran (Password Protected) -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1 relative">
                <div class="absolute -top-3 right-6 px-3 py-1 rounded-full bg-primary text-on-primary text-xs font-bold shadow uppercase tracking-wide">
                    Wajib Peserta
                </div>
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-primary text-on-primary flex items-center justify-center mb-space-md shadow-md group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">assignment</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed-variant text-[11px] font-bold uppercase">Langkah 2</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Isi Formulir Pendaftaran
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed mb-space-md">
                        Lengkapi biodata diri, riwayat pendidikan, serta peminatan sektor kejuruan (Manufaktur, Otomotif, SSW Tokutei Ginou) untuk verifikasi awal.
                    </p>
                </div>
                <button type="button" 
                        onclick="openFormModal()" 
                        class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-tertiary text-on-primary font-label-md font-bold shadow-md transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">lock_open</span>
                    <span>Isi Formulir (Perlu Kode)</span>
                </button>
            </div>

            <!-- Step Card 3: Upload Berkas PDF -->
            <div class="group bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-surface-container-high/60 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-secondary-container text-on-secondary-fixed flex items-center justify-center mb-space-md shadow-md group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[32px]">picture_as_pdf</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface text-[11px] font-bold uppercase">Langkah 3</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2 group-hover:text-primary transition-colors">
                        Unggah Berkas PDF
                    </h3>
                    <p class="text-body-md text-secondary leading-relaxed mb-space-md">
                        Kirimkan scan berkas pendukung seperti KTP, Kartu Keluarga, Ijazah terakhir, dan sertifikat pendukung melalui portal form berkas resmi.
                    </p>
                </div>
                <button type="button" 
                        onclick="window.open('https://forms.gle/1xujeByEfnSFKn4x8', '_blank')" 
                        class="w-full py-3 px-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md font-bold shadow-sm transition-all flex items-center justify-center gap-2 border border-surface-container-high">
                    <span class="material-symbols-outlined text-[18px] text-primary">upload</span>
                    <span>Upload Dokumen PDF</span>
                </button>
            </div>

        </div>
    </section>

    <!-- Syarat & Ketentuan Berkas -->
    <section class="max-w-7xl mx-auto w-full px-gutter pb-12">
        <div class="bg-surface-container-low rounded-2xl p-space-lg md:p-space-xl border border-surface-container-high/60">
            <div class="flex items-center gap-3 mb-space-md">
                <span class="material-symbols-outlined text-primary text-[28px]">info</span>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Panduan Berkas Persyaratan Administrasi</h3>
                    <p class="text-body-sm text-secondary">Pastikan seluruh dokumen memenuhi ketentuan sebelum diunggah:</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md mt-4">
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-surface-container-high/40 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">badge</span>
                    <div>
                        <h4 class="font-label-md font-bold text-on-surface text-sm">Identitas Kependudukan</h4>
                        <p class="text-xs text-secondary mt-1">Scan KTP &amp; Kartu Keluarga (KK) jelas, tidak terpotong, dan masih berlaku.</p>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-surface-container-high/40 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">school</span>
                    <div>
                        <h4 class="font-label-md font-bold text-on-surface text-sm">Ijazah &amp; Transkrip</h4>
                        <p class="text-xs text-secondary mt-1">Ijazah SMA/SMK/D3/S1 legalisir beserta transkrip nilai akademik lengkap.</p>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-surface-container-high/40 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">medical_services</span>
                    <div>
                        <h4 class="font-label-md font-bold text-on-surface text-sm">Surat Keterangan Sehat</h4>
                        <p class="text-xs text-secondary mt-1">Surat sehat dari dokter/RS (bebas buta warna, tato, tindik, dan penyakit kronis).</p>
                    </div>
                </div>
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-surface-container-high/40 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">description</span>
                    <div>
                        <h4 class="font-label-md font-bold text-on-surface text-sm">Format &amp; Ukuran File</h4>
                        <p class="text-xs text-secondary mt-1">Format file diutamakan PDF jelas terbaca dengan ukuran maksimal 10MB per berkas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Help Callout -->
    <section class="max-w-7xl mx-auto w-full px-gutter pb-16">
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl border border-surface-container-high/60 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-1">Butuh Bantuan Akses atau Kode Formulir?</h4>
                <p class="text-body-md text-secondary">
                    Jika Anda belum memiliki kode akses atau mengalami kendala saat upload berkas, hubungi layanan konsultasi kami.
                </p>
            </div>
            @php
                $waNumber = preg_replace('/[^0-9]/', '', config('gyokai.phone'));
                if (str_starts_with($waNumber, '0')) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
            @endphp
            <a href="https://wa.me/{{ $waNumber }}?text=Halo%20Admin%20Gyokai,%20saya%20ingin%20meminta%20kode%20akses%20formulir%20pendaftaran" 
               target="_blank"
               class="px-6 py-3 rounded-xl bg-primary text-on-primary font-label-md font-bold hover:bg-tertiary transition-all shadow shrink-0 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">chat</span>
                <span>Hubungi CS WhatsApp</span>
            </a>
        </div>
    </section>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-surface-container-lowest rounded-2xl overflow-hidden shadow-2xl border border-surface-container-high">
        <!-- Modal Header -->
        <div class="p-6 text-center border-b border-surface-container">
            <div class="w-14 h-14 rounded-full bg-primary-fixed text-primary flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px]">lock</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Akses Formulir</h3>
            <p class="text-body-sm text-secondary mt-1">Masukkan kode akses untuk membuka formulir pendaftaran LPK Gyokai.</p>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-[20px]">key</span>
                <input type="password" 
                       id="passwordInput" 
                       placeholder="Masukkan kode akses" 
                       class="w-full pl-10 pr-12 py-3 bg-surface-container-low text-on-surface rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-body-md border border-surface-container-high focus:border-primary/40"/>
                <button type="button" 
                        onclick="togglePasswordVisibility()" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-on-surface p-1">
                    <span id="toggleIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
            </div>
            <p id="passwordError" class="hidden text-xs text-error font-medium mt-2 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">error</span>
                <span>Kode akses salah! Silakan coba lagi.</span>
            </p>
            <p class="text-xs text-secondary/70 mt-3">
                *Petunjuk: Gunakan kode resmi yang diberikan oleh staf seleksi.
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-surface-container-low border-t border-surface-container flex items-center justify-end gap-3">
            <button type="button" 
                    onclick="closePasswordModal()" 
                    class="px-4 py-2 rounded-lg text-secondary hover:text-on-surface font-label-md text-sm font-semibold transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="submitPassword()" 
                    class="px-5 py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-sm font-bold hover:bg-tertiary transition-colors shadow">
                Masuk Formulir
            </button>
        </div>
    </div>
</div>

<script>
    function followInstagram() {
        const instagramUrl = '{{ config('gyokai.instagram_url', 'https://www.instagram.com/gyokaiindonesiakompeten/') }}';
        window.open(instagramUrl, '_blank');
    }

    function openFormModal() {
        const modal = document.getElementById('passwordModal');
        const input = document.getElementById('passwordInput');
        const err = document.getElementById('passwordError');
        err.classList.add('hidden');
        input.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => input.focus(), 100);
    }

    function closePasswordModal() {
        const modal = document.getElementById('passwordModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerText = 'visibility_off';
        } else {
            input.type = 'password';
            icon.innerText = 'visibility';
        }
    }

    function submitPassword() {
        const input = document.getElementById('passwordInput');
        const err = document.getElementById('passwordError');
        const val = input.value.trim().toLowerCase();

        if (val === 'kompeten') {
            const formUrl = 'https://forms.gle/CwC1FHBg6DpnNhAh8';
            closePasswordModal();
            window.open(formUrl, '_blank');
        } else {
            err.classList.remove('hidden');
            input.value = '';
            input.focus();
        }
    }

    document.getElementById('passwordInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitPassword();
        }
    });

    document.getElementById('passwordModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePasswordModal();
        }
    });
</script>
@endsection
