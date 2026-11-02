@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Edit Data Download Company Profile</h2>
            <a href="{{ route('admin.company-profile-downloads') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.company-profile-downloads.update', $download->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">
                                Nama Perusahaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name', $download->company_name) }}" 
                                   required
                                   minlength="2"
                                   maxlength="255">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('full_name') is-invalid @enderror" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="{{ old('full_name', $download->full_name) }}" 
                                   required
                                   minlength="2"
                                   maxlength="255">
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">
                                Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('position') is-invalid @enderror" 
                                   id="position" 
                                   name="position" 
                                   value="{{ old('position', $download->position) }}" 
                                   required
                                   minlength="2"
                                   maxlength="255">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label">
                                Nomor HP atau Email <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('contact') is-invalid @enderror" 
                                   id="contact" 
                                   name="contact" 
                                   value="{{ old('contact', $download->contact) }}" 
                                   required
                                   maxlength="255"
                                   placeholder="081234567890 atau email@example.com">
                            <small class="form-text text-muted">
                                Contoh: 081234567890 atau email@example.com
                            </small>
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_contacted" 
                                   name="is_contacted" 
                                   value="1"
                                   {{ old('is_contacted', $download->is_contacted) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_contacted">
                                <strong>Sudah Dihubungi</strong>
                            </label>
                            <small class="form-text text-muted d-block">
                                Centang jika pengguna sudah dihubungi
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Data ini dibuat pada: {{ $download->created_at->format('d/m/Y H:i:s') }}
                            @if($download->updated_at != $download->created_at)
                                <br>
                                Terakhir diperbarui: {{ $download->updated_at->format('d/m/Y H:i:s') }}
                            @endif
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Data
                        </button>
                        <a href="{{ route('admin.company-profile-downloads') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

