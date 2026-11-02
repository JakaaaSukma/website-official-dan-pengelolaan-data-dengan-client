@extends('layouts.gik-superadmin')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Laporan F2A — Super Admin</h1>
        <p class="text-gray-500 text-sm mt-1">Lihat dan cetak Formulir 2a PU BPJS Ketenagakerjaan per perusahaan.</p>
    </div>
</div>

<!-- Filter Selector -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Perusahaan</label>
            <select name="company_id" required class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm min-w-[200px]">
                <option value="">-- Pilih Perusahaan --</option>
                @foreach($companies as $c)
                <option value="{{ $c->id }}" {{ (int)$companyId === $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                @endforeach
            </select>
        </div>
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
        
        @if($data)
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('gik-superadmin.bpjs.f2a.excel', ['company_id' => $companyId, 'month' => $month, 'year' => $year]) }}" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-sm transition">📊 Export Excel</a>
            <a href="{{ route('gik-superadmin.bpjs.f2a.word', ['company_id' => $companyId, 'month' => $month, 'year' => $year]) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm transition">📄 Export Word</a>
        </div>
        @endif
    </form>
</div>

@if($data)
    <!-- Rate Info -->
    @if($data['rate'])
    <div class="mb-5 px-4 py-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
        <strong>Rate yang berlaku pada periode {{ $data['period'] }}:</strong>
        JKK {{ $data['rate']->jkk_rate }}% | JKM {{ $data['rate']->jkm_rate }}%
        @if((float)$data['rate']->jht_employer_rate > 0) | JHT PK {{ $data['rate']->jht_employer_rate }}% @endif
        @if((float)$data['rate']->jht_employee_rate > 0) | JHT TK {{ $data['rate']->jht_employee_rate }}% @endif
        — <a href="{{ route('gik-superadmin.bpjs.rate-settings') }}" class="underline font-semibold">Ubah Rate</a>
    </div>
    @else
    <div class="mb-5 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        ⚠️ Belum ada rate BPJS yang diatur untuk perusahaan ini pada periode tersebut.
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
                    <tr class="bg-gradient-to-r from-gray-700 to-gray-800 text-white">
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
                    <tr><td colspan="18" class="px-4 py-8 text-center text-gray-400">Tidak ada data F2A untuk perusahaan ini pada periode terpilih.</td></tr>
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
@else
    <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="text-gray-300 mb-3 text-4xl">📄</div>
        <h3 class="text-lg font-bold text-gray-700">Pilih Perusahaan</h3>
        <p class="text-gray-500 mt-1">Silakan pilih perusahaan terlebih dahulu untuk menampilkan laporan F2A.</p>
    </div>
@endif
@endsection
