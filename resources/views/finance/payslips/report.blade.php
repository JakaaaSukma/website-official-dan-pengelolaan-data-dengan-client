@extends('layouts.finance')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-primary"><i class="fas fa-file-alt me-2"></i>Laporan Sesi Pengiriman Slip Gaji</h4>
                        <div class="small text-muted">Kode sesi: {{ $sendSession->session_code }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        @if(!empty($canOpenSendingPage))
                            <a href="{{ route('finance.payslips.sending', ['send_session_id' => $sendSession->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-rotate-right me-1"></i>Menuju Proses Pengiriman
                            </a>
                        @endif
                        @if(!empty($canCancelSending))
                            <form action="{{ route('finance.payslips.send.cancel') }}" method="POST">
                                @csrf
                                <input type="hidden" name="send_session_id" value="{{ $sendSession->id }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-ban me-1"></i>Batalkan Proses
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('finance.payslips') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(!empty($canResumeSending))
                        <div class="alert alert-info">
                            Sesi ini masih aktif diproses. Masih ada {{ $remainingQueueCount }} file dalam antrian dan Anda bisa kembali ke halaman proses pengiriman atau membatalkannya dari sini.
                        </div>
                    @elseif(!empty($canOpenSendingPage))
                        <div class="alert alert-info">
                            Sesi ini masih berstatus sedang diproses. Gunakan tombol menuju proses pengiriman untuk membuka kembali halaman proses email.
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="text-muted small">Waktu Mulai</div>
                                    <div class="fw-semibold">{{ $sendSession->started_at ? $sendSession->started_at->format('d M Y H:i') : '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="text-muted small">Total File</div>
                                    <div class="fw-semibold">{{ $sendSession->total_files }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="text-muted small">Berhasil</div>
                                    <div class="fw-semibold text-success">{{ $sendSession->success_count }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="text-muted small">Gagal</div>
                                    <div class="fw-semibold text-danger">{{ $sendSession->failed_count }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="text-muted small">Status</div>
                                    <div>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <form action="{{ route('finance.payslips.reports.show', $sendSession) }}" method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                            <div>
                                <label class="form-label small text-muted mb-1">Urutkan Nama File</label>
                                <select name="filename_sort" class="form-select form-select-sm">
                                    <option value="time_desc" {{ ($filenameSort ?? 'time_desc') === 'time_desc' ? 'selected' : '' }}>Waktu Kirim Terbaru</option>
                                    <option value="filename_asc" {{ ($filenameSort ?? '') === 'filename_asc' ? 'selected' : '' }}>Abjad / Angka A-Z</option>
                                    <option value="filename_desc" {{ ($filenameSort ?? '') === 'filename_desc' ? 'selected' : '' }}>Abjad / Angka Z-A</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-sort-alpha-down me-1"></i>Terapkan
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Mailer SMTP</th>
                                    <th>Nama File</th>
                                    <th>Periode</th>
                                    <th>Penerima</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="small">{{ $log->created_at->format('d M Y H:i') }}</td>
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
                                            @if($log->status === 'success')
                                                <span class="badge bg-success">Berhasil</span>
                                            @else
                                                <span class="badge bg-danger">Gagal</span>
                                            @endif
                                        </td>
                                        <td class="small {{ $log->status === 'failed' ? 'text-danger' : 'text-muted' }}">
                                            {{ $log->error_message ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Belum ada detail pengiriman pada sesi ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
