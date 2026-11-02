@extends('layouts.gik-superadmin')

@php
$isSA = true;
$rPrefix = 'gik-superadmin.';
$submitRoute = route($rPrefix . 'profile.update-password');
$dashRoute = route($rPrefix . 'dashboard');
@endphp

@section('content')
<div class="mb-7 flex items-center justify-between flex-wrap gap-4">
  <div>
    <div class="text-[11px] font-bold uppercase tracking-widest text-gik-accent mb-1">{{ $isSA ? 'SUPER ADMIN • SECURITY' : 'HR COMPANY • SECURITY' }}</div>
    <h1 class="text-[30px] font-extrabold text-gik-primary-dark leading-tight">Ubah Password Akun</h1>
    <p class="text-gray-500 text-sm mt-1">Untuk keamanan akun, ganti password secara berkala. Pastikan password unik & minimal 6 karakter.</p>
  </div>
  <a href="{{ $dashRoute }}" class="inline-flex items-center gap-2 px-4 py-2 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-sm font-bold transition">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali ke Dashboard
  </a>
</div>

@if(session('success'))
<div class="mb-5 px-5 py-3.5 rounded-xl border-2 border-green-200 bg-green-50 text-green-800 text-sm font-bold shadow-sm">✅ {{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">
    <!-- Update Email Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7 mb-6">
      <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <h3 class="font-extrabold text-gray-800 text-lg">Ubah Email Akun</h3>
          <p class="text-xs text-gray-500 mt-0.5">Email yang digunakan untuk login dan menerima notifikasi</p>
        </div>
      </div>

      <form method="POST" action="{{ route($rPrefix . 'profile.update-email') }}" class="space-y-5">
      @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-gray-700 mb-2">Email BARU <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ Auth::user()->email }}" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="Masukkan alamat email baru">
          @error('email')<p class="text-red-500 text-xs font-bold mt-1">❌ {{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-gray-700 mb-2">Password SAAT INI (Untuk Verifikasi) <span class="text-red-500">*</span></label>
          <input type="password" name="current_password_email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="Masukkan password Anda">
          @error('current_password_email')<p class="text-red-500 text-xs font-bold mt-1">❌ {{ $message }}</p>@enderror
        </div>

        <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
          <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan Email Baru
          </button>
        </div>
      </form>
    </div>

    <!-- Update Password Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
      <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" class="w-[22px] h-[22px]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
          <h3 class="font-extrabold text-gray-800 text-lg">Ubah Password Akun</h3>
          <p class="text-xs text-gray-500 mt-0.5">Ubah password secara berkala untuk keamanan</p>
        </div>
      </div>

      <form method="POST" action="{{ $submitRoute }}" class="space-y-5">
      @csrf
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wide text-gray-700 mb-2">Password SAAT INI <span class="text-red-500">*</span></label>
          <input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-gik-accent" placeholder="Masukkan password lama Anda">
          @error('current_password')<p class="text-red-500 text-xs font-bold mt-1">❌ {{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-gray-700 mb-2">Password BARU <span class="text-red-500">*</span></label>
            <input type="password" name="password" required minlength="6" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="Min. 6 karakter (campur huruf & angka disarankan)">
            @error('password')<p class="text-red-500 text-xs font-bold mt-1">❌ {{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-gray-700 mb-2">Konfirmasi Password BARU <span class="text-red-500">*</span></label>
            <input type="password" name="password_confirmation" required minlength="6" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500" placeholder="Ketik ulang password baru di atas">
          </div>
        </div>

        <div class="pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
          <button type="reset" class="px-5 py-3 border border-gray-300 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-50 transition">Reset Form</button>
          <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-500/20 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan Password Baru
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="space-y-5">
    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-5">
      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
          <h4 class="font-extrabold text-amber-800 text-sm">Tips Keamanan</h4>
          <ul class="mt-2 space-y-1.5 text-[11px] text-amber-800/90 font-semibold leading-relaxed">
            <li>• Minimal 6 karakter, lebih panjang lebih baik.</li>
            <li>• Kombinasi HURUF BESAR, huruf kecil, angka, dan simbol.</li>
            <li>• Jangan gunakan password yang sama dengan akun lain.</li>
            <li>• Ganti password minimal 3 bulan sekali.</li>
            <li>• Periksa Log Aktivitas secara berkala.</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <h4 class="font-extrabold text-gray-800 text-sm mb-3">Shortcut Cepat</h4>
      <div class="space-y-2">
        <a href="{{ route($rPrefix . 'activity-logs') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-red-50 text-gray-700 hover:text-gik-primary text-xs font-bold transition">
          <span class="flex items-center gap-2"><span class="inline-flex w-2 h-2 rounded-full bg-red-50/600"></span> Lihat Log Aktivitas</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ $isSA ? route('gik-superadmin.clients') : route('gik-hrd.messages') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 text-xs font-bold transition">
          <span class="flex items-center gap-2"><span class="inline-flex w-2 h-2 rounded-full bg-emerald-500"></span> {{ $isSA ? 'Kelola Akun Client' : 'Pusat Pesan & Notifikasi' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
