@extends('layouts.gik-hrd')

@section('content')
<div class="mb-7">
    <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">Pusat Request</h1>
    <p class="text-gray-500 text-sm mt-1">Pilih jenis request untuk inisiasi proses operasional karyawan/peserta.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('gik-hrd.add-contract.list') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-7 hover:shadow-md hover:border-green-200 transition block relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-green-600 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-bl-xl">INBOX REVIEW</div>
        <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center mb-5 group-hover:scale-105 transition mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-1">Daftar Pengajuan Kandidat</h3>
        <p class="text-sm text-gray-500 leading-relaxed mb-4">Review semua Add New Contract (pengajuan kandidat baru) dari Super Admin. Centang kandidat yang lolos seleksi, lalu submit hasil untuk otomatis dibuatkan Peserta Aktif.</p>
        <div class="flex items-center gap-1.5 text-sm font-bold text-green-700 group-hover:gap-2.5 transition-all">
            Buka Inbox Seleksi
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </div>
    </a>

    <a href="{{ route('gik-hrd.batch.select', 'extend_contract') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-7 hover:shadow-md hover:border-blue-200 transition block">
        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center mb-5 group-hover:scale-105 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-1">Extend Contract Batch</h3>
        <p class="text-sm text-gray-500 leading-relaxed mb-4">Perpanjang kontrak banyak peserta sekaligus (multi-select checkbox). Data peserta (Nama, NIK, Tgl Lahir, Awal/Habis Kontrak Lama, Alamat) auto-fill read-only, HR hanya isi tanggal baru per row + catatan opsional.</p>
        <div class="flex items-center gap-1.5 text-sm font-bold text-blue-700 group-hover:gap-2.5 transition-all">
            Pilih Peserta & Buat Pengajuan
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </div>
    </a>

    <a href="{{ route('gik-hrd.batch.select', 'off_boarding') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-7 hover:shadow-md hover:border-red-200 transition block">
        <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center mb-5 group-hover:scale-105 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-1">Off-boarding Batch</h3>
        <p class="text-sm text-gray-500 leading-relaxed mb-4">Proses keluar banyak peserta sekaligus. Data auto-fill read-only, HR WAJIB isi Tanggal Efektif Off-boarding + Catatan/Alasan (resign, habis kontrak, pelanggaran, dll). Status diarsipkan TIDAK DIHAPUS.</p>
        <div class="flex items-center gap-1.5 text-sm font-bold text-red-700 group-hover:gap-2.5 transition-all">
            Start Off-boarding
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </div>
    </a>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="text-xs text-gray-500 font-medium mb-1">Total Requests</div>
        <div class="text-2xl font-extrabold text-gray-800">{{ $stats['total'] }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="text-xs text-gray-500 font-medium mb-1">Add Contract</div>
        <div class="text-2xl font-extrabold text-green-700">{{ $stats['add'] }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="text-xs text-gray-500 font-medium mb-1">Extend Contract</div>
        <div class="text-2xl font-extrabold text-blue-700">{{ $stats['extend'] }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="text-xs text-gray-500 font-medium mb-1">Off-boarding</div>
        <div class="text-2xl font-extrabold text-red-700">{{ $stats['off'] }}</div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="text-xs text-gray-500 font-medium mb-1">Pending Review</div>
        <div class="text-2xl font-extrabold text-amber-700">{{ $stats['pending'] }}</div>
    </div>
</div>

<div class="text-center bg-white rounded-2xl shadow-sm border border-dashed border-gray-200 p-8">
    <h3 class="font-bold text-gray-800 mb-2">Butuh bantuan?</h3>
    <p class="text-sm text-gray-500 mb-4">Jika ada pertanyaan terkait proses request, kirim pesan ke Tim Rekrutmen.</p>
    <a href="{{ route('gik-hrd.messages') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gik-primary text-white rounded-xl text-sm font-semibold hover:bg-gik-primary-dark transition">
        Hubungi Tim Rekrutmen
    </a>
</div>
@endsection
