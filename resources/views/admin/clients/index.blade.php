@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Manage Clients</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">Back to Dashboard</a>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Client
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
            <div class="card-body">
                <form id="bulk-action-form" action="{{ route('admin.clients.bulk-update') }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center mb-3 gap-2">
                        <select name="action" class="form-select form-select-sm" style="width: auto;" required>
                            <option value="">Bulk Actions</option>
                            <option value="hide">Hide from Website</option>
                            <option value="show">Show on Website</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Apply this action to selected items?')">Apply</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>Logo</th>
                                    <th>Company Name</th>
                                    <th>About</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="client_ids[]" value="{{ $client->id }}" class="form-check-input client-checkbox">
                                        </td>
                                        <td>
                                            @if($client->company_logo)
                                           <img src="{{ App\Helpers\AppHelper::storageUrl($client->company_logo) }}" alt="{{ $client->company_name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                                            @else
                                                <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-building text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $client->company_name }}</td>
                                        <td>{{ Str::limit($client->about_company, 100) }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if($client->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                                
                                                @if($client->is_hidden)
                                                    <span class="badge bg-danger">Hidden from Web</span>
                                                @else
                                                    <span class="badge bg-info text-dark">Visible on Web</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-client-btn" data-id="{{ $client->id }}" data-url="{{ route('admin.clients.delete', $client->id) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No clients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Hidden form for individual deletion --}}
<form id="individual-delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.client-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.querySelectorAll('.delete-client-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this client?')) {
                const form = document.getElementById('individual-delete-form');
                form.action = this.getAttribute('data-url');
                form.submit();
            }
        });
    });
</script>
@endsection

