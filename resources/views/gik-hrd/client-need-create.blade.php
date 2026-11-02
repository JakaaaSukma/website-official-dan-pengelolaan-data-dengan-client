@extends('layouts.gik-hrd')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-amber-100 to-orange-100 text-orange-800 rounded-lg text-xs font-bold uppercase tracking-wide mb-3">
            💼 Pengajuan Permintaan Khusus Tenaga Ahli
        </div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Butuh Tenaga Ahli / Berpengalaman?</h1>
        <p class="text-gray-500 text-sm mt-1">Ajukan kebutuhan posisi yang membutuhkan kualifikasi spesifik (berpengalaman & bersertifikat). Tim Rekrutmen Gyokai akan menjalankan <strong class="text-gik-primary">⚡ Match Engine</strong> untuk mencocokkan dengan Talent Database.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
</div>
@endif
@if(session('error'))
<div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <div><strong>Gagal!</strong> {{ session('error') }}</div>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-5">
    {{-- FORM PENGAJUAN --}}
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('gik-hrd.client-needs.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-7">
            @csrf
            <h2 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-gik-primary to-indigo-500 flex items-center justify-center text-white text-lg">📝</span>
                Form Pengajuan Permintaan Tenaga Ahli
            </h2>
            <div class="space-y-5">
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Posisi yang Dibutuhkan <span class="text-red-500">*</span></label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}" required placeholder="Contoh: Lead UI Designer, Data Engineer, Senior Backend Engineer..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary">
                        @error('job_title')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Jumlah Posisi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" min="1" max="100" name="quota" value="{{ old('quota', 1) }}" required class="w-full px-4 py-3 pr-14 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary font-bold">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">Orang</div>
                        </div>
                        @error('quota')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Tingkat Urgensi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 h-full">
                            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 has-[:checked]:border-blue-500 has-[:checked]:bg-red-50/60 cursor-pointer transition font-bold text-sm">
                                <input type="radio" name="urgency" value="open" class="w-4 h-4 text-gik-accent" {{ old('urgency')==='open' || !old('urgency')?'checked':'' }}>
                                <span>📘 OPEN</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 has-[:checked]:border-red-500 has-[:checked]:bg-red-50 cursor-pointer transition font-bold text-sm">
                                <input type="radio" name="urgency" value="urgent" class="w-4 h-4 text-red-600" {{ old('urgency')==='urgent'?'checked':'' }}>
                                <span>🚨 URGENT</span>
                            </label>
                        </div>
                        @error('urgency')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-800 mb-2 block">Skill / Keahlian Spesifik yang Dibutuhkan <span class="text-red-500">*</span></label>
                    <div class="p-1 bg-gray-50 rounded-xl border border-gray-200">
                        <textarea name="skill_tags" rows="3" required placeholder="Pisahkan dengan KOMA. Contoh: Figma, Design Systems, Prototyping, UX Research, Mobile Design — ATAU — Spark, AWS, Python, SQL, Airflow..." class="w-full px-3 py-3 bg-transparent rounded-lg text-sm focus:outline-none resize-none">{{ old('skill_tags') }}</textarea>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Contoh tag:</span>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">Figma</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">Python</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">AWS</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">Design Systems</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">SQL</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">Laravel</button>
                        <button type="button" onclick="this.parentNode.parentNode.querySelector('textarea').value += (this.parentNode.parentNode.querySelector('textarea').value.length? ', ' : '') + this.textContent" class="px-2.5 py-1 bg-red-50/60 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">Prototyping</button>
                    </div>
                    @error('skill_tags')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Tenggat Waktu (Due Date)</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary">
                        <div class="text-[11px] text-gray-400 mt-1">Opsional. Bisa dikosongkan jika tidak ada tenggat spesifik</div>
                        @error('deadline')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-800 mb-2 block">Departemen</label>
                        <input type="text" name="department" value="{{ old('department') }}" placeholder="Contoh: Product & Design, Tech Engineering, Finance..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary">
                        @error('department')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-800 mb-2 block">Deskripsi Tambahan / Job Desc Singkat</label>
                    <textarea name="description" rows="4" placeholder="Contoh: Posisi ini melapor ke CTO. Kualifikasi minimal S1 Informatika. Menangani migration data warehouse dari GCP ke AWS. Bisa WFO 2x/minggu di Jakarta..." class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary resize-none">{{ old('description') }}</textarea>
                    @error('description')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="text-sm font-bold text-gray-800 mb-2 block">Bukti Valid Perusahaan <span class="text-red-500">*</span></label>
                    <input type="file" name="valid_proof" accept=".pdf,.jpg,.jpeg,.png" required class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gik-primary/20 focus:border-gik-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gik-primary/10 file:text-gik-primary hover:file:bg-gik-primary/20">
                    <div class="text-[11px] text-gray-400 mt-1">Format: PDF, JPG, PNG. Maksimal 5MB.</div>
                    @error('valid_proof')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    Form wajib diisi: Posisi, Jumlah, Skill. Setelah submit, permintaan langsung masuk ke panel Talent DB Super Admin.
                </div>
                <button type="submit" class="px-7 py-3.5 bg-gradient-to-r from-orange-500 via-amber-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-xl font-extrabold shadow-lg shadow-orange-200 hover:shadow-xl hover:shadow-orange-300 transition flex items-center gap-2">
                    🚀 Kirim Permintaan ke Tim Rekrutmen
                </button>
            </div>
        </form>
    </div>

    {{-- HISTORY PENGAJUAN SAYA --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-red-50/60 flex items-center justify-center text-gik-accent">📋</span>
                Riwayat Pengajuan Perusahaan Saya
            </h3>
            @if($myNeeds->count() > 0)
            <div class="space-y-3">
                @foreach($myNeeds as $n)
                <div class="p-4 rounded-xl border border-gray-100 bg-gray-50">
                    <div class="flex items-start justify-between gap-2 mb-1.5">
                        <div class="font-bold text-gray-900 text-sm">{{ $n->job_title }}</div>
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $n->statusBadgeClass }}">{{ strtoupper($n->status) }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 mb-2 text-[11px] text-gray-600">
                        <span class="font-bold text-gray-800">{{ $n->quota }} orang</span>
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $n->urgencyBadgeClass }}">{{ strtoupper($n->urgency) }}</span>
                        @if($n->deadline)
                        <span>•</span><span>📅 {{ $n->deadline->format('d M Y') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach(array_slice($n->skillTagsArray, 0, 4) as $tag)
                        <span class="px-2 py-0.5 bg-red-50/60 text-blue-700 rounded text-[10px] font-semibold">{{ $tag }}</span>
                        @endforeach
                    </div>
                    <div class="text-[11px] text-gray-400">{{ $n->created_at->format('d M Y H:i') }} • {{ $n->createdBy?->name ?? 'HR Company' }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm text-gray-500">Belum ada riwayat pengajuan. Submit form pertama Anda!</p>
            </div>
            @endif
        </div>

        <div class="bg-gradient-to-br from-red-50 via-orange-50 to-rose-50 rounded-2xl border border-red-100 p-5">
            <h3 class="font-extrabold text-gray-900 mb-2 flex items-center gap-2">
                <span>⚡</span> Apa yang terjadi setelah submit?
            </h3>
            <ol class="list-decimal pl-5 space-y-2 text-sm text-gik-primary-dark">
                <li>Permintaan langsung masuk ke panel <strong>Talent DB & Matching</strong> Super Admin.</li>
                <li>Tim Rekrutmen menjalankan <strong>Match Engine</strong> untuk mencocokkan skill dengan talent.</li>
                <li>Status card berubah: <span class="bg-blue-100 px-1.5 rounded text-[11px] font-bold">OPEN</span> → <span class="bg-yellow-100 px-1.5 rounded text-[11px] font-bold">IN PROGRESS</span> → <span class="bg-green-100 px-1.5 rounded text-[11px] font-bold">MATCHED / FILLED</span>.</li>
                <li>Notifikasi progress muncul di <strong>🔔 Bell topbar</strong> dan Inbox HR Company.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
