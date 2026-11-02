@extends('layouts.gik-superadmin')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Semua Laporan Kecelakaan Kerja</h1>
        <p class="text-gray-500 text-sm mt-1">Daftar laporan kecelakaan kerja dari seluruh perusahaan mitra.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('gik-superadmin.bpjs.accidents.create') }}" class="px-4 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition">➕ Tambah Laporan</a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px] max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, lokasi..." class="w-full pl-4 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
        </div>
        <select name="company_id" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="all">Semua Perusahaan</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="">Semua Status</option>
            <option value="dilaporkan" {{ request('status') === 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
            <option value="dalam_proses" {{ request('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="klaim_diajukan" {{ request('status') === 'klaim_diajukan' ? 'selected' : '' }}>Klaim Diajukan</option>
        </select>
        <select name="category" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="">Semua Kategori</option>
            <option value="ringan" {{ request('category') === 'ringan' ? 'selected' : '' }}>Ringan</option>
            <option value="sedang" {{ request('category') === 'sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="berat" {{ request('category') === 'berat' ? 'selected' : '' }}>Berat</option>
        </select>
        <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Perusahaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50/50 transition" id="row-{{ $report->id }}">
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $report->company?->company_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-700">
                        <div class="font-medium">{{ $report->hrContract?->full_name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $report->report_number_display }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $report->incident_date_time_label }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $report->category_badge_class }}">{{ $report->category_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="openStatusModal({{ $report->id }}, '{{ $report->handling_status }}', '{{ $report->report_number_display }}')" class="text-blue-600 hover:text-blue-800 font-semibold text-xs transition">✏️ Update Status</button>
                        <a href="{{ Storage::url($report->documents[0] ?? '#') }}" target="_blank" class="ml-2 text-gray-500 hover:text-gray-700 font-semibold text-xs transition" @if(!$report->documents) style="display:none;" @endif>📎 File</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Tidak ada laporan kecelakaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $reports->withQueryString()->links() }}</div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Update Status</h3>
            <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <form id="statusForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Penanganan</label>
                    <select name="handling_status" id="edit_handling_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                        <option value="dilaporkan">Dilaporkan</option>
                        <option value="dalam_proses">Dalam Proses</option>
                        <option value="selesai">Selesai</option>
                        <option value="klaim_diajukan">Klaim Diajukan ke BPJS</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-blue-700 transition">Simpan</button>
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal(id, status, reportNumber) {
    document.getElementById('statusForm').action = '{{ url("hr-superadmin/bpjs/accidents") }}/' + id + '/update-status';
    document.getElementById('edit_handling_status').value = status;
    document.getElementById('modalTitle').innerText = 'Update Status ' + reportNumber;
    document.getElementById('statusModal').classList.remove('hidden');
    document.getElementById('statusModal').classList.add('flex');
}
function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('statusModal').classList.remove('flex');
}
document.getElementById('statusModal').addEventListener('click', function(e) { if (e.target === this) closeStatusModal(); });
</script>
@endsection
