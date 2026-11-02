@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container" style="max-width: 960px;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #e63946;">
                    <i class="fas fa-upload me-2"></i>Unggah &amp; Kelola Aset Gambar
                </h2>
                <p class="text-muted mb-0 small">
                    Pilih penempatan aset gambar dari dropdown di bawah, lalu pilih file gambar baru untuk diunggah.
                </p>
            </div>
            <a href="{{ route('admin.site-assets') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.site-assets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- 1. DROPDOWN PENEMPATAN TETAP / STANDBY -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <span class="badge bg-danger rounded-pill px-2.5 py-1">Langkah 1</span>
                            <span>Pilih Penempatan Gambar di Website: <span class="text-danger">*</span></span>
                        </label>
                        <select id="presetSelector" 
                                class="form-select form-select-lg border-2 border-danger-subtle shadow-xs" 
                                onchange="applyPreset(this)"
                                required>
                            <option value="">-- Silakan Pilih Lokasi Penempatan Gambar --</option>
                            
                            <optgroup label="🏠 Halaman Beranda (Home)">
                                <option value="home_hero_banner"
                                        data-title="Banner Hero Utama Beranda"
                                        data-page="Beranda (Home)"
                                        data-placement="Header Hero Beranda (Samping Teks Slogan Utama)"
                                        data-size="800 x 1000 px (Portrait / Rasio 4:5)"
                                        data-desc="Foto siswa pelatihan / seragam resmi LPK Gyokai dengan kartu Siap Penempatan Industri.">
                                    [Beranda] Banner Hero Utama (Samping Slogan)
                                </option>
                            </optgroup>

                            <optgroup label="💼 Halaman Layanan Utama (/our-services)">
                                <option value="service_program_01_pemagangan"
                                        data-title="Layanan Utama: Program 01 Pemagangan"
                                        data-page="Layanan (Our Services)"
                                        data-placement="Halaman Layanan Utama (/our-services) - Kartu Program 01 Pemagangan"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto dokumentasi pelatihan teknis / praktik kerja pemagang untuk Program 01.">
                                    [Layanan Utama] Program 01 : Pemagangan Industri
                                </option>
                                <option value="service_program_02_labor_supply"
                                        data-title="Layanan Utama: Program 02 Labor Supply"
                                        data-page="Layanan (Our Services)"
                                        data-placement="Halaman Layanan Utama (/our-services) - Kartu Program 02 Labor Supply"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto tenaga kerja industri manufaktur / lini perakitan suplai tenaga kerja siap pakai.">
                                    [Layanan Utama] Program 02 : Labor Supply
                                </option>
                                <option value="service_program_03_training"
                                        data-title="Layanan Utama: Program 03 Training Development"
                                        data-page="Layanan (Our Services)"
                                        data-placement="Halaman Layanan Utama (/our-services) - Kartu Program 03 Training Development"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto suasana kelas diklat, simulasi instruktur, atau modul kurikulum vokasi.">
                                    [Layanan Utama] Program 03 : Training Development
                                </option>
                                <option value="service_program_04_outsourcing"
                                        data-title="Layanan Utama: Program 04 Outsourcing Tenaga Kerja"
                                        data-page="Layanan (Our Services)"
                                        data-placement="Halaman Layanan Utama (/our-services) - Kartu Program 04 Outsourcing Tenaga Kerja"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto tim pekerja outsourcing (cleaning service, security, atau staf operasional pabrik).">
                                    [Layanan Utama] Program 04 : Outsourcing Tenaga Kerja
                                </option>
                            </optgroup>

                            <optgroup label="📋 Sub Page Layanan (Benefit Detail Pages)">
                                <option value="benefit_pemagangan_banner"
                                        data-title="Sub Layanan: Benefit Pemagangan"
                                        data-page="Sub Layanan: Benefit Pemagangan"
                                        data-placement="Halaman Detail Benefit Pemagangan (/benefit-pemagangan) - Banner Unduh Company Profile"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto representatif siswa diklat pemagangan untuk penempatan ke Jepang.">
                                    [Sub Layanan] Benefit Pemagangan (/benefit-pemagangan)
                                </option>
                                <option value="benefit_labor_supply_banner"
                                        data-title="Sub Layanan: Benefit Labor Supply"
                                        data-page="Sub Layanan: Benefit Labor Supply"
                                        data-placement="Halaman Detail Benefit Labor Supply (/benefit-labor-supply) - Banner Unduh Company Profile &amp; Konsultasi"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto tenaga kerja industri manufaktur untuk penempatan kerja fleksibel.">
                                    [Sub Layanan] Benefit Labor Supply (/benefit-labor-supply)
                                </option>
                                <option value="benefit_training_banner"
                                        data-title="Sub Layanan: Benefit Training Development"
                                        data-page="Sub Layanan: Benefit Training Development"
                                        data-placement="Halaman Detail Benefit Training (/benefit-training) - Banner Unduh Silabus &amp; Modul Diklat"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto proses pelatihan kurikulum khusus TNA dan kaizen perusahaan.">
                                    [Sub Layanan] Benefit Training Development (/benefit-training)
                                </option>
                                <option value="benefit_outsourcing_banner"
                                        data-title="Sub Layanan: Benefit Outsourcing"
                                        data-page="Sub Layanan: Benefit Outsourcing"
                                        data-placement="Halaman Detail Benefit Outsourcing (/benefit-outsourcing) - Banner Formulir Kerja Sama Alih Daya"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto staf operasional alih daya profesional Gyokai.">
                                    [Sub Layanan] Benefit Outsourcing (/benefit-outsourcing)
                                </option>
                            </optgroup>

                            <optgroup label="🏢 Halaman Lainnya">
                                <option value="about_hero_banner"
                                        data-title="Tentang Kami: Banner Gedung &amp; Fasilitas"
                                        data-page="Tentang Kami (About Us)"
                                        data-placement="Header visual atas halaman Tentang Kami (/about) di sebelah kanan visi misi"
                                        data-size="800 x 600 px (Landscape)"
                                        data-desc="Foto gedung workshop teknik dan laboratorium bahasa Jepang.">
                                    [Tentang Kami] Banner Hero Gedung Pelatihan (/about)
                                </option>
                                <option value="contact_campus_image"
                                        data-title="Kontak: Foto Kampus / Kantor Pusat"
                                        data-page="Kontak (Contact Us)"
                                        data-placement="Halaman Kontak (/contact) - Di bawah informasi alamat &amp; peta Google Maps"
                                        data-size="1200 x 600 px (Wide Landscape)"
                                        data-desc="Foto tampak depan gedung kantor pusat / fasilitas pelatihan LPK Gyokai di Cikupa Tangerang.">
                                    [Kontak] Foto Kampus Terpadu (/contact)
                                </option>
                                <option value="career_hero_bg"
                                        data-title="Lowongan Kerja: Background Hero Header"
                                        data-page="Lowongan Kerja (Career)"
                                        data-placement="Halaman Lowongan Kerja (/blog) - Background header pencarian lowongan kerja"
                                        data-size="1920 x 480 px (Ultra-wide Banner)"
                                        data-desc="Foto latar belakang suasana kerja industri atau calon peserta lowongan kerja.">
                                    [Lowongan Kerja] Background Hero Header (/blog)
                                </option>
                                <option value="news_default_thumbnail"
                                        data-title="Berita: Thumbnail Default Berita"
                                        data-page="Berita (News)"
                                        data-placement="Halaman Berita (/news) &amp; Detail Berita (/news/{slug}) - Gambar bawaan jika artikel belum ada foto"
                                        data-size="800 x 500 px (Landscape 16:10)"
                                        data-desc="Foto thumbnail cadangan untuk artikel berita atau liputan kegiatan resmi.">
                                    [Berita] Thumbnail Default Cadangan Artikel (/news)
                                </option>
                            </optgroup>

                            <option value="custom">✏️ Penempatan Baru / Kustom Lainnya...</option>
                        </select>
                    </div>

                    <!-- CARD DETAIL PENEMPATAN AKTIF -->
                    <div id="placementInfoCard" class="alert alert-danger border-0 rounded-3 p-3 mb-4 shadow-sm" style="display: none; background: #fff5f5;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-map-marker-alt text-danger fs-4 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-danger" id="infoPageBadge">Halaman</span>
                                    <span class="fw-bold text-dark" id="infoTitle">Judul Aset</span>
                                </div>
                                <div class="text-dark small mb-1">
                                    <strong>Lokasi:</strong> <span id="infoPlacement">-</span>
                                </div>
                                <div class="text-muted small mb-1" id="infoDesc">
                                    Deskripsi...
                                </div>
                                <div class="small text-danger fw-semibold" id="infoSizeWrap">
                                    <i class="fas fa-ruler-combined me-1"></i> Rekomendasi Ukuran: <span class="badge bg-white text-dark border" id="infoSize">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. UPLOAD FILE & PREVIEW -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-pill px-2.5 py-1">Langkah 2</span>
                                <span>Preview Gambar:</span>
                            </label>
                            <div class="rounded-3 border overflow-hidden p-2 bg-light text-center shadow-2xs mb-2">
                                <img id="imagePreviewNew" 
                                     src="{{ asset('img/jelas.png') }}" 
                                     alt="Preview Aset" 
                                     class="img-fluid rounded-2" 
                                     style="max-height: 220px; object-fit: contain;">
                            </div>
                            <div class="form-text small text-center text-muted">
                                Preview otomatis tampil setelah memilih gambar.
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-pill px-2.5 py-1">Langkah 3</span>
                                <span>Pilih Berkas Gambar Baru: <span class="text-danger">*</span></span>
                            </label>
                            <div class="mb-3">
                                <input type="file" 
                                       name="image" 
                                       id="imageInputNew" 
                                       class="form-control form-control-lg border-2 border-danger-subtle" 
                                       accept="image/png, image/jpeg, image/jpg, image/webp"
                                       onchange="previewNewUpload(event)"
                                       required>
                                <div class="form-text small">
                                    Format didukung: <strong>JPG, PNG, WEBP</strong>. Maksimal ukuran <strong>5MB</strong>.
                                </div>
                            </div>

                            <!-- Opsi Edit Rincian Teknis (Bisa Dibuka Jika Ingin Mengubah) -->
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center cursor-pointer" 
                                     onclick="document.getElementById('advancedFields').classList.toggle('d-none')"
                                     style="cursor: pointer;">
                                    <span class="small fw-bold text-muted">
                                        <i class="fas fa-sliders-h me-1"></i> Sesuaikan Rincian Teknis &amp; Teks (Opsional)
                                    </span>
                                    <i class="fas fa-chevron-down text-muted small"></i>
                                </div>

                                <div id="advancedFields" class="mt-3 pt-3 border-top d-none">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark small">Nama Aset / Judul Gambar:</label>
                                        <input type="text" id="titleInput" name="title" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label fw-bold text-dark small">Halaman:</label>
                                            <input type="text" id="pageInput" name="page" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold text-dark small">Asset Key:</label>
                                            <input type="text" id="keyInput" name="asset_key" class="form-control form-control-sm font-monospace" required>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark small">Keterangan Penempatan:</label>
                                        <input type="text" id="placementInput" name="placement" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark small">Rekomendasi Ukuran:</label>
                                        <input type="text" id="sizeInput" name="recommended_size" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark small">Catatan / Deskripsi:</label>
                                        <textarea id="descInput" name="description" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold text-dark small">URL Default (Fallback):</label>
                                        <input type="url" id="defaultUrlInput" name="default_url" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.site-assets') }}" class="btn btn-secondary px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fas fa-save me-1"></i> Simpan Aset Gambar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function applyPreset(select) {
        const option = select.options[select.selectedIndex];
        const infoCard = document.getElementById('placementInfoCard');
        const advFields = document.getElementById('advancedFields');

        if (!option || option.value === '') {
            infoCard.style.display = 'none';
            document.getElementById('titleInput').value = '';
            document.getElementById('pageInput').value = '';
            document.getElementById('keyInput').value = '';
            document.getElementById('placementInput').value = '';
            document.getElementById('sizeInput').value = '';
            document.getElementById('descInput').value = '';
            return;
        }

        if (option.value === 'custom') {
            infoCard.style.display = 'none';
            advFields.classList.remove('d-none');
            document.getElementById('titleInput').value = '';
            document.getElementById('pageInput').value = '';
            document.getElementById('keyInput').value = '';
            document.getElementById('placementInput').value = '';
            document.getElementById('sizeInput').value = '';
            document.getElementById('descInput').value = '';
            document.getElementById('titleInput').focus();
            return;
        }

        // Apply preset details
        const key = option.value;
        const title = option.dataset.title || '';
        const page = option.dataset.page || '';
        const placement = option.dataset.placement || '';
        const size = option.dataset.size || '-';
        const desc = option.dataset.desc || '';

        document.getElementById('keyInput').value = key;
        document.getElementById('titleInput').value = title;
        document.getElementById('pageInput').value = page;
        document.getElementById('placementInput').value = placement;
        document.getElementById('sizeInput').value = size;
        document.getElementById('descInput').value = desc;

        // Update info card UI
        document.getElementById('infoPageBadge').innerText = page;
        document.getElementById('infoTitle').innerText = title;
        document.getElementById('infoPlacement').innerText = placement;
        document.getElementById('infoDesc').innerText = desc;
        document.getElementById('infoSize').innerText = size;
        infoCard.style.display = 'block';
    }

    function previewNewUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreviewNew');
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
