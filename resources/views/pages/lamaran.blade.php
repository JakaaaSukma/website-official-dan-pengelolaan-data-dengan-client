@php
    $seoPage = 'lamaran';
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full bg-surface-container-low min-h-screen">
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
                <span class="text-primary font-label-sm font-bold">Pelamaran Kerja</span>
            </nav>

            <!-- Page Title & Description -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-space-sm py-1 rounded-full bg-primary-fixed/60 text-on-primary-fixed-variant text-label-sm font-label-sm uppercase tracking-wider mb-space-sm font-semibold">
                        <span class="material-symbols-outlined text-[16px] text-primary">assignment_ind</span>
                        Formulir Pendaftaran &amp; Pelamaran Resmi
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight mb-space-sm font-bold">
                        Formulir Pelamaran <span class="text-primary">Karir &amp; Magang</span>
                    </h1>
                    <p class="font-body-lg text-body-lg text-secondary">
                        Lengkapi profil biodata dan kualifikasi pendidikan Anda di bawah ini. Berkas CV yang diunggah akan otomatis tersimpan dalam sistem seleksi LPK Gyokai.
                    </p>
                </div>

                <!-- Trust Badge -->
                <div class="flex items-center gap-4 bg-surface-container-low px-space-md py-space-sm rounded-xl shrink-0 self-start md:self-auto border border-surface-container-high/60">
                    <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">verified</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-headline-sm text-on-surface leading-tight font-bold">Resmi &amp; Cepat</div>
                        <div class="font-body-sm text-body-sm text-secondary">Tersinkronisasi Otomatis</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Form Section -->
    <section class="max-w-5xl mx-auto w-full px-gutter py-12">
        @if(session('success_lamaran'))
            <div class="mb-8 p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 flex items-start gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-1">Pendaftaran Berhasil!</h4>
                    <p class="text-sm leading-relaxed">{{ session('success_lamaran') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-800 shadow-sm">
                <div class="flex items-center gap-2 mb-2 font-bold text-base">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>Terdapat beberapa kesalahan pengisian form:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-10 shadow-lg border border-surface-container-high/60">
            <form action="{{ route('lamaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="formPelamaran">
                @csrf

                <!-- Section 1: Identitas Pribadi -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-6">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">1</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Data Pribadi &amp; Kontak</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="flex flex-col gap-1.5">
                            <label for="nama_lengkap" class="font-label-md text-sm font-bold text-on-surface">
                                Nama Lengkap <span class="text-primary">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_lengkap" 
                                   name="nama_lengkap" 
                                   value="{{ old('nama_lengkap') }}" 
                                   placeholder="Contoh: Budi Santoso" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- Domisili -->
                        <div class="flex flex-col gap-1.5">
                            <label for="domisili" class="font-label-md text-sm font-bold text-on-surface">
                                Domisili <span class="text-primary">*</span>
                            </label>
                            <input type="text" 
                                   id="domisili" 
                                   name="domisili" 
                                   value="{{ old('domisili') }}" 
                                   placeholder="Contoh: Jakarta Barat, DKI Jakarta" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="font-label-md text-sm font-bold text-on-surface">
                                Email Aktif <span class="text-primary">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Contoh: nama@gmail.com" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- No HP / WhatsApp -->
                        <div class="flex flex-col gap-1.5">
                            <label for="no_hp" class="font-label-md text-sm font-bold text-on-surface">
                                No Handphone / WhatsApp <span class="text-primary">*</span>
                            </label>
                            <input type="tel" 
                                   id="no_hp" 
                                   name="no_hp" 
                                   value="{{ old('no_hp') }}" 
                                   placeholder="Contoh: 081234567890" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Riwayat Pendidikan -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-6">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">2</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Latar Belakang Pendidikan</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pendidikan Terakhir -->
                        <div class="flex flex-col gap-1.5">
                            <label for="pendidikan_terakhir" class="font-label-md text-sm font-bold text-on-surface">
                                Pendidikan Terakhir <span class="text-primary">*</span>
                            </label>
                            <select id="pendidikan_terakhir" 
                                    name="pendidikan_terakhir" 
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm">
                                <option value="" disabled {{ old('pendidikan_terakhir') ? '' : 'selected' }}>Pilih Jenjang Pendidikan</option>
                                @foreach(['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2'] as $p)
                                    <option value="{{ $p }}" {{ old('pendidikan_terakhir') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jurusan / Prodi -->
                        <div class="flex flex-col gap-1.5">
                            <label for="jurusan_prodi" class="font-label-md text-sm font-bold text-on-surface">
                                Jurusan / Prodi <span class="text-xs text-secondary font-normal">(Boleh dikosongkan jika SD/SMP)</span>
                            </label>
                            <input type="text" 
                                   id="jurusan_prodi" 
                                   name="jurusan_prodi" 
                                   value="{{ old('jurusan_prodi') }}" 
                                   placeholder="Contoh: Teknik Mesin, Sastra Jepang, dll" 
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- Nama Universitas / Sekolah -->
                        <div class="flex flex-col gap-1.5">
                            <label for="nama_institusi" class="font-label-md text-sm font-bold text-on-surface">
                                Nama Universitas / Sekolah <span class="text-primary">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_institusi" 
                                   name="nama_institusi" 
                                   value="{{ old('nama_institusi') }}" 
                                   placeholder="Contoh: SMKN 1 Jakarta / Universitas Indonesia" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- IPK / Nilai -->
                        <div class="flex flex-col gap-1.5">
                            <label for="ipk_nilai" class="font-label-md text-sm font-bold text-on-surface">
                                IPK / Nilai Rata-rata <span class="text-xs text-secondary font-normal">(Opsional)</span>
                            </label>
                            <input type="text" 
                                   id="ipk_nilai" 
                                   name="ipk_nilai" 
                                   value="{{ old('ipk_nilai') }}" 
                                   placeholder="Contoh: 3.50 atau 85.5" 
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pengalaman & Posisi Yang Dilamar -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-6">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">3</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Posisi &amp; Pengalaman Kerja</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Posisi Yang Dilamar (Searchable Dropdown) -->
                        <div class="flex flex-col gap-1.5 relative" id="posisiDropdownContainer">
                            <label for="posisi_dilamar" class="font-label-md text-sm font-bold text-on-surface">
                                Posisi yang Dilamar <span class="text-primary">*</span>
                            </label>

                            <!-- Hidden input to submit the actual value -->
                            <input type="hidden" 
                                   id="posisi_dilamar" 
                                   name="posisi_dilamar" 
                                   value="{{ old('posisi_dilamar') }}" 
                                   required />

                            <!-- Interactive Search / Display Input -->
                            <div class="relative">
                                <input type="text" 
                                       id="posisiSearchInput" 
                                       autocomplete="off"
                                       value="{{ old('posisi_dilamar') }}" 
                                       placeholder="Ketik untuk mencari atau pilih posisi..." 
                                       class="w-full pl-4 pr-10 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm transition-all"/>
                                
                                <button type="button" 
                                        id="togglePosisiBtn" 
                                        tabindex="-1"
                                        aria-label="Toggle dropdown posisi"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center text-secondary hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200" id="dropdownArrow">expand_more</span>
                                </button>
                            </div>

                            <!-- Dropdown Menu List -->
                            <div id="posisiDropdownList" 
                                 class="hidden absolute left-0 right-0 top-full mt-1.5 bg-surface-container-lowest border border-surface-container-high rounded-xl shadow-xl z-50 max-h-64 overflow-y-auto py-1 divide-y divide-surface-container-low">
                                @if(isset($positions) && count($positions) > 0)
                                    @foreach($positions as $pos)
                                        <div class="posisi-option px-4 py-2.5 hover:bg-surface-container cursor-pointer text-sm text-on-surface flex items-center justify-between transition-colors"
                                             data-value="{{ $pos }}">
                                            <span>{{ $pos }}</span>
                                            <span class="material-symbols-outlined text-[16px] text-primary opacity-0 check-icon">check</span>
                                        </div>
                                    @endforeach
                                @endif
                                <div id="posisiNoResult" class="hidden px-4 py-3 text-sm text-secondary italic text-center">
                                    Posisi tidak ditemukan dalam daftar standar.<br>
                                    <span class="text-xs text-primary font-medium">Tekan Enter atau klik di luar untuk menggunakan teks yang Anda ketik.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Ekspektasi Gaji -->
                        <div class="flex flex-col gap-1.5">
                            <label for="ekspektasi_gaji" class="font-label-md text-sm font-bold text-on-surface">
                                Ekspektasi Gaji <span class="text-primary">*</span>
                            </label>
                            <input type="text" 
                                   id="ekspektasi_gaji" 
                                   name="ekspektasi_gaji" 
                                   value="{{ old('ekspektasi_gaji') }}" 
                                   placeholder="Contoh: Rp 5.000.000 atau Sesuai UMR / Standar Jepang" 
                                   required
                                   class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm"/>
                        </div>

                        <!-- Kapan Bisa Bekerja -->
                        <div class="flex flex-col gap-1.5">
                            <label for="kapan_bisa_bekerja" class="font-label-md text-sm font-bold text-on-surface">
                                Kapan Bisa Bekerja? <span class="text-primary">*</span>
                            </label>
                            <select id="kapan_bisa_bekerja" 
                                    name="kapan_bisa_bekerja" 
                                    required
                                    class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm">
                                <option value="" disabled {{ old('kapan_bisa_bekerja') ? '' : 'selected' }}>Pilih Kesiapan</option>
                                <option value="Secepatnya" {{ old('kapan_bisa_bekerja') == 'Secepatnya' ? 'selected' : '' }}>Secepatnya</option>
                                <option value="1 Minggu" {{ old('kapan_bisa_bekerja') == '1 Minggu' ? 'selected' : '' }}>1 Minggu</option>
                                <option value="1 Bulan" {{ old('kapan_bisa_bekerja') == '1 Bulan' ? 'selected' : '' }}>1 Bulan</option>
                            </select>
                        </div>

                        <!-- Tipe Pengalaman -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-sm font-bold text-on-surface">
                                Pengalaman Kerja <span class="text-primary">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-surface-container-high bg-surface-container-low cursor-pointer hover:border-primary transition-colors">
                                    <input type="radio" 
                                           name="tipe_pengalaman" 
                                           value="non_pengalaman" 
                                           {{ old('tipe_pengalaman', 'non_pengalaman') === 'non_pengalaman' ? 'checked' : '' }}
                                           onchange="togglePengalamanForm(false)"
                                           class="text-primary focus:ring-primary"/>
                                    <span class="text-sm font-medium text-on-surface">Non Pengalaman (Fresh Graduate)</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-surface-container-high bg-surface-container-low cursor-pointer hover:border-primary transition-colors">
                                    <input type="radio" 
                                           name="tipe_pengalaman" 
                                           value="pengalaman" 
                                           {{ old('tipe_pengalaman') === 'pengalaman' ? 'checked' : '' }}
                                           onchange="togglePengalamanForm(true)"
                                           class="text-primary focus:ring-primary"/>
                                    <span class="text-sm font-medium text-on-surface">Berpengalaman</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Pengalaman (Muncul jika Berpengalaman) -->
                        <div id="pengalamanContainer" class="md:col-span-2 {{ old('tipe_pengalaman') === 'pengalaman' ? '' : 'hidden' }} flex flex-col gap-1.5">
                            <label for="deskripsi_pengalaman" class="font-label-md text-sm font-bold text-on-surface">
                                Detail Riwayat Pengalaman Kerja <span class="text-primary">*</span>
                            </label>
                            <textarea id="deskripsi_pengalaman" 
                                      name="deskripsi_pengalaman" 
                                      rows="4" 
                                      placeholder="Tuliskan nama perusahaan sebelumnya, posisi/jabatan, durasi bekerja, serta deskripsi singkat tugas yang pernah dikerjakan..."
                                      class="w-full px-4 py-3 rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface text-sm">{{ old('deskripsi_pengalaman') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Upload CV & Pernyataan -->
                <div>
                    <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-6">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">4</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Upload Dokumen CV &amp; Persetujuan</h3>
                    </div>

                    <div class="space-y-6">
                        <!-- Upload CV -->
                        <div class="flex flex-col gap-1.5">
                            <label for="cv_file" class="font-label-md text-sm font-bold text-on-surface">
                                Upload Curriculum Vitae (CV) <span class="text-primary">*</span>
                            </label>
                            <div class="border-2 border-dashed border-surface-container-high hover:border-primary/60 rounded-2xl p-6 bg-surface-container-low/50 text-center transition-colors relative">
                                <input type="file" 
                                       id="cv_file" 
                                       name="cv_file" 
                                       accept=".pdf,.doc,.docx" 
                                       required
                                       onchange="displayFileName(this)"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"/>
                                <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[28px]">upload_file</span>
                                    </div>
                                    <p class="font-medium text-sm text-on-surface" id="fileNamePreview">
                                        Klik untuk memilih file CV atau tarik file ke sini
                                    </p>
                                    <p class="text-xs text-secondary">
                                        Format didukung: <strong>PDF, DOC, DOCX</strong> (Maksimal 10 MB). Nama file otomatis diselaraskan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Pernyataan Benar -->
                        <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container-high">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="pernyataan_benar" 
                                       value="1" 
                                       required
                                       {{ old('pernyataan_benar') ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 rounded text-primary focus:ring-primary border-surface-container-high"/>
                                <span class="text-sm text-on-surface leading-relaxed">
                                    <strong>Pernyataan:</strong> Saya menyatakan dengan sebenar-benarnya bahwa seluruh data dan dokumen yang saya berikan dalam formulir pelamaran ini adalah benar dan dapat dipertanggungjawabkan sesuai hukum yang berlaku.
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-4 border-t border-surface-container">
                    <a href="{{ route('index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl text-secondary hover:text-on-surface font-label-md text-sm font-semibold transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            id="btnSubmit"
                            class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary hover:bg-tertiary text-on-primary font-headline-sm text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        <span>Kirim Lamaran Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function togglePengalamanForm(isExperienced) {
        const container = document.getElementById('pengalamanContainer');
        const textarea = document.getElementById('deskripsi_pengalaman');
        if (isExperienced) {
            container.classList.remove('hidden');
            textarea.setAttribute('required', 'required');
            textarea.focus();
        } else {
            container.classList.add('hidden');
            textarea.removeAttribute('required');
            textarea.value = '';
        }
    }

    function displayFileName(input) {
        const preview = document.getElementById('fileNamePreview');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            preview.innerHTML = `<span class="text-primary font-bold">File terpilih:</span> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        } else {
            preview.innerText = 'Klik untuk memilih file CV atau tarik file ke sini';
        }
    }

    // Searchable Dropdown for Posisi yang Dilamar
    (function initPosisiDropdown() {
        const container = document.getElementById('posisiDropdownContainer');
        const hiddenInput = document.getElementById('posisi_dilamar');
        const searchInput = document.getElementById('posisiSearchInput');
        const dropdownList = document.getElementById('posisiDropdownList');
        const toggleBtn = document.getElementById('togglePosisiBtn');
        const dropdownArrow = document.getElementById('dropdownArrow');
        const noResult = document.getElementById('posisiNoResult');
        const options = document.querySelectorAll('.posisi-option');

        if (!searchInput || !dropdownList) return;

        function openDropdown() {
            dropdownList.classList.remove('hidden');
            if (dropdownArrow) dropdownArrow.classList.add('rotate-180');
        }

        function closeDropdown() {
            dropdownList.classList.add('hidden');
            if (dropdownArrow) dropdownArrow.classList.remove('rotate-180');
        }

        function filterOptions(query) {
            const q = query.trim().toLowerCase();
            let visibleCount = 0;

            options.forEach(opt => {
                const text = opt.getAttribute('data-value').toLowerCase();
                if (text.includes(q)) {
                    opt.classList.remove('hidden');
                    visibleCount++;
                } else {
                    opt.classList.add('hidden');
                }
            });

            if (visibleCount === 0 && q.length > 0) {
                noResult.classList.remove('hidden');
            } else {
                noResult.classList.add('hidden');
            }
        }

        function updateCheckIcons(selectedValue) {
            options.forEach(opt => {
                const val = opt.getAttribute('data-value');
                const checkIcon = opt.querySelector('.check-icon');
                if (checkIcon) {
                    if (val === selectedValue) {
                        checkIcon.classList.remove('opacity-0');
                        opt.classList.add('bg-primary/5', 'text-primary', 'font-semibold');
                    } else {
                        checkIcon.classList.add('opacity-0');
                        opt.classList.remove('bg-primary/5', 'text-primary', 'font-semibold');
                    }
                }
            });
        }

        // Initialize with default or old value
        if (hiddenInput.value) {
            searchInput.value = hiddenInput.value;
            updateCheckIcons(hiddenInput.value);
        }

        // Toggle button click
        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (dropdownList.classList.contains('hidden')) {
                openDropdown();
                filterOptions(searchInput.value);
                searchInput.focus();
            } else {
                closeDropdown();
            }
        });

        // Search input events
        searchInput.addEventListener('focus', () => {
            openDropdown();
            filterOptions(searchInput.value);
        });

        searchInput.addEventListener('input', () => {
            openDropdown();
            hiddenInput.value = searchInput.value;
            filterOptions(searchInput.value);
            updateCheckIcons(searchInput.value);
        });

        // Option item click
        options.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const selectedVal = opt.getAttribute('data-value');
                hiddenInput.value = selectedVal;
                searchInput.value = selectedVal;
                updateCheckIcons(selectedVal);
                closeDropdown();
                // trigger change
                hiddenInput.dispatchEvent(new Event('change'));
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                closeDropdown();
                // If user typed something custom, ensure hidden input matches
                if (searchInput.value.trim() !== '') {
                    hiddenInput.value = searchInput.value.trim();
                }
            }
        });

        // Support Enter key inside input
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                // If there's an exact or first visible match, pick it, otherwise keep custom text
                const firstVisible = Array.from(options).find(opt => !opt.classList.contains('hidden'));
                if (firstVisible && searchInput.value.trim().length > 0) {
                    const match = Array.from(options).find(opt => opt.getAttribute('data-value').toLowerCase() === searchInput.value.trim().toLowerCase());
                    if (match) {
                        hiddenInput.value = match.getAttribute('data-value');
                        searchInput.value = hiddenInput.value;
                    } else {
                        hiddenInput.value = searchInput.value.trim();
                    }
                } else {
                    hiddenInput.value = searchInput.value.trim();
                }
                updateCheckIcons(hiddenInput.value);
                closeDropdown();
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    })();

    document.getElementById('formPelamaran')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = `<span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span><span>Mengirim Berkas...</span>`;
    });
</script>
@endpush
@endsection
