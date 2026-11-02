@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7">
    <h1 class="text-2xl font-extrabold text-gray-900">Pengaturan Rate Iuran BPJS per Perusahaan</h1>
    <p class="text-gray-500 text-sm mt-1">Atur persentase JKK, JKM, dan iuran lainnya untuk setiap perusahaan mitra.</p>
</div>

@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">❌ {{ session('error') }}</div>
@endif

<div class="space-y-6">
    @foreach($companies as $company)
    @php $rate = $rates[$company->id] ?? null; @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $company->company_name }}</h3>
                @if($rate)
                <p class="text-xs text-gray-500">Rate aktif sejak {{ optional($rate->effective_from)->format('d M Y') }}</p>
                @else
                <p class="text-xs text-amber-600 font-semibold">⚠️ Belum ada rate yang diatur</p>
                @endif
            </div>
            <a href="{{ route('gik-superadmin.bpjs.rate-history', $company->id) }}" class="text-xs text-blue-600 hover:underline font-semibold">📋 Histori Perubahan</a>
        </div>

        <form method="POST" action="{{ route('gik-superadmin.bpjs.rate-settings.update', $company->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JKK (%)</label>
                    <input type="number" name="jkk_rate" step="0.0001" min="0" max="10" value="{{ $rate->jkk_rate ?? '0.2400' }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JKM (%)</label>
                    <input type="number" name="jkm_rate" step="0.0001" min="0" max="10" value="{{ $rate->jkm_rate ?? '0.3000' }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JHT PK (%)</label>
                    <input type="number" name="jht_employer_rate" step="0.0001" min="0" max="10" value="{{ $rate->jht_employer_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JHT TK (%)</label>
                    <input type="number" name="jht_employee_rate" step="0.0001" min="0" max="10" value="{{ $rate->jht_employee_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JP PK (%)</label>
                    <input type="number" name="jp_employer_rate" step="0.0001" min="0" max="10" value="{{ $rate->jp_employer_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JP TK (%)</label>
                    <input type="number" name="jp_employee_rate" step="0.0001" min="0" max="10" value="{{ $rate->jp_employee_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JKP PK (%)</label>
                    <input type="number" name="jkp_employer_rate" step="0.0001" min="0" max="10" value="{{ $rate->jkp_employer_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">JKP Pem. (%)</label>
                    <input type="number" name="jkp_government_rate" step="0.0001" min="0" max="10" value="{{ $rate->jkp_government_rate ?? '0.0000' }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Berlaku Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="effective_from" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Perubahan</label>
                    <input type="text" name="reason" maxlength="500" placeholder="Opsional: alasan perubahan rate" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-700 to-red-600 text-white rounded-lg text-sm font-bold shadow-sm hover:from-red-800 hover:to-red-700 transition">💾 Simpan Rate</button>
                </div>
            </div>
        </form>
    </div>
    @endforeach
</div>
@endsection
