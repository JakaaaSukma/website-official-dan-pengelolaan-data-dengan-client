@extends('layouts.gik-superadmin')

@section('page-title', 'Review Pengajuan #' . $report->request_number)

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-2 mb-1 flex-wrap">
            <h2 class="text-2xl font-bold text-gik-primary-dark">📋 Review Pengajuan Pemberhentian Sementara</h2>
            <span class="inline-block px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded-full font-mono">#{{ $report->request_number }}</span>
            <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
        </div>
        <p class="text-gray-500 text-sm">Diajukan oleh {{ $report->requestedBy?->name ?? '-' }} ({{ $report->company?->company_name ?? '-' }}) • {{ optional($report->created_at)->format('l, d F Y H:i') }} WIB</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('gik-superadmin.suspensions.index') }}" class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">← Kembali</a>
    </div>
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
@if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        {{ session('info') }}
    </div>
@endif
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-5 shadow-sm">
        <ul class="list-disc ml-4 space-y-1 text-sm">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-800 px-6 py-5">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1 flex-wrap">
                            <h3 class="text-xl font-bold text-white">Informasi Pengajuan</h3>
                            @if($report->status === 'approved')
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full {{ $report->active_status_badge_class }} shadow-sm">Status Periode: {{ $report->active_status_text }}</span>
                            @endif
                        </div>
                        <div class="flex gap-4 text-xs text-white/80 flex-wrap">
                            <span>📁 Nomor: {{ $report->request_number }}</span>
                            <span>📅 Dibuat: {{ optional($report->created_at)->format('d M Y H:i') }}</span>
                            @if($report->handled_at) <span>✅ Ditangani: {{ optional($report->handled_at)->format('d M Y H:i') }} oleh {{ $report->handledBy?->name ?? '-' }}</span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1 bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-100 rounded-xl p-4">
                        <div class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-1">Total Durasi</div>
                        <div class="text-3xl font-black text-fuchsia-900">{{ $report->total_days }}<span class="text-base font-bold ml-1 text-purple-700">hari</span></div>
                        @if($report->status === 'approved' && $report->days_remaining > 0 && $report->isCurrentlyActive())
                            <div class="mt-2 text-xs font-semibold text-purple-700">⏱ Sisa {{ $report->days_remaining }} hari lagi</div>
                        @endif
                        @if($report->status === 'approved' && $report->isAlreadyExpired())
                            <div class="mt-2 text-xs font-semibold text-gray-500">✅ Periode sudah berakhir (peserta otomatis aktif)</div>
                        @endif
                    </div>
                    <div class="md:col-span-1">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Mulai</div>
                        <div class="text-lg font-bold text-gray-800">{{ optional($report->start_date)->format('l, d M Y') }}</div>
                    </div>
                    <div class="md:col-span-1">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Selesai</div>
                        <div class="text-lg font-bold text-gray-800">{{ optional($report->end_date)->format('l, d M Y') }}</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Alasan Pemberhentian Sementara</div>
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $report->reason }}</div>
                </div>

                @if($report->resolution_notes)
                    <div class="pt-2 border-t border-gray-100">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Catatan {{ $report->status === 'rejected' ? 'Penolakan' : 'Persetujuan' }}
                        </div>
                        <div class="bg-{{ $report->status === 'rejected' ? 'red' : 'purple' }}-50 border border-{{ $report->status === 'rejected' ? 'red' : 'purple' }}-100 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $report->resolution_notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-fuchsia-100 overflow-hidden">
            <div class="px-6 py-4 border-b-2 border-fuchsia-100 bg-gradient-to-r from-fuchsia-50 via-purple-50 to-white">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-fuchsia-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Update Status Pengajuan
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Perubahan status akan otomatis tercatat di timeline & mengirim notifikasi ke HRD pelapor (jika status Final).</p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('gik-superadmin.suspensions.update-status', $report->id) }}" id="update-status-form">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Status Saat Ini</label>
                            <span class="inline-block px-3 py-1.5 text-sm font-bold rounded-full {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Ubah Status Menjadi <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-3 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500 focus:outline-none" required>
                                <option value="pending_approval" @if(old('status', $report->status) === 'pending_approval') selected @endif class="text-amber-700 font-semibold bg-amber-50">⏳ Menunggu Persetujuan</option>
                                <option value="under_review" @if(old('status', $report->status) === 'under_review') selected @endif class="text-blue-700 font-semibold bg-blue-50">🔍 Sedang Ditinjau</option>
                                <option value="approved" @if(old('status', $report->status) === 'approved') selected @endif class="text-purple-700 font-bold bg-purple-50">✅ Disetujui — Peserta Diberhentikan Sementara</option>
                                <option value="rejected" @if(old('status', $report->status) === 'rejected') selected @endif class="text-red-700 font-bold bg-red-50">❌ Ditolak — Peserta Tetap Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan / Notes <span class="text-gray-400 font-normal">(wajib isi saat approve atau reject)</span></label>
                        <textarea name="notes" rows="4" class="w-full px-3 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500 focus:outline-none" placeholder="Contoh: Pengajuan disetujui. Selama periode pemberhentian, hak dan kewajiban peserta ditangguhkan. Peserta diharapkan kembali aktif sesuai tanggal yang ditentukan, atau menghubungi HR jika ada perubahan.">{{ old('notes') }}</textarea>
                    </div>
                    <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-red-50 border border-orange-100 rounded-lg p-4 mb-5">
                        <div class="flex gap-3 items-start">
                            <div class="bg-orange-500 text-white p-2 rounded-lg flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="text-xs text-orange-800 space-y-1">
                                <p class="font-bold">PERINGATAN SEBELUM UPDATE STATUS FINAL:</p>
                                <ul class="list-disc ml-4 space-y-0.5">
                                    <li>Jika <b>Disetujui</b>: peserta otomatis berstatus <b>Diberhentikan Sementara</b> mulai tanggal {{ optional($report->start_date)->format('d M Y') }}. Setelah {{ optional($report->end_date)->format('d M Y') }}, status peserta otomatis kembali <b>Aktif</b> (tanpa tindakan manual). HRD pelapor akan menerima notifikasi.</li>
                                    <li>Jika <b>Ditolak</b>: peserta tetap berstatus <b>Aktif</b> seperti biasa. HRD pelapor akan menerima notifikasi disertai alasan penolakan.</li>
                                    <li>⚠️ Setelah update final, status masih bisa diubah kembali jika diperlukan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="submit" class="px-7 py-2.5 text-sm bg-gradient-to-r from-purple-700 to-fuchsia-900 hover:from-purple-900 hover:to-fuchsia-950 text-white rounded-lg font-bold shadow-lg transition-all" onclick="return confirm('ANDA YAKIN update status pengajuan #{{ $report->request_number }} ini?\\n\\nPerubahan status akan masuk ke timeline riwayat & mengirim notifikasi ke HRD Company terkait (jika status Final).')">
                            💾 Simpan Perubahan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gik-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Timeline & Riwayat Status Pengajuan
                </h3>
            </div>
            <div class="p-6">
                <div class="relative">
                    <div class="absolute left-5 top-2 bottom-2 w-0.5 bg-gradient-to-b from-purple-200 via-gray-200 to-gray-100 border-l border-dashed border-gray-200"></div>
                    <div class="space-y-5">
                        @forelse($report->statusLogs as $idx => $log)
                            <div class="relative pl-12">
                                <div class="absolute left-2.5 top-1 w-6 h-6 rounded-full flex items-center justify-center shadow-sm border-2 border-white z-10 {{ $log->new_status === 'approved' ? 'bg-purple-600' : ($log->new_status === 'rejected' ? 'bg-red-500' : ($log->new_status === 'under_review' ? 'bg-blue-500' : 'bg-amber-500')) }}">
                                    @if($idx === 0)
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    @elseif($log->new_status === 'approved')
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($log->new_status === 'rejected')
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                @if($idx === 0)
                                    <span class="absolute right-0 top-0 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] bg-gradient-to-r from-purple-700 to-fuchsia-900 text-white rounded-full font-bold shadow-sm">
                                        ⭐ TERBARU
                                    </span>
                                @endif
                                <div class="bg-white border border-gray-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full {{ $log->new_status_badge_class }}">{{ $log->new_status_label }}</span>
                                        @if($log->old_status)
                                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                dari <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $log->old_status_label }}</span>
                                            </span>
                                        @endif
                                        <span class="text-[10px] font-mono text-gray-400 ml-auto">LOG#{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2 flex-wrap gap-2">
                                        <span>👤 {{ $log->updatedBy?->name ?? '-' }} <span class="text-gray-300 mx-1">|</span> {{ $log->updatedBy?->role ? strtoupper(str_replace('_', ' ', $log->updatedBy->role)) : '-' }}</span>
                                        <span>🕒 {{ optional($log->created_at)->format('d M Y H:i:s') }} WIB</span>
                                    </div>
                                    @if($log->notes)
                                        <div class="bg-gray-50 border border-gray-100 rounded-md p-3 mt-2 text-sm text-gray-700 italic">
                                            📝 {{ $log->notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400 italic text-sm">Belum ada riwayat status.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="bg-gradient-to-br from-white via-gray-50 to-white rounded-xl shadow-md border border-gray-100 p-5 overflow-hidden relative">
            <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full bg-gradient-to-br from-purple-300/20 to-fuchsia-300/20 blur-2xl"></div>
            <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2 relative">
                <span class="w-7 h-7 bg-purple-100 text-purple-800 rounded-full flex items-center justify-center text-xs">👤</span>
                Identitas Peserta Magang
            </h4>
            <div class="space-y-3 text-sm relative">
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Nama Lengkap</div>
                    <div class="font-bold text-gray-800 text-base">{{ $report->intern?->full_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Posisi / Job Role</div>
                    <div class="text-gray-700">{{ $report->intern?->position ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Status Saat Ini</div>
                    <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full {{ $report->intern?->status_badge_class }}">{{ $report->intern?->status_display_label ?? '-' }}</span>
                    @if($report->intern?->is_currently_suspended && $report->intern?->current_suspension_period_label)
                        <div class="text-[11px] text-fuchsia-800 mt-1 font-bold">📅 {{ $report->intern->current_suspension_period_label }}</div>
                    @endif
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Nomor Kontrak</div>
                    <div class="font-mono text-xs text-gray-600">{{ $report->intern?->contract_number ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-0.5">Masa Kontrak</div>
                    <div class="text-xs text-gray-700">{{ optional($report->intern?->start_date)->format('d M Y') }} s/d {{ optional($report->intern?->end_date)->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gik-primary-dark via-gik-primary to-red-700 rounded-xl shadow-md p-5 text-white overflow-hidden relative">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-2xl"></div>
            <h4 class="text-sm font-bold mb-4 flex items-center gap-2 relative">
                <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center text-xs">🏢</span>
                HR Company Pelapor
            </h4>
            <div class="space-y-3 text-sm relative">
                <div>
                    <div class="text-xs text-white/70 mb-0.5">Pengaju (Submitter)</div>
                    <div class="font-bold text-base">{{ $report->requestedBy?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-white/70 mb-0.5">Perusahaan</div>
                    <div class="text-white">{{ $report->company?->company_name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-white/70 mb-0.5">Role</div>
                    <span class="inline-block px-2 py-0.5 text-[10px] bg-white/20 rounded-full font-bold">
                        {{ $report->requestedBy?->role ? strtoupper(str_replace('_', ' ', $report->requestedBy->role)) : '-' }}
                    </span>
                </div>
                <div class="pt-3 border-t border-white/20 space-y-2">
                    @if($report->requestedBy?->phone)
                        <div>
                            <div class="text-xs text-white/70 mb-1.5">Kontak WA</div>
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $report->requestedBy->phone) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-green-500 hover:bg-green-600 font-bold rounded-md transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Chat WA
                            </a>
                        </div>
                    @endif
                    @if($report->requestedBy?->email)
                        <div>
                            <div class="text-xs text-white/70 mb-1.5">Email</div>
                            <a href="mailto:{{ $report->requestedBy->email }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-white/20 hover:bg-white/30 font-bold rounded-md transition-colors">
                                ✉ {{ $report->requestedBy->email }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-bold text-gray-800 mb-4">📈 Progres Status Stepper</h4>
            <div class="space-y-3">
                @php
                    $steps = [
                        ['pending_approval', 'Menunggu Persetujuan', 'amber'],
                        ['under_review', 'Sedang Ditinjau', 'blue'],
                        ['final', 'Keputusan Akhir', ''],
                    ];
                    $currentIdx = array_search($report->status, ['pending_approval', 'under_review']);
                    if ($report->status === 'approved' || $report->status === 'rejected') $currentIdx = 2;
                    if ($currentIdx === false) $currentIdx = -1;
                @endphp
                @foreach($steps as $i => [$key, $label, $color])
                    @php
                        $isCurrent = $i === $currentIdx;
                        $isPassed = $i < $currentIdx;
                        $bg = $isPassed ? 'bg-green-500 text-white' : ($isCurrent ? "bg-{$color}-500 text-white shadow-lg animate-pulse" : 'bg-gray-200 text-gray-400');
                        $line = $i < $currentIdx ? 'bg-green-400' : 'bg-gray-200';
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold z-10 {{ $bg }}">
                                @if($isPassed) <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @elseif($key === 'final') {{ $report->status === 'approved' ? '✅' : ($report->status === 'rejected' ? '❌' : '●') }}
                                @else {{ $i+1 }} @endif
                            </div>
                            @if($i < count($steps) - 1) <div class="absolute top-full left-1/2 -translate-x-1/2 w-0.5 h-4 {{ $line }}"></div> @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold {{ $isCurrent || $isPassed ? 'text-gray-800' : 'text-gray-400' }}">
                                {{ $label }}
                                @if($key === 'final' && ($report->status === 'approved' || $report->status === 'rejected'))
                                    <span class="inline-block ml-1 px-2 py-0.5 text-[10px] rounded-full {{ $report->status === 'approved' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700' }} font-bold">{{ $report->status_label }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
