@extends('layouts.gik-hrd')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-7">
    <div>
        <h1 class="text-[28px] font-bold text-gik-primary-dark leading-tight">📘 Panduan Penggunaan — HR Perusahaan</h1>
        <p class="text-gray-500 text-sm mt-1">Panduan lengkap cara menggunakan setiap fitur di Gyokai Connect untuk akun HR Perusahaan.</p>
    </div>
    <a href="{{ route('gik-hrd.panduan.download') }}" class="px-5 py-2.5 bg-gik-primary hover:bg-gik-primary-dark text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF Panduan
    </a>
</div>

{{-- Table of Contents --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 text-sm">📑</span>
        Daftar Isi
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <a href="#login" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">1</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Login ke Sistem</span>
        </a>
        <a href="#dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">2</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Dashboard</span>
        </a>
        <a href="#talents" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">3</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Candidate Database (Talent)</span>
        </a>
        <a href="#client-needs" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">4</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Pengajuan Tenaga Ahli</span>
        </a>
        <a href="#request-hub" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">5</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Request Hub (Pengajuan Kandidat, Extend, Off-boarding)</span>
        </a>
        <a href="#operations" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">6</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Operations</span>
        </a>
        <a href="#candidates" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">7</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Candidates (Data Calon Peserta)</span>
        </a>
        <a href="#bpjs" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">8</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">BPJS Ketenagakerjaan, F2A & Kecelakaan Kerja</span>
        </a>
        <a href="#violations" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">9</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Laporan Pelanggaran</span>
        </a>
        <a href="#suspensions" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">10</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Pemberhentian Sementara</span>
        </a>
        <a href="#messages" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">11</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Kotak Pesan & Notifikasi</span>
        </a>
        <a href="#security" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50/50 transition group">
            <span class="w-7 h-7 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:text-red-600">12</span>
            <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Log Aktivitas & Keamanan Akun</span>
        </a>
    </div>
</div>

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
            <li>Masukkan <strong>Email</strong> dan <strong>Password</strong> akun HR perusahaan Anda.</li>
            <li>Klik tombol <strong>"Login"</strong>.</li>
            <li>Sistem akan otomatis mengarahkan Anda ke <strong>Dashboard</strong> HR perusahaan.</li>
            <li>Jika login gagal, periksa kembali email & password. Setelah 5x percobaan gagal, akun akan di-lock sementara.</li>
        </ol>
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <p class="text-sm text-amber-800"><strong>💡 Catatan:</strong> Akun dan credential login Anda diberikan oleh Super Admin Rekrutmen (Gyokai). Jika Anda lupa password, hubungi Super Admin untuk reset.</p>
        </div>
    </div>

    {{-- 2. Dashboard --}}
    <div id="dashboard" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">2</span>
            <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Halaman utama setelah login. Menampilkan ringkasan data peserta perusahaan Anda.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📊 Informasi yang ditampilkan:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Total Peserta Aktif di perusahaan Anda</li>
            <li>Jumlah Kontrak yang akan segera habis (perlu diperpanjang)</li>
            <li>Daftar peserta dengan kontrak mendekati expired</li>
            <li>Ringkasan request/pengajuan terbaru</li>
            <li>Tombol quick action: Extend Contract, Off-boarding</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Login → otomatis masuk ke Dashboard.</li>
            <li>Pantau peserta yang kontraknya segera habis.</li>
            <li>Gunakan tombol <strong>"Perpanjang"</strong> atau <strong>"Off-boarding"</strong> untuk aksi cepat.</li>
        </ol>
    </div>

    {{-- 3. Candidate Database --}}
    <div id="talents" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">3</span>
            <h2 class="text-xl font-bold text-gray-800">Candidate Database (Talent)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Lihat database tenaga ahli/berpengalaman yang tersedia di sistem Gyokai Connect.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat & cari talent</strong> — Cari berdasarkan nama, skill, pengalaman, lokasi.</li>
            <li><strong>Lihat detail talent</strong> — Profil lengkap, riwayat pengalaman, skill.</li>
            <li><strong>Ajukan Pemanggilan (Call Request)</strong> — Kirim permintaan ke Super Admin untuk memanggil talent yang sesuai.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Call Request:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Candidate Database"</strong> di sidebar.</li>
            <li>Cari dan temukan talent yang sesuai kebutuhan.</li>
            <li>Klik pada talent → Lihat detail profil.</li>
            <li>Klik <strong>"Ajukan Pemanggilan"</strong>.</li>
            <li>Super Admin akan menerima notifikasi dan memproses permintaan Anda.</li>
        </ol>
    </div>

    {{-- 4. Pengajuan Tenaga Ahli --}}
    <div id="client-needs" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">4</span>
            <h2 class="text-xl font-bold text-gray-800">Pengajuan Tenaga Ahli</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Ajukan kebutuhan tenaga ahli/berpengalaman kepada Super Admin (Gyokai).</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur Pengajuan:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Pengajuan Tenaga Ahli"</strong> di sidebar.</li>
            <li>Isi form kebutuhan: Jabatan/Posisi yang dibutuhkan, Jumlah (Kuota), Tingkat Urgensi, Deadline, Deskripsi kebutuhan, Skill yang diperlukan.</li>
            <li>Submit pengajuan.</li>
            <li>Super Admin menerima notifikasi dan menjalankan <strong>Match Engine</strong> di Talent DB.</li>
            <li>Hasil matching akan dikirimkan kepada Anda.</li>
        </ol>
    </div>

    {{-- 5. Request Hub --}}
    <div id="request-hub" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">5</span>
            <h2 class="text-xl font-bold text-gray-800">Request Hub</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Pusat pengelolaan semua jenis pengajuan/request Anda. Termasuk Pengajuan Kandidat, Extend Contract, dan Off-boarding.</p>

        <div class="space-y-4">
            <div class="border border-gray-100 rounded-xl p-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-2">📌 Pengajuan Kandidat (Add Contract)</h4>
                <p class="text-sm text-gray-600 mb-2">Review kandidat baru yang diajukan oleh Super Admin untuk ditempatkan di perusahaan Anda.</p>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
                    <li>Klik sub-menu <strong>"Pengajuan Kandidat"</strong>.</li>
                    <li>Lihat daftar batch pengajuan yang masuk.</li>
                    <li>Klik pada batch → Review data masing-masing kandidat.</li>
                    <li>Pilih kandidat yang Anda terima atau tolak.</li>
                    <li>Submit hasil seleksi → Super Admin akan menerima hasilnya.</li>
                </ol>
            </div>

            <div class="border border-gray-100 rounded-xl p-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-2">🔄 Extend Contract (Perpanjangan Kontrak)</h4>
                <p class="text-sm text-gray-600 mb-2">Ajukan perpanjangan kontrak untuk peserta yang kontraknya akan segera habis.</p>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
                    <li>Klik sub-menu <strong>"Extend Contract"</strong>.</li>
                    <li>Pilih peserta yang kontraknya ingin diperpanjang (bisa multi-select).</li>
                    <li>Isi tanggal perpanjangan kontrak baru.</li>
                    <li>Submit → Menunggu persetujuan Super Admin.</li>
                </ol>
            </div>

            <div class="border border-gray-100 rounded-xl p-4">
                <h4 class="font-semibold text-gray-800 text-sm mb-2">🚪 Off-boarding (Selesai Kontrak)</h4>
                <p class="text-sm text-gray-600 mb-2">Ajukan off-boarding untuk peserta yang kontraknya telah selesai atau perlu dihentikan.</p>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
                    <li>Klik sub-menu <strong>"Off-boarding"</strong>.</li>
                    <li>Pilih peserta yang akan di-offboard (bisa multi-select).</li>
                    <li>Isi alasan dan catatan off-boarding.</li>
                    <li>Submit → Menunggu persetujuan Super Admin.</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- 6. Operations --}}
    <div id="operations" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">6</span>
            <h2 class="text-xl font-bold text-gray-800">Operations</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Lihat semua operasi dan request yang sedang berjalan atau telah selesai.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Operations"</strong> di sidebar.</li>
            <li>Lihat semua request beserta statusnya (Draft, Submitted, Approved, Rejected, Completed).</li>
            <li>Klik pada request untuk melihat detail dan tracking progress.</li>
        </ol>
    </div>

    {{-- 7. Candidates --}}
    <div id="candidates" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">7</span>
            <h2 class="text-xl font-bold text-gray-800">Candidates (Data Calon Peserta)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kelola data calon peserta/kandidat milik perusahaan Anda.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur yang tersedia:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Lihat semua kandidat</strong> — Filter berdasarkan status rekrutmen.</li>
            <li><strong>Tambah kandidat manual</strong> — Input data kandidat satu per satu.</li>
            <li><strong>Import kandidat via Excel</strong> — Upload file Excel untuk menambahkan banyak kandidat.</li>
            <li><strong>Download template</strong> — Unduh file template import.</li>
            <li><strong>Hapus kandidat</strong> — Menghapus data kandidat.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Import Kandidat:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Candidates"</strong> (jika ada di halaman dashboard, gunakan link terkait).</li>
            <li>Klik <strong>"Import Kandidat"</strong>.</li>
            <li>Download template Excel terlebih dahulu.</li>
            <li>Isi data kandidat sesuai format template.</li>
            <li>Upload file → Preview → <strong>Confirm Import</strong>.</li>
        </ol>
    </div>

    {{-- 8. BPJS --}}
    <div id="bpjs" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">8</span>
            <h2 class="text-xl font-bold text-gray-800">BPJS Ketenagakerjaan, Pelaporan F2A & Kecelakaan Kerja</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Modul BPJS Ketenagakerjaan khusus perusahaan Anda.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Sub-fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-2 ml-2">
            <li><strong>BPJS Ketenagakerjaan</strong> — Lihat status kepesertaan BPJS semua peserta di perusahaan Anda.</li>
            <li><strong>Lihat Rate Settings</strong> — Lihat tarif/rate iuran BPJS yang berlaku untuk perusahaan Anda (diatur oleh Super Admin).</li>
            <li><strong>Pelaporan F2A</strong> — Generate Formulir 2a PU BPJS Ketenagakerjaan per periode. Export ke Excel atau Word.</li>
            <li><strong>Uang Saku</strong> — Input atau import data uang saku peserta per bulan sebagai dasar perhitungan F2A.</li>
            <li><strong>Laporan Kecelakaan Kerja</strong> — Lihat dan kelola laporan kecelakaan kerja peserta di perusahaan Anda.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Pelaporan F2A:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Pelaporan F2A"</strong> (di bawah BPJS Ketenagakerjaan).</li>
            <li>Pilih <strong>Bulan</strong> dan <strong>Tahun</strong> periode laporan.</li>
            <li>Klik <strong>"Tampilkan"</strong>.</li>
            <li>Sistem menampilkan tabel rincian iuran tenaga kerja.</li>
            <li>Klik <strong>"Export Excel"</strong> atau <strong>"Export Word"</strong> untuk download.</li>
        </ol>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Input Uang Saku:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Di halaman F2A, klik <strong>"Input Uang Saku"</strong>.</li>
            <li>Pilih peserta → Masukkan jumlah uang saku untuk bulan tersebut.</li>
            <li>Atau gunakan <strong>"Import via Excel"</strong> untuk input massal.</li>
            <li>Data uang saku akan otomatis digunakan dalam perhitungan F2A.</li>
        </ol>
    </div>

    {{-- 9. Violations --}}
    <div id="violations" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">9</span>
            <h2 class="text-xl font-bold text-gray-800">Laporan Pelanggaran</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Laporkan pelanggaran yang dilakukan oleh peserta di perusahaan Anda.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur Membuat Laporan:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Laporan Pelanggaran"</strong> di sidebar.</li>
            <li>Klik <strong>"Buat Laporan Baru"</strong> atau pilih peserta dari Dashboard.</li>
            <li>Pilih peserta yang melanggar.</li>
            <li>Isi detail pelanggaran: Jenis, Tanggal, Deskripsi, Bukti lampiran.</li>
            <li>Submit laporan → Masuk ke Super Admin dengan status <em>"Pending Review"</em>.</li>
            <li>Tunggu keputusan dari Super Admin (notifikasi otomatis).</li>
        </ol>
    </div>

    {{-- 10. Suspensions --}}
    <div id="suspensions" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">10</span>
            <h2 class="text-xl font-bold text-gray-800">Pemberhentian Sementara</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Ajukan pemberhentian sementara (suspension) untuk peserta yang bermasalah.</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">📋 Alur:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Pemberhentian Sementara"</strong> di sidebar.</li>
            <li>Klik <strong>"Buat Pengajuan Baru"</strong>.</li>
            <li>Pilih peserta yang akan diberhentikan sementara.</li>
            <li>Isi alasan, durasi, dan bukti pendukung.</li>
            <li>Submit → Menunggu approval dari Super Admin.</li>
            <li>Jika disetujui, status peserta otomatis berubah menjadi <em>"Suspended"</em>.</li>
        </ol>
    </div>

    {{-- 11. Messages --}}
    <div id="messages" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">11</span>
            <h2 class="text-xl font-bold text-gray-800">Kotak Pesan & Notifikasi</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Kirim dan terima pesan dari/ke Super Admin (Gyokai).</p>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Kirim pesan</strong> — Kirim pertanyaan, permintaan, atau laporan ke Super Admin.</li>
            <li><strong>Balas pesan</strong> — Balas pesan yang diterima dari Super Admin.</li>
            <li><strong>Jenis pesan</strong> — Request Access, Permintaan Kuota, Masalah Akun, Laporan Performa, Umum.</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Kirim Pesan:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Kotak Pesan & Notif"</strong> di sidebar.</li>
            <li>Klik <strong>"Tulis Pesan Baru"</strong>.</li>
            <li>Pilih kategori pesan.</li>
            <li>Tulis judul dan isi pesan.</li>
            <li>Klik <strong>"Kirim"</strong>.</li>
        </ol>
    </div>

    {{-- 12. Security --}}
    <div id="security" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-sm font-bold">12</span>
            <h2 class="text-xl font-bold text-gray-800">Log Aktivitas & Keamanan Akun</h2>
        </div>
        <h3 class="text-sm font-bold text-gray-700 mb-2">🔧 Fitur:</h3>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li><strong>Log Aktivitas Saya</strong> — Lihat semua aktivitas yang Anda lakukan di sistem: login, edit data, submit request, dll.</li>
            <li><strong>Ubah Password</strong> — Ganti password akun HR Anda. Gunakan password yang kuat (minimal 8 karakter).</li>
        </ul>
        <h3 class="text-sm font-bold text-gray-700 mb-2 mt-4">📋 Alur Ubah Password:</h3>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1 ml-2">
            <li>Klik menu <strong>"Ubah Password"</strong> di sidebar.</li>
            <li>Masukkan <strong>Password Lama</strong>.</li>
            <li>Masukkan <strong>Password Baru</strong> (minimal 8 karakter).</li>
            <li>Konfirmasi password baru.</li>
            <li>Klik <strong>"Update Password"</strong>.</li>
        </ol>
    </div>

</div>

{{-- Footer --}}
<div class="mt-8 mb-6 text-center">
    <p class="text-gray-400 text-xs">Gyokai Connect — Panduan Penggunaan HR Perusahaan • Versi 1.0 • {{ now()->format('d M Y') }}</p>
</div>
@endsection
