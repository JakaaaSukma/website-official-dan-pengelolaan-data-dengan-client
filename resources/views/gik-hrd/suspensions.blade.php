@extends('layouts.gik-hrd')

@section('page-title', 'Pemberhentian Sementara Peserta')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gik-primary-dark">📋 Pengajuan Pemberhentian Sementara</h2>
    <p class="text-gray-500 mt-1 text-sm">Riwayat pengajuan pemberhentian sementara peserta magang perusahaan Anda.</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('success') }}
    </div>
@endif
@if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('warning') }}
    </div>
@endif
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        <ul class="list-disc ml-4 space-y-1 text-sm">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu Persetujuan</div>
                <div class="text-3xl font-bold text-amber-600 mt-2">{{ $stats['pending_approval'] }}</div>
            </div>
            <div class="bg-amber-500/20 p-3 rounded-full">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Sedang Ditinjau</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['under_review'] }}</div>
            </div>
            <div class="bg-blue-500/20 p-3 rounded-full">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Disetujui / Berlaku</div>
                <div class="text-3xl font-bold text-purple-700 mt-2">{{ $stats['approved'] }}</div>
            </div>
            <div class="bg-purple-500/20 p-3 rounded-full">
                <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" action="{{ route('gik-hrd.suspensions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Nama Peserta</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none" placeholder="Ketik nama lengkap...">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pengajuan</label>
            <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
                <option value="">-- Semua Status --</option>
                @foreach($statusLabels as $k => $label)
                    <option value="{{ $k }}" @if(request('status') === $k) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Submit (Dari)</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Submit (Sampai)</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-gik-primary focus:border-gik-primary focus:outline-none">
        </div>
        <div class="md:col-span-4 flex gap-2 justify-end">
            <a href="{{ route('gik-hrd.suspensions.index') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 font-medium">Reset</a>
            <button type="submit" class="px-5 py-2 text-sm bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg font-medium shadow-sm">
                🔍 Terapkan Filter
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-gik-primary-dark to-gik-primary text-white">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nomor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Peserta</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Hari</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Ajuan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $r)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-700">{{ $r->request_number }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $r->intern?->full_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $r->intern?->position ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">
                            <div class="font-medium">{{ $r->period_label }}</div>
                            @if($r->status === 'approved')
                                <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] rounded-full {{ $r->active_status_badge_class }}">{{ $r->active_status_text }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-700 text-center">{{ $r->total_days }} d</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full {{ $r->status_badge_class }}">{{ $r->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ optional($r->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('gik-hrd.suspensions.show', $r->id) }}" class="inline-block px-3 py-1.5 text-xs bg-gik-primary/10 text-gik-primary-dark hover:bg-gik-primary/20 font-semibold rounded-md transition-colors">
                                👁 Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="mx-auto w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-gray-500 font-medium mb-3">Belum ada pengajuan pemberhentian sementara.</p>
                            <a href="{{ route('gik-hrd.dashboard') }}" class="inline-block px-4 py-2 text-xs bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg font-medium">Ke Daftar Peserta →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->total() > $reports->perPage())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $reports->links() }}
        </div>
    @endif
</div>

@endsection
