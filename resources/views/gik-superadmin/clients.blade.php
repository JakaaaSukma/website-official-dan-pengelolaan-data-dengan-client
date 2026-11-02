@extends('layouts.gik-superadmin')

@section('content')
<div class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Kelola Login Client</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola akun HRD untuk masing-masing tenant Company A–D.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="relative min-w-[260px]">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <form method="GET" action="{{ route('gik-superadmin.clients') }}" class="m-0 p-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name, email..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 text-sm">
            </form>
        </div>
        <a href="{{ route('gik-superadmin.clients.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-green-600 text-green-700 bg-white hover:bg-green-50 rounded-lg text-sm font-bold transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            + Add Client
        </a>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3 justify-between">
        <div class="flex gap-1 bg-gray-100 rounded-xl px-4 py-1.5 text-xs font-bold">
            <a href="{{ route('gik-superadmin.clients', ['status' => null]) }}" class="px-3 py-1.5 rounded-lg transition
                {{ !request('status') ? 'bg-white text-gik-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">All</a>
            <a href="{{ route('gik-superadmin.clients', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-lg transition
                {{ request('status') == 'active' ? 'bg-white text-gik-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">Active</a>
            <a href="{{ route('gik-superadmin.clients', ['status' => 'pending_review']) }}" class="px-3 py-1.5 rounded-lg transition
                {{ request('status') == 'pending_review' ? 'bg-white text-gik-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">Pending Review</a>
            <a href="{{ route('gik-superadmin.clients', ['status' => 'suspended']) }}" class="px-3 py-1.5 rounded-lg transition
                {{ request('status') == 'suspended' ? 'bg-white text-gik-primary shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">Suspended</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5 text-left">🏢 ID Perusahaan</th>
                    <th class="px-6 py-3.5 text-left">Tenant</th>
                    <th class="px-6 py-3.5 text-left">Nama Client + Perusahaan</th>
                    <th class="px-6 py-3.5 text-left">Admin Account</th>
                    <th class="px-6 py-3.5 text-left">Status</th>
                    <th class="px-6 py-3.5 text-left">Peserta Aktif</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $activeCounts = App\Models\HrContract::withoutTenant()
                        ->selectRaw('company_id, count(*) as cnt')
                        ->where('status', 'active')
                        ->groupBy('company_id')
                        ->pluck('cnt', 'company_id');
                @endphp
                @forelse($clients as $c)
                    @php
                        $initials = strtoupper(substr($c->name, 0, 1));
                        $companyName = optional($c->company)->company_name ?? '—';
                        $tenantBadge = strtoupper(substr($companyName, 0, 3));
                        $activeInterns = $c->company_id ? ($activeCounts[$c->company_id] ?? 0) : 0;
                        $companyIdText = $c->company_id ? 'ID #' . $c->company_id : '—';
                    @endphp
                    <tr class="hover:bg-red-50/60/30 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-red-50 text-gik-primary-dark text-[12px] font-black tracking-tight ring-1 ring-red-100">
                                {{ $companyIdText }}
                            </span>
                            @if($c->company_id)
                                <div class="text-[10px] text-gray-400 mt-1">Gunakan di Template Excel Import</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-bold uppercase">
                                {{ $tenantBadge }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-extrabold shadow-sm">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">{{ $c->name }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $companyName }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-700">{{ $c->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($c->account_status == 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Active</span>
                            @elseif($c->account_status == 'pending_review')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pending Review</span>
                            @elseif($c->account_status == 'suspended')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Suspended</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($c->account_status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-full text-xs font-extrabold bg-gik-primary/10 text-gik-primary">
                                {{ $activeInterns }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-bold space-x-1 whitespace-nowrap">
                            <a href="{{ route('gik-superadmin.clients.edit', $c) }}" class="text-gik-accent hover:text-gik-primary">Edit</a>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('gik-superadmin.clients.force-delete', $c) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN! Ini akan MENGHAPUS PERMANEN akun login client dari database (tidak bisa dikembalikan). Yakin lanjut?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-extrabold">🗑 Hapus Login</button>
                            </form>
                            @if($c->account_status == 'suspended')
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('gik-superadmin.clients.update', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="account_status" value="active">
                                    <input type="hidden" name="name" value="{{ $c->name }}">
                                    <input type="hidden" name="email" value="{{ $c->email }}">
                                    <input type="hidden" name="role" value="{{ $c->role }}">
                                    <input type="hidden" name="company_id" value="{{ $c->company_id }}">
                                    <button type="submit" class="text-green-600 hover:text-green-700" onclick="return confirm('Activate this client account?')">Activate</button>
                                </form>
                            @else
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('gik-superadmin.clients.destroy', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-amber-600 hover:text-amber-700" onclick="return confirm('Suspend (nonaktifkan sementara) client account ini?')">Suspend</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center text-gray-500">Belum ada akun HRD client. Klik tombol + Add Client untuk menambahkan akun tenant baru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex items-center justify-between rounded-b-3xl">
            <span>Showing {{ $clients->firstItem() ?? 0 }}–{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients</span>
            {{ $clients->links() }}
        </div>
    @endif
</div>

@php
$showAdd = session('showAddClientModal', false);
$showEdit = session('showEditClientModal', false);
$editC = session('editClient', null);
$allC = session('allCompanies', $allCompanies ?? []);
@endphp

<div id="addClientModal" class="fixed inset-0 z-[80] items-center justify-center p-4 {{ $showAdd ? 'flex' : 'hidden' }}" style="background:rgba(15,23,42,.55)">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="text-xl font-extrabold text-gik-primary-dark">Tambah Akun HR Client</h3>
        <p class="text-xs text-gray-500 mt-0.5">Isi data login + data perusahaan untuk membuat akun HRD tenant baru.</p>
      </div>
      <button onclick="closeModal('addClientModal')" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('gik-superadmin.clients.store') }}" class="px-7 py-6 space-y-5">
    @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Nama Lengkap (HRD PIC)</label>
          <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="cth. Ibu HRD Company A">
          @error('name') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Email Login <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="hrd@company.com">
          @error('email') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">No. WhatsApp / HP</label>
          <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="0812...">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Password <span class="text-red-500">*</span></label>
          <input type="password" name="password" required minlength="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="Min. 6 karakter">
          @error('password') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
          <input type="password" name="password_confirmation" required minlength="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Role</label>
          <select name="role" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent bg-white">
            <option value="hrd_company" {{ old('role') === 'hrd_company' ? 'selected' : '' }}>HRD Company (Akses Penuh)</option>
            <option value="company" {{ old('role') === 'company' ? 'selected' : '' }}>Company Owner (View Only)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Account Status</label>
          <select name="account_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent bg-white">
            <option value="active" {{ old('account_status', 'active') === 'active' ? 'selected' : '' }}>Active (Bisa Login)</option>
            <option value="pending_review" {{ old('account_status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
            <option value="suspended" {{ old('account_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
        </div>
      </div>

      <div class="border-t border-dashed border-gray-200 pt-5">
        <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-3">Pilih Perusahaan</label>
        <div class="flex gap-2 mb-4">
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="company_mode" value="existing" id="cmExisting" class="peer hidden" checked onchange="toggleCompanyMode('existing')">
            <div class="text-center px-3 py-2.5 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 peer-checked:border-gik-accent peer-checked:bg-red-50/60 peer-checked:text-gik-primary transition">
              Pilih Perusahaan Existing
            </div>
          </label>
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="company_mode" value="new" id="cmNew" class="peer hidden" onchange="toggleCompanyMode('new')">
            <div class="text-center px-3 py-2.5 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
              + Buat Perusahaan BARU
            </div>
          </label>
        </div>

        <div id="companyExistingWrap" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-600 mb-1.5">Pilih Tenant Perusahaan</label>
            <select name="company_id" id="company_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
              <option value="">-- Pilih perusahaan --</option>
              @foreach($allC as $cp)
                <option value="{{ $cp->id }}" {{ old('company_id') == $cp->id ? 'selected' : '' }}>{{ $cp->company_name }}</option>
              @endforeach
            </select>
            @error('company_id') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
        </div>

        <div id="companyNewWrap" class="space-y-4 hidden">
          <div>
            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Perusahaan BARU <span class="text-red-500">*</span></label>
            <input type="text" name="new_company_name" id="new_company_name" value="{{ old('new_company_name') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="cth. PT. Teknologi Indonesia Baru">
            @error('new_company_name') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-600 mb-1.5">Email Perusahaan</label>
              <input type="email" name="new_company_email" value="{{ old('new_company_email') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="hr@newcompany.com">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-600 mb-1.5">Telp. Perusahaan</label>
              <input type="text" name="new_company_phone" value="{{ old('new_company_phone') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="021...">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-600 mb-1.5">Alamat / Deskripsi Singkat Perusahaan</label>
            <textarea name="new_company_about" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="Alamat kantor & tentang perusahaan">{{ old('new_company_about') }}</textarea>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
        <button type="button" onclick="closeModal('addClientModal')" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Cancel</button>
        <button type="submit" class="px-6 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Create Client Account
        </button>
      </div>
    </form>
  </div>
</div>

<div id="editClientModal" class="fixed inset-0 z-[80] items-center justify-center p-4 {{ $showEdit ? 'flex' : 'hidden' }}" style="background:rgba(15,23,42,.55)">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
    @if($editC)
    <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="text-xl font-extrabold text-gik-primary-dark">Edit Akun HR Client</h3>
        <p class="text-xs text-gray-500 mt-0.5">Perbarui data login, ganti password (jika perlu), atau pindahkan ke perusahaan lain.</p>
      </div>
      <button onclick="closeModal('editClientModal')" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('gik-superadmin.clients.update', $editC) }}" class="px-7 py-6 space-y-5">
    @csrf @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Nama Lengkap (HRD PIC)</label>
          <input type="text" name="name" value="{{ old('name', $editC->name) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Email Login</label>
          <input type="email" name="email" value="{{ old('email', $editC->email) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">No. WhatsApp / HP</label>
          <input type="text" name="phone" value="{{ old('phone', $editC->phone) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
          <input type="password" name="password" minlength="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="Min. 6 karakter">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation" minlength="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Role</label>
          <select name="role" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
            <option value="hrd_company" {{ old('role', $editC->role) === 'hrd_company' ? 'selected' : '' }}>HRD Company (Akses Penuh)</option>
            <option value="company" {{ old('role', $editC->role) === 'company' ? 'selected' : '' }}>Company Owner (View Only)</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Account Status</label>
          <select name="account_status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
            <option value="active" {{ old('account_status', $editC->account_status) === 'active' ? 'selected' : '' }}>Active (Bisa Login)</option>
            <option value="pending_review" {{ old('account_status', $editC->account_status) === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
            <option value="suspended" {{ old('account_status', $editC->account_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-1.5">Pindahkan ke Perusahaan (Tenant)</label>
          <select name="company_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent">
            @foreach($allC as $cp)
              <option value="{{ $cp->id }}" {{ old('company_id', $editC->company_id) == $cp->id ? 'selected' : '' }}>{{ $cp->company_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
        <button type="button" onclick="closeModal('editClientModal')" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Cancel</button>
        <button type="submit" class="px-6 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 inline -mt-0.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Simpan Perubahan
        </button>
      </div>
    </form>
    @endif
  </div>
</div>

<script>
function toggleCompanyMode(mode){
  document.getElementById('companyExistingWrap').classList.toggle('hidden', mode !== 'existing');
  document.getElementById('companyNewWrap').classList.toggle('hidden', mode !== 'new');
  document.getElementById('company_id').required = (mode === 'existing');
  document.getElementById('new_company_name').required = (mode === 'new');
}
function closeModal(id){
  const el = document.getElementById(id);
  el.classList.add('hidden'); el.classList.remove('flex');
}
window.addEventListener('DOMContentLoaded', () => {
  if ({{ old('company_mode', 'existing') === 'new' ? 'true' : 'false' }}) toggleCompanyMode('new');
});
@if ($errors->any())
  setTimeout(() => {
    const addM = document.getElementById('addClientModal');
    if (addM && !addM.classList.contains('flex')) { addM.classList.remove('hidden'); addM.classList.add('flex'); }
  }, 50);
@endif
</script>
@endsection
