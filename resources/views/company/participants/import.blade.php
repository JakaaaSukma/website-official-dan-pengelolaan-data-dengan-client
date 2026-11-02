@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-file-upload me-2"></i>Import Data Peserta Magang
            </h2>
            <a href="{{ route('company.participants') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if(session('import_result'))
            @php
                $importResult = session('import_result');
            @endphp
            <div class="alert alert-{{ !empty($importResult['errors']) || !empty($importResult['duplicates']) ? 'warning' : 'success' }} alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-{{ !empty($importResult['errors']) || !empty($importResult['duplicates']) ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
                    {{ $importResult['message'] ?? 'Import Selesai' }}
                </h5>
                <hr>
                @if(!empty($importResult['duplicates']))
                    <p><strong><i class="fas fa-copy me-1"></i>Data Duplikasi ({{ $importResult['duplicate'] ?? 0 }} data di-skip):</strong></p>
                    <ul class="mb-3 small">
                        @foreach($importResult['duplicates'] as $duplicate)
                            <li>{{ $duplicate }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(!empty($importResult['errors']))
                    <p><strong><i class="fas fa-exclamation-circle me-1"></i>Detail Error:</strong></p>
                    <ul class="mb-0 small">
                        @foreach($importResult['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan!</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-file-excel fa-3x" style="color: #457b9d;"></i>
                            </div>
                            <h5 class="fw-bold">Upload File Excel/CSV</h5>
                            <p class="text-muted mb-0">Sistem akan secara otomatis membaca dan mengambil data yang diperlukan</p>
                        </div>

                        <form action="{{ route('company.participants.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="file" class="form-label">
                                    <i class="fas fa-file me-1"></i>Pilih File Excel/CSV <span class="text-danger">*</span>
                                </label>
                                <input type="file" 
                                       class="form-control @error('file') is-invalid @enderror" 
                                       id="file" 
                                       name="file" 
                                       accept=".xlsx,.xls,.csv,.txt"
                                       required 
                                       onchange="previewFileName(this)">
                                <small class="text-muted">
                                    Format yang didukung: .xlsx, .xls, .csv (Maksimal 10MB)
                                </small>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="fileName" class="mt-2 text-muted"></div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="skip_header" name="skip_header" value="1" checked>
                                <label class="form-check-label" for="skip_header">
                                    <i class="fas fa-check-square me-1"></i>Baris pertama adalah header (abaikan baris pertama)
                                </label>
                                <small class="text-muted d-block mt-1">
                                    Centang jika file Anda memiliki baris header seperti: Nama, Tanggal Lahir, Alamat, dll.
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Format File yang Didukung:
                                </h6>
                                <ul class="mb-0 small mb-2">
                                    <li><strong>CSV (Recommended):</strong> File Excel dapat diekspor sebagai CSV (File > Save As > CSV UTF-8)</li>
                                    <li><strong>Format Kolom:</strong> Sistem akan otomatis mendeteksi kolom berdasarkan nama header atau posisi standar</li>
                                </ul>
                                <div class="mt-2">
                                    <a href="{{ route('company.participants.download-template') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Template CSV
                                    </a>
                                    <small class="text-muted ms-2">Gunakan template ini sebagai referensi format file</small>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-table me-2"></i>Format Standar File:
                                    </h6>
                                    <p class="small mb-2"><strong>Jika menggunakan header (recommended):</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Tanggal Lahir</th>
                                                    <th>Alamat</th>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Mulai Kontrak</th>
                                                    <th>Kontrak Habis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Contoh Nama</td>
                                                    <td>01/01/2000</td>
                                                    <td>Jl. Contoh No. 123</td>
                                                    <td>PT. Contoh</td>
                                                    <td>01/01/2024</td>
                                                    <td>12/31/2024</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="small mb-0"><strong>Catatan:</strong> Sistem akan otomatis mendeteksi kolom berdasarkan nama header (tidak case-sensitive). Nama Perusahaan akan otomatis diisi jika kosong.</p>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('company.participants') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none;">
                                    <i class="fas fa-upload me-2"></i>Import Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-lightbulb me-2" style="color: #457b9d;"></i>Tips Import:
                        </h6>
                        <ul class="mb-0 small text-muted">
                            <li>Pastikan file tidak memiliki baris kosong di tengah-tengah data</li>
                            <li>Format tanggal dapat menggunakan berbagai format: mm/dd/yyyy, mm-dd-yyyy, yyyy-mm-dd, atau format Excel (angka serial)</li>
                            <li>Sistem akan otomatis mendeteksi kolom berdasarkan nama header atau posisi kolom standar</li>
                            <li>Jika nama perusahaan kosong, akan otomatis diisi dengan nama user perusahaan Anda</li>
                            <li>Untuk file Excel besar, disarankan mengkonversi ke CSV terlebih dahulu untuk performa yang lebih baik</li>
                            <li>Jika terjadi error, periksa kembali format data pada file Anda</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFileName(input) {
        const fileName = input.files[0]?.name;
        const fileNameDiv = document.getElementById('fileName');
        if (fileName) {
            fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>File terpilih: <strong>' + fileName + '</strong>';
        } else {
            fileNameDiv.innerHTML = '';
        }
    }
</script>
@endsection

