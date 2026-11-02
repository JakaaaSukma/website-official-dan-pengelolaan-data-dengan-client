@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #e63946;">
                    <i class="fas fa-photo-video me-2"></i>Kelola Aset &amp; Gambar Website
                </h2>
                <p class="text-muted mb-0 small">
                    Atur foto banner, ilustrasi program, dan gambar penting pada website publik beserta penempatan lokasinya secara transparan.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <a href="{{ route('admin.site-assets.create') }}" class="btn btn-primary fw-bold">
                    <i class="fas fa-plus me-1"></i> Tambah Aset Gambar
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter by Page -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3">
                <form action="{{ route('admin.site-assets') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="form-label mb-0 fw-semibold text-muted small">
                            <i class="fas fa-filter me-1"></i>Filter Halaman:
                        </label>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <select name="page_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Halaman ({{ count($siteAssets) }} Gambar) --</option>
                            @foreach($pages as $pageName)
                                <option value="{{ $pageName }}" {{ request('page_filter') == $pageName ? 'selected' : '' }}>
                                    {{ $pageName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request('page_filter'))
                        <div class="col-auto">
                            <a href="{{ route('admin.site-assets') }}" class="btn btn-sm btn-outline-secondary">
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table Grid List -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 130px;">Preview Gambar</th>
                            <th style="width: 250px;">Nama Aset &amp; Halaman</th>
                            <th>Keterangan Penempatan di Website</th>
                            <th style="width: 140px;">Rekomendasi</th>
                            <th style="width: 110px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siteAssets as $asset)
                            <tr>
                                <td>
                                    <div class="position-relative rounded-3 overflow-hidden border bg-light d-flex align-items-center justify-center" 
                                         style="width: 110px; height: 85px;">
                                        <img src="{{ $asset->url }}" 
                                             alt="{{ $asset->title }}" 
                                             style="width: 100%; height: 100%; object-fit: cover;"
                                             onerror="this.src='{{ asset('img/jelas.png') }}'">
                                        @if($asset->image_path)
                                            <span class="position-absolute top-0 start-0 badge bg-success" style="font-size: 9px; border-radius: 0 0 6px 0;">
                                                Custom
                                            </span>
                                        @else
                                            <span class="position-absolute top-0 start-0 badge bg-secondary" style="font-size: 9px; border-radius: 0 0 6px 0;">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $asset->title }}</h6>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                        <i class="fas fa-globe me-1"></i>{{ $asset->page }}
                                    </span>
                                    <div class="text-muted small mt-1 font-monospace" style="font-size: 11px;">
                                        Key: {{ $asset->asset_key }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary mb-1 small">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $asset->placement }}
                                    </div>
                                    <p class="text-muted small mb-0 lh-sm">
                                        {{ $asset->description }}
                                    </p>
                                </td>
                                <td>
                                    @if($asset->recommended_size)
                                        <span class="badge bg-light text-dark border px-2 py-1 small">
                                            <i class="fas fa-ruler-combined me-1 text-muted"></i>{{ $asset->recommended_size }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.site-assets.edit', $asset->id) }}" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                                           title="Unggah / Ganti Gambar">
                                            <i class="fas fa-upload me-1"></i> Ganti
                                        </a>
                                        <form action="{{ route('admin.site-assets.delete', $asset->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset gambar ini dari daftar kelola?');" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger rounded-circle p-1" 
                                                    style="width: 31px; height: 31px;" 
                                                    title="Hapus Aset">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data aset gambar yang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
