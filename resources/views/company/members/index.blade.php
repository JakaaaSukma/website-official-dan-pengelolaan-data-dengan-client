@extends('layouts.company')

@section('content')
<style>
    .company-members-dashboard {
        color: #1f1f1f;
    }

    .company-members-dashboard .hero-card,
    .company-members-dashboard .filter-card,
    .company-members-dashboard .action-card,
    .company-members-dashboard .table-card,
    .company-members-dashboard .summary-card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.06);
    }

    .company-members-dashboard .hero-card {
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(255, 0, 0, 0.12), transparent 28%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.99), rgba(255, 246, 246, 0.94));
    }

    .company-members-dashboard .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        border-radius: 999px;
        padding: .55rem .9rem;
        background: rgba(255, 0, 0, 0.1);
        color: #d7263d;
        font-size: .85rem;
        font-weight: 700;
    }

    .company-members-dashboard .hero-title {
        font-size: clamp(2rem, 3vw, 3rem);
        line-height: 1.08;
        letter-spacing: -.03em;
    }

    .company-members-dashboard .hero-copy,
    .company-members-dashboard .muted-copy {
        color: #6c757d;
    }

    .company-members-dashboard .hero-aside {
        height: 100%;
        border-radius: 24px;
        padding: 1.5rem;
        color: #fff;
        background: linear-gradient(180deg, #1f1f1f 0%, #3b3b3b 100%);
    }

    .company-members-dashboard .hero-aside strong {
        font-size: 2.35rem;
        line-height: 1;
    }
    
    .status-select {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
    
    .status-select.belum_tes { background-color: #6c757d; color: white; }
    .status-select.sudah_training { background-color: #0d6efd; color: white; }
    .status-select.belum_training { background-color: #ffc107; color: #212529; }
    .status-select.sudah_kerja { background-color: #198754; color: white; }
    .status-select.gagal { background-color: #dc3545; color: white; }
        
        /* Dropdown checkbox styling */
        .dropdown-menu .form-check {
            padding-left: 1.75rem;
            cursor: pointer;
        }
        .dropdown-menu .form-check:hover {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
        .dropdown-menu .form-check-label {
            cursor: pointer;
            display: block;
            width: 100%;
            padding: 0.25rem 0;
        }
</style>
<div class="container-fluid px-3 px-lg-4 py-4 company-members-dashboard">
    <div class="hero-card p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-xl-8">
                <span class="hero-pill mb-3">
                    <i class="fas fa-id-card"></i>
                    <span>Dashboard Data Member</span>
                </span>
                <h1 class="hero-title fw-bold mb-3">Selamat datang di Dashboard Data Member</h1>
                <p class="hero-copy fs-5 mb-4">Kelola data member dengan mudah dan terintegrasi dengan Google Sheets.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('company.members.create') }}" class="btn btn-primary px-4">
                        <i class="fas fa-plus me-2"></i>Tambah Member
                    </a>
                    <a href="{{ route('company.members', ['refresh_cache' => 1]) }}" class="btn btn-outline-primary px-4">
                        <i class="fas fa-sync-alt me-2"></i>Sync dari Google Sheets
                    </a>
                    <a href="{{ route('company.members.export', request()->query()) }}" class="btn btn-success px-4">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </a>
                    
                    @if(file_exists(storage_path('app/google/token.json')))
                        <form action="{{ route('google.disconnect') }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fas fa-unlink me-2"></i>Putuskan Google Sheets
                            </button>
                        </form>
                    @else
                        <a href="{{ route('google.auth') }}" class="btn btn-outline-secondary px-4">
                            <i class="fab fa-google me-2"></i>Hubungkan Google Sheets
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-xl-4">
                <div class="hero-aside">
                    <div class="text-uppercase small opacity-75 mb-2">Total Member</div>
                    <strong>{{ $members->total() }}</strong>
                    <div class="mb-4">Member terdaftar dalam 14 bulan terakhir</div>
                    <div>
                        <div class="small opacity-75 mb-1">Halaman saat ini</div>
                        <div class="fw-semibold">{{ $members->firstItem() ?? 0 }} - {{ $members->lastItem() ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-card p-4 mb-4">
        <form method="GET" action="{{ route('company.members') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Cari</label>
                    <input type="text" class="form-control" name="search" placeholder="Nama, Email, NIK..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Referensi</label>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="referenceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="referenceSelected">Pilih Referensi</span>
                        </button>
                        <ul class="dropdown-menu p-2 w-100" aria-labelledby="referenceDropdown" style="max-height: 300px; overflow-y: auto;">
                            @foreach($references as $ref)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input reference-checkbox" type="checkbox" name="reference[]" value="{{ $ref }}" id="ref-{{ $loop->index }}" {{ in_array($ref, request('reference', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="ref-{{ $loop->index }}">
                                            {{ $ref }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                    <select class="form-select" name="gender">
                        <option value="">Semua</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Umur</label>
                    <select class="form-select" name="age">
                        <option value="">Semua</option>
                        <option value="under_25" {{ request('age') == 'under_25' ? 'selected' : '' }}>Dibawah 25</option>
                        <option value="above_25" {{ request('age') == 'above_25' ? 'selected' : '' }}>25 Tahun Keatas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="statusSelected">Pilih Status</span>
                        </button>
                        <ul class="dropdown-menu p-2 w-100" aria-labelledby="statusDropdown" style="max-height: 300px; overflow-y: auto;">
                            @php
                                $statusOptions = [
                                    'belum_tes' => 'Belum Tes',
                                    'sudah_training' => 'Sudah Training',
                                    'belum_training' => 'Belum Training',
                                    'sudah_kerja' => 'Sudah Kerja',
                                    'gagal' => 'Gagal'
                                ];
                            @endphp
                            @foreach($statusOptions as $value => $label)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="checkbox" name="status[]" value="{{ $value }}" id="status-{{ $loop->index }}" {{ in_array($value, request('status', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="status-{{ $loop->index }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Perusahaan Favorite</label>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="perusahaanDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="perusahaanSelected">Pilih Perusahaan</span>
                        </button>
                        <ul class="dropdown-menu p-2 w-100" aria-labelledby="perusahaanDropdown" style="max-height: 300px; overflow-y: auto;">
                            @foreach($perusahaanFavorites as $perusahaan)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input perusahaan-checkbox" type="checkbox" name="perusahaan_favorite[]" value="{{ $perusahaan }}" id="pf-{{ $loop->index }}" {{ in_array($perusahaan, request('perusahaan_favorite', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="pf-{{ $loop->index }}">
                                            {{ $perusahaan }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
        <div class="col-md-1 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('company.members') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Action Section -->
    <div class="action-card p-4 mb-4" id="bulkActionSection" style="display: none;">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="fw-semibold">
                <i class="fas fa-check-square me-2"></i>
                <span id="selectedCount">0</span> member dipilih
            </div>
            <div class="d-flex gap-2 align-items-center">
                <label class="mb-0">Ubah status menjadi:</label>
                <select id="bulkStatusSelect" class="form-select" style="width: auto;">
                    <option value="">Pilih status</option>
                    <option value="belum_tes">Belum Tes</option>
                    <option value="sudah_training">Sudah Training</option>
                    <option value="belum_training">Belum Training</option>
                    <option value="sudah_kerja">Sudah Kerja</option>
                    <option value="gagal">Gagal</option>
                </select>
                <button id="bulkUpdateBtn" class="btn btn-primary" disabled>
                    <i class="fas fa-sync-alt me-2"></i>Update Status
                </button>
            </div>
            <button id="clearSelectionBtn" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Bersihkan Pilihan
            </button>
        </div>
    </div>

    <div class="table-card border-0 p-4">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-hover align-middle table-sm">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th style="width: 40px;">No</th>
                        <th style="width: 80px;">Referensi</th>
                        <th style="width: 100px;">Tanggal Daftar</th>
                        <th style="min-width: 150px;">Nama Lengkap</th>
                        <th style="width: 140px;">NIK</th>
                        <th style="min-width: 100px;">Tempat Lahir</th>
                        <th style="width: 100px;">Tanggal Lahir</th>
                        <th style="width: 90px;">Jenis Kelamin</th>
                        <th style="min-width: 150px;">Perusahaan Favorite</th>
                        <th style="min-width: 180px;">Email Aktif</th>
                        <th style="width: 130px;">Nomor WA</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $index => $member)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input member-checkbox" data-id="{{ $member->id }}">
                            </td>
                            <td class="text-center">{{ $members->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $member->reference ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div>{{ $member->tanggal_daftar ? $member->tanggal_daftar->format('d/m/Y') : ($member->created_at ? $member->created_at->format('d/m/Y') : '-') }}</div>
                                <div class="small text-muted">{{ $member->waktu_terdaftar }}</div>
                            </td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;" title="{{ $member->name }}">
                                <strong>{{ $member->name }}</strong>
                            </td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;" title="{{ $member->nik_ktp }}">
                                {{ $member->nik_ktp ?? '-' }}
                            </td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100px;" title="{{ $member->tempat_lahir }}">
                                {{ $member->tempat_lahir ?? '-' }}
                            </td>
                            <td class="text-center">
                                <div>{{ $member->tanggal_lahir ? $member->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                                <div class="small text-muted">{{ $member->umur }}</div>
                            </td>
                            <td class="text-center">{{ $member->gender ?? '-' }}</td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;" title="{{ $member->perusahaan_favorite }}">
                                {{ $member->perusahaan_favorite ?? '-' }}
                            </td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;" title="{{ $member->email }}">
                                {{ $member->email ?? '-' }}
                            </td>
                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;">
                                @if($member->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}" target="_blank" class="text-decoration-none" title="{{ $member->phone }}">
                                        {{ $member->phone }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-select {{ $member->status }}" 
                                        data-id="{{ $member->id }}"
                                        onchange="updateMemberStatus(this)">
                                    <option value="belum_tes" {{ $member->status == 'belum_tes' ? 'selected' : '' }}>Belum Tes</option>
                                    <option value="sudah_training" {{ $member->status == 'sudah_training' ? 'selected' : '' }}>Sudah Training</option>
                                    <option value="belum_training" {{ $member->status == 'belum_training' ? 'selected' : '' }}>Belum Training</option>
                                    <option value="sudah_kerja" {{ $member->status == 'sudah_kerja' ? 'selected' : '' }}>Sudah Kerja</option>
                                    <option value="gagal" {{ $member->status == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                </select>
                            </td>
                            <td>
                                @if($member->source == 'local' || isset($member->id))
                                    <div class="btn-group">
                                        <a href="{{ route('company.members.edit', $member->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $member->id }})">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $member->id }}" action="{{ route('company.members.destroy', $member->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                    <span class="text-muted small">Data Google Sheet</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <h5>Belum ada data member</h5>
                                    <p class="mb-0">Klik "Tambah Member" untuk menambah member baru atau "Sync dari Google Sheets" untuk menarik data dari Google Sheets.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Menampilkan {{ $members->firstItem() ?? 0 }} sampai {{ $members->lastItem() ?? 0 }} dari {{ $members->total() }} data
            </div>
            {{ $members->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    // Update dropdown selected text
    function updateDropdownText(checkboxClass, displayElementId, defaultText) {
        const checkboxes = document.querySelectorAll(checkboxClass);
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        const displayElement = document.getElementById(displayElementId);
        
        if (selected.length === 0) {
            displayElement.textContent = defaultText;
        } else if (selected.length === 1) {
            displayElement.textContent = selected[0].nextElementSibling.textContent.trim();
        } else {
            displayElement.textContent = `${selected.length} dipilih`;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateDropdownText('.reference-checkbox', 'referenceSelected', 'Pilih Referensi');
        updateDropdownText('.status-checkbox', 'statusSelected', 'Pilih Status');
        updateDropdownText('.perusahaan-checkbox', 'perusahaanSelected', 'Pilih Perusahaan');

        // Add event listeners
        document.querySelectorAll('.reference-checkbox').forEach(cb => {
            cb.addEventListener('change', () => updateDropdownText('.reference-checkbox', 'referenceSelected', 'Pilih Referensi'));
        });
        document.querySelectorAll('.status-checkbox').forEach(cb => {
            cb.addEventListener('change', () => updateDropdownText('.status-checkbox', 'statusSelected', 'Pilih Status'));
        });
        document.querySelectorAll('.perusahaan-checkbox').forEach(cb => {
            cb.addEventListener('change', () => updateDropdownText('.perusahaan-checkbox', 'perusahaanSelected', 'Pilih Perusahaan'));
        });
    });

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    
    // Status mapping for classes
    const statusClasses = {
        'belum_tes': 'bg-secondary',
        'sudah_training': 'bg-primary',
        'belum_training': 'bg-warning text-dark',
        'sudah_kerja': 'bg-success',
        'gagal': 'bg-danger'
    };
    
    // Confirm delete
    function confirmDelete(id) {
        if (confirm('Anda yakin ingin menghapus member ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
    
    // Inline update status
    async function updateMemberStatus(select) {
        const id = select.dataset.id;
        const status = select.value;
        
        try {
            const formData = new FormData();
            formData.append('status', status);
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            
            const response = await fetch(`/company/members/${id}/update-status`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            
            if (response.ok) {
                // Update the class of the select
                select.className = 'form-select form-select-sm status-select ' + status;
                
                // Show success notification
                const originalText = select.options[select.selectedIndex].text;
                select.options[select.selectedIndex].text = '✓ ' + originalText;
                setTimeout(() => {
                    select.options[select.selectedIndex].text = originalText;
                }, 1000);
            } else {
                console.error('Response not ok:', response.status, response.statusText);
                const text = await response.text();
                console.error('Response text:', text);
                alert('Gagal mengupdate status. Lihat console untuk detail.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        }
    }
    
    // Bulk selection with localStorage persistence
    const STORAGE_KEY = 'company_selected_member_ids';
    let selectedMemberIds = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const bulkActionSection = document.getElementById('bulkActionSection');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkStatusSelect = document.getElementById('bulkStatusSelect');
    const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    
    function updateBulkSectionVisibility() {
        if (selectedMemberIds.length > 0) {
            bulkActionSection.style.display = 'block';
        } else {
            bulkActionSection.style.display = 'none';
        }
        selectedCountSpan.textContent = selectedMemberIds.length;
        bulkUpdateBtn.disabled = !bulkStatusSelect.value || selectedMemberIds.length === 0;
    }
    
    function saveSelection() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedMemberIds));
    }
    
    function restoreSelection() {
        checkboxes.forEach(checkbox => {
            const id = checkbox.dataset.id;
            if (selectedMemberIds.includes(id)) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
        updateSelectAllState();
    }
    
    // Individual checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const id = this.dataset.id;
            if (this.checked) {
                if (!selectedMemberIds.includes(id)) {
                    selectedMemberIds.push(id);
                }
            } else {
                selectedMemberIds = selectedMemberIds.filter(selId => selId !== id);
            }
            saveSelection();
            updateBulkSectionVisibility();
            updateSelectAllState();
        });
    });
    
    // Select all
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                const id = checkbox.dataset.id;
                if (selectAllCheckbox.checked) {
                    if (!selectedMemberIds.includes(id)) {
                        selectedMemberIds.push(id);
                    }
                } else {
                    // Only remove IDs that are visible on this page from selection
                    selectedMemberIds = selectedMemberIds.filter(selId => 
                        !Array.from(checkboxes).some(cb => cb.dataset.id === selId)
                    );
                }
            });
            saveSelection();
            updateBulkSectionVisibility();
        });
    }
    
    function updateSelectAllState() {
        if (checkboxes.length === 0) {
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            return;
        }
        
        const visibleIds = Array.from(checkboxes).map(cb => cb.dataset.id);
        const allVisibleSelected = visibleIds.every(id => selectedMemberIds.includes(id));
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allVisibleSelected;
        }
    }
    
    // Clear selection
    clearSelectionBtn.addEventListener('click', function() {
        selectedMemberIds = [];
        saveSelection();
        checkboxes.forEach(checkbox => checkbox.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        bulkStatusSelect.value = '';
        updateBulkSectionVisibility();
    });
    
    // Initialize on page load
    restoreSelection();
    updateBulkSectionVisibility();
    
    // Bulk status select
    bulkStatusSelect.addEventListener('change', function() {
        bulkUpdateBtn.disabled = !this.value || selectedMemberIds.length === 0;
    });
    
    // Bulk update
    bulkUpdateBtn.addEventListener('click', async function() {
        if (!bulkStatusSelect.value || selectedMemberIds.length === 0) {
            return;
        }
        
        if (!confirm(`Anda yakin ingin mengubah status ${selectedMemberIds.length} member menjadi "${bulkStatusSelect.options[bulkStatusSelect.selectedIndex].text}"?`)) {
            return;
        }
        
        try {
            const formData = new FormData();
            selectedMemberIds.forEach(id => formData.append('ids[]', id));
            formData.append('status', bulkStatusSelect.value);
            formData.append('_token', csrfToken);
            
            const response = await fetch('{{ route('company.members.bulk-update-status') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            
            if (response.ok) {
                alert('Berhasil mengupdate status!');
                // Clear selection after successful update
                localStorage.removeItem(STORAGE_KEY);
                location.reload();
            } else {
                console.error('Bulk update failed:', response.status, response.statusText);
                const text = await response.text();
                console.error('Response:', text);
                alert('Gagal mengupdate status. Lihat console untuk detail.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        }
    });
</script>
@endsection