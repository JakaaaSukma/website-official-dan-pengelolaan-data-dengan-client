@extends('layouts.gik-superadmin')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">📘 Panduan Penggunaan — Super Admin Rekrutmen</h1>
        <p class="text-gray-500 text-sm mt-1">Panduan lengkap cara menggunakan setiap fitur di Gyokai Connect untuk akun Super Admin Rekrutmen.</p>
    </div>
    <a href="{{ route('gik-superadmin.panduan.download') }}" class="px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF Panduan
    </a>
</div>

{{-- Table of Contents --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 text-sm">📑</span>
        Daftar Isi & Peta Alur
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <a href="#flowchart" class="md:col-span-2 flex items-center justify-between gap-3 px-5 py-4 rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-white hover:border-red-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">🧭</div>
                <div>
                    <span class="text-sm font-extrabold text-red-950 block">Diagram Alur Operasional Terintegrasi (Flowchart Gyokai Connect)</span>
                    <span class="text-xs text-red-700">Visualisasi swimlane interaksi lengkap antara Tim Rekrutmen Gyokai & Perusahaan Mitra</span>
                </div>
            </div>
            <span class="text-xs font-extrabold text-red-700 bg-red-100 group-hover:bg-red-200 px-3 py-1.5 rounded-xl transition">&rarr; Lihat Diagram</span>
        </a>
        <a href="#login" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">1</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Login ke Sistem</span>
        </a>
        <a href="#dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">2</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Control Center (Dashboard)</span>
        </a>
        <a href="#clients" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">3</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Client Account Management</span>
        </a>
        <a href="#add-contract" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">4</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Add New Contract & Pengajuan Kandidat</span>
        </a>
        <a href="#talents" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">5</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Talent DB & Matching Engine</span>
        </a>
        <a href="#operations" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">6</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Requests & Operations</span>
        </a>
        <a href="#employees" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">7</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">All Active Participants (Peserta Aktif)</span>
        </a>
        <a href="#candidates" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">8</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">All Company Candidates</span>
        </a>
        <a href="#offboarding" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-orange-100 group-hover:bg-orange-200 flex items-center justify-center text-xs font-bold text-orange-700">9</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-orange-700">Manajemen Off-boarding Peserta</span>
        </a>
        <a href="#violations" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">10</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Manajemen Pelanggaran</span>
        </a>
        <a href="#suspensions" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">11</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Pemberhentian Sementara</span>
        </a>
        <a href="#bpjs" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">12</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">BPJS Ketenagakerjaan, F2A & Kecelakaan Kerja</span>
        </a>
        <a href="#inbox" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">13</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Client Inbox & Messaging</span>
        </a>
        <a href="#security" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">14</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Log Aktivitas & Keamanan Akun</span>
        </a>
    </div>
</div>

{{-- MASTER FLOWCHART COMPONENT --}}
@include('gik-shared.flowchart-component')

{{-- Content Sections --}}
<div class="space-y-6">

    {{-- 1. Login --}}
    <div id="login" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">1</span>
            <h2 class="text-xl font-bold text-gray-800">Login ke Sistem</h2>
        </div>
        <div class="bg-gray-50 rounded-xl p-5 mb-4">
            <p class="text-sm text-gray-600 mb-3"><strong class="text-gray-800">URL Login:</strong> <code class="bg-red-50 text-red-700 px-2 py-1 rounded text-xs font-mono">{{ url('/admin/login') }}</code></p>
            <p class="text-sm text-gray-600 mb-1"><strong class="text-gray-800">Form yang digunakan:</strong> Form "Company / HR Login" di halaman login.</p>
        </div>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur Login:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-2 ml-2">
            <li>Buka halaman <strong>{{ url('/admin/login') }}</strong> di browser.</li>
            <li>Masukkan <strong>Email</strong> dan <strong>Password</strong> akun Super Admin Rekrutmen.</li>
            <li>Klik tombol <strong>"Login"</strong>.</li>
            <li>Sistem akan otomatis mendeteksi role Anda dan mengarahkan ke <strong>Control Center (Dashboard)</strong> Super Admin.</li>
            <li>Jika login gagal, periksa kembali email & password. Setelah 5x percobaan gagal, akun akan di-lock sementara.</li>
        </ol>
    </div>

    {{-- 2. Dashboard --}}
    <div id="dashboard" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">2</span>
            <h2 class="text-xl font-bold text-gray-800">Control Center (Dashboard)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Halaman utama setelah login. Menampilkan ringkasan statistik seluruh tenant/perusahaan.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📊 Informasi yang ditampilkan:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Total Perusahaan Klien yang terdaftar</li>
            <li>Total Peserta Aktif (semua perusahaan)</li>
            <li>Jumlah Kontrak yang akan segera habis (expiring)</li>
            <li>Pesan masuk terbaru dari perusahaan klien</li>
            <li>Request/pengajuan terbaru yang perlu di-review</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Setelah login → otomatis masuk ke Dashboard.</li>
            <li>Pantau semua statistik dan notifikasi di satu tempat.</li>
            <li>Klik menu di sidebar kiri untuk navigasi ke fitur lain.</li>
        </ol>
    </div>

    {{-- 3. Client Account Management --}}
    <div id="clients" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">3</span>
            <h2 class="text-xl font-bold text-gray-800">Client Account Management</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola semua akun perusahaan klien yang terdaftar di sistem.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat daftar perusahaan</strong> — Menampilkan semua perusahaan klien beserta jumlah peserta aktif.</li>
            <li><strong>Tambah perusahaan baru</strong> — Mendaftarkan perusahaan klien baru ke sistem beserta akun HR-nya.</li>
            <li><strong>Edit data perusahaan</strong> — Mengubah informasi perusahaan (nama, alamat, kontak, dll).</li>
            <li><strong>Hapus perusahaan</strong> — Menonaktifkan atau menghapus perusahaan klien dari sistem.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Menambah Perusahaan Baru:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Client Account Mgmt"</strong> di sidebar.</li>
            <li>Klik tombol <strong>"Tambah Perusahaan"</strong>.</li>
            <li>Isi data: Nama Perusahaan, Alamat, Email PIC, No. Telepon.</li>
            <li>Sistem otomatis membuat akun HR Company untuk perusahaan tersebut.</li>
            <li>Bagikan credential login kepada PIC perusahaan.</li>
        </ol>
    </div>

    {{-- 4. Add New Contract --}}
    <div id="add-contract" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">4</span>
            <h2 class="text-xl font-bold text-gray-800">Add New Contract & Pengajuan Kandidat</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Buat pengajuan kontrak baru untuk kandidat yang akan ditempatkan di perusahaan klien.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur Add New Contract:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Add New Contract"</strong> di sidebar.</li>
            <li>Pilih <strong>perusahaan penempatan</strong> (destination company).</li>
            <li>Isi data kandidat: Nama, NIK, Posisi/Jabatan, Tanggal Mulai Kontrak, Tanggal Selesai, Gaji, dll.</li>
            <li>Submit pengajuan → Pengajuan akan masuk ke <strong>Daftar Pengajuan Kandidat</strong>.</li>
            <li>HR Perusahaan akan menerima notifikasi untuk me-review dan menyetujui/menolak kandidat.</li>
        </ol>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Daftar Pengajuan Kandidat:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Daftar Pengajuan Kandidat"</strong> di sidebar.</li>
            <li>Lihat semua batch pengajuan beserta status (Draft, Submitted, Approved, Rejected).</li>
            <li>Klik pada batch untuk melihat detail kandidat yang diajukan.</li>
        </ol>
    </div>

    {{-- 5. Talent DB --}}
    <div id="talents" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">5</span>
            <h2 class="text-xl font-bold text-gray-800">Talent DB & Matching Engine</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Database tenaga ahli/berpengalaman yang siap dipanggil oleh perusahaan klien.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat & cari talent</strong> — Cari berdasarkan nama, skill, pengalaman, dll.</li>
            <li><strong>Tambah talent manual</strong> — Input data talent satu per satu.</li>
            <li><strong>Import talent via Excel</strong> — Upload file Excel untuk menambahkan banyak talent sekaligus.</li>
            <li><strong>Download template Excel</strong> — Unduh template file untuk panduan import.</li>
            <li><strong>Hapus talent</strong> — Menghapus data talent dari database.</li>
            <li><strong>Match Engine</strong> — Mencocokkan talent yang sesuai dengan kebutuhan perusahaan klien secara otomatis.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Import Talent via Excel:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Talent DB & Matching"</strong> di sidebar.</li>
            <li>Klik tombol <strong>"Import Talent"</strong>.</li>
            <li>Download <strong>template Excel</strong> terlebih dahulu (klik "Download Template").</li>
            <li>Isi data talent di file Excel sesuai format template.</li>
            <li>Upload file Excel yang sudah diisi → Klik <strong>"Upload & Preview"</strong>.</li>
            <li>Sistem menampilkan preview data yang valid dan yang gagal (error).</li>
            <li>Jika sudah benar, klik <strong>"Confirm Import"</strong> untuk memasukkan data ke database.</li>
        </ol>
    </div>

    {{-- 6. Operations --}}
    <div id="operations" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">6</span>
            <h2 class="text-xl font-bold text-gray-800">Requests & Operations</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola semua request/pengajuan dari perusahaan klien dan internal.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Jenis Request:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Extend Contract</strong> — Pengajuan perpanjangan kontrak dari HR Company.</li>
            <li><strong>Off-boarding</strong> — Pengajuan pemberhentian/selesainya kontrak peserta.</li>
            <li><strong>Add New Contract</strong> — Pengajuan penambahan peserta baru.</li>
            <li><strong>General Request</strong> — Request umum dari perusahaan klien.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Review Request:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Requests & Operations"</strong> di sidebar.</li>
            <li>Lihat daftar request yang masuk beserta statusnya.</li>
            <li>Klik pada request untuk melihat detail.</li>
            <li>Review dan pilih: <strong>Approve</strong> atau <strong>Reject</strong> dengan catatan.</li>
            <li>HR Company akan menerima notifikasi hasil review.</li>
        </ol>
    </div>

    {{-- 7. All Active Participants --}}
    <div id="employees" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">7</span>
            <h2 class="text-xl font-bold text-gray-800">All Active Participants (Peserta Aktif)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Data seluruh peserta aktif di semua perusahaan klien (cross-tenant).</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat semua peserta</strong> — Filter berdasarkan perusahaan, status, pencarian nama/NIK.</li>
            <li><strong>Tambah peserta manual</strong> — Input data peserta satu per satu.</li>
            <li><strong>Import peserta via Excel</strong> — Upload file Excel (max 500 baris per upload). Kolom wajib: Nama Lengkap, NIK, Employee ID, dll.</li>
            <li><strong>Download template Excel</strong> — Unduh template file import.</li>
            <li><strong>Edit peserta</strong> — Ubah data peserta yang sudah terdaftar.</li>
            <li><strong>Hapus peserta</strong> — Menghapus data peserta dari sistem.</li>
            <li><strong>Lihat detail</strong> — Lihat profil lengkap peserta termasuk BPJS, kontrak, dll.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Import Peserta via Excel:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"All Active Participants"</strong> di sidebar.</li>
            <li>Klik tombol <strong>"Import Peserta"</strong>.</li>
            <li>Pilih <strong>perusahaan penempatan</strong> dari dropdown.</li>
            <li>Download <strong>template Excel</strong> terlebih dahulu.</li>
            <li>Isi data peserta di file Excel (gunakan kolom: Nama Lengkap, NIK, Employee ID, Email, dll).</li>
            <li>Upload file → Sistem menampilkan preview data valid & invalid.</li>
            <li>Klik <strong>"Confirm Import"</strong> untuk menyimpan data ke database.</li>
            <li>Data baru akan ditambahkan; data yang sudah ada (berdasarkan Employee ID/Email) akan di-update otomatis.</li>
        </ol>
    </div>

    {{-- 8. All Company Candidates --}}
    <div id="candidates" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">8</span>
            <h2 class="text-xl font-bold text-gray-800">All Company Candidates</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Data calon peserta/kandidat dari semua perusahaan klien.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat semua kandidat</strong> — Filter berdasarkan perusahaan, status rekrutmen.</li>
            <li><strong>Tambah kandidat manual</strong> — Input data kandidat satu per satu.</li>
            <li><strong>Import kandidat via Excel</strong> — Upload file Excel untuk menambahkan banyak kandidat.</li>
            <li><strong>Hapus kandidat</strong> — Menghapus data kandidat dari sistem.</li>
            <li><strong>Lihat detail kandidat</strong> — Profil lengkap, skill, pengalaman.</li>
        </ul>
    </div>

    {{-- 9. Off-boarding Management --}}
    <div id="offboarding" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-sm font-bold">9</span>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Off-boarding Peserta</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Pusat kendali penyelesaian masa kontrak, pelepasan peserta, dan arsip data peserta off-boarded lintas seluruh perusahaan mitra.</p>
        
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur Utama:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2 mb-4">
            <li><strong>Tab Pengajuan Masuk dari HR Klien:</strong> Memeriksa permohonan off-boarding batch yang diajukan oleh perusahaan mitra, lengkap dengan modal review interaktif dan opsi quick approve/reject.</li>
            <li><strong>Tab Arsip Peserta Off-boarded:</strong> Melihat riwayat seluruh peserta yang telah selesai masa kontrak/off-board, dilengkapi filter perusahaan dan pencarian.</li>
            <li><strong>+ Proses Off-boarding Langsung:</strong> Otoritas Super Admin untuk memproses off-boarding multi-peserta secara langsung tanpa menunggu pengajuan klien (misal peserta bermasalah, deportasi, atau pemutusan darurat).</li>
            <li><strong>Aksi Cepat di Tabel Peserta:</strong> Tombol "Off-board" langsung pada baris tabel <em>All Active Participants</em> dan detail peserta.</li>
        </ul>

        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur Review Pengajuan Klien:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Buka menu <strong>"Manajemen Off-boarding"</strong> di sidebar (lihat badge notifikasi angka pengajuan pending).</li>
            <li>Pada Tab 1 <strong>"Pengajuan Masuk dari HR Klien"</strong>, klik tombol <strong>"Review Permohonan"</strong>.</li>
            <li>Periksa daftar nama peserta, tanggal efektif keluar, dan alasan/catatan.</li>
            <li>Klik <strong>"Setujui Permohonan (Approve)"</strong> untuk langsung mengarsipkan peserta, atau isi alasan jika ingin <strong>"Tolak Permohonan"</strong>.</li>
        </ol>
    </div>

    {{-- 10. Violations --}}
    <div id="violations" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">10</span>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Pelanggaran</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola laporan pelanggaran peserta yang dilaporkan oleh HR perusahaan.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>HR Perusahaan membuat laporan pelanggaran dari menu mereka.</li>
            <li>Laporan masuk ke <strong>Manajemen Pelanggaran</strong> Super Admin dengan status <em>"Pending Review"</em>.</li>
            <li>Super Admin membuka dan mereview detail laporan.</li>
            <li>Super Admin mengubah status: <strong>Diterima</strong>, <strong>Ditolak</strong>, <strong>Dalam Proses</strong>, atau <strong>Selesai</strong>.</li>
            <li>HR Perusahaan menerima notifikasi hasil keputusan.</li>
        </ol>
    </div>

    {{-- 10. Suspensions --}}
    <div id="suspensions" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">10</span>
            <h2 class="text-xl font-bold text-gray-800">Pemberhentian Sementara</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola pengajuan pemberhentian sementara (suspension) peserta dari HR perusahaan.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>HR Perusahaan mengajukan pemberhentian sementara dari menu mereka.</li>
            <li>Pengajuan masuk ke Super Admin dengan status <em>"Pending Approval"</em>.</li>
            <li>Super Admin mereview alasan dan bukti yang dilampirkan.</li>
            <li>Super Admin menyetujui atau menolak pengajuan.</li>
            <li>Jika disetujui, status peserta akan berubah menjadi <em>"Suspended"</em>.</li>
        </ol>
    </div>

    {{-- 11. BPJS --}}
    <div id="bpjs" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">11</span>
            <h2 class="text-xl font-bold text-gray-800">BPJS Ketenagakerjaan, Pelaporan F2A & Kecelakaan Kerja</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Modul lengkap pengelolaan BPJS Ketenagakerjaan untuk semua perusahaan klien.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Sub-fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-2 ml-2">
            <li><strong>BPJS Ketenagakerjaan</strong> — Lihat status kepesertaan BPJS seluruh peserta, ubah status BPJS (aktif/nonaktif).</li>
            <li><strong>Rate Settings</strong> — Atur tarif/rate iuran BPJS (JKK, JKM, JHT, JP, JKP) per perusahaan. Riwayat perubahan rate tercatat otomatis.</li>
            <li><strong>Pelaporan F2A</strong> — Generate Formulir 2a PU BPJS Ketenagakerjaan per periode dan per perusahaan. Export ke Excel atau Word.</li>
            <li><strong>Laporan Kecelakaan Kerja</strong> — Input dan kelola laporan kecelakaan kerja peserta. Dashboard rekapitulasi. Export ke Excel.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Generate Laporan F2A:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Pelaporan F2A"</strong> di sidebar (di bawah BPJS Ketenagakerjaan).</li>
            <li>Pilih <strong>Perusahaan</strong>, <strong>Bulan</strong>, dan <strong>Tahun</strong>.</li>
            <li>Klik <strong>"Tampilkan"</strong>.</li>
            <li>Sistem menampilkan tabel Formulir 2a PU dengan data peserta aktif dan rincian iuran.</li>
            <li>Klik <strong>"Export Excel"</strong> atau <strong>"Export Word"</strong> untuk mengunduh laporan.</li>
        </ol>
    </div>

    {{-- 12. Client Inbox --}}
    <div id="inbox" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">12</span>
            <h2 class="text-xl font-bold text-gray-800">Client Inbox & Messaging</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola pesan dan komunikasi dari/untuk perusahaan klien.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat pesan masuk</strong> — Semua pesan dari HR perusahaan klien.</li>
            <li><strong>Balas pesan</strong> — Kirim balasan langsung ke HR perusahaan.</li>
            <li><strong>Tandai selesai</strong> — Resolve pesan yang sudah ditangani.</li>
            <li><strong>Assign pesan</strong> — Delegasikan pesan ke tim internal.</li>
        </ul>
    </div>

    {{-- 13. Security --}}
    <div id="security" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">13</span>
            <h2 class="text-xl font-bold text-gray-800">Log Aktivitas & Keamanan Akun</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Pantau semua aktivitas dan kelola keamanan akun.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Log Aktivitas Sistem</strong> — Lihat semua log aktivitas: login, import data, edit peserta, review request, dll. Setiap perubahan tercatat otomatis.</li>
            <li><strong>Ubah Password</strong> — Ganti password akun Super Admin. Gunakan password yang kuat (minimal 8 karakter, kombinasi huruf & angka).</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Ubah Password:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Ubah Password SA"</strong> di sidebar, atau klik tombol "Change Password" di footer sidebar.</li>
            <li>Masukkan <strong>Password Lama</strong>.</li>
            <li>Masukkan <strong>Password Baru</strong> (minimal 8 karakter).</li>
            <li>Konfirmasi password baru.</li>
            <li>Klik <strong>"Update Password"</strong>.</li>
        </ol>
    </div>

</div>

{{-- Footer --}}
<div class="mt-8 mb-6 text-center">
    <p class="text-gray-400 text-xs">Gyokai Connect — Panduan Penggunaan Super Admin Rekrutmen • Versi 1.0 • {{ now()->format('d M Y') }}</p>
</div>
@endsection
