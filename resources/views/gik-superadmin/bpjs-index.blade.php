@extends('layouts.gik-superadmin')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">BPJS Ketenagakerjaan — Semua Perusahaan</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola status BPJS peserta magang di seluruh perusahaan mitra.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('gik-superadmin.bpjs.rate-settings') }}" class="px-4 py-2.5 bg-amber-50 text-amber-800 rounded-xl text-sm font-semibold hover:bg-amber-100 transition">⚙️ Pengaturan Rate</a>
        <a href="{{ route('gik-superadmin.bpjs.accidents.dashboard') }}" class="px-4 py-2.5 bg-blue-50 text-blue-800 rounded-xl text-sm font-semibold hover:bg-blue-100 transition">📊 Dashboard Kecelakaan</a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, nomor BPJS..." class="w-full pl-4 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
        </div>
        <select name="company_id" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="all">Semua Perusahaan</option>
            @foreach($companies as $c)
            <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
        </select>
        <select name="bpjs_status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('bpjs_status') === 'aktif' ? 'selected' : '' }}>BPJS Aktif</option>
            <option value="tidak_aktif" {{ request('bpjs_status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            <option value="belum" {{ request('bpjs_status') === 'belum' ? 'selected' : '' }}>Belum Terdaftar</option>
        </select>
        <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Perusahaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status BPJS</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. BPJS</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Kepesertaan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($contracts as $contract)
                @php $bpjs = $contract->bpjsKetenagakerjaan; @endphp
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $contract->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $contract->company?->company_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $contract->national_id ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($bpjs)
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $bpjs->status_badge_class }}">{{ $bpjs->status_label }}</span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Belum</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $bpjs->bpjs_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $bpjs ? optional($bpjs->bpjs_join_date)->format('d M Y') : '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="openEditModal({{ $contract->id }}, {{ json_encode(['bpjs_status' => $bpjs->bpjs_status ?? 'tidak_aktif', 'bpjs_number' => $bpjs->bpjs_number ?? '', 'bpjs_join_date' => $bpjs ? optional($bpjs->bpjs_join_date)->format('Y-m-d') : '', 'nomor_referensi' => $bpjs->nomor_referensi ?? '', 'nomor_pegawai' => $bpjs->nomor_pegawai ?? '']) }})" class="text-blue-600 hover:text-blue-800 font-semibold text-xs transition">✏️ Edit</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Tidak ada data ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $contracts->withQueryString()->links() }}</div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Edit Status BPJS</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status BPJS</label>
                    <select name="bpjs_status" id="edit_bpjs_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                        <option value="aktif">Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor BPJS Ketenagakerjaan</label>
                    <input type="text" name="bpjs_number" id="edit_bpjs_number" maxlength="16" pattern="\d{10,16}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-blue-200" placeholder="Contoh: 12345678901">
                    <p class="text-xs text-gray-400 mt-1">Numerik, 10-16 digit</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kepesertaan BPJS</label>
                    <input type="date" name="bpjs_join_date" id="edit_bpjs_join_date" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Referensi</label>
                        <input type="text" name="nomor_referensi" id="edit_nomor_referensi" maxlength="50" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Pegawai</label>
                        <input type="text" name="nomor_pegawai" id="edit_nomor_pegawai" maxlength="50" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-gik-primary to-red-600 text-white rounded-xl text-sm font-bold shadow-md hover:from-gik-primary-dark hover:to-red-700 transition">Simpan</button>
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, data) {
    document.getElementById('editForm').action = '{{ url("hr-superadmin/bpjs") }}/' + id + '/update-status';
    document.getElementById('edit_bpjs_status').value = data.bpjs_status || 'tidak_aktif';
    document.getElementById('edit_bpjs_number').value = data.bpjs_number || '';
    document.getElementById('edit_bpjs_join_date').value = data.bpjs_join_date || '';
    document.getElementById('edit_nomor_referensi').value = data.nomor_referensi || '';
    document.getElementById('edit_nomor_pegawai').value = data.nomor_pegawai || '';
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
document.getElementById('editModal').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
</script>
@endsection
