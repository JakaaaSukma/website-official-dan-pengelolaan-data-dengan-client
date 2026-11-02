@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-edit me-2"></i>Edit Peserta Magang
            </h2>
            <a href="{{ route('company.participants') }}" class="btn btn-secondary">
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
                        <form action="{{ route('company.participants.update', $participant->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nama <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nama') is-invalid @enderror" 
                                           id="nama" 
                                           name="nama" 
                                           value="{{ old('nama', $participant->nama) }}" 
                                           required 
                                           placeholder="Masukkan nama peserta">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nik" class="form-label">
                                        <i class="fas fa-id-card me-1"></i>NIK <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nik') is-invalid @enderror" 
                                           id="nik" 
                                           name="nik" 
                                           value="{{ old('nik', $participant->nik) }}" 
                                           placeholder="Masukkan NIK (opsional)">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Tanggal Lahir <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" 
                                           name="tanggal_lahir" 
                                           value="{{ old('tanggal_lahir', $participant->tanggal_lahir->format('Y-m-d')) }}" 
                                           required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nama_perusahaan" class="form-label">
                                        <i class="fas fa-building me-1"></i>Nama Perusahaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nama_perusahaan') is-invalid @enderror" 
                                           id="nama_perusahaan" 
                                           name="nama_perusahaan" 
                                           value="{{ old('nama_perusahaan', $participant->nama_perusahaan) }}" 
                                           required 
                                           placeholder="Masukkan nama perusahaan">
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Alamat <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                          id="alamat" 
                                          name="alamat" 
                                          rows="3" 
                                          required 
                                          placeholder="Masukkan alamat lengkap">{{ old('alamat', $participant->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mulai_kontrak" class="form-label">
                                        <i class="fas fa-calendar-check me-1"></i>Mulai Kontrak <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('mulai_kontrak') is-invalid @enderror" 
                                           id="mulai_kontrak" 
                                           name="mulai_kontrak" 
                                           value="{{ old('mulai_kontrak', $participant->mulai_kontrak->format('Y-m-d')) }}" 
                                           required>
                                    @error('mulai_kontrak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="kontrak_habis" class="form-label">
                                        <i class="fas fa-calendar-times me-1"></i>Kontrak Habis <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('kontrak_habis') is-invalid @enderror" 
                                           id="kontrak_habis" 
                                           name="kontrak_habis" 
                                           value="{{ old('kontrak_habis', $participant->kontrak_habis->format('Y-m-d')) }}" 
                                           required>
                                    @error('kontrak_habis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tanggal kontrak habis harus setelah tanggal mulai kontrak</small>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('company.participants') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none;">
                                    <i class="fas fa-save me-2"></i>Update Data
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
                            <li>Tanggal kontrak habis harus setelah tanggal mulai kontrak</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set max date for tanggal lahir to today
    document.getElementById('tanggal_lahir').max = new Date().toISOString().split('T')[0];
    
    // Validate kontrak habis must be after mulai kontrak
    document.getElementById('mulai_kontrak').addEventListener('change', function() {
        const mulaiKontrak = new Date(this.value);
        const kontrakHabisInput = document.getElementById('kontrak_habis');
        
        if (mulaiKontrak) {
            const minDate = new Date(mulaiKontrak);
            minDate.setDate(minDate.getDate() + 1); // Set minimum date to next day
            kontrakHabisInput.min = minDate.toISOString().split('T')[0];
        }
    });

    // Set minimum date for kontrak habis on page load if mulai kontrak is already filled
    document.addEventListener('DOMContentLoaded', function() {
        const mulaiKontrak = document.getElementById('mulai_kontrak').value;
        if (mulaiKontrak) {
            const minDate = new Date(mulaiKontrak);
            minDate.setDate(minDate.getDate() + 1);
            document.getElementById('kontrak_habis').min = minDate.toISOString().split('T')[0];
        }
    });
</script>
@endsection

