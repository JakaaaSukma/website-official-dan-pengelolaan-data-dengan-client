@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Pengaturan</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Video YouTube Halaman Berita</h5>
                <form method="POST" action="{{ route('admin.settings.news-youtube-list') }}">
                    @csrf
                    <div id="youtubeList">
                        @php
                            $urls = old('youtube_urls', $youtubeUrls ?? []);
                            if (empty($urls) && !empty($youtubeUrl)) {
                                $urls = [$youtubeUrl];
                            }
                            if (empty($urls)) { $urls = ['']; }
                        @endphp
                        @foreach($urls as $i => $u)
                        <div class="mb-2 d-flex align-items-center gap-2 youtube-item">
                            <input type="text" name="youtube_urls[]" class="form-control @error('youtube_urls.'.$i) is-invalid @enderror" placeholder="https://www.youtube.com/watch?v=..." value="{{ $u }}">
                            <button type="button" class="btn btn-outline-danger btn-remove" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            @error('youtube_urls.'.$i)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary" id="btnAddYoutube"><i class="fas fa-plus me-1"></i> Tambah Link</button>
                    </div>
                    <small class="text-muted d-block mb-3">Dukung tautan: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., youtube.com/shorts/...</small>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('news') }}" class="btn btn-outline-secondary">Lihat Halaman Berita</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
 </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('youtubeList');
    const btnAdd = document.getElementById('btnAddYoutube');
    btnAdd.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'mb-2 d-flex align-items-center gap-2 youtube-item';
        div.innerHTML = `
            <input type="text" name="youtube_urls[]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
            <button type="button" class="btn btn-outline-danger btn-remove" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        `;
        list.appendChild(div);
    });
    list.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            const item = e.target.closest('.youtube-item');
            if (item) {
                if (list.querySelectorAll('.youtube-item').length > 1) {
                    item.remove();
                } else {
                    item.querySelector('input').value = '';
                }
            }
        }
    });
});
</script>
@endsection
