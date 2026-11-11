@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin Recruitment</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">{{ $contract->full_name ?? $contract->first_name . ' ' . $contract->last_name }}</h1>
        <p class="text-gray-500 text-sm mt-1">Detail data peserta aktif (Intern / Employee).</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.employees') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
        <a href="{{ route('gik-superadmin.employees.edit', $contract) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-accent text-white hover:bg-gik-accent/90 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
            Edit Data
        </a>
        @if(in_array($contract->status, ['active', 'extended']))
            <button type="button" onclick="openOffboardShowModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-sm font-bold shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Off-board Peserta
            </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gik-primary to-gik-accent flex items-center justify-center text-white font-extrabold text-2xl mb-3">
                {{ strtoupper(substr($contract->first_name, 0, 1)) }}{{ strtoupper(substr($contract->last_name ?? '', 0, 1)) }}
            </div>
            <h2 class="font-extrabold text-gray-800 text-lg">{{ $contract->first_name }} {{ $contract->last_name }}</h2>
            <p class="text-gray-500 text-sm">{{ $contract->job_title ?? '-' }}</p>
            <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                {{ $contract->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                {{ $contract->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $contract->status === 'terminated' ? 'bg-red-100 text-red-700' : '' }}
                {{ $contract->status === 'suspended' ? 'bg-amber-100 text-amber-700' : '' }}
                {{ $contract->status === 'expired' ? 'bg-orange-100 text-orange-700' : '' }}
            ">{{ ucfirst($contract->status) }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Employee ID</span>
                <span class="font-bold text-gray-800">{{ $contract->employee_id ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">NIK</span>
                <span class="font-bold text-gray-800 font-mono">{{ $contract->national_id ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal Lahir</span>
                <span class="font-bold text-gray-800">{{ $contract->birth_date ? \Carbon\Carbon::parse($contract->birth_date)->format('d M Y') : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Detail Cards --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Informasi Pekerjaan --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <h3 class="font-extrabold text-gray-800 text-lg mb-5 border-b pb-3">Informasi Pekerjaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Departemen</span>
                    <span class="font-bold text-gray-800">{{ $contract->department ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Posisi</span>
                    <span class="font-bold text-gray-800">{{ $contract->job_title ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Tipe Kerja</span>
                    <span class="font-bold text-gray-800">{{ ucfirst(str_replace('_', '-', $contract->work_type ?? '-')) }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Tipe Kontrak</span>
                    <span class="font-bold text-gray-800">{{ ucfirst($contract->contract_type ?? '-') }}</span>
                </div>
            </div>
        </div>

        {{-- Informasi Kontrak --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <h3 class="font-extrabold text-gray-800 text-lg mb-5 border-b pb-3">Informasi Kontrak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Mulai Kontrak</span>
                    <span class="font-bold text-gray-800">{{ $contract->contract_start ? \Carbon\Carbon::parse($contract->contract_start)->format('d M Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Berakhir Kontrak</span>
                    <span class="font-bold text-gray-800">{{ $contract->contract_end ? \Carbon\Carbon::parse($contract->contract_end)->format('d M Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Sisa Waktu</span>
                    @if($contract->contract_end)
                        @php
                            $remaining = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($contract->contract_end), false);
                        @endphp
                        <span class="font-bold {{ $remaining > 30 ? 'text-green-600' : ($remaining > 0 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ $remaining > 0 ? $remaining . ' hari lagi' : 'Sudah berakhir' }}
                        </span>
                    @else
                        <span class="font-bold text-gray-400">-</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-500 block text-xs font-bold uppercase tracking-wider mb-1">Status</span>
                    <span class="font-bold text-gray-800">{{ ucfirst($contract->status) }}</span>
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <h3 class="font-extrabold text-gray-800 text-lg mb-5 border-b pb-3">Alamat</h3>
            <p class="text-sm text-gray-700">{{ $contract->address ?? 'Belum diisi' }}</p>
        </div>
    </div>
</div>

<!-- Modal Single Off-boarding -->
<div id="singleOffboardModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden">
        <form method="POST" action="{{ route('gik-superadmin.offboarding.single', $contract) }}">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-orange-50 to-white">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold">🚪</div>
                    <div>
                        <h3 class="font-extrabold text-gray-800 text-base">Off-board Peserta Ini</h3>
                        <p class="text-xs text-gray-500">{{ $contract->full_name }} • {{ optional($contract->company)->company_name ?? '-' }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeOffboardShowModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Efektif Keluar <span class="text-red-500">*</span></label>
                    <input type="date" name="off_boarding_effective_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kategori / Alasan Keluar <span class="text-red-500">*</span></label>
                    <select name="reason_preset" onchange="if(this.value && this.value!=='Lainnya') document.getElementById('showOffReason').value=this.value" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs bg-white focus:outline-none mb-2">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="Selesai Masa Kontrak Penuh">Selesai Masa Kontrak Penuh</option>
                        <option value="Pengunduran Diri (Resign)">Pengunduran Diri (Resign)</option>
                        <option value="Pemulangan akibat Pelanggaran Disiplin">Pemulangan akibat Pelanggaran Disiplin</option>
                        <option value="Alasan Medis / Kesehatan">Alasan Medis / Kesehatan</option>
                        <option value="Lainnya">Lainnya (Tulis Manual)</option>
                    </select>
                    <textarea name="off_boarding_reason" id="showOffReason" rows="2" required placeholder="Jelaskan alasan pengakhiran kontrak..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan internal..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-2 rounded-b-3xl">
                <button type="button" onclick="closeOffboardShowModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">Batal</button>
                <button type="submit" onclick="return confirm('Konfirmasi off-board peserta ini? Status akan berubah menjadi Off-boarded.')" class="px-5 py-2 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl text-xs font-bold shadow-sm">
                    ✓ Proses Off-board
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openOffboardShowModal() {
    const modal = document.getElementById('singleOffboardModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeOffboardShowModal() {
    const modal = document.getElementById('singleOffboardModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection
