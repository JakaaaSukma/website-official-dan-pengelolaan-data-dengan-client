@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Laporan F2A — Rincian Iuran Tenaga Kerja</h1>
        <p class="text-gray-500 text-sm mt-1">Formulir 2a PU BPJS Ketenagakerjaan untuk {{ $companyName }}.</p>
    </div>
</div>

<!-- Period Selector -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
            <select name="month" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (int)$month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
            <select name="year" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Tampilkan</button>
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('gik-hrd.bpjs.f2a.excel', ['month' => $month, 'year' => $year]) }}" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-sm transition">📊 Export Excel</a>
            <a href="{{ route('gik-hrd.bpjs.f2a.word', ['month' => $month, 'year' => $year]) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm transition">📄 Export Word</a>
        </div>
    </form>
</div>

<!-- Input Uang Saku Action -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Uang Saku Peserta</h2>
        <p class="text-sm text-gray-500 mt-1">Input atau import data Uang Saku untuk periode ini. Data Uang Saku akan digunakan sebagai dasar perhitungan F2A.</p>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" onclick="openInputModal()" class="px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition">
            ✏️ Input Uang Saku
        </button>
    </div>
</div>

<!-- Rate Info -->
@if($data['rate'])
<div class="mb-5 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
    <strong>Rate yang berlaku pada periode {{ $data['period'] }}:</strong>
    JKK {{ $data['rate']->jkk_rate }}% | JKM {{ $data['rate']->jkm_rate }}%
    @if((float)$data['rate']->jht_employer_rate > 0) | JHT PK {{ $data['rate']->jht_employer_rate }}% @endif
    @if((float)$data['rate']->jht_employee_rate > 0) | JHT TK {{ $data['rate']->jht_employee_rate }}% @endif
    — <a href="{{ route('gik-hrd.bpjs.rate-settings') }}" class="underline font-semibold">Lihat Detail Rate</a>
</div>
@else
<div class="mb-5 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
    ⚠️ Belum ada rate BPJS yang diatur untuk perusahaan Anda pada periode ini. Hubungi Super Admin.
</div>
@endif

<!-- F2A Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Formulir 2a PU — RINCIAN IURAN TENAGA KERJA</h2>
        <p class="text-sm text-gray-500">Periode: {{ $data['period'] }} — {{ $companyName }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gradient-to-r from-red-600 to-red-700 text-white">
                    <th class="px-2 py-3 text-center font-bold">No</th>
                    <th class="px-2 py-3 text-left font-bold">No. Referensi</th>
                    <th class="px-2 py-3 text-left font-bold">NIK</th>
                    <th class="px-2 py-3 text-left font-bold">No. Pegawai</th>
                    <th class="px-2 py-3 text-left font-bold">Nama Tenaga Kerja</th>
                    <th class="px-2 py-3 text-center font-bold">Tgl Lahir</th>
                    <th class="px-2 py-3 text-center font-bold">Tgl Kepesertaan</th>
                    <th class="px-2 py-3 text-right font-bold">Upah (Rp)</th>
                    <th class="px-2 py-3 text-right font-bold">Rapel (Rp)</th>
                    <th class="px-2 py-3 text-right font-bold">JKK (Rp)</th>
                    <th class="px-2 py-3 text-right font-bold">JKM (Rp)</th>
                    <th class="px-2 py-3 text-right font-bold">JHT PK</th>
                    <th class="px-2 py-3 text-right font-bold">JHT TK</th>
                    <th class="px-2 py-3 text-right font-bold">JP PK</th>
                    <th class="px-2 py-3 text-right font-bold">JP TK</th>
                    <th class="px-2 py-3 text-right font-bold">JKP PK</th>
                    <th class="px-2 py-3 text-right font-bold">JKP Pem.</th>
                    <th class="px-2 py-3 text-right font-bold">Total (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data['rows'] as $row)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-2 py-2.5 text-center">{{ $row['no'] }}</td>
                    <td class="px-2 py-2.5 font-mono">{{ $row['nomor_referensi'] }}</td>
                    <td class="px-2 py-2.5 font-mono">{{ $row['nik'] }}</td>
                    <td class="px-2 py-2.5 font-mono">{{ $row['nomor_pegawai'] }}</td>
                    <td class="px-2 py-2.5 font-medium text-gray-900 whitespace-nowrap">{{ $row['nama'] }}</td>
                    <td class="px-2 py-2.5 text-center">{{ $row['tanggal_lahir'] }}</td>
                    <td class="px-2 py-2.5 text-center">{{ $row['tgl_kepesertaan'] }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['salary'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['rapel'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jkk'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jkm'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jht_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jht_employee'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jp_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jp_employee'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jkp_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono">{{ number_format($row['jkp_government'], 0, ',', '.') }}</td>
                    <td class="px-2 py-2.5 text-right font-mono font-bold text-gray-900">{{ number_format($row['total'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="18" class="px-4 py-8 text-center text-gray-400">Tidak ada peserta BPJS aktif pada periode ini.</td></tr>
                @endforelse
            </tbody>
            @if(count($data['rows']) > 0)
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="7" class="px-2 py-3 text-right text-gray-700">TOTAL</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['salary'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['rapel'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jkk'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jkm'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jht_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jht_employee'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jp_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jp_employee'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jkp_employer'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono">{{ number_format($data['totals']['jkp_government'], 0, ',', '.') }}</td>
                    <td class="px-2 py-3 text-right font-mono text-gik-primary-dark">{{ number_format($data['totals']['total'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<!-- Input Modal -->
<div id="inputModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Input Uang Saku — Periode {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}</h3>
            <button onclick="closeInputModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Manual Input Form -->
            <div class="border border-gray-100 rounded-xl p-5 bg-gray-50/50">
                <h4 class="font-bold text-gray-800 mb-3 text-sm">Input Manual per Peserta</h4>
                <form action="{{ route('gik-hrd.bpjs.pocket-money.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Peserta</label>
                        <select name="hr_contract_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($data['rows'] as $r)
                                @php
                                    $contract = \App\Models\HrContract::where('national_id', $r['nik'])->where('company_id', $data['company_id'])->first();
                                @endphp
                                @if($contract)
                                    <option value="{{ $contract->id }}">{{ $r['nama'] }} ({{ $r['nik'] }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Uang Saku (Rp)</label>
                        <input type="number" name="amount" required min="0" step="1000" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200" placeholder="Contoh: 1000000">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition">Simpan Manual</button>
                </form>
            </div>

            <!-- Import Excel Form -->
            <div class="border border-gray-100 rounded-xl p-5 bg-gray-50/50">
                <h4 class="font-bold text-gray-800 mb-3 text-sm">Import via Excel</h4>
                <p class="text-xs text-gray-500 mb-4">Gunakan format 2 kolom: <strong>Nama/NIK/No.Ref</strong> dan <strong>Nominal Uang Saku</strong> (tanpa titik koma pada nominal). Baris pertama akan diabaikan (sebagai header).</p>
                <form action="{{ route('gik-hrd.bpjs.pocket-money.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    
                    <div>
                        <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold transition">Mulai Import</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openInputModal() {
    document.getElementById('inputModal').classList.remove('hidden');
    document.getElementById('inputModal').classList.add('flex');
}
function closeInputModal() {
    document.getElementById('inputModal').classList.add('hidden');
    document.getElementById('inputModal').classList.remove('flex');
}
document.getElementById('inputModal').addEventListener('click', function(e) { if (e.target === this) closeInputModal(); });
</script>
@endsection
