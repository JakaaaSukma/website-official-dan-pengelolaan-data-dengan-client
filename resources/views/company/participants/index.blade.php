@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-users me-2"></i>Data Peserta Magang
            </h2>
            <div>
                <a href="{{ route('company.participants.export', request()->query()) }}" class="btn btn-info me-2" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none; color: white;">
                    <i class="fas fa-file-excel me-2"></i>Export ke Excel
                </a>
                <a href="{{ route('company.participants.import') }}" class="btn btn-success me-2">
                    <i class="fas fa-file-upload me-2"></i>Import dari Excel/CSV
                </a>
                <a href="{{ route('company.participants.create') }}" class="btn" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none; color: white;">
                    <i class="fas fa-plus me-2"></i>Tambah Peserta Magang
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Reminder Notification for Expiring Contracts --}}
        @if($expiringCount > 0)
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #ffc107;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-bell me-2"></i>Reminder: Kontrak Akan Habis dalam 1 Bulan
                        </h5>
                        <p class="mb-2">
                            Terdapat <strong>{{ $expiringCount }}</strong> peserta magang yang kontraknya akan habis dalam 1 bulan ke depan.
                        </p>
                        <div class="mt-2">
                            <strong>Daftar Peserta:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($expiringSoon as $participant)
                                    <li>
                                        <strong>{{ $participant->nama }}</strong> - 
                                        Kontrak habis: <strong>{{ $participant->kontrak_habis->format('m/d/Y') }}</strong>
                                        <span class="badge bg-warning text-dark ms-2">
                                            {{ $participant->kontrak_habis->diffForHumans() }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Expired Contracts Alert --}}
        @if($expiredCount > 0)
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-times-circle me-2"></i>Perhatian: Kontrak Sudah Habis
                        </h5>
                        <p class="mb-0">
                            Terdapat <strong>{{ $expiredCount }}</strong> peserta magang yang kontraknya sudah habis. Silakan perpanjang atau update status kontrak.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $participants->total() }}</h3>
                        <small>Total Peserta @if(request()->has('nama_perusahaan') || request()->has('status_kontrak')) (Filtered) @endif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $activeCount }}</h3>
                        <small>Kontrak Aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h3 class="mb-0">{{ $expiringCount }}</h3>
                        <small>Kontrak Akan Habis (1 Bulan)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); color: white;">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filter Data
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('company.participants') }}" id="filterForm">
                    {{-- Search Box --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="search" class="form-label">
                                <i class="fas fa-search me-1"></i>Cari Peserta Magang (Berdasarkan Nama)
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Masukkan nama peserta magang untuk mencari...">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-primary" type="submit" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none;">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nama_perusahaan" class="form-label">
                                <i class="fas fa-building me-1"></i>Filter berdasarkan Nama Perusahaan
                            </label>
                            <select class="form-select" id="nama_perusahaan" name="nama_perusahaan">
                                <option value="">Semua Perusahaan</option>
                                @foreach($companyNames as $company)
                                    <option value="{{ $company }}" {{ request('nama_perusahaan') == $company ? 'selected' : '' }}>
                                        {{ $company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status_kontrak" class="form-label">
                                <i class="fas fa-calendar-check me-1"></i>Filter berdasarkan Status Kontrak
                            </label>
                            <select class="form-select" id="status_kontrak" name="status_kontrak">
                                <option value="all" {{ request('status_kontrak') == 'all' || !request('status_kontrak') ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ request('status_kontrak') == 'active' ? 'selected' : '' }}>Kontrak Aktif</option>
                                <option value="expiring_soon" {{ request('status_kontrak') == 'expiring_soon' ? 'selected' : '' }}>Akan Habis (1 Bulan)</option>
                                <option value="expired" {{ request('status_kontrak') == 'expired' ? 'selected' : '' }}>Kontrak Habis</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status_followup" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>Filter berdasarkan Status Followup
                            </label>
                            <select class="form-select" id="status_followup" name="status_followup">
                                <option value="all" {{ request('status_followup') == 'all' || !request('status_followup') ? 'selected' : '' }}>Semua Status Followup</option>
                                <option value="followed_up" {{ request('status_followup') == 'followed_up' ? 'selected' : '' }}>Sudah Difollowup</option>
                                <option value="not_followed_up" {{ request('status_followup') == 'not_followed_up' ? 'selected' : '' }}>Belum Difollowup</option>
                            </select>
                        </div>
                        <div class="col-md-12 d-flex gap-2 justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border: none;">
                                <i class="fas fa-filter me-1"></i>Terapkan Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilter()">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Active Filters Display --}}
                @if(request()->has('search') || request()->has('nama_perusahaan') || (request()->has('status_kontrak') && request('status_kontrak') != 'all') || (request()->has('status_followup') && request('status_followup') != 'all'))
                    <div class="mt-3 pt-3 border-top">
                        <strong class="text-muted">Filter Aktif:</strong>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if(request()->has('search'))
                                @php
                                    $queryParams = request()->query();
                                    unset($queryParams['search']);
                                    $removeSearchUrl = count($queryParams) > 0 
                                        ? route('company.participants') . '?' . http_build_query($queryParams)
                                        : route('company.participants');
                                @endphp
                                <span class="badge bg-info">
                                    <i class="fas fa-search me-1"></i>Pencarian: "{{ request('search') }}"
                                    <a href="{{ $removeSearchUrl }}" class="text-white ms-2" style="text-decoration: none;" title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request()->has('nama_perusahaan'))
                                @php
                                    $queryParams = request()->query();
                                    unset($queryParams['nama_perusahaan']);
                                    $removeCompanyUrl = count($queryParams) > 0 
                                        ? route('company.participants') . '?' . http_build_query($queryParams)
                                        : route('company.participants');
                                @endphp
                                <span class="badge bg-primary">
                                    <i class="fas fa-building me-1"></i>Perusahaan: {{ request('nama_perusahaan') }}
                                    <a href="{{ $removeCompanyUrl }}" class="text-white ms-2" style="text-decoration: none;" title="Hapus filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request()->has('status_kontrak') && request('status_kontrak') != 'all')
                                @php
                                    $statusLabels = [
                                        'active' => 'Kontrak Aktif',
                                        'expiring_soon' => 'Akan Habis (1 Bulan)',
                                        'expired' => 'Kontrak Habis'
                                    ];
                                    $statusColors = [
                                        'active' => 'success',
                                        'expiring_soon' => 'warning',
                                        'expired' => 'danger'
                                    ];
                                    $queryParams = request()->query();
                                    unset($queryParams['status_kontrak']);
                                    if (empty($queryParams)) {
                                        $removeStatusUrl = route('company.participants');
                                    } else {
                                        $removeStatusUrl = route('company.participants') . '?' . http_build_query($queryParams);
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusColors[request('status_kontrak')] ?? 'secondary' }}">
                                    <i class="fas fa-filter me-1"></i>Status: {{ $statusLabels[request('status_kontrak')] ?? request('status_kontrak') }}
                                    <a href="{{ $removeStatusUrl }}" class="text-white ms-2" style="text-decoration: none;">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request()->has('status_followup') && request('status_followup') != 'all')
                                @php
                                    $followupLabels = [
                                        'followed_up' => 'Sudah Difollowup',
                                        'not_followed_up' => 'Belum Difollowup'
                                    ];
                                    $followupColors = [
                                        'followed_up' => 'success',
                                        'not_followed_up' => 'secondary'
                                    ];
                                    $queryParams = request()->query();
                                    unset($queryParams['status_followup']);
                                    if (empty($queryParams)) {
                                        $removeFollowupUrl = route('company.participants');
                                    } else {
                                        $removeFollowupUrl = route('company.participants') . '?' . http_build_query($queryParams);
                                    }
                                @endphp
                                <span class="badge bg-{{ $followupColors[request('status_followup')] ?? 'secondary' }}">
                                    <i class="fas fa-check-circle me-1"></i>Followup: {{ $followupLabels[request('status_followup')] ?? request('status_followup') }}
                                    <a href="{{ $removeFollowupUrl }}" class="text-white ms-2" style="text-decoration: none;">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($participants->total() > 0)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Menampilkan <strong>{{ $participants->firstItem() }}</strong> - <strong>{{ $participants->lastItem() }}</strong> dari <strong>{{ $participants->total() }}</strong> data peserta magang
                    </div>
                @endif
                <div class="mb-3 d-flex gap-2">
                    <form id="bulkEditForm" method="GET" action="{{ route('company.participants.bulk-edit') }}">
                        <input type="hidden" name="ids" id="selectedIdsForEdit" value="">
                        <button type="button" class="btn btn-primary btn-sm" id="bulkEditBtn" onclick="goToBulkEdit()" disabled>
                            <i class="fas fa-edit me-1"></i>Edit Terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </form>
                    <button type="button" class="btn btn-success btn-sm" id="bulkFollowupBtn" onclick="showBulkFollowupModal()" disabled>
                        <i class="fas fa-check-circle me-1"></i>Followup Terpilih (<span id="selectedCountFollowup">0</span>)
                    </button>
                    <form id="bulkUnfollowupForm" method="POST" action="{{ route('company.participants.bulk-unfollowup') }}">
                        @csrf
                        <input type="hidden" name="ids" id="selectedIdsForUnfollowup" value="">
                        <button type="button" class="btn btn-warning btn-sm" id="bulkUnfollowupBtn" onclick="confirmBulkUnfollowup()" disabled>
                            <i class="fas fa-times-circle me-1"></i>Batalkan Followup (<span id="selectedCountUnfollowup">0</span>)
                        </button>
                    </form>
                    <form id="bulkDeleteForm" method="POST" action="{{ route('company.participants.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids" id="selectedIds" value="">
                        <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" onclick="confirmBulkDelete()" disabled>
                            <i class="fas fa-trash me-1"></i>Hapus Terpilih (<span id="selectedCountDelete">0</span>)
                        </button>
                    </form>
                </div>
                <div style="overflow-x: visible;">
                    <table class="table table-hover align-middle" style="table-layout: auto; width: 100%; font-size: 0.9rem;">
                        <thead style="background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); color: white;">
                            <tr>
                                <th style="width: 40px; padding: 8px;">
                                    <input type="checkbox" id="selectAll" title="Pilih Semua">
                                </th>
                                <th style="width: 50px; padding: 8px;">No</th>
                                <th style="width: 150px; padding: 8px;">Nama</th>
                                <th style="width: 100px; padding: 8px;">Tanggal Lahir</th>
                                <th style="width: 200px; padding: 8px;">Alamat</th>
                                <th style="width: 120px; padding: 8px;">Nama Perusahaan</th>
                                <th style="width: 100px; padding: 8px;">Mulai Kontrak</th>
                                <th style="width: 120px; padding: 8px;">Kontrak Habis</th>
                                <th style="width: 140px; padding: 8px;">Durasi Kontrak</th>
                                <th style="width: 130px; padding: 8px;">Status Followup</th>
                                <th style="width: 120px; padding: 8px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $index => $participant)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $oneMonthFromNow = $now->copy()->addMonth();
                                    $isExpired = $participant->kontrak_habis->isPast();
                                    $isExpiringSoon = !$isExpired && $participant->kontrak_habis >= $now && $participant->kontrak_habis <= $oneMonthFromNow;
                                    $isActive = !$isExpired && $participant->kontrak_habis > $oneMonthFromNow;
                                    $daysLeft = $now->diffInDays($participant->kontrak_habis, false);
                                    
                                    // Calculate contract duration (from mulai_kontrak to kontrak_habis)
                                    $mulaiKontrak = \Carbon\Carbon::parse($participant->mulai_kontrak);
                                    $kontrakHabis = \Carbon\Carbon::parse($participant->kontrak_habis);
                                    $totalMonths = $mulaiKontrak->diffInMonths($kontrakHabis);
                                    $tempDate = $mulaiKontrak->copy()->addMonths($totalMonths);
                                    $remainingDays = $tempDate->diffInDays($kontrakHabis, false);
                                    // If remainingDays is negative, it means we went past the end date, so adjust
                                    if ($remainingDays < 0) {
                                        $totalMonths = max(0, $totalMonths - 1);
                                        $tempDate = $mulaiKontrak->copy()->addMonths($totalMonths);
                                        $remainingDays = $tempDate->diffInDays($kontrakHabis, false);
                                    }
                                @endphp
                                <tr class="{{ $isExpired ? 'table-danger' : ($isExpiringSoon ? 'table-warning' : '') }}" style="{{ $isExpiringSoon ? 'border-left: 3px solid #ffc107;' : ($isExpired ? 'border-left: 3px solid #dc3545;' : '') }}">
                                    <td style="padding: 8px;">
                                        <input type="checkbox" class="participant-checkbox" name="participant_ids[]" value="{{ $participant->id }}" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td style="padding: 8px; white-space: nowrap;">
                                        @if($isExpiringSoon)
                                            <i class="fas fa-exclamation-circle text-warning me-1" title="Kontrak akan habis dalam 1 bulan"></i>
                                        @elseif($isExpired)
                                            <i class="fas fa-times-circle text-danger me-1" title="Kontrak sudah habis"></i>
                                        @else
                                            <i class="fas fa-check-circle text-success me-1" title="Kontrak masih aktif"></i>
                                        @endif
                                        {{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}
                                    </td>
                                    <td style="padding: 8px;">
                                        <strong style="font-size: 0.9rem;">{{ $participant->nama }}</strong>
                                        @if($isExpiringSoon)
                                            <br><span class="badge bg-warning text-dark mt-1" style="font-size: 0.7rem;" title="Akan habis: {{ $participant->kontrak_habis->format('m/d/Y') }}">
                                                <i class="fas fa-clock me-1"></i>Reminder ({{ abs($daysLeft) }} hari)
                                            </span>
                                        @elseif($isExpired)
                                            <br><span class="badge bg-danger mt-1" style="font-size: 0.7rem;" title="Sudah habis: {{ $participant->kontrak_habis->format('m/d/Y') }}">
                                                <i class="fas fa-times me-1"></i>Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 8px; white-space: nowrap; font-size: 0.85rem;">{{ $participant->tanggal_lahir->format('m/d/Y') }}</td>
                                    <td style="padding: 8px; max-width: 200px; word-wrap: break-word; font-size: 0.8rem;" title="{{ $participant->alamat }}">{{ Str::limit($participant->alamat, 40) }}</td>
                                    <td style="padding: 8px; white-space: nowrap; font-size: 0.85rem;">{{ $participant->nama_perusahaan }}</td>
                                    <td style="padding: 8px; white-space: nowrap; font-size: 0.85rem;">{{ $participant->mulai_kontrak->format('m/d/Y') }}</td>
                                    <td>
                                        @if($isExpired)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>{{ $participant->kontrak_habis->format('m/d/Y') }}
                                            </span>
                                            <br><small class="text-danger">Sudah habis</small>
                                        @elseif($isExpiringSoon)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $participant->kontrak_habis->format('m/d/Y') }}
                                            </span>
                                            <br><small class="text-warning">
                                                @if($daysLeft == 0)
                                                    Habis hari ini
                                                @elseif($daysLeft == 1)
                                                    Tersisa 1 hari
                                                @else
                                                    Tersisa {{ $daysLeft }} hari
                                                @endif
                                            </small>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>{{ $participant->kontrak_habis->format('m/d/Y') }}
                                            </span>
                                            <br><small class="text-muted">Masih aktif</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>
                                            @if($totalMonths > 0 && $remainingDays > 0)
                                                {{ $totalMonths }} {{ $totalMonths == 1 ? 'bulan' : 'bulan' }}, {{ $remainingDays }} {{ $remainingDays == 1 ? 'hari' : 'hari' }}
                                            @elseif($totalMonths > 0 && $remainingDays == 0)
                                                {{ $totalMonths }} {{ $totalMonths == 1 ? 'bulan' : 'bulan' }}
                                            @elseif($totalMonths == 0 && $remainingDays > 0)
                                                {{ $remainingDays }} {{ $remainingDays == 1 ? 'hari' : 'hari' }}
                                            @else
                                                0 hari
                                            @endif
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $mulaiKontrak->format('m/d/Y') }} - {{ $kontrakHabis->format('m/d/Y') }}
                                        </small>
                                    </td>
                                    <td style="padding: 8px;">
                                        @if($participant->is_followed_up)
                                            <span class="badge bg-success" style="font-size: 0.75rem;">
                                                <i class="fas fa-check-circle me-1"></i>Sudah
                                            </span>
                                            @if($participant->followed_up_at)
                                                <br><small class="text-muted" style="font-size: 0.7rem;">{{ $participant->followed_up_at->format('m/d/Y') }}</small>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size: 0.75rem; padding: 2px 6px;" onclick="toggleFollowup({{ $participant->id }})" title="Klik untuk menandai sudah difollowup">
                                                <i class="fas fa-clock me-1"></i>Belum
                                            </button>
                                        @endif
                                    </td>
                                    <td style="padding: 8px;">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('company.participants.edit', $participant->id) }}" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 2px 6px;" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" style="font-size: 0.75rem; padding: 2px 6px;" onclick="confirmDelete({{ $participant->id }}, '{{ $participant->nama }}')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        @if(request()->has('search') || request()->has('nama_perusahaan') || (request()->has('status_kontrak') && request('status_kontrak') != 'all') || (request()->has('status_followup') && request('status_followup') != 'all'))
                                            <p class="text-muted mb-2">Tidak ada data yang sesuai dengan pencarian atau filter yang dipilih.</p>
                                            @if(request()->has('search'))
                                                <p class="text-muted small mb-2">Kata kunci: <strong>"{{ request('search') }}"</strong></p>
                                            @endif
                                            <a href="{{ route('company.participants') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-redo me-1"></i>Reset Filter & Pencarian
                                            </a>
                                        @else
                                            <p class="text-muted">Belum ada data peserta magang.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Links --}}
                @if($participants->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $participants->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Results Info --}}
        @if($participants->total() > 0)
            <div class="mt-3 p-3 bg-light rounded text-muted small">
                <i class="fas fa-info-circle me-1"></i>Menampilkan <strong>{{ $participants->firstItem() }}</strong> - <strong>{{ $participants->lastItem() }}</strong> dari <strong>{{ $participants->total() }}</strong> data peserta magang
                @if(request()->has('search') || request()->has('nama_perusahaan') || (request()->has('status_kontrak') && request('status_kontrak') != 'all') || (request()->has('status_followup') && request('status_followup') != 'all'))
                    (hasil pencarian/filter)
                    @if(request()->has('search'))
                        untuk kata kunci: <strong>"{{ request('search') }}"</strong>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    function resetFilter() {
        window.location.href = "{{ route('company.participants') }}";
    }

    // Add loading state when form is submitted
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
                    
                    // Re-enable after 5 seconds if form doesn't submit
                    setTimeout(function() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                }
            });
        }
    });

    // Select All checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.participant-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }
    });

    // Update bulk delete, edit, and followup button state
    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
        const count = checkboxes.length;
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkEditBtn = document.getElementById('bulkEditBtn');
        const bulkFollowupBtn = document.getElementById('bulkFollowupBtn');
        const selectedCount = document.getElementById('selectedCount');
        const selectedCountDelete = document.getElementById('selectedCountDelete');
        const selectedCountFollowup = document.getElementById('selectedCountFollowup');
        
        if (bulkDeleteBtn && selectedCountDelete) {
            selectedCountDelete.textContent = count;
            bulkDeleteBtn.disabled = count === 0;
        }
        
        if (bulkEditBtn && selectedCount) {
            selectedCount.textContent = count;
            bulkEditBtn.disabled = count === 0;
        }
        
        const bulkUnfollowupBtn = document.getElementById('bulkUnfollowupBtn');
        const selectedCountUnfollowup = document.getElementById('selectedCountUnfollowup');
        
        if (bulkFollowupBtn && selectedCountFollowup) {
            selectedCountFollowup.textContent = count;
            bulkFollowupBtn.disabled = count === 0;
        }
        
        if (bulkUnfollowupBtn && selectedCountUnfollowup) {
            selectedCountUnfollowup.textContent = count;
            bulkUnfollowupBtn.disabled = count === 0;
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.participant-checkbox');
        const selectAll = document.getElementById('selectAll');
        if (allCheckboxes.length > 0 && selectAll) {
            selectAll.checked = checkboxes.length === allCheckboxes.length;
            selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
        }
    }
    
    // Show bulk followup modal
    function showBulkFollowupModal() {
        const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih minimal 1 data peserta untuk di-followup.');
            return;
        }
        
        const ids = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('modalSelectedIdsForFollowup').value = ids.join(',');
        document.getElementById('modalFollowupCount').textContent = ids.length;
        
        // Reset notes
        const notesTextarea = document.getElementById('followup_notes');
        const charCount = document.getElementById('notesCharCount');
        if (notesTextarea) {
            notesTextarea.value = '';
        }
        if (charCount) {
            charCount.textContent = '0';
        }
        
        // Show modal
        const modalElement = document.getElementById('bulkFollowupModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }
    
    // Confirm bulk unfollowup
    function confirmBulkUnfollowup() {
        const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih minimal 1 data peserta untuk membatalkan followup.');
            return;
        }
        
        const ids = Array.from(checkboxes).map(cb => cb.value);
        const names = Array.from(checkboxes).map(cb => {
            const row = cb.closest('tr');
            const nameCell = row.querySelector('td:nth-child(3)');
            return nameCell ? nameCell.textContent.trim().split('\n')[0] : 'Peserta';
        });
        
        let message = 'Apakah Anda yakin ingin membatalkan followup untuk ' + ids.length + ' data peserta?\n\n';
        if (ids.length <= 5) {
            message += 'Data yang akan dibatalkan followup:\n';
            names.forEach((name, index) => {
                message += (index + 1) + '. ' + name + '\n';
            });
        } else {
            message += 'Data yang akan dibatalkan followup:\n';
            names.slice(0, 5).forEach((name, index) => {
                message += (index + 1) + '. ' + name + '\n';
            });
            message += '... dan ' + (ids.length - 5) + ' data lainnya\n';
        }
        
        if (confirm(message)) {
            document.getElementById('selectedIdsForUnfollowup').value = ids.join(',');
            document.getElementById('bulkUnfollowupForm').submit();
        }
    }
    
    // Go to bulk edit page
    function goToBulkEdit() {
        const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih minimal 1 data peserta untuk diedit.');
            return;
        }
        
        const ids = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('selectedIdsForEdit').value = ids.join(',');
        document.getElementById('bulkEditForm').submit();
    }

    // Confirm bulk delete
    function confirmBulkDelete() {
        const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
        const count = checkboxes.length;
        
        if (count === 0) {
            alert('Pilih minimal satu data untuk dihapus!');
            return;
        }
        
        const names = Array.from(checkboxes).map(cb => {
            const row = cb.closest('tr');
            const nameElement = row.querySelector('td:nth-child(3) strong');
            return nameElement ? nameElement.textContent.trim() : 'Data';
        });
        
        let message = 'Apakah Anda yakin ingin menghapus ' + count + ' data peserta magang berikut?\n\n';
        if (count <= 5) {
            message += names.join('\n');
        } else {
            message += names.slice(0, 5).join('\n') + '\n... dan ' + (count - 5) + ' data lainnya';
        }
        message += '\n\nTindakan ini tidak dapat dibatalkan!';
        
        if (confirm(message)) {
            const ids = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('selectedIds').value = ids.join(',');
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    // Delete confirmation
    function confirmDelete(id, nama) {
        if (confirm('Apakah Anda yakin ingin menghapus data peserta magang "' + nama + '"?\n\nTindakan ini tidak dapat dibatalkan!')) {
            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('company.participants.destroy', ':id') }}".replace(':id', id);
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Toggle followup status
    function toggleFollowup(id, currentStatus) {
        const action = currentStatus ? 'mengubah status menjadi belum difollowup' : 'menandai sebagai sudah difollowup';
        if (confirm('Apakah Anda yakin ingin ' + action + '?')) {
            // Create and submit followup toggle form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('company.participants.toggle-followup', ':id') }}".replace(':id', id);
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add is_followed_up value (toggle)
            const followupInput = document.createElement('input');
            followupInput.type = 'hidden';
            followupInput.name = 'is_followed_up';
            followupInput.value = currentStatus ? '0' : '1';
            form.appendChild(followupInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Clear search
    function clearSearch() {
        document.getElementById('search').value = '';
        const form = document.getElementById('filterForm');
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.delete('search');
        window.location.href = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
    }

    // Enter key to submit search
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('filterForm').submit();
                }
            });
        }
    });
</script>

<!-- Modal for Bulk Followup with Notes -->
<div class="modal fade" id="bulkFollowupModal" tabindex="-1" aria-labelledby="bulkFollowupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                <h5 class="modal-title" id="bulkFollowupModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Followup Peserta Terpilih
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkFollowupForm" method="POST" action="{{ route('company.participants.bulk-followup') }}">
                @csrf
                <input type="hidden" name="ids" id="modalSelectedIdsForFollowup" value="">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda akan menandai <strong id="modalFollowupCount">0</strong> data peserta sebagai "Sudah Difollowup".
                    </div>
                    
                    <div class="mb-3">
                        <label for="followup_notes" class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>Catatan Followup <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea class="form-control" 
                                  id="followup_notes" 
                                  name="followup_notes" 
                                  rows="4" 
                                  placeholder="Masukkan catatan followup (opsional)..."
                                  maxlength="1000"></textarea>
                        <small class="text-muted">
                            <span id="notesCharCount">0</span>/1000 karakter
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i>Simpan Followup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

