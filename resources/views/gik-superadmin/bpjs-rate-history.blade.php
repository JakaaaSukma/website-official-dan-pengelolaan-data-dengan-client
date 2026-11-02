@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7">
    <a href="{{ route('gik-superadmin.bpjs.rate-settings') }}" class="text-sm text-gray-500 hover:text-gik-primary transition">← Kembali ke Pengaturan Rate</a>
    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Histori Perubahan Rate — {{ $company->company_name }}</h1>
    <p class="text-gray-500 text-sm mt-1">Log perubahan rate iuran BPJS beserta alasan dan pelaku perubahan.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Field</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nilai Lama</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">→</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nilai Baru</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Berlaku Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Diubah Oleh</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Alasan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ optional($log->created_at)->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $log->field_label }}</td>
                    <td class="px-4 py-3 text-right font-mono text-red-600">{{ $log->old_value !== null ? $log->old_value . '%' : '-' }}</td>
                    <td class="px-4 py-3 text-center text-gray-400">→</td>
                    <td class="px-4 py-3 text-right font-mono text-green-700 font-bold">{{ $log->new_value }}%</td>
                    <td class="px-4 py-3 text-gray-600">{{ optional($log->effective_from)->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $log->changedByUser?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-[200px] truncate">{{ $log->reason ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">Belum ada histori perubahan rate.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $logs->links() }}</div>
</div>
@endsection
