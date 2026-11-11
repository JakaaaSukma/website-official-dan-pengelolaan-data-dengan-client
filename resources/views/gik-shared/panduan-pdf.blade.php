<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Penggunaan Gyokai Connect — {{ $roleTitle }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; line-height: 1.6; background: #fff; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 30px; }

        /* Cover Page */
        .cover { text-align: center; padding: 80px 20px; page-break-after: always; min-height: 90vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .cover .logo { width: 100px; height: 100px; margin-bottom: 30px; background: linear-gradient(135deg, #DC2626, #991B1B); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 800; }
        .cover h1 { font-size: 32px; font-weight: 800; color: #991B1B; margin-bottom: 10px; }
        .cover h2 { font-size: 20px; font-weight: 600; color: #6b7280; margin-bottom: 30px; }
        .cover .meta { font-size: 14px; color: #9ca3af; }
        .cover .role-badge { display: inline-block; background: #FEE2E2; color: #991B1B; padding: 8px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; margin-bottom: 20px; }

        /* TOC */
        .toc { page-break-after: always; }
        .toc h2 { font-size: 22px; font-weight: 800; color: #991B1B; margin-bottom: 20px; border-bottom: 3px solid #FEE2E2; padding-bottom: 10px; }
        .toc-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .toc-num { width: 30px; height: 30px; background: #FEE2E2; color: #991B1B; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .toc-text { font-size: 14px; color: #374151; font-weight: 500; }

        /* Section */
        .section { margin-bottom: 30px; page-break-inside: avoid; }
        .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #FEE2E2; }
        .section-num { width: 36px; height: 36px; background: linear-gradient(135deg, #DC2626, #991B1B); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .section-title { font-size: 20px; font-weight: 700; color: #1f2937; }
        .section p { font-size: 14px; color: #4b5563; margin-bottom: 12px; }
        .section h3 { font-size: 15px; font-weight: 700; color: #374151; margin: 16px 0 8px; }
        .section ul, .section ol { margin-left: 20px; margin-bottom: 12px; }
        .section li { font-size: 14px; color: #4b5563; margin-bottom: 6px; }
        .section li strong { color: #1f2937; }

        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
        .info-box code { background: #FEE2E2; color: #991B1B; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .warning-box { background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 14px; margin-top: 12px; }
        .warning-box p { font-size: 13px; color: #92400E; margin: 0; }

        .sub-section { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
        .sub-section h4 { font-size: 14px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .sub-section p { font-size: 13px; margin-bottom: 8px; }
        .sub-section ol { margin-left: 16px; }
        .sub-section li { font-size: 13px; }

        /* Footer */
        .page-footer { text-align: center; padding: 30px 0; border-top: 1px solid #e5e7eb; margin-top: 40px; }
        .page-footer p { font-size: 12px; color: #9ca3af; }

        /* Print Styles */
        @media print {
            body { background: white; }
            .container { padding: 20px; }
            .section { page-break-inside: avoid; }
            .cover { page-break-after: always; }
            .toc { page-break-after: always; }
        }
    </style>
</head>
<body>

<div class="container">

    {{-- Cover Page --}}
    <div class="cover">
        <div class="logo">GC</div>
        <h1>Panduan Penggunaan</h1>
        <h2>Gyokai Connect</h2>
        <div class="role-badge">{{ $roleTitle }}</div>
        <div class="meta">
            <p>Versi 1.0 • {{ now()->format('d F Y') }}</p>
            <p style="margin-top: 5px;">PT Gyokai Indonesia Kompeten</p>
        </div>
        <div style="margin-top: 40px; font-size: 13px; color: #6b7280;">
            <p>Dokumen ini berisi panduan lengkap penggunaan setiap fitur</p>
            <p>pada sistem Gyokai Connect untuk akun <strong>{{ $roleTitle }}</strong>.</p>
            <p style="margin-top: 15px; font-size: 12px;">💡 Untuk menyimpan sebagai PDF: Tekan <strong>Ctrl+P</strong> (atau <strong>Cmd+P</strong> di Mac) → pilih <strong>"Save as PDF"</strong>.</p>
        </div>
    </div>

    {{-- Table of Contents --}}
    <div class="toc">
        <h2>📑 Daftar Isi</h2>

        <div class="toc-item" style="background: #FEF2F2; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
            <div class="toc-num" style="background: #DC2626; color: white;">🧭</div>
            <div class="toc-text" style="font-weight: 700; color: #991B1B;">Diagram Alur Kerja Terintegrasi (Master Flowchart)</div>
        </div>

        @if($role === 'superadmin')
            @php $tocItems = [
                'Login ke Sistem',
                'Control Center (Dashboard)',
                'Client Account Management',
                'Add New Contract & Pengajuan Kandidat',
                'Talent DB & Matching Engine',
                'Requests & Operations',
                'All Active Participants (Peserta Aktif)',
                'All Company Candidates',
                'Manajemen Off-boarding Peserta',
                'Manajemen Pelanggaran',
                'Pemberhentian Sementara',
                'BPJS Ketenagakerjaan, F2A & Kecelakaan Kerja',
                'Client Inbox & Messaging',
                'Log Aktivitas & Keamanan Akun',
            ]; @endphp
        @else
            @php $tocItems = [
                'Login ke Sistem',
                'Dashboard',
                'Candidate Database (Talent)',
                'Pengajuan Tenaga Ahli',
                'Pusat Pengajuan (Hub: Review Kandidat, Extend, Off-boarding)',
                'Riwayat & Status Request (Operations)',
                'Candidates (Data Calon Peserta)',
                'BPJS Ketenagakerjaan, F2A & Kecelakaan Kerja',
                'Laporan Pelanggaran',
                'Pemberhentian Sementara',
                'Kotak Pesan & Notifikasi',
                'Log Aktivitas & Keamanan Akun',
            ]; @endphp
        @endif

        @foreach($tocItems as $i => $item)
        <div class="toc-item">
            <div class="toc-num">{{ $i + 1 }}</div>
            <div class="toc-text">{{ $item }}</div>
        </div>
        @endforeach
    </div>

    {{-- ==================== MASTER FLOWCHART ==================== --}}
    <div class="section" style="page-break-after: always;">
        <div class="section-header" style="border-bottom: 3px solid #DC2626;">
            <div class="section-num" style="background: #991B1B;">🧭</div>
            <div class="section-title" style="color: #991B1B;">Diagram Alur Operasional Terintegrasi (Master Flowchart)</div>
        </div>
        <p style="font-size:13px;color:#4b5563;margin-bottom:16px;">
            Peta interaksi terpadu antara <strong>Tim Rekrutmen LPK Gyokai (Super Admin Rekrutmen)</strong> dan <strong>Perusahaan Mitra Industri Jepang & Lokal (HR Company)</strong> dalam siklus kerja Gyokai Connect.
        </p>

        {{-- Swimlane Box Comparison --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
            <thead>
                <tr style="background: #f9fafb;">
                    <th style="width: 50%; padding: 10px; border: 2px solid #FCA5A5; color: #991B1B; text-align: left;">
                        🏛️ Tim Rekrutmen LPK Gyokai (Super Admin)
                    </th>
                    <th style="width: 50%; padding: 10px; border: 2px solid #93C5FD; color: #1E40AF; text-align: left;">
                        🏢 Perusahaan Mitra Industri (HR Company)
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>1. Onboarding Mitra:</strong> Mendaftarkan perusahaan klien di <em>Client Account Mgmt</em> dan mendistribusikan kredensial akses.
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>1. Login & Profil:</strong> Login di portal via tab <em>Company/HR Login</em> dan melengkapi data profil perusahaan.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>2. Pengajuan Kandidat:</strong> Upload / import batch kandidat via Excel di menu <em>Add New Contract</em> dan kirim ke HR Klien.
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>2. Seleksi Kandidat:</strong> Membuka <em>Review Kandidat Baru (Inbox Seleksi)</em>, centang <strong>Lolos / Tidak Lolos</strong> per kandidat, lalu submit hasil.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 8px 12px; background: #ECFDF5; border: 1px solid #A7F3D0; text-align: center; color: #065F46; font-weight: 700;">
                        ⚡ Sistem Otomatis: Kandidat yang lolos seleksi otomatis dibuatkan entri Kontrak Peserta Aktif (hr_contracts) di kedua belah pihak.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>3. Review Extend Kontrak:</strong> Menerima permohonan di <em>Monitoring Request & Operasi</em> $\rightarrow$ Quick Approve atau Review Modal $\rightarrow$ Terbitkan kontrak baru (ID-EXT).
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>3. Pengajuan Extend:</strong> Memantau peserta <em>Expiring Soon</em> $\rightarrow$ masuk menu <em>Extend Contract</em> $\rightarrow$ multi-select peserta & isi tgl kontrak baru.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>4. Manajemen Off-boarding:</strong>
                        <ul style="margin: 4px 0 0 16px; font-size: 11px;">
                            <li><strong>Jalur A:</strong> Review & approve permohonan off-boarding dari klien.</li>
                            <li><strong>Jalur B (Direct):</strong> Eksekusi off-boarding langsung multi-select lintas tenant $\rightarrow$ auto-notify ke HR Klien.</li>
                        </ul>
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>4. Pengajuan Off-boarding:</strong>
                        <p style="margin: 4px 0 0; font-size: 11px;">Membuka <em>Off-boarding Batch</em> $\rightarrow$ pilih peserta selesai masa kontrak/resign $\rightarrow$ tentukan tanggal efektif & alasan $\rightarrow$ kirim request.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>5. Sanksi & Kepatuhan:</strong> Memverifikasi laporan pelanggaran $\rightarrow$ menerbitkan SP1/SP2/SP3 $\rightarrow$ meninjau permohonan suspend $\rightarrow$ memproses klaim BPJS.
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>5. Pelaporan Insiden:</strong> Melaporkan pelanggaran tata tertib, pengajuan suspend peserta mangkir, dan pelaporan iuran F2A / kecelakaan kerja.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #fff5f5;">
                        <strong>6. Matching Engine Talent:</strong> Mencocokkan kriteria permintaan klien dengan database talent SSW/Tokutei Ginou $\rightarrow$ mengirimkan shortlist profil.
                    </td>
                    <td style="padding: 10px; border: 1px solid #e5e7eb; vertical-align: top; background: #eff6ff;">
                        <strong>6. Permintaan Tenaga Ahli:</strong> Mengisi form <em>Pengajuan Tenaga Ahli</em> atau mengirimkan <em>Call Request</em> dari database kandidat.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ==================== SECTIONS ==================== --}}

    {{-- 1. Login --}}
    <div class="section">
        <div class="section-header">
            <div class="section-num">1</div>
            <div class="section-title">Login ke Sistem</div>
        </div>
        <div class="info-box">
            <p style="font-size:14px;color:#4b5563;margin-bottom:8px;"><strong style="color:#1f2937;">URL Login:</strong> <code>{{ $loginUrl }}</code></p>
            <p style="font-size:14px;color:#4b5563;margin:0;"><strong style="color:#1f2937;">Form:</strong> Form "Company / HR Login" di halaman login.</p>
        </div>
        <h3>📋 Alur Login:</h3>
        <ol>
            <li>Buka halaman <strong>{{ $loginUrl }}</strong> di browser.</li>
            <li>Masukkan <strong>Email</strong> dan <strong>Password</strong> akun {{ $roleTitle }}.</li>
            <li>Klik tombol <strong>"Login"</strong>.</li>
            <li>Sistem akan otomatis mengarahkan ke halaman <strong>Dashboard</strong>.</li>
            <li>Jika login gagal, periksa email & password. Setelah 5x percobaan gagal, akun di-lock sementara.</li>
        </ol>
        @if($role === 'hrd')
        <div class="warning-box">
            <p><strong>💡 Catatan:</strong> Akun dan credential login Anda diberikan oleh Super Admin Rekrutmen (Gyokai). Jika lupa password, hubungi Super Admin untuk reset.</p>
        </div>
        @endif
    </div>

    @if($role === 'superadmin')

    {{-- SA: 2. Dashboard --}}
    <div class="section">
        <div class="section-header"><div class="section-num">2</div><div class="section-title">Control Center (Dashboard)</div></div>
        <p>Halaman utama setelah login. Menampilkan ringkasan statistik seluruh tenant/perusahaan.</p>
        <h3>📊 Informasi yang ditampilkan:</h3>
        <ul>
            <li>Total Perusahaan Klien yang terdaftar</li>
            <li>Total Peserta Aktif (semua perusahaan)</li>
            <li>Jumlah Kontrak yang akan segera habis (expiring)</li>
            <li>Pesan masuk terbaru dari perusahaan klien</li>
            <li>Request/pengajuan terbaru yang perlu di-review</li>
        </ul>
    </div>

    {{-- SA: 3. Client Account Management --}}
    <div class="section">
        <div class="section-header"><div class="section-num">3</div><div class="section-title">Client Account Management</div></div>
        <p>Kelola semua akun perusahaan klien yang terdaftar di sistem.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Lihat daftar perusahaan</strong> — Semua perusahaan klien beserta jumlah peserta aktif.</li>
            <li><strong>Tambah perusahaan baru</strong> — Mendaftarkan perusahaan klien baru beserta akun HR-nya.</li>
            <li><strong>Edit data perusahaan</strong> — Mengubah informasi perusahaan.</li>
            <li><strong>Hapus perusahaan</strong> — Menonaktifkan atau menghapus perusahaan klien.</li>
        </ul>
        <h3>📋 Alur Menambah Perusahaan Baru:</h3>
        <ol>
            <li>Klik menu <strong>"Client Account Mgmt"</strong>.</li>
            <li>Klik tombol <strong>"Tambah Perusahaan"</strong>.</li>
            <li>Isi data: Nama Perusahaan, Alamat, Email PIC, No. Telepon.</li>
            <li>Sistem otomatis membuat akun HR Company.</li>
            <li>Bagikan credential login kepada PIC perusahaan.</li>
        </ol>
    </div>

    {{-- SA: 4. Add New Contract --}}
    <div class="section">
        <div class="section-header"><div class="section-num">4</div><div class="section-title">Add New Contract & Pengajuan Kandidat</div></div>
        <p>Buat pengajuan kontrak baru untuk kandidat yang akan ditempatkan di perusahaan klien.</p>
        <h3>📋 Alur Add New Contract:</h3>
        <ol>
            <li>Klik menu <strong>"Add New Contract"</strong>.</li>
            <li>Pilih <strong>perusahaan penempatan</strong>.</li>
            <li>Isi data kandidat: Nama, NIK, Posisi, Tanggal Kontrak, Gaji, dll.</li>
            <li>Submit → Masuk ke <strong>Daftar Pengajuan Kandidat</strong>.</li>
            <li>HR Perusahaan akan mereview dan menyetujui/menolak.</li>
        </ol>
    </div>

    {{-- SA: 5. Talent DB --}}
    <div class="section">
        <div class="section-header"><div class="section-num">5</div><div class="section-title">Talent DB & Matching Engine</div></div>
        <p>Database tenaga ahli/berpengalaman yang siap dipanggil oleh perusahaan klien.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Lihat & cari talent</strong> — Cari berdasarkan nama, skill, pengalaman.</li>
            <li><strong>Tambah talent manual</strong> — Input data talent satu per satu.</li>
            <li><strong>Import talent via Excel</strong> — Upload file Excel untuk import massal.</li>
            <li><strong>Download template Excel</strong> — Unduh template import.</li>
            <li><strong>Match Engine</strong> — Mencocokkan talent sesuai kebutuhan klien.</li>
        </ul>
        <h3>📋 Alur Import Talent via Excel:</h3>
        <ol>
            <li>Klik menu <strong>"Talent DB & Matching"</strong>.</li>
            <li>Klik <strong>"Import Talent"</strong>.</li>
            <li>Download template Excel.</li>
            <li>Isi data talent sesuai format template.</li>
            <li>Upload file → <strong>"Upload & Preview"</strong>.</li>
            <li>Review data valid & invalid.</li>
            <li>Klik <strong>"Confirm Import"</strong>.</li>
        </ol>
    </div>

    {{-- SA: 6. Operations --}}
    <div class="section">
        <div class="section-header"><div class="section-num">6</div><div class="section-title">Requests & Operations</div></div>
        <p>Kelola semua request/pengajuan dari perusahaan klien.</p>
        <h3>🔧 Jenis Request:</h3>
        <ul>
            <li><strong>Extend Contract</strong> — Perpanjangan kontrak.</li>
            <li><strong>Off-boarding</strong> — Selesainya kontrak peserta.</li>
            <li><strong>Add New Contract</strong> — Penambahan peserta baru.</li>
            <li><strong>General Request</strong> — Request umum.</li>
        </ul>
        <h3>📋 Alur Review:</h3>
        <ol>
            <li>Klik <strong>"Requests & Operations"</strong>.</li>
            <li>Lihat daftar request.</li>
            <li>Klik untuk detail → Review → <strong>Approve</strong> atau <strong>Reject</strong>.</li>
        </ol>
    </div>

    {{-- SA: 7. Employees --}}
    <div class="section">
        <div class="section-header"><div class="section-num">7</div><div class="section-title">All Active Participants (Peserta Aktif)</div></div>
        <p>Data seluruh peserta aktif di semua perusahaan klien (cross-tenant).</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Lihat semua peserta</strong> — Filter berdasarkan perusahaan, status.</li>
            <li><strong>Tambah / Import peserta</strong> — Manual atau via Excel.</li>
            <li><strong>Edit & Hapus peserta</strong></li>
        </ul>
        <h3>📋 Alur Import Peserta via Excel:</h3>
        <ol>
            <li>Klik <strong>"All Active Participants"</strong>.</li>
            <li>Klik <strong>"Import Peserta"</strong>.</li>
            <li>Pilih perusahaan penempatan.</li>
            <li>Download template → Isi data → Upload → Preview → <strong>Confirm Import</strong>.</li>
        </ol>
    </div>

    {{-- SA: 8. Candidates --}}
    <div class="section">
        <div class="section-header"><div class="section-num">8</div><div class="section-title">All Company Candidates</div></div>
        <p>Data calon peserta/kandidat dari semua perusahaan klien.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li>Lihat, tambah, import, hapus kandidat.</li>
            <li>Lihat detail kandidat: profil, skill, pengalaman.</li>
        </ul>
    </div>

    {{-- SA: 9. Violations --}}
    <div class="section">
        <div class="section-header"><div class="section-num">9</div><div class="section-title">Manajemen Pelanggaran</div></div>
        <p>Kelola laporan pelanggaran peserta dari HR perusahaan.</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>HR Perusahaan membuat laporan pelanggaran.</li>
            <li>Laporan masuk dengan status <em>"Pending Review"</em>.</li>
            <li>Super Admin mereview dan mengubah status.</li>
        </ol>
    </div>

    {{-- SA: 10. Suspensions --}}
    <div class="section">
        <div class="section-header"><div class="section-num">10</div><div class="section-title">Pemberhentian Sementara</div></div>
        <p>Kelola pengajuan pemberhentian sementara dari HR perusahaan.</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>HR Perusahaan mengajukan pemberhentian sementara.</li>
            <li>Pengajuan masuk dengan status <em>"Pending Approval"</em>.</li>
            <li>Super Admin menyetujui atau menolak.</li>
        </ol>
    </div>

    {{-- SA: 11. BPJS --}}
    <div class="section">
        <div class="section-header"><div class="section-num">11</div><div class="section-title">BPJS Ketenagakerjaan, Pelaporan F2A & Kecelakaan Kerja</div></div>
        <p>Modul lengkap pengelolaan BPJS Ketenagakerjaan untuk semua perusahaan klien.</p>
        <h3>🔧 Sub-fitur:</h3>
        <ul>
            <li><strong>BPJS Ketenagakerjaan</strong> — Status kepesertaan BPJS, ubah status.</li>
            <li><strong>Rate Settings</strong> — Atur tarif iuran per perusahaan.</li>
            <li><strong>Pelaporan F2A</strong> — Generate Formulir 2a PU. Export ke Excel/Word.</li>
            <li><strong>Laporan Kecelakaan Kerja</strong> — Input dan kelola laporan kecelakaan.</li>
        </ul>
        <h3>📋 Alur F2A:</h3>
        <ol>
            <li>Klik <strong>"Pelaporan F2A"</strong>.</li>
            <li>Pilih Perusahaan, Bulan, Tahun → <strong>"Tampilkan"</strong>.</li>
            <li>Export ke <strong>Excel</strong> atau <strong>Word</strong>.</li>
        </ol>
    </div>

    {{-- SA: 12. Inbox --}}
    <div class="section">
        <div class="section-header"><div class="section-num">12</div><div class="section-title">Client Inbox & Messaging</div></div>
        <p>Kelola pesan dan komunikasi dari/untuk perusahaan klien.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li>Lihat, balas, tandai selesai, assign pesan.</li>
        </ul>
    </div>

    {{-- SA: 13. Security --}}
    <div class="section">
        <div class="section-header"><div class="section-num">13</div><div class="section-title">Log Aktivitas & Keamanan Akun</div></div>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Log Aktivitas Sistem</strong> — Semua log aktivitas tercatat otomatis.</li>
            <li><strong>Ubah Password</strong> — Minimal 8 karakter, kombinasi huruf & angka.</li>
        </ul>
    </div>

    @else

    {{-- HRD: 2. Dashboard --}}
    <div class="section">
        <div class="section-header"><div class="section-num">2</div><div class="section-title">Dashboard</div></div>
        <p>Halaman utama setelah login. Menampilkan ringkasan data peserta perusahaan Anda.</p>
        <h3>📊 Informasi:</h3>
        <ul>
            <li>Total Peserta Aktif</li>
            <li>Kontrak yang segera habis</li>
            <li>Tombol quick action: Extend Contract, Off-boarding</li>
        </ul>
    </div>

    {{-- HRD: 3. Talent --}}
    <div class="section">
        <div class="section-header"><div class="section-num">3</div><div class="section-title">Candidate Database (Talent)</div></div>
        <p>Lihat database tenaga ahli/berpengalaman di Gyokai Connect.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Lihat & cari talent</strong></li>
            <li><strong>Ajukan Pemanggilan (Call Request)</strong> — Kirim permintaan ke Super Admin.</li>
        </ul>
    </div>

    {{-- HRD: 4. Pengajuan Tenaga Ahli --}}
    <div class="section">
        <div class="section-header"><div class="section-num">4</div><div class="section-title">Pengajuan Tenaga Ahli</div></div>
        <p>Ajukan kebutuhan tenaga ahli kepada Super Admin (Gyokai).</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>Klik <strong>"Pengajuan Tenaga Ahli"</strong>.</li>
            <li>Isi form: Jabatan, Jumlah, Urgensi, Deadline, Skill.</li>
            <li>Submit → Super Admin menjalankan Match Engine.</li>
        </ol>
    </div>

    {{-- HRD: 5. Request Hub --}}
    <div class="section">
        <div class="section-header"><div class="section-num">5</div><div class="section-title">Request Hub</div></div>
        <p>Pusat pengelolaan semua jenis pengajuan/request Anda.</p>
        <div class="sub-section">
            <h4>📌 Pengajuan Kandidat</h4>
            <p>Review kandidat baru dari Super Admin.</p>
            <ol>
                <li>Klik <strong>"Pengajuan Kandidat"</strong>.</li>
                <li>Review data kandidat → Terima atau Tolak → Submit.</li>
            </ol>
        </div>
        <div class="sub-section">
            <h4>🔄 Extend Contract</h4>
            <p>Ajukan perpanjangan kontrak peserta.</p>
            <ol>
                <li>Klik <strong>"Extend Contract"</strong>.</li>
                <li>Pilih peserta → Isi tanggal perpanjangan → Submit.</li>
            </ol>
        </div>
        <div class="sub-section">
            <h4>🚪 Off-boarding</h4>
            <p>Ajukan off-boarding peserta.</p>
            <ol>
                <li>Klik <strong>"Off-boarding"</strong>.</li>
                <li>Pilih peserta → Isi alasan → Submit.</li>
            </ol>
        </div>
    </div>

    {{-- HRD: 6. Operations --}}
    <div class="section">
        <div class="section-header"><div class="section-num">6</div><div class="section-title">Operations</div></div>
        <p>Lihat semua operasi dan request beserta statusnya.</p>
    </div>

    {{-- HRD: 7. Candidates --}}
    <div class="section">
        <div class="section-header"><div class="section-num">7</div><div class="section-title">Candidates (Data Calon Peserta)</div></div>
        <p>Kelola data calon peserta milik perusahaan Anda.</p>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li>Lihat, tambah manual, import via Excel, hapus kandidat.</li>
        </ul>
        <h3>📋 Alur Import:</h3>
        <ol>
            <li>Download template → Isi data → Upload → Preview → <strong>Confirm Import</strong>.</li>
        </ol>
    </div>

    {{-- HRD: 8. BPJS --}}
    <div class="section">
        <div class="section-header"><div class="section-num">8</div><div class="section-title">BPJS Ketenagakerjaan, Pelaporan F2A & Kecelakaan Kerja</div></div>
        <h3>🔧 Sub-fitur:</h3>
        <ul>
            <li><strong>BPJS Ketenagakerjaan</strong> — Status kepesertaan BPJS peserta.</li>
            <li><strong>Rate Settings</strong> — Lihat tarif iuran berlaku.</li>
            <li><strong>Pelaporan F2A</strong> — Generate Formulir 2a PU. Export ke Excel/Word.</li>
            <li><strong>Uang Saku</strong> — Input/import data uang saku peserta.</li>
            <li><strong>Laporan Kecelakaan Kerja</strong></li>
        </ul>
        <h3>📋 Alur F2A:</h3>
        <ol>
            <li>Pilih Bulan & Tahun → <strong>"Tampilkan"</strong> → Export.</li>
        </ol>
        <h3>📋 Alur Input Uang Saku:</h3>
        <ol>
            <li>Di halaman F2A, klik <strong>"Input Uang Saku"</strong>.</li>
            <li>Pilih peserta → Masukkan jumlah, atau import via Excel.</li>
        </ol>
    </div>

    {{-- HRD: 9. Violations --}}
    <div class="section">
        <div class="section-header"><div class="section-num">9</div><div class="section-title">Laporan Pelanggaran</div></div>
        <p>Laporkan pelanggaran peserta di perusahaan Anda.</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>Klik <strong>"Laporan Pelanggaran"</strong> → <strong>"Buat Laporan Baru"</strong>.</li>
            <li>Pilih peserta, isi detail dan bukti → Submit.</li>
            <li>Tunggu keputusan dari Super Admin.</li>
        </ol>
    </div>

    {{-- HRD: 10. Suspensions --}}
    <div class="section">
        <div class="section-header"><div class="section-num">10</div><div class="section-title">Pemberhentian Sementara</div></div>
        <p>Ajukan pemberhentian sementara (suspension) peserta.</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>Klik <strong>"Pemberhentian Sementara"</strong> → <strong>"Buat Pengajuan Baru"</strong>.</li>
            <li>Pilih peserta, isi alasan dan bukti → Submit.</li>
            <li>Tunggu approval dari Super Admin.</li>
        </ol>
    </div>

    {{-- HRD: 11. Messages --}}
    <div class="section">
        <div class="section-header"><div class="section-num">11</div><div class="section-title">Kotak Pesan & Notifikasi</div></div>
        <p>Kirim dan terima pesan dari/ke Super Admin (Gyokai).</p>
        <h3>📋 Alur:</h3>
        <ol>
            <li>Klik <strong>"Kotak Pesan & Notif"</strong>.</li>
            <li>Tulis Pesan Baru → Pilih kategori → Tulis isi pesan → Kirim.</li>
        </ol>
    </div>

    {{-- HRD: 12. Security --}}
    <div class="section">
        <div class="section-header"><div class="section-num">12</div><div class="section-title">Log Aktivitas & Keamanan Akun</div></div>
        <h3>🔧 Fitur:</h3>
        <ul>
            <li><strong>Log Aktivitas Saya</strong> — Semua aktivitas tercatat otomatis.</li>
            <li><strong>Ubah Password</strong> — Minimal 8 karakter.</li>
        </ul>
    </div>

    @endif

    {{-- Footer --}}
    <div class="page-footer">
        <p>Gyokai Connect — Panduan Penggunaan {{ $roleTitle }} • Versi 1.0 • {{ now()->format('d F Y') }}</p>
        <p style="margin-top: 5px;">© {{ now()->year }} PT Gyokai Indonesia Kompeten. All rights reserved.</p>
    </div>

</div>

</body>
</html>
