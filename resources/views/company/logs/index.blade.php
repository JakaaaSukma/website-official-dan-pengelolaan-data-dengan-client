@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-history me-2"></i>Riwayat Login
            </h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu Login (WIB)</th>
                                <th>IP Address</th>
                                <th>Lokasi</th>
                                <th>Perangkat (Device)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $log->ip_address }}</span>
                                </td>
                                <td>{{ $log->location ?? 'Tidak diketahui' }}</td>
                                <td class="small text-muted" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat login.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
