@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: calc(100vh - 100px);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Login Logs (Admin)</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Timestamp (WIB)</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-2">
                                            <div class="fw-bold">{{ $log->user->name ?? 'Unknown' }}</div>
                                            <div class="text-muted small">{{ $log->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $log->ip_address }}</span>
                                </td>
                                <td>{{ $log->location ?? 'Unknown' }}</td>
                                <td class="small text-muted" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No login logs found.</td>
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
