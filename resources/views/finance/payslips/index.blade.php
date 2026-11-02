@extends('layouts.finance')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h4 class="mb-0 text-primary"><i class="fas fa-envelope-open-text me-2"></i>Kirim Slip Gaji</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h5>
                                <p>Sistem ini menggunakan Python untuk memproses file PDF slip gaji dan mengirimkannya secara otomatis ke email karyawan.</p>
                                <hr>
                                <h6>Persyaratan:</h6>
                                <ul class="small">
                                    <li>File PDF harus berisi alamat email karyawan.</li>
                                    <li>Pastikan file <code>credentials.json</code> sudah ada di folder <code>database/gmail</code>.</li>
                                    <li>Pastikan Python dan library yang dibutuhkan sudah terinstall.</li>
                                </ul>
                            </div>

                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <h6>Cara Menjalankan Script Python:</h6>
                                    <pre class="bg-dark text-white p-3 rounded"><code>cd c:\xampp\htdocs\Gyokai01\python_scripts\payslip_sender
pip install -r requirements.txt
python send_slip.py</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Upload Card -->
                            <div class="card mb-4 border-0 shadow-sm bg-light">
                                <div class="card-body">
                                    <h5 class="card-title mb-3"><i class="fas fa-cloud-upload-alt me-2 text-primary"></i>Upload File Slip Gaji</h5>
                                    <p class="text-muted small">Upload file slip gaji untuk diproses dan dikirim. Anda dapat mempreview daftar penerima sebelum mengirim.</p>
                                    <form action="{{ route('finance.payslips.upload') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="payslip_file" class="form-label">Pilih File (PDF atau ZIP)</label>
                                            <input class="form-control" type="file" id="payslip_file" name="payslip_file" accept=".pdf,.zip" required>
                                            <div class="form-text">
                                                <ul>
                                                    <li>Upload <strong>.pdf</strong> untuk satu slip gaji.</li>
                                                    <li>Upload <strong>.zip</strong> berisi kumpulan file PDF untuk banyak slip gaji.</li>
                                                    <li>Maksimal ukuran file: <strong>100MB</strong>.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-arrow-right me-2"></i>Lanjut ke Preview
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($sendSessions))
                        <div class="mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Laporan Sesi Pengiriman</h5>
                            </div>

                            @if($sendSessions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode Sesi</th>
                                                <th>Waktu Mulai</th>
                                                <th>Total</th>
                                                <th>Berhasil</th>
                                                <th>Gagal</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sendSessions as $sendSession)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $sendSession->session_code }}</div>
                                                        <small class="text-muted">Selesai: {{ $sendSession->finished_at ? $sendSession->finished_at->format('d M Y H:i') : '-' }}</small>
                                                    </td>
                                                    <td>{{ $sendSession->started_at ? $sendSession->started_at->format('d M Y H:i') : $sendSession->created_at->format('d M Y H:i') }}</td>
                                                    <td>{{ $sendSession->total_files }}</td>
                                                    <td class="text-success fw-semibold">{{ $sendSession->success_count }}</td>
                                                    <td class="text-danger fw-semibold">{{ $sendSession->failed_count }}</td>
                                                    <td>
                                                        @if($sendSession->status === 'completed')
                                                            <span class="badge bg-success">Selesai</span>
                                                        @elseif($sendSession->status === 'completed_with_errors')
                                                            <span class="badge bg-warning text-dark">Selesai Dengan Kendala</span>
                                                        @elseif($sendSession->status === 'cancelled')
                                                            <span class="badge bg-secondary">Dibatalkan</span>
                                                        @elseif($sendSession->status === 'failed')
                                                            <span class="badge bg-danger">Gagal</span>
                                                        @else
                                                            <span class="badge bg-primary">Sedang Diproses</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @if($sendSession->status === 'processing')
                                                                <a href="{{ route('finance.payslips.sending', ['send_session_id' => $sendSession->id]) }}" class="btn btn-outline-success btn-sm">
                                                                    <i class="fas fa-paper-plane me-1"></i>Proses Pengiriman
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('finance.payslips.reports.show', $sendSession) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i>Lihat Laporan
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end">
                                    {{ $sendSessions->links() }}
                                </div>
                            @else
                                <div class="alert alert-light border text-center">
                                    Belum ada laporan sesi pengiriman tersimpan.
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Riwayat Pengiriman -->
                    @if(isset($logs))
                        <div class="mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-history me-2 text-secondary"></i>Detail Log Pengiriman Per File</h5>
                            </div>
                            
                            <!-- Filter & Sorting Form -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body py-3">
                                    <form action="{{ route('finance.payslips') }}" method="GET" class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted mb-1">Filter Tanggal</label>
                                            <input type="date" name="filter_date" class="form-control form-control-sm" value="{{ request('filter_date') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted mb-1">Filter Status</label>
                                            <select name="filter_status" class="form-select form-select-sm">
                                                <option value="all" {{ request('filter_status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                                <option value="success" {{ request('filter_status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                                                <option value="failed" {{ request('filter_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted mb-1">Filter Mailer</label>
                                            <select name="filter_mailer" class="form-select form-select-sm">
                                                <option value="all" {{ request('filter_mailer', 'all') == 'all' ? 'selected' : '' }}>Semua Mailer</option>
                                                @foreach(($smtpMailerOptions ?? collect()) as $mailerOption)
                                                    <option value="{{ $mailerOption->id }}" {{ (string) request('filter_mailer') === (string) $mailerOption->id ? 'selected' : '' }}>
                                                        {{ $mailerOption->mailer_name }} - {{ $mailerOption->from_address }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted mb-1">Urutan Waktu Kirim</label>
                                            <select name="sort_by" class="form-select form-select-sm">
                                                <option value="time_desc" {{ request('sort_by', 'time_desc') == 'time_desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                                                <option value="time_asc" {{ request('sort_by') == 'time_asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                                <i class="fas fa-filter me-1"></i> Terapkan
                                            </button>
                                            <a href="{{ route('finance.payslips') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @if($logs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Kode Sesi</th>
                                            <th>Mailer SMTP</th>
                                            <th>Nama File</th>
                                            <th>Periode</th>
                                            <th>Penerima</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td class="small">{{ $log->created_at->format('d M Y H:i') }}</td>
                                                <td class="small">
                                                    @if($log->sendSession)
                                                        <a href="{{ route('finance.payslips.reports.show', $log->sendSession) }}">{{ $log->sendSession->session_code }}</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="small">
                                                    <div class="fw-semibold">{{ $log->smtp_mailer_name ?? '-' }}</div>
                                                    <small class="text-muted">{{ $log->smtp_mailer_address ?? '-' }}</small>
                                                </td>
                                                <td>{{ $log->filename }}</td>
                                                <td>{{ $log->period ?? '-' }}</td>
                                                <td>
                                                    <div><strong>{{ $log->recipient_name ?? '-' }}</strong></div>
                                                    <small class="text-muted">{{ $log->recipient_email }}</small>
                                                </td>
                                                <td>
                                                    @if($log->status == 'success')
                                                        <span class="badge bg-success">Berhasil</span>
                                                    @else
                                                        <span class="badge bg-danger">Gagal</span>
                                                    @endif
                                                </td>
                                                <td class="small text-danger">{{Str::limit($log->error_message, 50)}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {{ $logs->links() }}
                            </div>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-search me-2"></i>Data tidak ditemukan dengan filter tersebut.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
