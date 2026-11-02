@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Edit News</h2>
            <a href="{{ route('admin.news') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($newsItem->thumbnail)
                    <div class="mb-3">
                        <label class="form-label">Current Thumbnail</label><br>
                        <img src="{{ App\Helpers\AppHelper::storageUrl($newsItem->thumbnail) }}" alt="{{ $newsItem->title }}" style="max-width: 240px; border-radius: 12px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                    </div>
                @endif

                <form action="{{ route('admin.news.update', $newsItem->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $newsItem->title) }}" required>
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (optional)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $newsItem->slug) }}" placeholder="auto-generate if empty">
                        @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="8" required>{{ old('content', $newsItem->content) }}</textarea>
                        @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail (replace)</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                        @error('thumbnail') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="is_headline" class="form-label">Headline</label>
                            <select class="form-select" id="is_headline" name="is_headline" required>
                                <option value="1" {{ old('is_headline', $newsItem->is_headline ? '1' : '0') === '1' ? 'selected' : '' }}>Ya (tampilkan sebagai berita utama)</option>
                                <option value="0" {{ old('is_headline', $newsItem->is_headline ? '1' : '0') === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('is_headline') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_published" class="form-label">Publish</label>
                            <select class="form-select" id="is_published" name="is_published" required>
                                <option value="1" {{ old('is_published', $newsItem->is_published ? '1' : '0') === '1' ? 'selected' : '' }}>Publish</option>
                                <option value="0" {{ old('is_published', $newsItem->is_published ? '1' : '0') === '0' ? 'selected' : '' }}>Draft</option>
                            </select>
                            @error('is_published') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection






