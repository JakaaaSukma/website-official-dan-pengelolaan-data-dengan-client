<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Reset Password - LPK Gyokai Indonesia</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .reset-container { max-width: 480px; width: 100%; }
        .reset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
        }
        .reset-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .reset-icon i { font-size: 2rem; color: white; }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 15px;
            background: #f8f9fa;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
            outline: none;
            background: white;
        }
        .otp-input {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 10px;
            color: #10b981;
        }
        .btn-reset {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 14px;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }
        .alert { border-radius: 12px; border: none; }
        .email-badge {
            background: #ecfdf5;
            padding: 6px 16px;
            border-radius: 30px;
            font-weight: 600;
            color: #10b981;
            font-size: 0.9rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-icon">
                <i class="fas fa-lock-open"></i>
            </div>
            <h2 class="text-center mb-2" style="font-weight: 700; color: #333;">Reset Password</h2>
            <p class="text-center text-muted mb-2">Masukkan kode OTP yang dikirim ke email:</p>
            <div class="text-center mb-4">
                <span class="email-badge"><i class="fas fa-envelope me-1"></i> {{ $email }}</span>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset') }}" id="resetForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600;"><i class="fas fa-key me-1 text-muted"></i> Kode OTP (6 digit)</label>
                    <input type="text" class="form-control otp-input" name="otp_code"
                           maxlength="6" inputmode="numeric" pattern="[0-9]*"
                           placeholder="000000" required autofocus
                           autocomplete="one-time-code">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600;"><i class="fas fa-lock me-1 text-muted"></i> Password Baru</label>
                    <input type="password" class="form-control" name="password" required minlength="6" placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-4">
                    <label class="form-label" style="font-weight: 600;"><i class="fas fa-lock me-1 text-muted"></i> Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" name="password_confirmation" required minlength="6" placeholder="Ketik ulang password baru">
                </div>
                <button type="submit" class="btn btn-reset mb-3" id="resetBtn">
                    <i class="fas fa-save me-2"></i>Simpan Password Baru
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('admin.login') }}" class="text-decoration-none" style="font-weight: 600; color: #999;">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                </a>
            </div>

            <div class="text-center mt-3 pt-3" style="border-top: 1px solid #eee;">
                <small class="text-muted"><i class="fas fa-clock me-1"></i>Kode berlaku selama <strong>5 menit</strong>.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.otp-input').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('resetForm').addEventListener('submit', function() {
            const btn = document.getElementById('resetBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        });
    </script>
</body>
</html>
