@extends('layouts.company')

@section('content')
<style>
    .pelamar-dashboard {
        color: #1f1f1f;
    }

    .pelamar-dashboard .hero-card,
    .pelamar-dashboard .filter-card,
    .pelamar-dashboard .table-card,
    .pelamar-dashboard .stat-card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.04);
    }

    .pelamar-dashboard .hero-card {
        background:
            radial-gradient(circle at top right, rgba(255, 0, 0, 0.1), transparent 30%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.99), rgba(255, 246, 246, 0.94));
    }

    .status-badge {
        font-size: 0.78rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .status-menunggu_review { background: #fff3cd; color: #856404; }
    .status-diproses { background: #cce5ff; color: #004085; }
    .status-dihubungi { background: #d4edda; color: #155724; }
    .status-diterima { background: #d1e7dd; color: #0f5132; }
    .status-ditolak { background: #f8d7da; color: #721c24; }
</style>

<div class="container pelamar-dashboard">
    <!-- Header Hero Banner -->
    <div class="hero-card p-4 p-lg-5 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-danger bg-opacity-10 text-danger fw-bold small mb-3">
                    <i class="fas fa-user-tie"></i>
                    <span>Portal Penerimaan Dokumen Terbuka</span>
                </div>
                <h1 class="h2 fw-bold text-dark mb-2">Pelamar Non Member</h1>
                <p class="text-muted mb-0 lead fs-6">
                    Data pelamar kerja langsung yang masuk melalui halaman formulir publik website. Berkas CV otomatis tersinkronisasi dan tersimpan pada Google Drive folder <strong class="text-danger">"pelamar non member"</strong>.
                </p>
                <div class="mt-3 d-flex align-items-center gap-2">
                    <a href="{{ route('gdrive.connect') }}" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3">
                        <i class="fab fa-google-drive me-1"></i> Pengaturan Google Drive
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-card p-3 text-center">
                            <span class="text-muted small d-block">Total Pelamar</span>
                            <span class="h3 fw-bold text-dark mb-0">{{ $totalApplicants }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3 text-center">
                            <span class="text-muted small d-block">Menunggu Review</span>
                            <span class="h3 fw-bold text-warning mb-0">{{ $totalReview }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3 text-center">
                            <span class="text-muted small d-block">Sudah Dihubungi</span>
                            <span class="h3 fw-bold text-info mb-0">{{ $totalDihubungi }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3 text-center">
                            <span class="text-muted small d-block">Diterima</span>
                            <span class="h3 fw-bold text-success mb-0">{{ $totalDiterima }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="filter-card p-4 mb-4">
        <form method="GET" action="{{ route('company.pelamar-non-member') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Nama, posisi, domisili, email, no hp..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu_review" {{ request('status') == 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="dihubungi" {{ request('status') == 'dihubungi' ? 'selected' : '' }}>Sudah Dihubungi</option>
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Pendidikan</label>
                <select name="pendidikan" class="form-select">
                    <option value="">Semua Pendidikan</option>
                    @foreach(['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2'] as $p)
                        <option value="{{ $p }}" {{ request('pendidikan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Pengalaman</label>
                <select name="pengalaman" class="form-select">
                    <option value="">Semua</option>
                    <option value="non_pengalaman" {{ request('pengalaman') == 'non_pengalaman' ? 'selected' : '' }}>Non Pengalaman</option>
                    <option value="pengalaman" {{ request('pengalaman') == 'pengalaman' ? 'selected' : '' }}>Berpengalaman</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('company.pelamar-non-member') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="table-card p-4">
        <div class="d-flex align-items-center justify-between pb-3 mb-3 border-bottom">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list me-2 text-danger"></i>Daftar Berkas Pelamar
            </h5>
            <span class="badge bg-light text-dark border">Total: {{ $applicants->total() }} Pelamar</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama &amp; Kontak</th>
                        <th>Pendidikan &amp; Almamater</th>
                        <th>Posisi &amp; Gaji</th>
                        <th>Pengalaman</th>
                        <th>Kesiapan Kerja</th>
                        <th>Berkas CV</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $index => $item)
                        <tr>
                            <td class="text-muted fw-bold">{{ $applicants->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama_lengkap }}</div>
                                <div class="small text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $item->domisili }}</div>
                                <div class="small text-muted"><i class="fas fa-envelope me-1"></i>{{ $item->email }}</div>
                                @if($item->no_hp)
                                    <div class="small">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_hp) }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                            <i class="fab fa-whatsapp me-1"></i>{{ $item->no_hp }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div><span class="badge bg-secondary">{{ $item->pendidikan_terakhir }}</span> {{ $item->jurusan_prodi ?? '' }}</div>
                                <div class="small text-dark fw-semibold mt-1">{{ $item->nama_institusi }}</div>
                                @if($item->ipk_nilai)
                                    <div class="small text-muted">IPK / Nilai: <strong>{{ $item->ipk_nilai }}</strong></div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-danger">{{ $item->posisi_dilamar }}</div>
                                <div class="small text-muted">Ekspektasi: {{ $item->ekspektasi_gaji }}</div>
                            </td>
                            <td>
                                @if($item->tipe_pengalaman === 'pengalaman')
                                    <span class="badge bg-primary text-white">Berpengalaman</span>
                                    @if($item->deskripsi_pengalaman)
                                        <button type="button" class="btn btn-link btn-sm p-0 d-block mt-1 text-decoration-none small" 
                                                onclick="showDetailModal('{{ addslashes($item->nama_lengkap) }}', '{{ addslashes($item->posisi_dilamar) }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $item->deskripsi_pengalaman)) }}')">
                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                        </button>
                                    @endif
                                @else
                                    <span class="badge bg-light text-muted border">Fresh Graduate</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->kapan_bisa_bekerja }}</span>
                                <div class="small text-muted mt-1">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($item->gdrive_web_view_link)
                                        <a href="{{ $item->gdrive_web_view_link }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="fab fa-google-drive text-warning"></i>
                                            <span>Buka CV di G-Drive</span>
                                        </a>
                                    @endif
                                    
                                    @if($item->cv_file_path)
                                        <a href="{{ asset('storage/' . $item->cv_file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            <span>Unduh File Cadangan</span>
                                        </a>
                                    @elseif(!$item->gdrive_web_view_link)
                                        <span class="text-muted small italic">Tidak ada berkas</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('company.pelamar-non-member.update-status', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm status-badge status-{{ $item->status }}" onchange="this.form.submit()">
                                        <option value="menunggu_review" {{ $item->status == 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                                        <option value="diproses" {{ $item->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="dihubungi" {{ $item->status == 'dihubungi' ? 'selected' : '' }}>Sudah Dihubungi</option>
                                        <option value="diterima" {{ $item->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                        <option value="ditolak" {{ $item->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('company.pelamar-non-member.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelamar {{ $item->nama_lengkap }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pelamar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6>Belum ada data pelamar non-member yang masuk.</h6>
                                <p class="small mb-0">Setiap formulir pelamaran yang diisi pengunjung website akan langsung muncul di halaman ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $applicants->links() }}
        </div>
    </div>
</div>

<!-- Modal Detail Pengalaman -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="detailModalLabel">Riwayat Pengalaman Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <span class="text-muted small d-block">Nama Pelamar:</span>
                    <strong id="modalNamaPelamar" class="text-dark"></strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Posisi yang Dilamar:</span>
                    <span id="modalPosisiPelamar" class="badge bg-danger"></span>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Deskripsi Pengalaman:</span>
                    <div id="modalDescPengalaman" class="p-3 rounded-3 bg-light border text-secondary small" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetailModal(nama, posisi, desc) {
        document.getElementById('modalNamaPelamar').innerText = nama;
        document.getElementById('modalPosisiPelamar').innerText = posisi;
        document.getElementById('modalDescPengalaman').innerText = desc || 'Tidak ada deskripsi.';
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
</script>
@endsection
