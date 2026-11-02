@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container" style="max-width: 900px;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #e63946;">
                    <i class="fas fa-edit me-2"></i>Ubah Gambar Aset Website
                </h2>
                <p class="text-muted mb-0 small">
                    Unggah gambar baru untuk menggantikan gambar tampilan saat ini.
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
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill font-semibold">
                        <i class="fas fa-file-alt me-1"></i> {{ $asset->page }}
                    </span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $asset->title }}</h5>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Info Penempatan Banner -->
                <div class="alert alert-info border-0 rounded-3 d-flex align-items-start gap-3 p-3 mb-4 shadow-sm" style="background: #e8f4fd;">
                    <i class="fas fa-info-circle text-primary fs-4 mt-1"></i>
                    <div>
                        <div class="fw-bold text-dark mb-1">
                            Lokasi Penempatan: <span class="text-primary">{{ $asset->placement }}</span>
                        </div>
                        <div class="text-muted small">
                            {{ $asset->description }}
                        </div>
                        @if($asset->recommended_size)
                            <div class="mt-2 text-dark small fw-semibold">
                                <i class="fas fa-crop-alt me-1 text-secondary"></i> Ukuran Rekomendasi: 
                                <span class="badge bg-white text-dark border">{{ $asset->recommended_size }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('admin.site-assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Preview Section -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark">Gambar Saat Ini:</label>
                            <div class="rounded-3 border overflow-hidden p-2 bg-light text-center shadow-2xs">
                                <img id="imagePreviewCurrent" 
                                     src="{{ $asset->url }}" 
                                     alt="{{ $asset->title }}" 
                                     class="img-fluid rounded-2" 
                                     style="max-height: 220px; object-fit: contain;">
                                <div class="mt-2 small text-muted">
                                    Status: 
                                    @if($asset->image_path)
                                        <span class="badge bg-success">Foto Kustom Terunggah</span>
                                    @else
                                        <span class="badge bg-secondary">Default Sistem</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark">
                                Unggah Gambar Baru: <span class="text-danger">*</span>
                            </label>
                            <div class="mb-3">
                                <input type="file" 
                                       name="image" 
                                       id="imageInput" 
                                       class="form-control" 
                                       accept="image/png, image/jpeg, image/jpg, image/webp"
                                       onchange="previewUpload(event)">
                                <div class="form-text small">
                                    Format: JPG, PNG, WEBP. Maksimal ukuran berkas 5MB.
                                </div>
                            </div>

                            @if($asset->image_path)
                                <div class="form-check p-3 bg-light rounded-3 border mb-3">
                                    <input class="form-check-input" type="checkbox" name="remove_custom_image" value="1" id="removeCheck">
                                    <label class="form-check-label small fw-semibold text-danger" for="removeCheck">
                                        <i class="fas fa-undo me-1"></i> Hapus gambar kustom dan kembalikan ke gambar default
                                    </label>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">Judul / Label Aset:</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $asset->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">Keterangan Penempatan (Bisa diubah jika perlu):</label>
                                <input type="text" name="placement" class="form-control form-control-sm" value="{{ old('placement', $asset->placement) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">Deskripsi Panduan:</label>
                                <textarea name="description" class="form-control form-control-sm" rows="2">{{ old('description', $asset->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.site-assets') }}" class="btn btn-secondary px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan Gambar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreviewCurrent');
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
