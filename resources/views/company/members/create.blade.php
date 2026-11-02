@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-user-plus me-2"></i>Tambah Member Baru
            </h2>
            <a href="{{ route('company.members') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan!</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('company.members.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_daftar" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Tanggal Daftar <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_daftar') is-invalid @enderror" 
                                           id="tanggal_daftar" 
                                           name="tanggal_daftar" 
                                           value="{{ old('tanggal_daftar', date('Y-m-d')) }}" 
                                           required>
                                    @error('tanggal_daftar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="reference" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Referensi <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('reference') is-invalid @enderror" 
                                           id="reference" 
                                           name="reference" 
                                           value="{{ old('reference') }}" 
                                           placeholder="Contoh: MS, Gusti, Pabrik">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nik_ktp" class="form-label">
                                        <i class="fas fa-id-card me-1"></i>NIK <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nik_ktp') is-invalid @enderror" 
                                           id="nik_ktp" 
                                           name="nik_ktp" 
                                           value="{{ old('nik_ktp') }}" 
                                           placeholder="Masukkan NIK">
                                    @error('nik_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label">
                                        <i class="fas fa-birthday-cake me-1"></i>Tanggal Lahir <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" 
                                           name="tanggal_lahir" 
                                           value="{{ old('tanggal_lahir') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars me-1"></i>Jenis Kelamin <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tempat_lahir" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>Tempat Lahir <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                           id="tempat_lahir" 
                                           name="tempat_lahir" 
                                           value="{{ old('tempat_lahir') }}" 
                                           placeholder="Masukkan tempat lahir">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="perusahaan_favorite" class="form-label">
                                        <i class="fas fa-building me-1"></i>Perusahaan Favorite <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('perusahaan_favorite') is-invalid @enderror" 
                                           id="perusahaan_favorite" 
                                           name="perusahaan_favorite" 
                                           value="{{ old('perusahaan_favorite') }}" 
                                           placeholder="Masukkan perusahaan favorite">
                                    @error('perusahaan_favorite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="email@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Nomor Telepon/WA <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="Contoh: 081234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag me-1"></i>Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="belum_tes" {{ old('status') == 'belum_tes' ? 'selected' : '' }}>Belum Tes</option>
                                    <option value="sudah_training" {{ old('status') == 'sudah_training' ? 'selected' : '' }}>Sudah Training</option>
                                    <option value="belum_training" {{ old('status') == 'belum_training' ? 'selected' : '' }}>Belum Training</option>
                                    <option value="sudah_kerja" {{ old('status') == 'sudah_kerja' ? 'selected' : '' }}>Sudah Kerja</option>
                                    <option value="gagal" {{ old('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('company.members') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none;">
                                    <i class="fas fa-save me-2"></i>Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2" style="color: #457b9d;"></i>Informasi
                        </h6>
                        <ul class="mb-0 small text-muted">
                            <li>Semua field yang ditandai dengan <span class="text-danger">*</span> wajib diisi</li>
                            <li>Pastikan data yang dimasukkan sudah benar sebelum disimpan</li>
                            <li>Referensi bisa berisi nama atau kode referensi (contoh: MS, Gusti, Pabrik)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set max date for tanggal daftar to today
    document.getElementById('tanggal_daftar').max = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_lahir').max = new Date().toISOString().split('T')[0];
</script>
@endsection
