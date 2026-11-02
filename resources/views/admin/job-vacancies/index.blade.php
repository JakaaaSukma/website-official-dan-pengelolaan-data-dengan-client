@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Manage Job Vacancies</h2>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">Back to Dashboard</a>
                <a href="{{ route('admin.job-vacancies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Job Vacancy
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
                                <th>Position</th>
                                <th>Responsibilities</th>
                                <th>Qualifications</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobVacancies as $job)
                                <tr>
                                    <td>
                                        @if($job->photo)
                                                                                       <img src="{{ App\Helpers\AppHelper::storageUrl($job->photo) }}" alt="{{ $job->position }}" width="100" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                                        @else
                                            <span>No Photo</span>
                                        @endif
                                    </td>
                                    <td>{{ $job->position }}</td>
                                    <td>{{ Str::limit($job->responsibilities, 100) }}</td>
                                    <td>{{ Str::limit($job->qualifications, 100) }}</td>
                                    <td>
                                        @if($job->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.job-vacancies.edit', $job->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.job-vacancies.delete', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="6" class="text-center">No job vacancies found.</td>
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
