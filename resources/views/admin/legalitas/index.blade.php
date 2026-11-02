@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Manage Legalitas</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">Back to Dashboard</a>
                <a href="{{ route('admin.legalitas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Legalitas
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
                                <th>Document</th>
                                <th>Name</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($legalitas as $legal)
                                <tr>
                                    <td>
                                        @if($legal->document)
                                                                                       <img src="{{ App\Helpers\AppHelper::storageUrl($legal->document) }}" alt="{{ $legal->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                                        @else
                                            <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $legal->name }}</td>
                                    <td>{{ $legal->order }}</td>
                                    <td>
                                        @if($legal->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.legalitas.edit', $legal->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.legalitas.delete', $legal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="5" class="text-center">No legalitas found.</td>
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









