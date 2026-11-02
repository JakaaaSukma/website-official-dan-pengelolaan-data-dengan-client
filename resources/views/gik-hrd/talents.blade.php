@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50/60 text-blue-700 rounded-lg text-xs font-bold uppercase tracking-wide mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-[14px] h-[14px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Candidate Database — Talent Berpengalaman & Bersertifikat
        </div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Pilih Kandidat Berpengalaman</h1>
        <p class="text-gray-500 text-sm mt-1">Database kandidat yang telah berpengalaman & memiliki sertifikat. Klik kandidat untuk lihat detail & ajukan pemanggilan (Test / Interview).</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-hrd.client-needs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-200 hover:shadow-orange-300 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-[16px] h-[16px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            💼 Butuh Tenaga Ahli? Ajukan Disini
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('gik-hrd.talents') }}" class="grid md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama, role, posisi, skill..." class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
        </div>
        <div>
            <select name="role" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm">
                <option value="">Semua Role / Posisi</option>
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ request('role')==$r?'selected':'' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <select name="availability" class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm">
                <option value="">Ketersediaan</option>
                <option value="available" {{ request('availability')==='available'?'selected':'' }}>AVAILABLE</option>
                <option value="notice_period" {{ request('availability')==='notice_period'?'selected':'' }}>NOTICE PERIOD</option>
                <option value="employed" {{ request('availability')==='employed'?'selected':'' }}>EMPLOYED</option>
            </select>
            <button type="submit" class="px-5 py-3 bg-gik-primary text-white rounded-xl font-semibold text-sm hover:bg-gik-primary-dark transition">Filter</button>
        </div>
    </form>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif

<div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($talents as $t)
    <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-blue-50 transition p-5 flex flex-col">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white text-2xl font-extrabold flex-shrink-0 shadow-lg shadow-red-200">{{ $t->initials }}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="text-lg font-bold text-gray-900 leading-tight truncate">{{ $t->full_name }}</h3>
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold {{ $t->availabilityBadgeClass }}">{{ $t->availabilityStatusLabel }}</span>
                </div>
                <div class="text-sm font-semibold text-gik-primary mb-1.5">{{ $t->target_role }}</div>
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                    @if($t->years_experience > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-50 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            {{ $t->years_experience }} Tahun Pengalaman
                        </span>
                    @endif
                    @if($t->match_score > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 text-amber-700 rounded-full font-bold">
                            ⚡ Score {{ $t->match_score }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="mb-4">
            <div class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-2">Keahlian Utama</div>
            <div class="flex flex-wrap gap-1.5">
                @forelse(array_slice($t->skillTagsArray, 0, 6) as $tag)
                    <span class="px-2.5 py-1 bg-red-50/60 text-blue-700 rounded-lg text-xs font-semibold">{{ $tag }}</span>
                @empty
                    @forelse(array_slice($t->skillsArray, 0, 4) as $sk)
                        <span class="px-2.5 py-1 bg-gray-50 text-gray-700 rounded-lg text-xs">{{ $sk }}</span>
                    @empty
                        <span class="text-xs text-gray-400 italic">Tidak ada data skill</span>
                    @endforelse
                @endforelse
            </div>
        </div>
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold {{ $t->statusBadgeClass }}">{{ strtoupper(str_replace('_', ' ', $t->status)) }}</span>
            <a href="{{ route('gik-hrd.talents.show', $t) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gik-primary text-white rounded-xl text-sm font-bold hover:bg-gik-primary-dark transition">
                Lihat Detail & Ajukan
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    @endforeach
</div>

@if($talents->count() === 0)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <h3 class="text-lg font-bold text-gray-700 mb-2">Belum ada kandidat yang cocok</h3>
    <p class="text-gray-500 text-sm mb-4">Coba ubah filter pencarian atau ajukan permintaan tenaga ahli spesifik ke tim rekrutmen.</p>
    <a href="{{ route('gik-hrd.client-needs.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gik-primary text-white rounded-xl font-bold">Ajukan Kebutuhan Tenaga Ahli</a>
</div>
@endif

<div class="mt-8 flex justify-center">
    {{ $talents->links() }}
</div>
@endsection
