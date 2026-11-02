@extends('layouts.finance')
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary"><i class="fas fa-paper-plane me-2"></i>Mengirim Slip Gaji</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ $reportUrl ?? route('finance.payslips') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-alt me-1"></i>Lihat Laporan Sesi
                        </a>
                        <a href="{{ route('finance.payslips') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(!empty($sendingUnavailableReason))
                        <div class="alert alert-warning">
                            {{ $sendingUnavailableReason }}
                        </div>
                    @endif
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas {{ !empty($autoProcessEnabled) ? 'fa-spinner fa-spin' : 'fa-pause-circle' }} fa-2x me-3"></i>
                            <div>
                                @if(!empty($autoProcessEnabled))
                                    <div>Pengiriman berjalan otomatis maksimal 10 email secara berurutan per batch.</div>
                                    <div class="small text-muted">Sistem akan memotong batch lebih cepat bila proses terlalu lama agar mengurangi risiko error 500. Jangan menutup halaman ini sampai selesai.</div>
                                @else
                                    <div>Halaman proses berhasil dibuka kembali, tetapi proses otomatis tidak dapat langsung dilanjutkan dari state browser saat ini.</div>
                                    <div class="small text-muted">Anda masih bisa memantau progres, membuka laporan sesi, atau membatalkan sesi dari halaman ini.</div>
                                @endif
                                @if(!empty($sessionCode))
                                    <div class="small mt-1">Kode sesi: <strong>{{ $sessionCode }}</strong></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="progress" style="height: 24px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progressPercent }}%;">
                                {{ $progressPercent }}%
                            </div>
                        </div>
                    </div>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between"><span>Diproses</span><strong id="processedCount">{{ $processed }} / {{ $total }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Berhasil</span><strong id="successCount" class="text-success">{{ $success }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Gagal</span><strong id="failedCount" class="text-danger">{{ $failed }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Sisa antrian</span><strong id="remainingCount">{{ $remaining }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Batch berikutnya</span><strong id="nextBatchCount">{{ $nextBatchCount }} email</strong></li>
                    </ul>
                    <form id="continueForm" action="{{ route('finance.payslips.send') }}" method="POST">
                        @csrf
                        @if(!empty($activeSendSessionId))
                            <input type="hidden" name="send_session_id" value="{{ $activeSendSessionId }}">
                        @endif
                    </form>
                    @if(!empty($canCancelSending))
                        <form id="cancelForm" action="{{ route('finance.payslips.send.cancel') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="send_session_id" value="{{ $activeSendSessionId ?? session('current_payslip_send_session_id') }}">
                        </form>
                    @endif
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div id="progressMessage" class="small text-muted">
                            @if(!empty($autoProcessEnabled) && $remaining > 0)
                                Batch berikutnya akan dimulai otomatis.
                            @elseif(empty($autoProcessEnabled))
                                Lanjutkan dari laporan sesi atau batalkan proses bila state sementara sudah tidak tersedia.
                            @else
                                Menyelesaikan proses pengiriman.
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            @if(!empty($canCancelSending))
                                <button id="cancelBtn" type="button" class="btn btn-outline-danger">
                                    Batalkan Proses
                                </button>
                            @endif
                            <button id="continueBtn" type="button" class="btn btn-primary" @if(empty($autoProcessEnabled)) disabled @endif>
                                Proses Batch Berikutnya
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('continueBtn');
    const form = document.getElementById('continueForm');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelForm = document.getElementById('cancelForm');
    let isProcessing = false;

    function submitNext() {
        if (isProcessing) {
            return;
        }

        isProcessing = true;
        btn.disabled = true;
        btn.innerText = 'Memproses Batch...';
        form.submit();
    }

    function cancelSending() {
        if (isProcessing || !cancelForm) {
            return;
        }

        isProcessing = true;
        btn.disabled = true;
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.innerText = 'Membatalkan...';
        }
        cancelForm.submit();
    }

    const autoProcessEnabled = @json(!empty($autoProcessEnabled));

    if (autoProcessEnabled) {
        setTimeout(submitNext, 1200);
    }
    btn.addEventListener('click', submitNext);
    if (cancelBtn) {
        cancelBtn.addEventListener('click', cancelSending);
    }
});
</script>
@endsection
