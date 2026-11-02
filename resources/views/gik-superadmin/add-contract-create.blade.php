@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin Recruitment</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Add New Contract — Pengajuan Kandidat Baru</h1>
        <p class="text-gray-500 text-sm mt-1">Kirim kandidat batch ke HR Company untuk seleksi lolos/tidak lolos.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </div>
</div>

<form method="POST" action="{{ route('gik-superadmin.add-contract.store') }}" id="addContractForm">
@csrf

<div class="space-y-6">

    <div class="bg-white rounded-3xl shadow-sm border-2 border-blue-200 p-7">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h3 class="font-extrabold text-gray-800 text-lg">Tujuan Pengajuan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih HR Company yang akan menerima kandidat batch ini.</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Tujuan Pengajuan (HR Company Tujuan) <span class="text-red-600">*</span></label>
                <select name="company_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition">
                    <option value="">— Pilih HR Company —</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->company_name }} — {{ $comp->company_code ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Catatan untuk HR Company <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="notes" rows="3" placeholder="Tambahkan catatan untuk HR Company (misal: prioritas posisi, interview schedule, dsb.)" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition"></textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-7 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-gray-800 text-lg">Daftar Kandidat</h3>
                <p class="text-xs text-gray-500 mt-0.5">Input kandidat secara manual. Tambah baris sesuka hati atau gunakan import Excel nanti.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('gik-superadmin.add-contract.template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-blue-300 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Download Template
                </a>
                <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" class="hidden" onchange="uploadExcel(this)">
                <button type="button" onclick="document.getElementById('excelFile').click()" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 text-gray-600 bg-white hover:bg-gray-50 rounded-lg text-xs font-bold transition" id="btnImport">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
                <button type="button" onclick="addRow()" class="inline-flex items-center gap-2 px-4 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah 1 Kandidat
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="candidatesTable">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5 text-left w-10">#</th>
                        <th class="px-5 py-3.5 text-left">Nama Lengkap</th>
                        <th class="px-5 py-3.5 text-left">NIK <span class="text-red-500">*</span></th>
                        <th class="px-5 py-3.5 text-left">Tanggal Lahir</th>
                        <th class="px-5 py-3.5 text-left">Posisi yang Dilamar <span class="text-red-500">*</span></th>
                        <th class="px-5 py-3.5 text-left">Tanggal Mulai <span class="text-red-500">*</span></th>
                        <th class="px-5 py-3.5 text-left">Durasi Kontrak <span class="text-red-500">*</span></th>
                        <th class="px-5 py-3.5 text-left">Alamat</th>
                        <th class="px-5 py-3.5 text-center w-20">Hapus?</th>
                    </tr>
                </thead>
                <tbody id="candidatesTbody" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>
        <div class="px-7 py-5 border-t border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-4">
            <div class="text-xs text-gray-500">
                Jumlah kandidat: <span id="countPreview" class="font-extrabold text-gik-primary text-sm">0</span> orang
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('gik-superadmin.dashboard') }}" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 rounded-xl text-sm font-bold transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Submit Pengajuan
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="font-extrabold text-gray-800 text-lg">Informasi Preview Sebelum Submit</h3>
                <p class="text-xs text-gray-500 mt-0.5">Ringkasan flow pengajuan setelah submit ditekan.</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <div class="font-bold text-amber-800 text-sm mb-1">Flow Seleksi Otomatis</div>
                <p class="text-xs text-amber-700 leading-relaxed">
                    Kandidat yang lolos seleksi HR Company akan otomatis masuk <span class="font-bold">Data Peserta Aktif</span>.
                    Kandidat tidak lolos disimpan sebagai arsip <span class="font-bold">Not Selected</span>.
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200 p-4">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Jumlah diajukan</div>
                <div class="flex items-baseline gap-2">
                    <div id="statCount" class="text-3xl font-extrabold text-gik-primary">0</div>
                    <div class="text-xs text-gray-500 font-semibold">kandidat</div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 p-4">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Estimasi Seleksi</div>
                <div class="flex items-baseline gap-2">
                    <div class="text-3xl font-extrabold text-amber-600">1-3</div>
                    <div class="text-xs text-gray-500 font-semibold">hari kerja</div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 p-4">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2">Status Pengajuan</div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        SUBMITTED
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
</form>

<script>
let rowIndex = 0;
const tbody = document.getElementById('candidatesTbody');

async function uploadExcel(input) {
    if (!input.files || input.files.length === 0) return;
    const btn = document.getElementById('btnImport');
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Uploading...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('excel_file', input.files[0]);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    try {
        const response = await fetch("{{ route('gik-superadmin.add-contract.import') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });
        const result = await response.json();
        if (result.success) {
            if (rowIndex === 3 && document.querySelectorAll('#candidatesTbody tr').length === 3) {
                document.getElementById('candidatesTbody').innerHTML = '';
            }
            result.data.forEach(cand => {
                let dob = '';
                try { if(cand.birth_date) dob = new Date(cand.birth_date).toISOString().split('T')[0]; } catch(e){}
                let start = new Date().toISOString().split('T')[0];
                try { if(cand.contract_start) start = new Date(cand.contract_start).toISOString().split('T')[0]; } catch(e){}
                
                addRow({
                    full_name: cand.full_name,
                    national_id: cand.national_id,
                    birth_date: dob || cand.birth_date,
                    job_title: cand.job_title,
                    contract_start: start || cand.contract_start,
                    contract_duration: cand.contract_duration,
                    address: cand.address
                });
            });
            alert(`Berhasil import ${result.data.length} kandidat!`);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (e) {
        alert('Gagal mengupload file: ' + e);
    }

    input.value = '';
    btn.innerHTML = originalText;
    btn.disabled = false;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function addRow(data = {}) {
    const x = rowIndex++;
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-red-50/60/30 transition';
    tr.dataset.index = x;
    tr.innerHTML = `
        <td class="px-5 py-4 text-xs font-bold text-gray-400 align-top" data-col="no">${x + 1}</td>
        <td class="px-5 py-4 align-top">
            <input type="text" name="candidates[${x}][full_name]" required placeholder="Nama Lengkap" value="${escapeHtml(data.full_name ?? '')}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[240px]">
        </td>
        <td class="px-5 py-4 align-top">
            <input type="text" name="candidates[${x}][national_id]" required placeholder="16-digit NIK" maxlength="16" value="${escapeHtml(data.national_id ?? '')}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[160px]">
        </td>
        <td class="px-5 py-4 align-top">
            <input type="date" name="candidates[${x}][birth_date]" value="${escapeHtml(data.birth_date ?? '')}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[140px]">
        </td>
        <td class="px-5 py-4 align-top">
            <input type="text" name="candidates[${x}][job_title]" required placeholder="Posisi / Jabatan" value="${escapeHtml(data.job_title ?? '')}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[180px]">
        </td>
        <td class="px-5 py-4 align-top">
            <input type="date" name="candidates[${x}][contract_start]" required value="${escapeHtml(data.contract_start ?? new Date().toISOString().split('T')[0])}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[140px]">
        </td>
        <td class="px-5 py-4 align-top">
            <select name="candidates[${x}][contract_duration]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[150px]">
                <option value="" ${!data.contract_duration ? 'selected' : ''}>-- Pilih --</option>
                <option value="3" ${data.contract_duration == 3 ? 'selected' : ''}>3 Bulan</option>
                <option value="6" ${data.contract_duration == 6 ? 'selected' : ''}>6 Bulan</option>
                <option value="12" ${data.contract_duration == 12 ? 'selected' : ''}>12 Bulan (1 Tahun)</option>
                <option value="18" ${data.contract_duration == 18 ? 'selected' : ''}>18 Bulan</option>
                <option value="24" ${data.contract_duration == 24 ? 'selected' : ''}>24 Bulan (2 Tahun)</option>
                <option value="36" ${data.contract_duration == 36 ? 'selected' : ''}>36 Bulan (3 Tahun)</option>
            </select>
        </td>
        <td class="px-5 py-4 align-top">
            <textarea name="candidates[${x}][address]" rows="1" placeholder="Alamat lengkap" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent transition min-w-[220px] resize-y">${escapeHtml(data.address ?? '')}</textarea>
        </td>
        <td class="px-5 py-4 text-center align-top">
            <button type="button" onclick="deleteRow(this)" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 inline-flex items-center justify-center transition" title="Hapus baris ini">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>
    `;
    tbody.prepend(tr);
    renumberRows();
    updateCounts();
}

function deleteRow(btn) {
    if (tbody.children.length <= 1) {
        alert('Minimal 1 kandidat harus tersisa.');
        return;
    }
    const tr = btn.closest('tr');
    tr.remove();
    renumberRows();
    updateCounts();
}

function renumberRows() {
    const rows = tbody.querySelectorAll('tr');
    let displayNo = rows.length;
    rows.forEach(tr => {
        const noCell = tr.querySelector('[data-col="no"]');
        if (noCell) noCell.textContent = displayNo--;
    });
}

function updateCounts() {
    const count = tbody.querySelectorAll('tr').length;
    document.getElementById('countPreview').textContent = count;
    document.getElementById('statCount').textContent = count;
}

document.addEventListener('DOMContentLoaded', () => {
    addRow({
        full_name: 'Citra Dewi',
        national_id: '3201000000000003', birth_date: '1999-11-03',
        job_title: 'Product Manager', address: 'Jl. Ahmad Yani Bogor',
        contract_start: new Date().toISOString().split('T')[0], contract_duration: '6'
    });
    addRow({
        full_name: 'Ani Setiawan',
        national_id: '3201000000000002', birth_date: '1997-07-25',
        job_title: 'Frontend Engineer', address: 'Jl. Sudirman Jakarta',
        contract_start: new Date().toISOString().split('T')[0], contract_duration: '12'
    });
    addRow({
        full_name: 'Budi Santoso',
        national_id: '3201000000000001', birth_date: '1998-03-12',
        job_title: 'UI/UX Designer', address: 'Jl. Merdeka Bandung',
        contract_start: new Date().toISOString().split('T')[0], contract_duration: '6'
    });
});
</script>
@endsection
