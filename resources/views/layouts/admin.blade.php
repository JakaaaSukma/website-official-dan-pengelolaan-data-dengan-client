<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Panel - LPK Gyokai Indonesia</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" href="{{ asset('logo/Gyokai.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        :root {
            --panel-accent: #ff0000;
            --panel-accent-soft: rgba(255, 0, 0, 0.1);
            --panel-text: #1f1f1f;
            --panel-muted: #6c757d;
            --panel-border: rgba(0, 0, 0, 0.08);
            --panel-surface: rgba(255, 255, 255, 0.92);
        }

        body {
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255, 0, 0, 0.08), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #fff6f6 58%, #ffffff 100%);
            color: var(--panel-text);
        }

        .panel-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid var(--panel-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        .panel-brand {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            color: var(--panel-text);
            font-weight: 800;
            text-decoration: none;
        }

        .panel-brand-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, var(--panel-accent) 0%, #ff5f5f 100%);
            box-shadow: 0 14px 28px rgba(255, 0, 0, 0.22);
        }

        .panel-brand-label {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .panel-brand-label small {
            color: var(--panel-muted);
            font-size: .78rem;
            font-weight: 600;
            font-family: "Rubik", sans-serif;
        }

        .panel-toggler {
            border: 0;
            background: transparent;
            color: var(--panel-text);
            font-size: 1.35rem;
            padding: .5rem .75rem;
        }

        .panel-nav-link {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .75rem 1rem;
            border-radius: 14px;
            color: var(--panel-muted);
            font-weight: 600;
            text-decoration: none;
            transition: .2s ease;
            font-family: "Rubik", sans-serif;
        }

        .panel-nav-link:hover,
        .panel-nav-link:focus-visible {
            color: var(--panel-accent);
            background: var(--panel-accent-soft);
            outline: none;
        }

        .panel-nav-link.active {
            color: var(--panel-accent);
            background: var(--panel-accent-soft);
            box-shadow: inset 0 0 0 1px rgba(230, 57, 70, 0.16);
        }

        .panel-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .panel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            min-height: 2.75rem;
            border-radius: 14px;
            font-weight: 700;
            padding: .7rem 1rem;
        }

        .panel-button-primary {
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, var(--panel-accent) 0%, #ff5f5f 100%);
            box-shadow: 0 14px 28px rgba(255, 0, 0, 0.2);
        }

        .panel-button-secondary {
            color: var(--panel-text);
            border: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, 0.78);
        }

        .panel-button-danger {
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, #d7263d 0%, #ff5d73 100%);
        }

        .panel-content {
            padding: 2rem 0 3rem;
        }

        .panel-surface {
            background: var(--panel-surface);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .panel-utility-text {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: var(--panel-muted);
            font-weight: 600;
            padding: .65rem .9rem;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid var(--panel-border);
        }

        .alert {
            border: 0;
            border-radius: 18px;
        }

        @media (max-width: 991.98px) {
            .panel-navbar .navbar-collapse {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid var(--panel-border);
            }

            .panel-actions {
                width: 100%;
                margin-top: 1rem;
            }

            .panel-button,
            .panel-utility-text {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg panel-navbar">
        <div class="container py-2">
            <a href="{{ route('admin.dashboard') }}" class="panel-brand">
                <span class="panel-brand-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </span>
                <span class="panel-brand-label">
                    <span>Admin Panel</span>
                    <small>Kontrol data dan konten utama</small>
                </span>
            </a>

            <button class="panel-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="navbar-nav ms-lg-4 gap-lg-2">
                    <a href="{{ route('admin.dashboard') }}" class="panel-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.clients') }}" class="panel-nav-link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Clients</span>
                    </a>
                    <a href="{{ route('admin.job-vacancies') }}" class="panel-nav-link {{ request()->routeIs('admin.job-vacancies*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i>
                        <span>Vacancies</span>
                    </a>
                    <a href="{{ route('admin.teams') }}" class="panel-nav-link {{ request()->routeIs('admin.teams*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                    <a href="{{ route('admin.facilities') }}" class="panel-nav-link {{ request()->routeIs('admin.facilities*') ? 'active' : '' }}">
                        <i class="fas fa-images"></i>
                        <span>Facilities</span>
                    </a>
                    <a href="{{ route('admin.legalitas') }}" class="panel-nav-link {{ request()->routeIs('admin.legalitas*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract"></i>
                        <span>Legalitas</span>
                    </a>
                    <a href="{{ route('admin.news') }}" class="panel-nav-link {{ request()->routeIs('admin.news*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper"></i>
                        <span>News</span>
                    </a>
                    <a href="{{ route('admin.videos') }}" class="panel-nav-link {{ request()->routeIs('admin.videos*') ? 'active' : '' }}">
                        <i class="fas fa-video"></i>
                        <span>Videos</span>
                    </a>
                    <a href="{{ route('admin.company-profile-downloads') }}" class="panel-nav-link {{ request()->routeIs('admin.company-profile-downloads*') ? 'active' : '' }}">
                        <i class="fas fa-file-download"></i>
                        <span>Downloads</span>
                    </a>
                    <a href="{{ route('admin.site-assets') }}" class="panel-nav-link {{ request()->routeIs('admin.site-assets*') ? 'active' : '' }}">
                        <i class="fas fa-photo-video"></i>
                        <span>Gambar Web</span>
                    </a>
                    <a href="{{ route('admin.login-logs') }}" class="panel-nav-link {{ request()->routeIs('admin.login-logs*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>Login Logs</span>
                    </a>
                </div>

                <div class="ms-lg-auto panel-actions">
                    <a href="{{ route('admin.change-password') }}" class="panel-button panel-button-secondary text-decoration-none">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </a>
                    <a href="{{ route('index') }}" class="panel-button panel-button-secondary text-decoration-none" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>View Site</span>
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="panel-button panel-button-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="panel-content">
        @yield('content')
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
