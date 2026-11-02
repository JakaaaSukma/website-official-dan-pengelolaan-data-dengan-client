@extends('layouts.company')

@section('content')
<style>
    .gdrive-dashboard {
        color: #1f1f1f;
    }

    .gdrive-dashboard .card-custom {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.04);
    }

    .gdrive-dashboard .hero-card {
        background:
            radial-gradient(circle at top right, rgba(66, 133, 244, 0.12), transparent 35%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.99), rgba(240, 247, 255, 0.95));
        border: 1px solid rgba(66, 133, 244, 0.15);
        border-radius: 24px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.04);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .copy-box {
        background: #f8f9fa;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-family: monospace;
        font-size: 0.9rem;
        word-break: break-all;
    }
</style>

<div class="container gdrive-dashboard py-3">
    <!-- Breadcrumb / Nav -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('company.pelamar-non-member') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pelamar Non Member
        </a>
        <span class="text-muted small">Integrasi Cloud Storage</span>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm rounded-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Hero Card -->
    <div class="hero-card p-4 p-lg-5 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold small mb-3">
                    <i class="fab fa-google-drive"></i>
                    <span>Google Drive Integration</span>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">Koneksi Google Drive CV Pelamar</h1>
                <p class="text-muted mb-0 lead fs-6">
                    Sistem ini mengunggah berkas CV pelamar kerja secara otomatis ke folder <strong class="text-primary">"pelamar non member"</strong> di Google Drive Anda, lalu langsung menghapus salinan file fisik dari server lokal sehingga penggunaan disk server tetap <strong class="text-success">0%</strong>.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                @if($isConnected)
                    <div class="status-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 mb-3">
                        <i class="fas fa-check-circle"></i>
                        <span>Google Drive Terhubung</span>
                    </div>
                @else
                    <div class="status-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 mb-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Belum Terhubung</span>
                    </div>
                @endif
                <div class="text-muted small">Folder Target: <code>{{ $folderId }}</code></div>
            </div>
        </div>
    </div>

    <!-- Main Content Cards -->
    <div class="row g-4">
        <!-- Status & Aksi Koneksi -->
        <div class="col-lg-7">
            <div class="card-custom p-4 p-lg-5 h-100">
                <h3 class="h5 fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <i class="fas fa-plug text-primary"></i> Status & Otorisasi Akun
                </h3>

                @if($isConnected)
                    <div class="p-4 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="fs-2 text-success">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold text-success mb-1">Aplikasi Sudah Memiliki Izin Akses Google Drive</h4>
                                <p class="text-muted small mb-0">
                                    Token otorisasi OAuth2 tersimpan di server. File CV yang diunggah oleh pelamar akan otomatis dikirimkan ke Google Drive Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button type="button" id="btnTestUpload" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="runTestUpload()">
                            <i class="fas fa-paper-plane me-2"></i> Uji Coba Upload File Sekarang
                        </button>

                        <a href="{{ route('gdrive.redirect') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-sync-alt me-1"></i> Sambung Ulang Akun
                        </a>

                        <form action="{{ route('gdrive.disconnect') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi Google Drive?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                                <i class="fas fa-unlink me-1"></i> Putuskan Koneksi
                            </button>
                        </form>
                    </div>

                    <div id="testUploadResult" class="d-none"></div>

                @else
                    <div class="p-4 rounded-4 bg-light border mb-4">
                        <p class="text-dark fw-semibold mb-2">
                            Hubungkan akun Google pemilik folder <span class="text-primary">"pelamar non member"</span> (gyokairesepsionis@gmail.com).
                        </p>
                        <p class="text-muted small mb-0">
                            Klik tombol di bawah untuk login ke akun Google dan mengizinkan Gyokai menyimpan berkas lamaran ke Drive Anda.
                        </p>
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('gdrive.redirect') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fab fa-google fs-5"></i>
                            <span>Hubungkan dengan Google Drive</span>
                        </a>
                    </div>

                    <hr class="my-4">

                    <h4 class="h6 fw-bold text-dark mb-2">Atau Masukkan Authorization Code Manual</h4>
                    <p class="text-muted small mb-3">
                        Jika Google tidak otomatis kembali ke halaman ini, masukkan kode verifikasi yang diberikan oleh Google:
                    </p>

                    <form action="{{ route('gdrive.manual-auth') }}" method="POST" class="d-flex flex-column gap-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="auth_code" class="form-control rounded-start-pill ps-3" placeholder="Paste kode otorisasi dari Google di sini..." required>
                            <button type="submit" class="btn btn-dark rounded-end-pill px-4 fw-bold">
                                Simpan Token
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Panduan Pengaturan Google Cloud Console -->
        <div class="col-lg-5">
            <div class="card-custom p-4 p-lg-5 h-100">
                <h3 class="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fas fa-cog text-secondary"></i> Pengaturan Redirect URI
                </h3>

                <p class="text-muted small mb-3">
                    Jika muncul pesan <code>Error 400: redirect_uri_mismatch</code> saat login, pastikan URL berikut sudah terdaftar di <strong>Google Cloud Console</strong>:
                </p>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Authorized redirect URI saat ini:</label>
                    <div class="copy-box d-flex align-items-center justify-content-between">
                        <span id="uriToCopy">{{ $currentRedirectUri }}</span>
                        <button class="btn btn-link btn-sm text-primary p-0 text-decoration-none" onclick="copyUri()">
                            <i class="fas fa-copy"></i> Salin
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Tambahkan juga URI cadangan (localhost):</label>
                    <div class="copy-box d-flex align-items-center justify-content-between">
                        <span>http://localhost:8000/gdrive/callback</span>
                    </div>
                </div>

                <div class="alert alert-light border small text-muted rounded-3 mb-0">
                    <div class="fw-bold text-dark mb-1"><i class="fas fa-info-circle text-info me-1"></i> Cara menambahkan:</div>
                    <ol class="ps-3 mb-0">
                        <li>Buka <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="fw-bold">Google Cloud Console > Credentials</a>.</li>
                        <li>Klik nama <strong>OAuth 2.0 Client IDs</strong> Anda.</li>
                        <li>Di bagian <strong>Authorized redirect URIs</strong>, klik <em>Add URI</em> lalu tempelkan URL di atas.</li>
                        <li>Klik <strong>Save</strong> lalu klik "Hubungkan dengan Google Drive".</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyUri() {
    const text = document.getElementById('uriToCopy').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('URI berhasil disalin ke clipboard: ' + text);
    });
}

function runTestUpload() {
    const btn = document.getElementById('btnTestUpload');
    const resultBox = document.getElementById('testUploadResult');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengunggah file uji coba...';
    resultBox.className = 'd-none';

    fetch('{{ route('gdrive.test') }}')
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Uji Coba Upload File Sekarang';
            
            resultBox.className = 'mt-4 p-4 rounded-4 ' + (data.success ? 'bg-success bg-opacity-10 border border-success' : 'bg-danger bg-opacity-10 border border-danger');
            
            if (data.success) {
                resultBox.innerHTML = `
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div>
                            <div class="fw-bold text-success mb-1">Upload Berhasil!</div>
                            <div class="small text-muted mb-2">File tes berhasil diunggah langsung ke folder Google Drive Anda.</div>
                            <a href="${data.web_view_link}" target="_blank" class="btn btn-sm btn-success rounded-pill fw-bold">
                                <i class="fab fa-google-drive me-1"></i> Buka File Tes di Google Drive
                            </a>
                        </div>
                    </div>
                `;
            } else {
                resultBox.innerHTML = `
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-exclamation-triangle fs-3 text-danger"></i>
                        <div>
                            <div class="fw-bold text-danger mb-1">Gagal Mengunggah:</div>
                            <div class="small text-muted">${data.error || data.message || 'Terjadi kesalahan tidak terduga.'}</div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Uji Coba Upload File Sekarang';
            resultBox.className = 'mt-4 p-4 rounded-4 bg-danger bg-opacity-10 border border-danger';
            resultBox.innerHTML = '<div class="text-danger small fw-bold">Terjadi kesalahan koneksi server: ' + err.message + '</div>';
        });
}
</script>
@endsection
