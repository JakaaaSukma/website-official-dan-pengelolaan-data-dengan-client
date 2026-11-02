@extends('layouts.finance')

@section('content')
@php
    $logs = isset($logs) ? collect($logs) : collect();
    $recentSessions = isset($recentSessions) ? collect($recentSessions) : collect();
    $smtpMailers = isset($smtpMailers) ? collect($smtpMailers) : collect();
    $sessionStats = array_merge([
        'total_sessions' => 0,
        'processing_sessions' => 0,
        'successful_deliveries' => 0,
        'failed_deliveries' => 0,
    ], $sessionStats ?? []);
    $lastLogin = $logs->first();
    $knownLocations = $logs->pluck('location')->filter(fn ($location) => $location && $location !== 'Unknown')->unique()->count();
@endphp
<style>
    .finance-dashboard {
        color: #153042;
    }

    .finance-dashboard .hero-card,
    .finance-dashboard .content-card,
    .finance-dashboard .metric-card,
    .finance-dashboard .session-card {
        border: 1px solid rgba(21, 48, 66, 0.07);
        border-radius: 28px;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.92);
    }

    .finance-dashboard .hero-card {
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(255, 0, 0, 0.12), transparent 26%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.99), rgba(255, 246, 246, 0.95));
    }

    .finance-dashboard .hero-pill,
    .finance-dashboard .session-status {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        border-radius: 999px;
        padding: .55rem .9rem;
        font-size: .85rem;
        font-weight: 700;
    }

    .finance-dashboard .hero-pill {
        color: #d7263d;
        background: rgba(255, 0, 0, 0.1);
    }

    .finance-dashboard .hero-title {
        font-size: clamp(2rem, 3vw, 3rem);
        line-height: 1.08;
        letter-spacing: -.03em;
    }

    .finance-dashboard .muted-copy,
    .finance-dashboard .section-copy,
    .finance-dashboard .metric-copy {
        color: #597083;
    }

    .finance-dashboard .cta-primary,
    .finance-dashboard .cta-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        min-height: 3rem;
        border-radius: 16px;
        font-weight: 700;
        padding: .8rem 1.1rem;
        text-decoration: none;
    }

    .finance-dashboard .cta-primary {
        color: #fff;
        background: linear-gradient(135deg, #ff0000 0%, #ff6666 100%);
        box-shadow: 0 16px 30px rgba(255, 0, 0, 0.2);
    }

    .finance-dashboard .cta-secondary {
        color: #153042;
        border: 1px solid rgba(21, 48, 66, 0.12);
        background: rgba(255, 255, 255, 0.82);
    }

    .finance-dashboard .hero-aside {
        height: 100%;
        border-radius: 24px;
        padding: 1.5rem;
        background: linear-gradient(180deg, #1f1f1f 0%, #3b3b3b 100%);
        color: #f2fbff;
    }

    .finance-dashboard .hero-aside strong {
        font-size: 2.4rem;
        line-height: 1;
    }

    .finance-dashboard .metric-card,
    .finance-dashboard .session-card {
        height: 100%;
    }

    .finance-dashboard .metric-icon {
        width: 3.15rem;
        height: 3.15rem;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .finance-dashboard .section-title {
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: .35rem;
    }

    .finance-dashboard .session-list {
        display: grid;
        gap: 1rem;
    }

    .finance-dashboard .session-card {
        padding: 1.2rem 1.25rem;
    }

    .finance-dashboard .session-status.processing {
        color: #8a6a00;
        background: rgba(255, 193, 7, 0.18);
    }

    .finance-dashboard .session-status.completed {
        color: #b02132;
        background: rgba(255, 0, 0, 0.12);
    }

    .finance-dashboard .session-status.failed {
        color: #b42332;
        background: rgba(220, 53, 69, 0.14);
    }

    .finance-dashboard .session-status.active {
        color: #1c6f43;
        background: rgba(25, 135, 84, 0.14);
    }

    .finance-dashboard .session-status.limit {
        color: #8a6a00;
        background: rgba(255, 193, 7, 0.18);
    }

    .finance-dashboard .session-status.temporary_issue,
    .finance-dashboard .session-status.inactive {
        color: #b42332;
        background: rgba(220, 53, 69, 0.14);
    }

    .finance-dashboard .table thead th {
        border-bottom: 0;
        color: #4f6476;
        font-size: .86rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .finance-dashboard .table tbody td {
        padding-top: 1rem;
        padding-bottom: 1rem;
        vertical-align: middle;
    }

    .finance-dashboard .mailer-meta {
        color: #597083;
        font-size: .9rem;
    }

    .finance-dashboard .mailer-warning {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-top: .45rem;
        padding: .4rem .65rem;
        border-radius: 12px;
        background: rgba(255, 193, 7, 0.14);
        color: #8a6a00;
        font-size: .78rem;
        font-weight: 700;
    }
</style>
<div class="container finance-dashboard">
    <div class="hero-card p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-xl-8">
                <span class="hero-pill mb-3">
                    <i class="fas fa-shield-alt"></i>
                    <span>Area kerja finance aktif</span>
                </span>
                <h1 class="hero-title fw-bold mb-3">Pantau pengiriman slip gaji, sesi terakhir, dan aktivitas login dalam satu alur kerja yang lebih fokus.</h1>
                <p class="muted-copy fs-5 mb-4">Dashboard ini memprioritaskan tugas yang paling sering dilakukan tim finance: mengirim slip gaji, melihat status sesi, dan memastikan akses akun tetap aman.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('finance.payslips') }}" class="cta-primary">
                        <i class="fas fa-paper-plane"></i>
                        <span>Mulai Kirim Slip Gaji</span>
                    </a>
                    <a href="{{ route('finance.payslips') }}" class="cta-secondary">
                        <i class="fas fa-file-alt"></i>
                        <span>Lihat Laporan Sesi</span>
                    </a>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="hero-aside">
                    <div class="text-uppercase small opacity-75 mb-2">Aksi paling penting</div>
                    <strong>{{ $sessionStats['processing_sessions'] }}</strong>
                    <div class="mb-4">sesi sedang berjalan atau siap dilanjutkan</div>
                    <div class="d-grid gap-3">
                        <div>
                            <div class="small opacity-75 mb-1">Email berhasil terkirim</div>
                            <div class="fw-semibold">{{ $sessionStats['successful_deliveries'] }}</div>
                        </div>
                        <div>
                            <div class="small opacity-75 mb-1">Email gagal terkirim</div>
                            <div class="fw-semibold">{{ $sessionStats['failed_deliveries'] }}</div>
                        </div>
                        <div>
                            <div class="small opacity-75 mb-1">Lokasi login tercatat</div>
                            <div class="fw-semibold">{{ $knownLocations }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-2">Total sesi</div>
                        <div class="display-6 fw-bold">{{ $sessionStats['total_sessions'] }}</div>
                    </div>
                    <span class="metric-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-layer-group"></i></span>
                </div>
                <div class="metric-copy">Semua laporan pengiriman yang tersimpan permanen.</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-2">Sedang proses</div>
                        <div class="display-6 fw-bold">{{ $sessionStats['processing_sessions'] }}</div>
                    </div>
                    <span class="metric-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-spinner"></i></span>
                </div>
                <div class="metric-copy">Sesi yang masih perlu diteruskan atau dipantau sampai selesai.</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-2">Berhasil terkirim</div>
                        <div class="display-6 fw-bold">{{ $sessionStats['successful_deliveries'] }}</div>
                    </div>
                    <span class="metric-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="metric-copy">Akumulasi email slip gaji yang sukses diproses sistem.</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-2">Gagal terkirim</div>
                        <div class="display-6 fw-bold">{{ $sessionStats['failed_deliveries'] }}</div>
                    </div>
                    <span class="metric-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-triangle"></i></span>
                </div>
                <div class="metric-copy">Perlu ditinjau untuk koreksi data atau masalah autentikasi email.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="content-card p-4 p-lg-5 h-100">
                <div class="section-title">Ringkasan akses</div>
                <p class="section-copy mb-4">Memudahkan finance memastikan aktivitas login terbaru tetap terpantau.</p>
                <div class="session-list">
                    <div class="session-card">
                        <div class="text-uppercase small fw-bold text-muted mb-2">Login terakhir</div>
                        <div class="fw-bold fs-5">{{ $lastLogin ? $lastLogin->created_at->format('d M Y, H:i:s') : '-' }}</div>
                        <div class="metric-copy mt-2">{{ $lastLogin ? ($lastLogin->location && $lastLogin->location !== 'Unknown' ? $lastLogin->location : 'Lokasi belum terdeteksi') : 'Belum ada aktivitas login' }}</div>
                    </div>
                    <div class="session-card">
                        <div class="text-uppercase small fw-bold text-muted mb-2">IP terakhir</div>
                        <div class="fw-bold fs-5">{{ $lastLogin->ip_address ?? '-' }}</div>
                        <div class="metric-copy mt-2">Gunakan data ini untuk memeriksa akses yang tidak dikenali.</div>
                    </div>
                    <div class="session-card">
                        <div class="text-uppercase small fw-bold text-muted mb-2">Arah kerja</div>
                        <div class="fw-bold fs-5">Kirim slip, pantau hasil, cek laporan</div>
                        <div class="metric-copy mt-2">Seluruh kebutuhan utama finance sebaiknya bisa selesai dalam 2–3 klik.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="content-card p-4 p-lg-5 h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                    <div>
                        <div class="section-title">Status mailer SMTP</div>
                        <p class="section-copy mb-0">Pantau prioritas, jumlah kirim hari ini, cooldown, dan kondisi tiap akun pengirim sebelum menjalankan batch baru.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mailer</th>
                                <th>Priority</th>
                                <th>Kuota Lokal</th>
                                <th>Limit Gmail Aktual</th>
                                <th>Cooldown Sampai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($smtpMailers as $mailer)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $mailer['mailer_name'] }}</div>
                                        <div class="mailer-meta">{{ $mailer['from_address'] }}</div>
                                        @if($mailer['gmail_limit_detected_early'])
                                            <div class="mailer-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>Limit Gmail lebih awal, selisih {{ $mailer['gmail_limit_gap'] }} dari kuota lokal</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">#{{ $mailer['priority'] }}</span></td>
                                    <td>
                                        <div>{{ $mailer['sent_today'] }} / {{ $mailer['daily_limit'] }}</div>
                                        <div class="mailer-meta">Sisa lokal: {{ $mailer['remaining_local_quota'] }}</div>
                                    </td>
                                    <td>
                                        @if($mailer['gmail_limit_detected_early'])
                                            <div class="fw-semibold text-warning">Terdeteksi lebih awal</div>
                                            <div class="mailer-meta">Gmail berhenti sebelum kuota lokal habis</div>
                                        @else
                                            <div class="fw-semibold text-success">Sesuai kuota lokal</div>
                                            <div class="mailer-meta">Belum ada selisih limit Gmail</div>
                                        @endif
                                    </td>
                                    <td>{{ $mailer['cooldown_until'] ? $mailer['cooldown_until']->format('d M Y, H:i') : '-' }}</td>
                                    <td><span class="session-status {{ $mailer['status_class'] }}">{{ $mailer['status_label'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada mailer SMTP yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-12">
            <div class="content-card p-4 p-lg-5 h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                    <div>
                        <div class="section-title">Riwayat sesi pengiriman terbaru</div>
                        <p class="section-copy mb-0">Status sesi membantu finance mengetahui batch mana yang sudah selesai dan mana yang perlu perhatian.</p>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    @forelse($recentSessions as $session)
                        @php
                            $statusClass = $session->status === 'processing' ? 'processing' : ($session->status === 'completed' ? 'completed' : 'failed');
                            $statusLabel = $session->status === 'processing'
                                ? 'Sedang Diproses'
                                : ($session->status === 'completed'
                                    ? 'Selesai'
                                    : ($session->status === 'completed_with_errors'
                                        ? 'Selesai Dengan Kendala'
                                        : ($session->status === 'cancelled' ? 'Dibatalkan' : 'Gagal')));
                        @endphp
                        <div class="col-md-6">
                            <div class="session-card">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="fw-bold">{{ $session->session_code }}</div>
                                        <div class="metric-copy small">{{ $session->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                    <span class="session-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                                <div class="metric-copy small mb-3">Berhasil {{ $session->success_count }} · Gagal {{ $session->failed_count }} · Total {{ $session->total_files }}</div>
                                <a href="{{ route('finance.payslips.reports.show', $session) }}" class="cta-secondary w-100">
                                    <i class="fas fa-eye"></i>
                                    <span>Lihat laporan sesi</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="session-card">
                                <div class="fw-bold mb-2">Belum ada sesi pengiriman</div>
                                <div class="metric-copy">Mulai kirim slip gaji agar laporan sesi dan performa pengiriman mulai terbentuk.</div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="section-title">Aktivitas login terbaru</div>
                <p class="section-copy mb-3">Riwayat ini berguna untuk menilai apakah akun finance diakses dari perangkat dan lokasi yang sesuai.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu Login</th>
                                <th>IP Address</th>
                                <th>Browser / Perangkat</th>
                                <th>Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $log->ip_address }}</span></td>
                                    <td class="small text-muted">{{ \Illuminate\Support\Str::limit($log->user_agent, 80) }}</td>
                                    <td>
                                        @if($log->location && $log->location !== 'Unknown')
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $log->location }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat login.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
