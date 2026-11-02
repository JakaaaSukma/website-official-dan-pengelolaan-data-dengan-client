<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lupa Password - LPK Gyokai Indonesia</title>
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
        .fp-container { max-width: 480px; width: 100%; }
        .fp-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
        }
        .fp-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            border-radius: 50%;
            display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .fp-icon i { font-size: 2rem; color: white; }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 15px;
            background: #f8f9fa;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
            background: white;
        }
        .btn-send {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            border: none;
            padding: 14px;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
            color: white;
        }
        .alert { border-radius: 12px; border: none; }
    </style>
</head>
<body>
    <div class="fp-container">
        <div class="fp-card">
            <div class="fp-icon">
                <i class="fas fa-key"></i>
            </div>
            <h2 class="text-center mb-2" style="font-weight: 700; color: #333;">Lupa Password?</h2>
            <p class="text-center text-muted mb-4">Masukkan alamat email yang terdaftar. Kami akan mengirimkan kode verifikasi OTP untuk mengatur ulang password Anda.</p>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.forgot.send') }}" id="fpForm">
                @csrf
                <div class="mb-4">
                    <label class="form-label" style="font-weight: 600;"><i class="fas fa-envelope me-1 text-muted"></i> Email Address</label>
                    <input type="email" class="form-control" name="email"
                           value="{{ old('email') }}"
                           placeholder="contoh@perusahaan.com" required autofocus>
                </div>
                <button type="submit" class="btn btn-send mb-3" id="sendBtn">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Kode OTP
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('admin.login') }}" class="text-decoration-none" style="font-weight: 600; color: #667eea;">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('fpForm').addEventListener('submit', function() {
            const btn = document.getElementById('sendBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim kode...';
        });
    </script>
</body>
</html>
