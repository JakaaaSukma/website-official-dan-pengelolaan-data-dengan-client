@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="text-xs text-gik-accent font-bold uppercase tracking-widest mb-1.5">Super Admin Recruitment</div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Edit Participant Data</h1>
        <p class="text-gray-500 text-sm mt-1">Edit data peserta aktif (Intern / Employee).</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('gik-superadmin.employees') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-bold transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>
</div>

<form method="POST" action="{{ route('gik-superadmin.employees.update', $contract->id) }}">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7">
        <h3 class="font-extrabold text-gray-800 text-lg mb-5 border-b pb-3">Profil Personal</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" required value="{{ old('full_name', trim(($contract->first_name ?? '') . ' ' . ($contract->last_name ?? ''))) }}" placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                <input type="text" name="national_id" required maxlength="16" value="{{ old('national_id', $contract->national_id) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-mono focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $contract->birth_date ? \Carbon\Carbon::parse($contract->birth_date)->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">No. Handphone / WA</label>
                <input type="text" name="phone" value="{{ old('phone', $contract->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Email Aktif</label>
                <input type="email" name="email" value="{{ old('email', $contract->email) }}" placeholder="contoh@email.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">{{ old('address', $contract->address) }}</textarea>
            </div>
        </div>

        <h3 class="font-extrabold text-gray-800 text-lg mb-5 border-b pb-3">Informasi Pekerjaan & Kontrak</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Posisi (Job Title) <span class="text-red-500">*</span></label>
                <input type="text" name="job_title" required value="{{ old('job_title', $contract->job_title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="department" required value="{{ old('department', $contract->department) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">ID Karyawan</label>
                <input type="text" name="employee_id" value="{{ old('employee_id', $contract->employee_id) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Mulai Kontrak <span class="text-red-500">*</span></label>
                <input type="date" name="contract_start" required value="{{ old('contract_start', $contract->contract_start ? \Carbon\Carbon::parse($contract->contract_start)->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Berakhir Kontrak</label>
                <input type="date" name="contract_end" value="{{ old('contract_end', $contract->contract_end ? \Carbon\Carbon::parse($contract->contract_end)->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Tipe Kontrak</label>
                <select name="contract_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
                    <option value="internship" {{ old('contract_type', $contract->contract_type) === 'internship' ? 'selected' : '' }}>Internship</option>
                    <option value="contract" {{ old('contract_type', $contract->contract_type) === 'contract' ? 'selected' : '' }}>Contract (PKWT)</option>
                    <option value="permanent" {{ old('contract_type', $contract->contract_type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="probation" {{ old('contract_type', $contract->contract_type) === 'probation' ? 'selected' : '' }}>Probation</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Tipe Kerja</label>
                <select name="work_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
                    <option value="on_site" {{ old('work_type', $contract->work_type) === 'on_site' ? 'selected' : '' }}>On-Site</option>
                    <option value="remote" {{ old('work_type', $contract->work_type) === 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="hybrid" {{ old('work_type', $contract->work_type) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Status Aktif</label>
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gik-accent/20 focus:border-gik-accent transition">
                    <option value="active" {{ old('status', $contract->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $contract->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="terminated" {{ old('status', $contract->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    <option value="suspended" {{ old('status', $contract->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl font-bold transition shadow-lg shadow-red-600/20">
                Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection
