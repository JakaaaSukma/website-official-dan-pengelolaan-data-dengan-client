<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login - LPK Gyokai Indonesia</title>
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

        .login-container {
            max-width: 1200px;
            width: 100%;
        }

        .page-title {
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }

        .page-title h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .page-title p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-logo img {
            max-width: 150px;
            height: auto;
        }

        .login-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            padding-bottom: 1rem;
            border-bottom: 3px solid;
            display: inline-block;
            width: 100%;
        }

        .login-title-admin {
            color: #e63946;
            border-color: #e63946;
        }

        .login-title-company {
            color: #457b9d;
            border-color: #457b9d;
        }

        .login-title-finance {
            color: #2a9d8f;
            border-color: #2a9d8f;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            color: #666;
            margin-right: 0.5rem;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .form-check {
            margin-top: 1rem;
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .btn-login-admin,
        .btn-login-company,
        .btn-login-finance {
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            width: 100%;
            font-size: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 1rem;
        }

        .btn-login-admin {
            background: linear-gradient(135deg, #e63946 0%, #ff6b9d 100%);
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
        }

        .btn-login-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 57, 70, 0.4);
            color: white;
        }

        .btn-login-company {
            background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%);
            box-shadow: 0 4px 15px rgba(29, 53, 87, 0.3);
        }

        .btn-login-company:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29, 53, 87, 0.4);
            color: white;
        }

        .btn-login-finance {
            background: linear-gradient(135deg, #2a9d8f 0%, #264653 100%);
            box-shadow: 0 4px 15px rgba(42, 157, 143, 0.3);
        }

        .btn-login-finance:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(42, 157, 143, 0.4);
            color: white;
        }

        .btn-login-admin:active,
        .btn-login-company:active,
        .btn-login-finance:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }

        .input-icon .form-control {
            padding-left: 45px;
        }

        @media (max-width: 992px) {
            .page-title h1 {
                font-size: 1.5rem;
            }

            .login-card {
                margin-bottom: 2rem;
                padding: 2rem;
            }

            .login-logo img {
                max-width: 120px;
            }
        }

        .btn-login-admin:disabled,
        .btn-login-company:disabled,
        .btn-login-finance:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="page-title">
            <h1><i class="fas fa-lock me-2"></i>Welcome Back</h1>
            <p>Please login to access your account</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Unified Login Card -->
            <div class="col-lg-5 col-md-8 col-sm-10">
                <div class="login-card">
                    <div class="login-logo">
                        <img src="{{ asset('img/jelas.png') }}" alt="Logo" loading="lazy">
                    </div>
                    <h2 class="login-title login-title-admin" style="color: #667eea; border-color: #667eea;">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </h2>
                    
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>Email
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       required placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i>Password
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" id="password" name="password" 
                                       required placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.forgot') }}" class="text-decoration-none" style="font-weight: 600; font-size: 0.9rem; color: #667eea;">
                                Lupa Password?
                            </a>
                        </div>
                        <button type="submit" class="btn btn-login-admin w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) {
                    setTimeout(function() {
                        bsAlert.close();
                    }, 100);
                }
            });
        }, 5000);

        // Add loading state to button on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
                
                setTimeout(function() {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, 10000);
            }
        });
    </script>
</body>
</html>
