@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Manage teams</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">Back to Dashboard</a>
                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Team
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $Team)
                                <tr>
                                    <td>
                                        @if($Team->photo)
                                                                                       <img src="{{ App\Helpers\AppHelper::storageUrl($Team->photo) }}" alt="{{ $Team->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                                        @else
                                            <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $Team->name }}</td>
                                    <td>{{ Str::limit($Team->position, 100) }}</td>
                                    <td>
                                        @if($Team->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.teams.edit', $Team->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.teams.delete', $Team->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No teams found.</td>
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

