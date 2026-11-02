@extends(request()->routeIs('gik-superadmin.*') ? 'layouts.gik-superadmin' : 'layouts.gik-hrd')

@php
$isSA = request()->routeIs('gik-superadmin.*');
$rPrefix = $isSA ? 'gik-superadmin.' : 'gik-hrd.';
@endphp

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-[11px] font-bold uppercase tracking-widest text-gik-accent mb-1.5">
            {{ $isSA ? 'SUPER ADMIN • CROSS-TENANT DATA ENTRY' : 'HR COMPANY • DATA PESERTA BARU' }}
        </div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">{{ $pageTitle ?? 'Tambah Intern / Employee Baru' }}</h1>
        <p class="text-gray-500 text-sm mt-1">
            {{ $isSA ? 'Input data peserta aktif baru secara manual untuk perusahaan mana saja (cross-tenant). Pilih perusahaan penempatan yang benar!' : 'Form pendaftaran peserta aktif baru. Data otomatis tercatat ke perusahaan Anda.' }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route($rPrefix . ($isSA ? 'employees' : 'dashboard')) }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ $isSA ? 'Kembali ke Participants List' : 'Kembali ke Dashboard' }}
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 flex items-start gap-3">
    <div class="w-9 h-9 rounded-xl bg-green-100 text-green-700 flex items-center justify-center flex-shrink-0 text-xl">✅</div>
    <div>
        <div class="font-extrabold text-green-800 text-sm">Berhasil Disimpan!</div>
        <div class="text-xs text-green-700 mt-0.5">{{ session('success') }}</div>
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
    <div class="flex items-start gap-3 mb-2">
        <div class="w-9 h-9 rounded-xl bg-red-100 text-red-700 flex items-center justify-center flex-shrink-0 text-xl">⚠️</div>
        <div>
            <div class="font-extrabold text-red-800 text-sm">Mohon Periksa Kembali Formulir!</div>
            <div class="text-xs text-red-700 mt-0.5">Beberapa field wajib tidak terisi dengan benar.</div>
        </div>
    </div>
    <ul class="text-xs text-red-700 ml-12 space-y-1 list-disc">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route($rPrefix . 'employees.store') }}" enctype="multipart/form-data" id="newEmployeeForm">
@csrf

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT COLUMN (Data Pribadi + Penempatan) -->
    <div class="xl:col-span-2 space-y-6">

        @if($isSA)
        <!-- Panel SuperAdmin: Pilih Perusahaan Penempatan -->
        <div class="bg-white rounded-3xl shadow-sm border-2 border-red-200 p-7">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-lg">🏢 Tempat Penempatan Perusahaan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">WAJIB DIPILIH! Sistem akan mengaitkan data ini ke HR Company yang dipilih (cross-tenant).</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-red-100 text-gik-primary-dark flex items-center justify-center ring-1 ring-red-200 shadow-sm shadow-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-extrabold text-gray-700 mb-1.5 uppercase tracking-wide">Pilih HR Company Tujuan Penempatan <span class="text-red-600">*</span></label>
                    <select name="company_id" required class="w-full px-3.5 py-3 border-2 border-red-200 rounded-xl text-sm bg-red-50/40 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-gik-primary transition font-semibold text-gray-800">
                        <option value="">— PILIH PERUSAHAAN PENEMPATAN (WAJIB) —</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id', $viewingCompanyId ?? null) == $comp->id ? 'selected' : '' }}>
                                {{ $comp->company_name }} @if($comp->company_code) — ({{ $comp->company_code }}) @endif · ID #{{ $comp->id }}
                            </option>
                        @endforeach
                    </select>
                    <div class="text-[11px] text-gray-500 mt-1.5 font-medium">
                        💡 <strong>ID di atas = kolom "Company ID" di template Excel Import.</strong> Pastikan ID sama dengan yang diinput di Excel untuk penempatan yang benar.
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Panel: Informasi Data Pribadi Karyawan -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-lg">👤 Informasi Data Pribadi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Data identitas dasar karyawan / intern.</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-red-50 to-pink-50 text-gik-primary-dark flex items-center justify-center ring-1 ring-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Employee ID <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}" maxlength="50" placeholder="Contoh: INT-2026-0001" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                </div>
                <div></div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Depan (First Name) <span class="text-red-600">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="100" placeholder="Contoh: Budi" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nama Belakang (Last Name) <span class="text-red-600">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="100" placeholder="Contoh: Santoso" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" maxlength="150" placeholder="budi.santoso@gyokai.co.id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">No. HP / WhatsApp <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="20" placeholder="+62 812 3456 7890" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">NIK / KTP <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" maxlength="50" placeholder="Contoh: 3201010505950001" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Foto Profil (Avatar) <span class="text-gray-400 font-normal">(opsional, max 2MB)</span></label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition bg-gray-50">
                </div>
            </div>
        </div>

        <!-- Panel: Informasi Pekerjaan & Kontrak -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-lg">💼 Informasi Pekerjaan & Kontrak</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Detail penempatan kerja, posisi, dan durasi kontrak.</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 text-orange-700 flex items-center justify-center ring-1 ring-orange-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Departemen / Division <span class="text-red-600">*</span></label>
                    <input type="text" name="department" value="{{ old('department') }}" required maxlength="100" list="deptList" placeholder="Contoh: Software Engineering, UI/UX Design, Finance" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium text-gray-800">
                    <datalist id="deptList">
                        <option value="Engineering">
                        <option value="Software Engineering">
                        <option value="Frontend Engineering">
                        <option value="Backend Engineering">
                        <option value="Data Engineering">
                        <option value="UI/UX Design">
                        <option value="Product Management">
                        <option value="Marketing">
                        <option value="Sales">
                        <option value="Finance">
                        <option value="Accounting">
                        <option value="Human Resources">
                        <option value="Operations">
                        <option value="General Affairs">
                        <option value="Legal">
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Job Title / Posisi <span class="text-red-600">*</span></label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}" required maxlength="150" placeholder="Contoh: Senior Backend Engineer, Junior UI/UX Designer" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tipe Kerja (Work Type) <span class="text-red-600">*</span></label>
                    <select name="work_type" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                        <option value="">— Pilih Tipe Kerja —</option>
                        <option value="on_site" {{ old('work_type') === 'on_site' ? 'selected' : '' }}>🏢 On-Site (WFO - Kantor)</option>
                        <option value="remote" {{ old('work_type') === 'remote' ? 'selected' : '' }}>💻 Remote (WFH - Rumah)</option>
                        <option value="hybrid" {{ old('work_type') === 'hybrid' ? 'selected' : '' }}>🔀 Hybrid (WFO + WFH)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tipe Kontrak <span class="text-red-600">*</span></label>
                    <select name="contract_type" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                        <option value="">— Pilih Tipe Kontrak —</option>
                        <option value="internship" {{ old('contract_type') === 'internship' ? 'selected' : '' }}>🎓 Internship / Magang</option>
                        <option value="probation" {{ old('contract_type') === 'probation' ? 'selected' : '' }}>⏳ Probation / Masa Percobaan</option>
                        <option value="contract" {{ old('contract_type') === 'contract' ? 'selected' : '' }}>📄 Kontrak Kerja (PKWT)</option>
                        <option value="part_time" {{ old('contract_type') === 'part_time' ? 'selected' : '' }}>⌚ Part-Time / Paruh Waktu</option>
                        <option value="full_time" {{ old('contract_type') === 'full_time' ? 'selected' : '' }}>💼 Full-Time / Tetap (PKWTT)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tanggal Mulai Kontrak <span class="text-red-600">*</span></label>
                    <input type="date" name="contract_start" value="{{ old('contract_start', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Tanggal Berakhir Kontrak <span class="text-red-600">*</span></label>
                    <input type="date" name="contract_end" value="{{ old('contract_end', date('Y-m-d', strtotime('+6 months'))) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Estimasi Gaji (IDR) <span class="text-gray-400 font-normal">(opsional, numeric only)</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-gray-400">Rp</span>
                        <input type="number" name="salary" min="0" step="100000" value="{{ old('salary') }}" placeholder="Contoh: 8000000" class="w-full pl-12 pr-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Alamat & Catatan -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-lg">📍 Alamat & Catatan Tambahan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Data pelengkap untuk kebutuhan operasional.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Alamat Lengkap <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea name="address" rows="2" maxlength="1000" placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi 12345" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition resize-none">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Catatan Internal <span class="text-gray-400 font-normal">(opsional, hanya terlihat di dashboard internal)</span></label>
                    <textarea name="notes" rows="3" maxlength="2000" placeholder="Contoh: Perlu laptop MacBook Pro, Sudah vaksin booster, Dapat referensi dari tim design, dsb." class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-gik-primary transition resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: Summary & Submit Sticky -->
    <div class="space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border-2 border-gik-accent/30 p-6 sticky top-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-gik-primary to-orange-500 text-white flex items-center justify-center shadow-md shadow-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5.5 h-5.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-extrabold text-gik-primary-dark text-sm leading-tight">Ringkasan Validasi</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">Checklist data sebelum submit.</div>
                </div>
            </div>
            <div class="space-y-2.5 text-[12px] mb-6">
                <div class="flex items-start gap-2.5">
                    <span class="mt-0.5 w-4 h-4 rounded-full bg-green-100 text-green-700 text-[9px] font-extrabold flex items-center justify-center flex-shrink-0">*</span>
                    <div>
                        <div class="font-bold text-gray-800">Wajib ({{ $isSA ? '11' : '10' }} field)</div>
                        <div class="text-gray-500">First/Last Name, Dept, Job Title, Work/Contract Type, Start/End {{ $isSA ? ', Company' : '' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="mt-0.5 w-4 h-4 rounded-full bg-gray-100 text-gray-600 text-[9px] font-extrabold flex items-center justify-center flex-shrink-0">O</span>
                    <div>
                        <div class="font-bold text-gray-800">Opsional (8 field)</div>
                        <div class="text-gray-500">Employee ID, Email, Phone, NIK, Salary, Address, Notes, Avatar</div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-3">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-gik-primary to-orange-500 hover:from-gik-primary-dark hover:to-orange-600 text-white rounded-xl text-sm font-extrabold transition shadow-lg shadow-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    💾 Simpan Data Peserta Aktif
                </button>
                <a href="{{ route($rPrefix . ($isSA ? 'employees' : 'dashboard')) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-200 text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-bold transition">
                    ⬅️ Batal / Kembali
                </a>
            </div>
        </div>

        @if($isSA)
        <div class="rounded-3xl border-2 border-dashed border-red-300 bg-gradient-to-br from-red-50 via-white to-orange-50 p-6">
            <div class="text-[11px] font-extrabold uppercase tracking-widest text-gik-primary mb-2">💡 Tips Super Admin</div>
            <ul class="space-y-2 text-xs text-gray-600">
                <li class="flex gap-2">
                    <span class="text-gik-primary font-extrabold">1.</span>
                    <span><strong>Gunakan Excel Import</strong> untuk input > 5 karyawan sekaligus. Klik "Template" di halaman Participants.</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-gik-primary font-extrabold">2.</span>
                    <span>Kolom <strong class="text-gik-primary">"Company ID"</strong> di Excel = ID perusahaan yang dipilih di atas.</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-gik-primary font-extrabold">3.</span>
                    <span>Data otomatis muncul di dashboard HR Company tujuan setelah disubmit.</span>
                </li>
            </ul>
        </div>
        @endif
    </div>

</div>
</form>
@endsection
