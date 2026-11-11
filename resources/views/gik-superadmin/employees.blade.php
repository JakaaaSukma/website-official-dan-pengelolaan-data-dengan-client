@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Tenant Data</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">All Active Participants</h1>
        <p class="text-gray-500 text-sm mt-1">Read-only aggregated data of interns & employees across Company A–D.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.employees.template') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-green-600 text-green-700 hover:bg-green-50 rounded-lg text-sm font-bold transition">Template</a>
        <a href="{{ route('gik-superadmin.employees.import') }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-green-600 text-green-700 bg-white hover:bg-green-50 rounded-lg text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import Excel
        </a>
        <a href="{{ route('gik-superadmin.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm shadow-red-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            + New Intern/Employee
        </a>
    </div>
</div>

<!-- Tenant filter -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6 flex flex-wrap items-center gap-3">
    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Filter by Tenant:</span>
    <div class="flex flex-wrap gap-2">
        <a href="{{ request()->fullUrlWithQuery(['company_id'=>null]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition
            {{ !request('company_id') ? 'bg-gik-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">ALL</a>
        @foreach($companies as $c)
            <a href="{{ request()->fullUrlWithQuery(['company_id'=>$c->id]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition
                {{ request('company_id') == $c->id ? 'bg-gik-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">{{ $c->company_name }}</a>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <form action="{{ request()->url() }}" method="GET" class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
        @if(request('company_id'))
            <input type="hidden" name="company_id" value="{{ request('company_id') }}">
        @endif
        <div class="relative flex-1 min-w-[260px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, job title..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
        </div>
        <select name="department" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="all">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="all">Semua Status</option>
            <option value="active" {{ ($selectedStatus ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="extended" {{ ($selectedStatus ?? '') === 'extended' ? 'selected' : '' }}>Extended</option>
            <option value="offboarded" {{ ($selectedStatus ?? '') === 'offboarded' ? 'selected' : '' }}>Off-boarded</option>
            <option value="expired" {{ ($selectedStatus ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="suspended" {{ ($selectedStatus ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="hidden">Search</button>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5 text-left">Tenant</th>
                    <th class="px-6 py-3.5 text-left">Intern / Employee Details</th>
                    <th class="px-6 py-3.5 text-left">Department & Role</th>
                    <th class="px-6 py-3.5 text-left w-56">Contract Progress</th>
                    <th class="px-6 py-3.5 text-left">End</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $emp)
                    <tr class="hover:bg-red-50/60/30 transition">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold uppercase">
                                {{ strtoupper(substr(optional($emp->company)->company_name ?? 'C', 0, 3)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-extrabold shadow-sm">
                                    {{ $emp->initials }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">{{ $emp->full_name }}</div>
                                    <div class="text-[11px] text-gray-500 font-mono">{{ $emp->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700">{{ $emp->department }}</div>
                            <div class="text-xs text-gray-500">{{ $emp->job_title }}</div>
                            <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md
                                @if($emp->work_type === 'on_site') bg-blue-100 text-blue-700
                                @elseif($emp->work_type === 'remote') bg-teal-100 text-teal-700
                                @else bg-purple-100 text-purple-700 @endif">{{ $emp->work_type_label }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-full">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-gray-700">{{ $emp->contractRemainingLabel() }}</span>
                                    <span class="font-extrabold text-gray-800">{{ $emp->contractProgressPercentage() }}%</span>
                                </div>
                                <div class="gik-progress w-full"><div class="gik-progress-bar {{ $emp->progress_bar_class }}" style="width:{{ $emp->contractProgressPercentage() }}%"></div></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold text-gray-700">{{ optional($emp->contract_end)->format('M d, Y') ?? '—' }}</div>
                            @if($emp->isExpiringSoon(30))
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 text-[10px] font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Expiring soon
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-bold space-x-1 whitespace-nowrap">
                            <a href="{{ route('gik-superadmin.employees.show', $emp) }}" class="text-gik-accent hover:text-gik-primary">View</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('gik-superadmin.employees.edit', $emp) }}" class="text-gik-accent hover:text-gik-primary">Edit</a>
                            @if(in_array($emp->status, ['active', 'extended']))
                                <span class="text-gray-300">|</span>
                                <button type="button" onclick="openOffboardSingleModal({{ $emp->id }}, '{{ addslashes($emp->full_name) }}', '{{ addslashes(optional($emp->company)->company_name ?? '-') }}')" class="text-orange-600 hover:text-orange-800 font-bold">Off-board</button>
                            @endif
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('gik-superadmin.employees.destroy', $emp) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen data ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-bold bg-transparent border-none p-0 inline">Delete</button>
                            </form>
                            <span class="text-gray-300">|</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $emp->status_badge_class }}">{{ strtoupper(str_replace('_', ' ', $emp->status)) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-gray-500">No participant data yet. Run seeds or import from Excel to populate.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex items-center justify-between rounded-b-3xl">
            <span>Showing {{ $employees->firstItem() ?? 0 }}–{{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} participants</span>
            {{ $employees->links() }}
        </div>
    @endif
</div>

<!-- Modal Single Off-boarding -->
<div id="singleOffboardModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden">
        <form id="singleOffboardForm" method="POST" action="">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-orange-50 to-white">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">🚪</div>
                    <div>
                        <h3 class="font-extrabold text-gray-800 text-base">Off-board Peserta</h3>
                        <p class="text-xs text-gray-500" id="offboardParticipantInfo">Peserta: —</p>
                    </div>
                </div>
                <button type="button" onclick="closeOffboardSingleModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Efektif Keluar <span class="text-red-500">*</span></label>
                    <input type="date" name="off_boarding_effective_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kategori / Alasan Keluar <span class="text-red-500">*</span></label>
                    <select name="reason_preset" onchange="if(this.value && this.value!=='Lainnya') document.getElementById('singleOffReason').value=this.value" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:outline-none mb-2">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="Selesai Masa Kontrak Penuh">Selesai Masa Kontrak Penuh</option>
                        <option value="Pengunduran Diri (Resign)">Pengunduran Diri (Resign)</option>
                        <option value="Pemulangan akibat Pelanggaran Disiplin">Pemulangan akibat Pelanggaran Disiplin</option>
                        <option value="Alasan Medis / Kesehatan">Alasan Medis / Kesehatan</option>
                        <option value="Lainnya">Lainnya (Tulis Manual)</option>
                    </select>
                    <textarea name="off_boarding_reason" id="singleOffReason" rows="2" required placeholder="Jelaskan alasan pengakhiran kontrak..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan internal..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-2 rounded-b-3xl">
                <button type="button" onclick="closeOffboardSingleModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">Batal</button>
                <button type="submit" onclick="return confirm('Konfirmasi off-board peserta ini? Status akan berubah menjadi Off-boarded.')" class="px-5 py-2 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-xs font-bold shadow-sm">
                    ✓ Proses Off-board
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openOffboardSingleModal(id, name, company) {
    const modal = document.getElementById('singleOffboardModal');
    const form = document.getElementById('singleOffboardForm');
    form.action = `/hr-superadmin/offboarding/${id}/single`;
    document.getElementById('offboardParticipantInfo').innerText = `${name} • ${company}`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeOffboardSingleModal() {
    const modal = document.getElementById('singleOffboardModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection
