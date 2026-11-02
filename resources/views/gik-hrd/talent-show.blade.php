@extends('layouts.gik-hrd')

@section('content')
<div class="mb-5 flex items-center gap-3">
    <a href="{{ route('gik-hrd.talents') }}" class="p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-gik-primary-dark rounded-lg text-xs font-bold uppercase tracking-wide mb-1">
            Detail Kandidat Berpengalaman
        </div>
        <h1 class="text-[26px] font-bold text-gik-primary-dark leading-tight">{{ $talent->full_name }}</h1>
    </div>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-5">
    {{-- KIRI: Profile Detail --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- CARD PROFILE --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="h-28 bg-gradient-to-r from-red-500 via-orange-500 to-pink-500"></div>
            <div class="px-7 pb-6">
                <div class="flex flex-wrap items-end justify-between gap-4 -mt-10 mb-4">
                    <div class="flex items-end gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-white ring-4 ring-white shadow-xl flex items-center justify-center text-3xl font-extrabold text-gray-800">{{ $talent->initials }}</div>
                        <div class="pb-1">
                            <h2 class="text-2xl font-extrabold text-gray-900 leading-tight">{{ $talent->full_name }}</h2>
                            <div class="inline-flex items-center gap-2 mt-1">
                                <span class="text-sm font-semibold text-gik-primary">{{ $talent->target_role }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $talent->availabilityBadgeClass }}">{{ $talent->availabilityStatusLabel }}</span>
                                @if($talent->match_score > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">⚡ {{ $talent->match_score }}% Match</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($talent->years_experience > 0)
                        <span class="px-3 py-1.5 bg-red-50 text-gik-primary-dark rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4H5v10h14V11h-3zm-2 0H10V7a2 2 0 114 0v4z"/></svg>
                            {{ $talent->years_experience }} Tahun Pengalaman
                        </span>
                        @endif
                        @if($talent->current_job_title)
                        <span class="px-3 py-1.5 bg-gray-50 text-gray-700 rounded-xl text-xs font-bold">📍 {{ $talent->current_job_title }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">Email</div>
                        <div class="text-gray-800 font-semibold truncate">{{ $talent->email ?? '-' }}</div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">No. HP</div>
                        <div class="text-gray-800 font-semibold">{{ $talent->phone ?? '-' }}</div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">Lokasi</div>
                        <div class="text-gray-800 font-semibold truncate">{{ $talent->location ?? '-' }}</div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">Pendidikan Terakhir</div>
                        <div class="text-gray-800 font-semibold">{{ $talent->education ?? '-' }}</div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">Expected Salary</div>
                        <div class="text-gray-800 font-semibold">{{ $talent->expected_salary ? 'Rp ' . number_format($talent->expected_salary, 0, ',', '.') : '-' }}</div>
                    </div>
                    <div class="p-3.5 bg-gray-50 rounded-xl">
                        <div class="text-[11px] font-bold uppercase text-gray-400 mb-1">NIK / ID</div>
                        <div class="text-gray-800 font-semibold truncate">{{ $talent->national_id ?? '-' }}</div>
                    </div>
                </div>

                @if($talent->cv_path)
                <div class="mt-5 p-4 bg-red-50/60 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-blue-900 text-sm">Curriculum Vitae / CV</div>
                            <div class="text-xs text-blue-700">File PDF / DOC tersedia untuk diunduh</div>
                        </div>
                    </div>
                    <a href="{{ Storage::disk('public')->url($talent->cv_path) }}" target="_blank" class="px-4 py-2 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-lg text-sm font-bold transition">Unduh CV</a>
                </div>
                @endif
            </div>
        </div>

        {{-- RIWAYAT & SERTIFIKAT --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-50/60 flex items-center justify-center text-gik-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900">Riwayat Pengalaman</h3>
                </div>
                @if($talent->skills)
                <div class="mb-4 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        <span class="text-xs font-bold text-green-800 uppercase tracking-wide">Berpengalaman Terverifikasi</span>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed">{{ $talent->skills }}</div>
                </div>
                @else
                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-500 italic">Detail riwayat pengalaman tersedia di CV.</div>
                @endif
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-2">Tech Stack & Keahlian</div>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse(array_merge($talent->skillTagsArray, $talent->skillsArray) as $tag)
                            @once @php $seen = []; @endphp @endonce
                            @if(!in_array($tag, $seen))
                            @php $seen[] = $tag; @endphp
                            <span class="px-2.5 py-1 bg-red-50 text-gik-primary-dark rounded-lg text-xs font-semibold">{{ $tag }}</span>
                            @endif
                        @empty
                            <span class="text-xs text-gray-400 italic">-</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900">Sertifikat & Penghargaan</h3>
                </div>
                @if($talent->notes)
                <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wide">Memiliki Sertifikat</span>
                    </div>
                    <div class="text-sm text-gray-700 leading-relaxed">{{ $talent->notes }}</div>
                </div>
                @elseif($talent->skill_tags && count($talent->skillTagsArray) >= 3)
                <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border border-amber-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wide">Bersertifikat (Berdasarkan Skill Tag)</span>
                    </div>
                    <div class="text-sm text-gray-700">Kandidat memiliki {{ count($talent->skillTagsArray) }} keahlian spesifik (kemungkinan besar sudah ada sertifikat pendukung). Download CV untuk detail.</div>
                </div>
                @else
                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-500 italic">Detail sertifikat tersedia di CV. Unduh CV untuk informasi lengkap.</div>
                @endif
            </div>
        </div>

        {{-- HISTORY PEMANGGILAN --}}
        @if($existingCalls->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-extrabold text-gray-900 mb-4">📜 Riwayat Pemanggilan Kandidat Ini (Perusahaan Anda)</h3>
            <div class="space-y-2">
                @foreach($existingCalls as $c)
                <div class="p-4 bg-gray-50 rounded-xl flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $c->callTypeBadgeClass }}">{{ $c->callTypeLabel }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $c->statusBadgeClass }}">{{ strtoupper($c->status) }}</span>
                        <span class="text-sm text-gray-600">{{ $c->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="text-sm text-gray-700 max-w-md truncate">{{ $c->notes ?? '-' }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- KANAN: FORM AJUKAN PEMANGGILAN --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border-2 border-gik-primary/10 shadow-lg shadow-blue-50/50 p-6 sticky top-5">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-gik-primary to-indigo-500 text-white rounded-lg text-xs font-bold uppercase tracking-wide mb-4">
                📞 Ajukan Pemanggilan Kandidat
            </div>
            <h3 class="text-xl font-extrabold text-gray-900 leading-tight mb-2">Panggil Kandidat Ini</h3>
            <p class="text-sm text-gray-500 mb-6">Pilih jenis pemanggilan. Permintaan Anda akan langsung diterima & dijadwalkan oleh Tim Rekrutmen Gyokai.</p>

            <form method="POST" action="{{ route('gik-hrd.talents.call-request', $talent) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Jenis Pemanggilan <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-200 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 cursor-pointer transition">
                                <input type="radio" name="call_type" value="test" class="w-4 h-4 text-purple-600" {{ old('call_type')==='test'?'checked':'' }} required>
                                <div>
                                    <div class="font-bold text-gray-900">🧠 Test / Ujian</div>
                                    <div class="text-xs text-gray-500">Technical test, psikotest, atau assessment skill lainnya</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-200 has-[:checked]:border-blue-500 has-[:checked]:bg-red-50/60 cursor-pointer transition">
                                <input type="radio" name="call_type" value="interview" class="w-4 h-4 text-gik-accent" {{ old('call_type')==='interview' || !old('call_type')?'checked':'' }}>
                                <div>
                                    <div class="font-bold text-gray-900">💬 Interview</div>
                                    <div class="text-xs text-gray-500">Interview langsung (online / offline) dengan HRD dan user</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-200 has-[:checked]:border-gray-500 has-[:checked]:bg-gray-50 cursor-pointer transition">
                                <input type="radio" name="call_type" value="other" class="w-4 h-4 text-gray-600" {{ old('call_type')==='other'?'checked':'' }}>
                                <div>
                                    <div class="font-bold text-gray-900">📋 Lainnya</div>
                                    <div class="text-xs text-gray-500">Misal: Gathering, sharing session, user interview (isi catatan)</div>
                                </div>
                            </label>
                        </div>
                        @error('call_type')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Catatan / Detail Tambahan</label>
                        <textarea name="notes" rows="4" placeholder="Misal: Jadwal diusulkan Senin sore via Zoom. Fokus penilaian: Design System & Figma. Ada user interview dengan CTO..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-indigo-400 resize-none">{{ old('notes') }}</textarea>
                        <div class="text-[11px] text-gray-400 mt-1">Minimal 3 karakter, maksimal 2000 karakter</div>
                        @error('notes')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="w-full px-5 py-3.5 bg-gradient-to-r from-gik-primary to-indigo-500 hover:from-gik-primary-dark hover:to-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-red-200 hover:shadow-xl hover:shadow-indigo-300 transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Kirim Permintaan ke Rekrutmen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
