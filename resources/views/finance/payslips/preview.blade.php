@extends('layouts.finance')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary"><i class="fas fa-list-check me-2"></i>Preview Pengiriman Slip Gaji</h4>
                    <a href="{{ route('finance.payslips') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Konfirmasi Pengiriman</h5>
                        <p class="mb-0">Silakan periksa daftar email di bawah ini. Pastikan alamat email sudah benar sebelum mengirim.</p>
                    </div>

                    <form action="{{ route('finance.payslips.send.start') }}" method="POST" id="sendForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email CC Masal (Opsional)</label>
                                <input type="email" class="form-control" name="global_cc" placeholder="Masukkan email CC untuk semua penerima">
                                <small class="text-muted">Email ini akan di-CC pada semua pengiriman slip gaji.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email BCC Masal (Opsional)</label>
                                <input type="email" class="form-control" name="global_bcc" placeholder="Masukkan email BCC untuk semua penerima">
                                <small class="text-muted">Email ini akan di-BCC pada semua pengiriman slip gaji.</small>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="checkAll" checked>
                                        </th>
                                        <th width="24%">Nama File</th>
                                        <th width="22%">Periode Slip</th>
                                        <th width="24%">Email Tujuan</th>
                                        <th width="15%">Status</th>
                                        <th width="10%">Pesan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $index => $file)
                                        <tr class="{{ $file['status'] == 'valid' ? '' : 'table-warning' }}">
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input file-checkbox" name="selected_files[{{ $index }}][filename]" value="{{ $file['filename'] }}" {{ $file['status'] == 'valid' ? 'checked' : '' }}>
                                                <input type="hidden" name="selected_files[{{ $index }}][period]" value="{{ $file['period'] }}">
                                            </td>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-2"></i>{{ $file['filename'] }}
                                            </td>
                                            <td>
                                                @if(!empty($file['period']))
                                                    <span class="fw-semibold">{{ $file['period'] }}</span>
                                                @else
                                                    <span class="text-muted">Periode tidak terbaca</span>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="email" class="form-control form-control-sm email-input" name="selected_files[{{ $index }}][email]" value="{{ $file['email'] }}" placeholder="Masukkan email karyawan" required>
                                            </td>
                                            <td>
                                                @if($file['status'] == 'valid')
                                                    <span class="badge bg-success status-badge"><i class="fas fa-check me-1"></i>Siap Kirim</span>
                                                @else
                                                    <span class="badge bg-warning text-dark status-badge"><i class="fas fa-exclamation-triangle me-1"></i>Periksa Email</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ $file['message'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                    <p>Tidak ada file PDF ditemukan.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('finance.payslips') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSend" {{ count($files) == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-2"></i>Kirim Email Terpilih
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Check All
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.file-checkbox');
        const btnSend = document.getElementById('btnSend');

        function toggleEmailInput(checkbox) {
            const row = checkbox.closest('tr');
            const emailInput = row.querySelector('.email-input');
            if (emailInput) {
                emailInput.disabled = !checkbox.checked;
            }
        }

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                toggleEmailInput(cb);
            });
            updateSendButton();
        });

        checkboxes.forEach(cb => {
            // Initialize state
            toggleEmailInput(cb);
            
            cb.addEventListener('change', function() {
                toggleEmailInput(this);
                updateSendButton();
            });
        });

        function updateSendButton() {
            const checkedCount = document.querySelectorAll('.file-checkbox:checked').length;
            btnSend.disabled = checkedCount === 0;
            btnSend.innerHTML = `<i class="fas fa-paper-plane me-2"></i>Kirim ${checkedCount} Email`;
        }
        
        // Initial update
        updateSendButton();

        // Submit loading state
        document.getElementById('sendForm').addEventListener('submit', function() {
            if (!btnSend.disabled) {
                btnSend.disabled = true;
                const originalText = btnSend.innerHTML;
                btnSend.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sedang Mengirim...';
                
                // Allow form to submit
                return true;
            }
        });
    });
</script>
@endsection
