@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin • Operasional Siklus Kontrak</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Manajemen Off-boarding Peserta</h1>
        <p class="text-gray-500 text-sm mt-1">Pusat kendali pelepasan, penyelesaian masa kontrak, dan monitoring peserta off-boarded lintas seluruh perusahaan mitra.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.offboarding.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-sm font-bold transition shadow-sm shadow-orange-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            + Proses Off-boarding Langsung
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-7">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Pending Approval</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $pendingReviewsCount }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Off-boarded Bulan Ini</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $offboardedThisMonth }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Total Arsip Off-board</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $totalOffboardedCount }}</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-100 text-green-700 flex items-center justify-center font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider">Peserta Masih Aktif</div>
                <div class="text-2xl font-extrabold text-gray-800 leading-tight">{{ $totalActiveCount }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-3 border-b border-gray-200 mb-6">
    <a href="{{ route('gik-superadmin.offboarding.index', ['tab' => 'requests', 'company_id' => $selectedCompany]) }}"
        class="pb-3 px-2 text-sm font-bold border-b-2 transition flex items-center gap-2 {{ $activeTab === 'requests' ? 'border-gik-primary text-gik-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Pengajuan Masuk dari HR Klien
        @if($pendingReviewsCount > 0)
            <span class="text-[10px] font-extrabold bg-red-500 text-white rounded-full px-2 py-0.5">{{ $pendingReviewsCount }}</span>
        @endif
    </a>
    <a href="{{ route('gik-superadmin.offboarding.index', ['tab' => 'history', 'company_id' => $selectedCompany]) }}"
        class="pb-3 px-2 text-sm font-bold border-b-2 transition flex items-center gap-2 {{ $activeTab === 'history' ? 'border-gik-primary text-gik-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        Arsip Peserta Off-boarded
        <span class="text-[10px] font-bold bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ $totalOffboardedCount }}</span>
    </a>
</div>

@if($activeTab === 'requests')
    <!-- TAB 1: PENGAJUAN OFF-BOARDING DARI HR COMPANY -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('gik-superadmin.offboarding.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="requests">
                <div class="relative flex-1 min-w-[220px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor request atau nama perusahaan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <select name="company_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $selectedCompany == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted (Perlu Review)</option>
                    <option value="in_review" {{ $statusFilter === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed (Disetujui)</option>
                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gik-primary text-white rounded-xl text-xs font-bold hover:bg-gik-primary-dark transition">Filter</button>
                @if($search || ($selectedCompany && $selectedCompany !== 'all') || ($statusFilter && $statusFilter !== 'all'))
                    <a href="{{ route('gik-superadmin.offboarding.index', ['tab' => 'requests']) }}" class="text-xs text-gray-500 hover:text-red-600 font-semibold px-2 py-1">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-left">No. Request</th>
                        <th class="px-6 py-3.5 text-left">Perusahaan Klien</th>
                        <th class="px-6 py-3.5 text-left">Peserta Diajukan</th>
                        <th class="px-6 py-3.5 text-left">Tgl Pengajuan</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        @php
                            $itemsCount = $req->batchItems->count();
                            $previewNames = $req->batchItems->take(2)->map(fn($bi) => $bi->contract?->full_name)->filter()->implode(', ');
                            if ($itemsCount === 0 && $req->contract) {
                                $itemsCount = 1;
                                $previewNames = $req->contract->full_name;
                            }
                        @endphp
                        <tr class="hover:bg-red-50/60/30 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs font-bold text-gray-800">{{ $req->request_number }}</div>
                                <span class="inline-flex mt-1 text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">Off-boarding</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ optional($req->company)->company_name ?? '—' }}</div>
                                <div class="text-[11px] text-gray-500">Oleh: {{ optional($req->submittedBy)->name ?? 'HRD' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $itemsCount }} Peserta</div>
                                <div class="text-xs text-gray-500 truncate max-w-[240px]">{{ $previewNames ?: '—' }}{{ $itemsCount > 2 ? ' +' . ($itemsCount - 2) . ' lainnya' : '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div>{{ optional($req->submitted_at ?? $req->created_at)->format('d M Y, H:i') }}</div>
                                <div class="text-[10px] text-gray-400">{{ optional($req->submitted_at ?? $req->created_at)->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $req->status_badge_class }}">
                                    {{ strtoupper(str_replace('_', ' ', $req->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <button type="button" onclick="openReviewModal({{ $req->id }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Review & Keputusan
                                </button>
                                @if(in_array($req->status, ['submitted', 'in_review']))
                                    <form method="POST" action="{{ route('gik-superadmin.requests.review-batch', $req) }}" class="inline" onsubmit="return confirm('Setujui langsung permohonan off-boarding {{ $req->request_number }}?')">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg text-xs font-bold transition">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="font-bold text-gray-700">Tidak ada pengajuan off-boarding</h3>
                                <p class="text-xs text-gray-400 mt-1">Seluruh pengajuan dari HR Company telah ditangani atau belum ada request baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $requests->links() }}</div>
        @endif
    </div>

@else
    <!-- TAB 2: ARSIP PESERTA OFF-BOARDED -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('gik-superadmin.offboarding.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="history">
                <div class="relative flex-1 min-w-[220px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIK, atau Employee ID..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <select name="company_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-xs bg-white focus:outline-none">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ $selectedCompany == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gik-primary text-white rounded-xl text-xs font-bold hover:bg-gik-primary-dark transition">Filter</button>
                @if($search || ($selectedCompany && $selectedCompany !== 'all'))
                    <a href="{{ route('gik-superadmin.offboarding.index', ['tab' => 'history']) }}" class="text-xs text-gray-500 hover:text-red-600 font-semibold px-2 py-1">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Peserta</th>
                        <th class="px-6 py-3.5 text-left">Perusahaan</th>
                        <th class="px-6 py-3.5 text-left">Jabatan / Departemen</th>
                        <th class="px-6 py-3.5 text-left">Periode Kontrak</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($offboardedParticipants as $p)
                        <tr class="hover:bg-red-50/60/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                                        {{ $p->initials }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 leading-tight">{{ $p->full_name }}</div>
                                        <div class="text-[11px] text-gray-500 font-mono">{{ $p->national_id ?? $p->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ optional($p->company)->company_name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div class="font-bold text-gray-700">{{ $p->job_title ?? '—' }}</div>
                                <div class="text-gray-400">{{ $p->department ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div>Mulai: {{ optional($p->contract_start)->format('d M Y') ?? '—' }}</div>
                                <div class="text-gray-400">Habis: {{ optional($p->contract_end)->format('d M Y') ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800">
                                    OFF-BOARDED
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('gik-superadmin.employees.show', $p) }}" class="text-xs font-bold text-gik-accent hover:underline">
                                    Lihat Profil
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                Tidak ada data peserta yang telah di-offboard.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offboardedParticipants->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">{{ $offboardedParticipants->links() }}</div>
        @endif
    </div>
@endif

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-orange-50 to-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">🚪</div>
                <div>
                    <h3 class="font-extrabold text-gray-800 text-base" id="modalReqNumber">Review Pengajuan Off-boarding</h3>
                    <p class="text-xs text-gray-500" id="modalCompanyInfo">Perusahaan: —</p>
                </div>
            </div>
            <button type="button" onclick="closeReviewModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <div class="p-6 overflow-y-auto space-y-5 flex-1">
            <div class="bg-orange-50/50 rounded-2xl p-4 border border-orange-100">
                <div class="text-xs font-bold text-orange-800 mb-2">📋 Rincian Peserta yang Diajukan Keluar:</div>
                <div id="modalItemsList" class="space-y-2 text-xs">
                    <!-- Populated via JS -->
                </div>
            </div>

            <form id="modalReviewForm" method="POST" action="">
                @csrf
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-gray-700">Catatan Review Super Admin (Opsional):</label>
                    <textarea name="review_notes" rows="2" placeholder="Tuliskan catatan verifikasi atau alasan keputusan..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeReviewModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50">Tutup</button>
                    <button type="submit" name="action" value="reject" onclick="return confirm('Tolak pengajuan off-boarding ini?')" class="px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 rounded-xl text-xs font-bold transition">
                        ✕ Tolak Pengajuan
                    </button>
                    <button type="submit" name="action" value="approve" onclick="return confirm('Setujui pengajuan off-boarding ini? Status peserta akan otomatis berubah menjadi OFF-BOARDED.')" class="px-5 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        ✓ Setujui & Terapkan Status Off-board
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openReviewModal(reqId) {
    const modal = document.getElementById('reviewModal');
    const form = document.getElementById('modalReviewForm');
    form.action = `/hr-superadmin/requests/${reqId}/review-batch`;

    document.getElementById('modalReqNumber').innerText = 'Memuat data...';
    document.getElementById('modalItemsList').innerHTML = '<div class="text-gray-400 py-3 text-center">Mengambil data peserta...</div>';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch(`/hr-superadmin/requests/${reqId}/detail-json`)
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                alert('Gagal mengambil detail request');
                closeReviewModal();
                return;
            }
            const d = res.data;
            document.getElementById('modalReqNumber').innerText = `Review Pengajuan: ${d.request_number}`;
            document.getElementById('modalCompanyInfo').innerText = `Perusahaan: ${d.company_name} • Pengaju: ${d.submitted_by} (${d.submitted_at})`;

            let html = '';
            if (d.items && d.items.length > 0) {
                html += '<div class="divide-y divide-orange-100">';
                d.items.forEach((it, idx) => {
                    html += `
                        <div class="py-2.5 flex items-start justify-between gap-4">
                            <div>
                                <div class="font-bold text-gray-800">${idx + 1}. ${it.full_name} <span class="font-mono text-gray-500 font-normal">(${it.national_id || it.employee_id})</span></div>
                                <div class="text-[11px] text-gray-500">Jabatan: ${it.job_title} • Akhir Kontrak: ${it.contract_end}</div>
                                <div class="mt-1 text-gray-700 font-medium">Alasan: <span class="text-orange-900">${it.off_boarding_reason || '—'}</span></div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-block px-2 py-0.5 rounded bg-orange-100 text-orange-800 font-bold text-[10px]">Efektif: ${it.off_boarding_effective_date || '—'}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = '<div class="text-gray-400 py-2">Tidak ada data peserta ditemukan.</div>';
            }
            document.getElementById('modalItemsList').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('modalItemsList').innerHTML = `<div class="text-red-500">Gagal memuat: ${err.message}</div>`;
        });
}

function closeReviewModal() {
    const modal = document.getElementById('reviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection

