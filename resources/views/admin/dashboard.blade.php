@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: calc(100vh - 100px);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Admin Dashboard</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-building fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['clients'] }}</h3>
                        <p class="text-muted mb-0">Clients</p>
                        <a href="{{ route('admin.clients') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-briefcase fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['job_vacancies'] }}</h3>
                        <p class="text-muted mb-0">Job Vacancies</p>
                        <a href="{{ route('admin.job-vacancies') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['teams'] }}</h3>
                        <p class="text-muted mb-0">Team Members</p>
                        <a href="{{ route('admin.teams') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-images fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['facilities'] }}</h3>
                        <p class="text-muted mb-0">Facilities</p>
                        <a href="{{ route('admin.facilities') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-file-contract fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['legalitas'] }}</h3>
                        <p class="text-muted mb-0">Legalitas</p>
                        <a href="{{ route('admin.legalitas') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-newspaper fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['news'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">News</p>
                        <a href="{{ route('admin.news') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-video fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['videos'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Videos</p>
                        <a href="{{ route('admin.videos') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-file-download fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['company_profile_downloads'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Company Profile Downloads</p>
                        <a href="{{ route('admin.company-profile-downloads') }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-photo-video fa-3x mb-3" style="color: #e63946;"></i>
                        <h3 class="fw-bold">{{ $stats['site_assets'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Gambar &amp; Banner Web</p>
                        <a href="{{ route('admin.site-assets') }}" class="btn btn-sm btn-outline-primary mt-2">Kelola Gambar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Quick Actions</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Client
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.job-vacancies.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Job Vacancy
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Team Member
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Facility
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.news.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add News
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.videos.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Video
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

