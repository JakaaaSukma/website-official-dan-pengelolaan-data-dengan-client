@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Add News</h2>
            <a href="{{ route('admin.news') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan!</h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (optional)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}" placeholder="auto-generate if empty">
                        @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                        @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                        @error('thumbnail') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="images" class="form-label">Foto Tambahan (Maksimal 3) <span class="text-muted">(Opsional)</span></label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Anda dapat mengupload maksimal 3 foto tambahan. Format yang didukung: JPG, PNG, GIF. Maksimal ukuran per file: 2MB.
                        </small>
                        @error('images') <div class="text-danger mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        @error('images.*') <div class="text-danger mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        <div id="image-preview" class="mt-3 row g-2"></div>
                        <div id="image-count" class="mt-2 text-muted small"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="is_headline" class="form-label">Headline</label>
                            <select class="form-select" id="is_headline" name="is_headline" required>
                                <option value="1" {{ old('is_headline', '0') === '1' ? 'selected' : '' }}>Ya (tampilkan sebagai berita utama)</option>
                                <option value="0" {{ old('is_headline', '0') === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('is_headline') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_published" class="form-label">Publish</label>
                            <select class="form-select" id="is_published" name="is_published" required>
                                <option value="1" {{ old('is_published', '1') === '1' ? 'selected' : '' }}>Publish</option>
                                <option value="0" {{ old('is_published', '1') === '0' ? 'selected' : '' }}>Draft</option>
                            </select>
                            @error('is_published') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imagesInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');
    const imageCount = document.getElementById('image-count');
    const maxImages = 3;

    function updateImageCount(count) {
        if (imageCount) {
            if (count > 0) {
                imageCount.innerHTML = `<i class="fas fa-images"></i> ${count} dari ${maxImages} foto dipilih`;
                if (count === maxImages) {
                    imageCount.className = 'mt-2 text-warning small';
                    imageCount.innerHTML += ' <i class="fas fa-exclamation-triangle"></i> Maksimal tercapai';
                } else {
                    imageCount.className = 'mt-2 text-success small';
                }
            } else {
                imageCount.innerHTML = '';
            }
        }
    }

    if (imagesInput) {
        imagesInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            // Validate max 3 images
            if (files.length > maxImages) {
                alert(`Maksimal ${maxImages} foto yang dapat diupload. Silakan pilih ulang.`);
                e.target.value = '';
                imagePreview.innerHTML = '';
                updateImageCount(0);
                return;
            }

            // Validate file types and sizes
            const validFiles = [];
            const invalidFiles = [];
            
            files.forEach((file, index) => {
                // Check file type
                if (!file.type.startsWith('image/')) {
                    invalidFiles.push(`File ${index + 1}: Bukan file gambar`);
                    return;
                }
                
                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    invalidFiles.push(`File ${index + 1}: Ukuran file terlalu besar (maksimal 2MB)`);
                    return;
                }
                
                validFiles.push(file);
            });

            if (invalidFiles.length > 0) {
                alert('Beberapa file tidak valid:\n' + invalidFiles.join('\n'));
            }

            // Clear previous preview
            imagePreview.innerHTML = '';

            // Show preview for valid files
            validFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 col-sm-6';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="img-thumbnail w-100" style="height: 150px; object-fit: cover; border-radius: 8px;">
                            <div class="position-absolute top-0 end-0 m-1">
                                <span class="badge bg-primary">${index + 1}</span>
                            </div>
                        </div>
                    `;
                    imagePreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });

            updateImageCount(validFiles.length);
        });
    }
});
</script>
@endsection






