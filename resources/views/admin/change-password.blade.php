@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">
                <i class="fas fa-key me-2"></i>Change Password
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-envelope fa-3x" style="color: #4a90e2;"></i>
                            </div>
                            <h5 class="fw-bold">Ubah Email Admin</h5>
                            <p class="text-muted mb-0">Email saat ini: <strong>{{ Auth::user()->email }}</strong></p>
                        </div>

                        <form action="{{ route('admin.change-email.update') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Baru <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ Auth::user()->email }}"
                                       required>
                                @error('email')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="current_password_email" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password Saat Ini (Untuk Verifikasi) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('current_password_email') is-invalid @enderror" 
                                           id="current_password_email" 
                                           name="current_password_email" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password_email', 'toggleEmailPass')">
                                        <i class="fas fa-eye" id="toggleEmailPass"></i>
                                    </button>
                                </div>
                                @error('current_password_email')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" style="background: #4a90e2; border: none;">
                                    <i class="fas fa-save me-2"></i>Simpan Email Baru
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-lock fa-3x" style="color: #e63946;"></i>
                            </div>
                            <h5 class="fw-bold">Ubah Password Admin</h5>
                            <p class="text-muted mb-0">Masukkan password saat ini dan password baru Anda</p>
                        </div>

                        <form action="{{ route('admin.change-password.update') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           required 
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', 'toggleCurrent')">
                                        <i class="fas fa-eye" id="toggleCurrent"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key me-1"></i>Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" 
                                           name="new_password" 
                                           required 
                                           autocomplete="new-password"
                                           minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', 'toggleNew')">
                                        <i class="fas fa-eye" id="toggleNew"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                                @error('new_password')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i>Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           required 
                                           autocomplete="new-password"
                                           minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation', 'toggleConfirm')">
                                        <i class="fas fa-eye" id="toggleConfirm"></i>
                                    </button>
                                </div>
                                @error('new_password')
                                    <div class="text-danger mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Update Password
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2" style="color: #e63946;"></i>Tips Keamanan Password
                        </h6>
                        <ul class="mb-0 small text-muted">
                            <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                            <li>Jangan gunakan password yang mudah ditebak (seperti tanggal lahir, nama, dll)</li>
                            <li>Jangan bagikan password Anda kepada siapapun</li>
                            <li>Ganti password secara berkala untuk keamanan yang lebih baik</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection

