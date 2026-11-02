@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">User Download Company Profile</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.company-profile-downloads.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Data Pengguna yang Download Company Profile</h5>
                    <span class="badge bg-primary">{{ $downloads->total() }} Total</span>
                </div>
            </div>
            <div class="card-body">
                @if($downloads->total() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Kontak</th>
                                    <th>Status</th>
                                    <th>Tanggal Download</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($downloads as $index => $download)
                                    <tr>
                                        <td class="text-muted">{{ ($downloads->currentPage() - 1) * $downloads->perPage() + $index + 1 }}</td>
                                        <td>
                                            <strong style="color: #1a1a1a;">{{ $download->company_name }}</strong>
                                        </td>
                                        <td>{{ $download->full_name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $download->position }}</span>
                                        </td>
                                        <td>
                                            @if(filter_var($download->contact, FILTER_VALIDATE_EMAIL))
                                                <i class="fas fa-envelope me-1 text-primary"></i>
                                                <a href="mailto:{{ $download->contact }}" style="text-decoration: none;">
                                                    {{ $download->contact }}
                                                </a>
                                            @else
                                                <i class="fas fa-phone me-1 text-success"></i>
                                                <a href="tel:{{ $download->contact }}" style="text-decoration: none;">
                                                    {{ $download->contact }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($download->is_contacted)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Sudah Dihubungi
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Belum Dihubungi
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $download->created_at->format('d/m/Y') }}
                                                <br>
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $download->created_at->format('H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.company-profile-downloads.edit', $download->id) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.company-profile-downloads.toggle-contacted', $download->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin mengubah status?');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $download->is_contacted ? 'btn-secondary' : 'btn-success' }}" 
                                                            title="{{ $download->is_contacted ? 'Tandai Belum Dihubungi' : 'Tandai Sudah Dihubungi' }}">
                                                        <i class="fas {{ $download->is_contacted ? 'fa-undo' : 'fa-phone' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.company-profile-downloads.delete', $download->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $downloads->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data pengguna yang mengunduh company profile.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 600;
        color: #1a1a1a;
        border-bottom: 2px solid #e63946;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.8em;
    }
    
    .d-flex.gap-1 {
        gap: 0.25rem;
    }
    
    .table td {
        vertical-align: middle;
    }
</style>
@endsection

