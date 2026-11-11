@extends(request()->routeIs('gik-superadmin.*') ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@php
$isSA = request()->routeIs('gik-superadmin.*');
$detailJsonRouteName = $isSA ? 'gik-superadmin.requests.detail-json' : 'gik-hrd.requests.detail-json';
$reviewRouteName = $isSA ? 'gik-superadmin.requests.review-batch' : 'gik-hrd.requests.review';

if ($isSA) {
    $pageEyebrow = 'Super Admin • Cross-Tenant Monitoring';
    $pageTitle = 'Monitoring Request & Operasi Seluruh Mitra';
    $pageSubtitle = 'Tracking status dan approval permohonan Add Contract, Extend Contract, dan Off-boarding dari seluruh HR Company.';
} else {
    $pageEyebrow = 'HR Company • Status Operasional';
    $pageTitle = 'Riwayat & Status Request';
    $pageSubtitle = 'Pantau proses approval dan status pengajuan kontrak, perpanjangan, serta off-boarding karyawan.';
}
@endphp

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">{{ $pageEyebrow }}</div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">{{ $pageTitle }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $pageSubtitle }}</p>
    </div>
    <div class="flex items-center gap-3">
        @if($isSA)
            <a href="{{ route('gik-superadmin.offboarding.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Manajemen Off-boarding
            </a>
            <a href="{{ route('gik-superadmin.add-contract.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-xl text-sm font-bold hover:bg-gik-primary-dark transition shadow-sm shadow-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                + Buat Add Contract
            </a>
        @else
            <a href="{{ route('gik-hrd.request-hub') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-xl text-sm font-bold hover:bg-gik-primary-dark transition shadow-sm shadow-red-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                + Buat Pengajuan Baru
            </a>
        @endif
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <!-- Filter Toolbar -->
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <div class="relative flex-1 min-w-[220px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ request('q', '') }}" placeholder="Cari no. request, nama, NIK, atau email..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        @if($isSA && isset($companies) && $companies->isNotEmpty())
            <select name="company_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
                <option value="">Semua Perusahaan</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        @endif

        <select name="type" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
            <option value="">Semua Jenis</option>
            <option value="add_contract" {{ request('type') === 'add_contract' ? 'selected' : '' }}>Add Contract</option>
            <option value="extend_contract" {{ request('type') === 'extend_contract' ? 'selected' : '' }}>Extend Contract</option>
            <option value="off_boarding" {{ request('type') === 'off_boarding' ? 'selected' : '' }}>Off-boarding</option>
        </select>

        <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted (Perlu Review)</option>
            <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-gik-primary text-white rounded-xl text-xs font-bold hover:bg-gik-primary-dark transition">Filter</button>

        @if(request('q') || request('type') || request('status') || request('company_id'))
            <a href="{{ url()->current() }}" class="text-xs text-gray-500 hover:text-red-600 font-semibold px-2 py-1">Reset</a>
        @endif
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-left">No. Request</th>
                    <th class="px-6 py-3.5 text-left">Jenis</th>
                    @if($isSA)
                        <th class="px-6 py-3.5 text-left">Perusahaan Klien</th>
                    @endif
                    <th class="px-6 py-3.5 text-left">Peserta / Pemohon</th>
                    <th class="px-6 py-3.5 text-left">Progress</th>
                    <th class="px-6 py-3.5 text-left">Status</th>
                    <th class="px-6 py-3.5 text-left">Diperbarui</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-6 py-4 font-mono text-xs text-gray-800 font-bold">
                            {{ $req->request_number }}
                        </td>
                        <td class="px-6 py-4">
                            @if($req->request_type === 'add_contract')
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-green-700 bg-green-100 px-2.5 py-0.5 rounded-full">+ ADD CONTRACT</span>
                            @elseif($req->request_type === 'extend_contract')
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full">↻ EXTEND</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-red-700 bg-red-100 px-2.5 py-0.5 rounded-full">← OFF-BOARDING</span>
                            @endif
                        </td>

                        @if($isSA)
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 block">{{ $req->company?->company_name ?? '—' }}</span>
                                <span class="text-[11px] text-gray-400">Tenant ID: #{{ $req->company_id }}</span>
                            </td>
                        @endif

                        <td class="px-6 py-4">
                            @if($req->batchItems && $req->batchItems->count() > 0)
                                <div class="font-bold text-gray-800 flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 text-[10px] font-extrabold uppercase">Batch</span>
                                    <span>{{ $req->batchItems->count() }} Peserta</span>
                                </div>
                                <div class="text-xs text-gray-500 truncate max-w-[240px] mt-0.5" title="{{ $req->batchItems->map(fn($bi) => $bi->contract?->full_name)->filter()->implode(', ') }}">
                                    {{ $req->batchItems->take(2)->map(fn($bi) => $bi->contract?->full_name)->filter()->implode(', ') }}{{ $req->batchItems->count() > 2 ? ' +' . ($req->batchItems->count() - 2) . ' lainnya' : '' }}
                                </div>
                            @else
                                <div class="font-semibold text-gray-800">{{ $req->full_name ?: ($req->contract?->full_name ?? '—') }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[240px]">{{ $req->email ?: ($req->contract?->email ?? '—') }}</div>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="w-36">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Step {{ $req->current_step }}/{{ $req->total_steps }}</span>
                                    <span class="font-bold text-gray-700">{{ $req->overallCompletionPercentage() }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gik-primary h-1.5 rounded-full" style="width:{{ $req->overallCompletionPercentage() }}%"></div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $req->status_badge_class }}">
                                {{ strtoupper(str_replace('_', ' ', $req->status)) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $req->updated_at->diffForHumans() }}
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap space-x-1.5">
                            @if($isSA)
                                {{-- Super Admin Actions --}}
                                @if(in_array($req->status, ['submitted', 'in_review']))
                                    <form method="POST" action="{{ route('gik-superadmin.requests.review-batch', $req) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin langsung menyetujui request {{ $req->request_number }}?')">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="text-xs font-bold text-green-700 hover:text-green-800 px-2.5 py-1.5 bg-green-50 hover:bg-green-100 rounded-lg transition inline-flex items-center gap-1">
                                            ✓ Quick Approve
                                        </button>
                                    </form>
                                    <button type="button" onclick="openRequestDetailModal({{ $req->id }}, true)" class="text-xs font-bold text-gik-primary hover:text-gik-primary-dark px-2.5 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg transition inline-flex items-center gap-1">
                                        Review
                                    </button>
                                @else
                                    <button type="button" onclick="openRequestDetailModal({{ $req->id }}, false)" class="text-xs font-semibold text-gray-600 hover:text-gray-800 px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg transition inline-flex items-center gap-1">
                                        Lihat Detail
                                    </button>
                                @endif

                                @if($req->status === 'draft')
                                    <form method="POST" action="{{ route('gik-superadmin.requests.destroy', $req) }}" onsubmit="return confirm('Hapus draft request {{ $req->request_number }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline px-2 py-1">Hapus</button>
                                    </form>
                                @endif
                            @else
                                {{-- HRD Company Actions --}}
                                @if(in_array($req->status, ['draft', 'rejected']))
                                    <a href="{{ route('gik-hrd.requests.edit', $req) }}" class="text-xs font-bold text-gik-accent hover:underline px-2.5 py-1.5 bg-blue-50 rounded-lg inline-flex items-center gap-1">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('gik-hrd.requests.destroy', $req) }}" onsubmit="return confirm('Hapus request {{ $req->request_number }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline px-2 py-1">Hapus</button>
                                    </form>
                                @else
                                    <button type="button" onclick="openRequestDetailModal({{ $req->id }}, false)" class="text-xs font-bold text-gray-700 hover:text-gray-900 px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg transition inline-flex items-center gap-1">
                                        Lihat Detail
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isSA ? 8 : 7 }}" class="px-6 py-16 text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="font-bold text-gray-700 mb-1">Belum ada request operasional</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if(request('q') || request('type') || request('status') || request('company_id'))
                                    Tidak ada data yang cocok dengan parameter filter pencarian.
                                @else
                                    {{ $isSA ? 'Semua permohonan dari HR Company akan tercantum di sini.' : 'Buat pengajuan baru melalui Pusat Pengajuan (Hub).' }}
                                @endif
                            </p>
                            @if($isSA)
                                <a href="{{ route('gik-superadmin.add-contract.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-xl text-sm font-semibold hover:bg-gik-primary-dark transition">
                                    + Buat Add Contract Baru
                                </a>
                            @else
                                <a href="{{ route('gik-hrd.request-hub') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-xl text-sm font-semibold hover:bg-gik-primary-dark transition">
                                    Buka Pusat Pengajuan
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<!-- UNIVERSAL REQUEST DETAIL & REVIEW MODAL -->
<div id="requestDetailModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-gik-primary flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-900" id="modalReqNumber">Loading...</h3>
                        <span id="modalReqTypeBadge" class="text-[10px] font-extrabold px-2 py-0.5 rounded-full"></span>
                        <span id="modalReqStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                    </div>
                    <p class="text-xs text-gray-500" id="modalReqCompany">Memuat informasi request...</p>
                </div>
            </div>
            <button type="button" onclick="closeRequestDetailModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-7 overflow-y-auto space-y-5 flex-1 text-sm" id="modalContent">
            <!-- Loading state -->
            <div id="modalLoading" class="py-12 text-center text-gray-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gik-primary mb-3"></div>
                <div>Mengambil detail request...</div>
            </div>

            <div id="modalLoadedData" class="hidden space-y-5">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Diajukan Oleh</div>
                        <div class="font-bold text-gray-800 text-xs mt-0.5" id="modalSubmittedBy">—</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Tanggal Pengajuan</div>
                        <div class="font-bold text-gray-800 text-xs mt-0.5" id="modalSubmittedAt">—</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Direview Oleh</div>
                        <div class="font-bold text-gray-800 text-xs mt-0.5" id="modalReviewedBy">—</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Jumlah Peserta</div>
                        <div class="font-bold text-gray-800 text-xs mt-0.5" id="modalItemsCount">—</div>
                    </div>
                </div>

                <!-- Rejection Alert (if rejected) -->
                <div id="modalRejectionBox" class="hidden p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-800">
                    <div class="font-bold mb-1 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Alasan Penolakan:
                    </div>
                    <div id="modalRejectionText" class="text-gray-700"></div>
                </div>

                <!-- Participants Table -->
                <div>
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">Daftar Peserta Terkait</h4>
                    <div class="border border-gray-100 rounded-2xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-100/70 text-gray-500 font-semibold uppercase">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Peserta / NIK</th>
                                    <th class="px-4 py-2.5 text-left">Jabatan</th>
                                    <th class="px-4 py-2.5 text-left" id="modalColOldDate">Kontrak Lama</th>
                                    <th class="px-4 py-2.5 text-left" id="modalColNewDate">Tgl Baru / Efektif</th>
                                    <th class="px-4 py-2.5 text-left">Alasan / Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="modalParticipantsBody" class="divide-y divide-gray-100 bg-white">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Forms for Super Admin -->
                @if($isSA)
                    <div id="modalSaActionArea" class="hidden pt-4 border-t border-gray-100 space-y-4">
                        <div class="text-xs font-bold text-gray-800 uppercase tracking-wider">Aksi Review Super Admin</div>
                        
                        <!-- Rejection Accordion / Form -->
                        <div id="rejectSection" class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penolakan (Wajib diisi jika menolak)</label>
                            <textarea id="rejectionReasonInput" rows="2" placeholder="Tuliskan catatan atau alasan mengapa permohonan ini ditolak..." class="w-full px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:ring-2 focus:ring-red-200"></textarea>
                            <div class="flex items-center justify-end gap-3 mt-3">
                                <button type="button" onclick="submitReviewAction('reject')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition">
                                    Tolak Permohonan
                                </button>
                                <button type="button" onclick="submitReviewAction('approve')" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-green-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Setujui Permohonan (Approve)
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-7 py-4 border-t border-gray-100 flex items-center justify-end bg-gray-50">
            <button type="button" onclick="closeRequestDetailModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
let currentDetailReqId = null;

function openRequestDetailModal(requestId, canReview = false) {
    currentDetailReqId = requestId;
    const modal = document.getElementById('requestDetailModal');
    const loading = document.getElementById('modalLoading');
    const loadedData = document.getElementById('modalLoadedData');
    
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    loadedData.classList.add('hidden');

    const detailUrl = "{{ route($detailJsonRouteName, ':id') }}".replace(':id', requestId);

    fetch(detailUrl, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                alert(res.message || 'Gagal memuat detail request.');
                closeRequestDetailModal();
                return;
            }

            const data = res.data;
            document.getElementById('modalReqNumber').innerText = data.request_number;
            document.getElementById('modalReqCompany').innerText = (data.company_name || '—') + ' • ID #' + requestId;

            // Badges
            const typeBadge = document.getElementById('modalReqTypeBadge');
            typeBadge.innerText = data.request_type_label;
            if (data.request_type === 'add_contract') {
                typeBadge.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-green-100 text-green-700';
            } else if (data.request_type === 'extend_contract') {
                typeBadge.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700';
            } else {
                typeBadge.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-red-100 text-red-700';
            }

            const statusBadge = document.getElementById('modalReqStatusBadge');
            statusBadge.innerText = (data.status || '').toUpperCase();
            statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full ' + (data.status_badge_class || 'bg-gray-100 text-gray-700');

            // Metadata
            document.getElementById('modalSubmittedBy').innerText = data.submitted_by || '—';
            document.getElementById('modalSubmittedAt').innerText = data.submitted_at || '—';
            document.getElementById('modalReviewedBy').innerText = data.reviewed_by || '—';
            document.getElementById('modalItemsCount').innerText = (data.items || []).length + ' Peserta';

            // Rejection reason box
            const rejBox = document.getElementById('modalRejectionBox');
            if (data.status === 'rejected' && data.rejection_reason) {
                rejBox.classList.remove('hidden');
                document.getElementById('modalRejectionText').innerText = data.rejection_reason;
            } else {
                rejBox.classList.add('hidden');
            }

            // Participants Table
            const tbody = document.getElementById('modalParticipantsBody');
            tbody.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(it => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50/50';
                    row.innerHTML = `
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            ${it.full_name || '—'}
                            <div class="text-[11px] text-gray-400 font-mono">NIK: ${it.national_id || it.employee_id || '—'}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">${it.job_title || '—'}</td>
                        <td class="px-4 py-3 text-gray-500">${it.contract_end || '—'}</td>
                        <td class="px-4 py-3 font-bold text-gray-800">${it.off_boarding_effective_date || it.new_contract_end || '—'}</td>
                        <td class="px-4 py-3 text-gray-500">${it.off_boarding_reason || it.notes || '—'}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada daftar item peserta pada request ini.</td></tr>';
            }

            // Super Admin Action Area
            const saArea = document.getElementById('modalSaActionArea');
            if (saArea) {
                if (canReview && ['submitted', 'in_review'].includes(data.status)) {
                    saArea.classList.remove('hidden');
                } else {
                    saArea.classList.add('hidden');
                }
            }

            loading.classList.add('hidden');
            loadedData.classList.remove('hidden');
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat memuat data.');
            closeRequestDetailModal();
        });
}

function closeRequestDetailModal() {
    document.getElementById('requestDetailModal').classList.add('hidden');
    currentDetailReqId = null;
}

@if($isSA)
function submitReviewAction(action) {
    if (!currentDetailReqId) return;

    const reasonInput = document.getElementById('rejectionReasonInput');
    const reason = reasonInput ? reasonInput.value.trim() : '';

    if (action === 'reject' && !reason) {
        alert('Mohon tuliskan alasan penolakan sebelum menolak permohonan.');
        if (reasonInput) reasonInput.focus();
        return;
    }

    if (!confirm(action === 'approve' ? 'Setujui permohonan ini dan terapkan status peserta secara otomatis?' : 'Tolak permohonan ini?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('gik-superadmin.requests.review-batch', ':id') }}".replace(':id', currentDetailReqId);

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);

    if (reason) {
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'rejection_reason';
        reasonField.value = reason;
        form.appendChild(reasonField);
    }

    document.body.appendChild(form);
    form.submit();
}
@endif
</script>
@endsection
