# LPK Gyokai Indonesia Kompeten — Integrated Web Portal & HR Talent Ecosystem

<p align="center">
  <img src="public/logo/Gyokai.png" alt="LPK Gyokai Logo" width="160">
</p>

<p align="center">
  <strong>Enterprise-Grade Web Application & Multi-Role Management System</strong><br>
  Built with <strong>Laravel 10</strong>, <strong>Tailwind CSS</strong>, <strong>MySQL</strong>, and <strong>Automated Service Integrations</strong>.
</p>

<p align="center">
  <a href="#-ringkasan-proyek-executive-summary">Ringkasan Proyek</a> •
  <a href="#-fitur-utama--solusi-bisnis">Fitur & Solusi Bisnis</a> •
  <a href="#-arsitektur-dan-role-permission">Arsitektur & RBAC</a> •
  <a href="#-teknologi-yang-digunakan">Teknologi</a> •
  <a href="#-keamanan--best-practices">Keamanan</a> •
  <a href="#-cara-instalasi--menjalankan-proyek">Cara Instalasi</a>
</p>

---

## 📌 Ringkasan Proyek (Executive Summary)

**Gyokai02** adalah platform digital monolitik terpadu yang dikembangkan untuk **LPK Gyokai Indonesia Kompeten** (Lembaga Pelatihan Kerja & *Sending Organization* resmi untuk program pemagangan dan tenaga kerja berketerampilan spesifik / SSW ke Jepang).

Sistem ini tidak hanya berfungsi sebagai **Website Profil Publik & Rekrutmen Online**, melainkan juga bertindak sebagai **Sistem ERP/Portal Multi-Role Internal** yang mengintegrasikan pengelolaan rekrutmen kandidat (*Talent Matching*), portal mitra industri perusahaan (*Company Portal*), hingga otomatisasi distribusi slip gaji karyawan via *Multi-SMTP Mailer Pool*.

> **Target Pengguna & Stakeholder:**
> 1. **HR / Recruiter & Superadmin:** Mengelola pencocokan kandidat dengan kebutuhan klien, riwayat kontrak kerja, evaluasi BPJS Ketenagakerjaan, dan rekrutmen pelamar.
> 2. **Perusahaan Mitra (Company):** Memantau perkembangan peserta magang/tenaga kerja, data verifikasi, dan broadcast koordinasi.
> 3. **Divisi Finance:** Distribusi massal slip gaji melalui parsing PDF otomatis dan algoritma rotasi SMTP failover.
> 4. **Pelamar & Publik:** Mengakses lowongan kerja, legalitas resmi LPK, pengajuan CV digital, dan profil layanan.

---

## 🚀 Fitur Utama & Solusi Bisnis

### 1. 🎯 HR Talent Matching Engine & Contract Flow (`/gik-superadmin/*`)
* **Automated Candidate-to-Client Matching:** Algoritma pencocokan kandidat berdasarkan keahlian (*skills*), sertifikasi bahasa, pengalaman, dan kriteria spesifik industri Jepang.
* **Batch Contract Workflow:** Manajemen siklus hidup kontrak magang (pra-keberangkatan, kontrak aktif, evaluasi, hingga pasca-penempatan).
* **BPJS Ketenagakerjaan & Accident Compliance:** Modul kalkulasi iuran, pemantauan status jaminan sosial, dan penanganan pelaporan kecelakaan kerja terintegrasi.
* **Audit Trail & Logging:** Riwayat perubahan data kandidat dan kontrak terdokumentasi untuk transparansi administratif.

### 2. 🏢 Mitra Industri & Company Portal (`/company/*`)
* **Dedicated Company Dashboard:** Antarmuka aman bagi perwakilan perusahaan mitra untuk memantau data peserta magang yang ditempatkan.
* **Real-time Member Synchronization:** Integrasi data dua arah untuk memastikan akurasi data peserta pelatihan dan tenaga kerja aktif.
* **Automated WhatsApp Notification:** Terintegrasi dengan microservice WhatsApp (Baileys) untuk pengiriman pembaruan status dan pesan otomatis.

### 3. 💼 Finance & Automated Payroll Dispatch (`/finance/*`)
* **Batch PDF Payslip Parser:** Otomatis memecah dan membaca data slip gaji dari dokumen master PDF (*Smalot PdfParser*).
* **Multi-SMTP Mailer Failover Pool:** Menangani limit pengiriman email harian (misal limit Gmail) dengan rotasi otomatis ke mailer cadangan secara cerdas tanpa interupsi.
* **Live Progress & Emergency Abort:** Indikator progress bar pengiriman real-time dan opsi pembatalan instan saat proses dispatch berlangsung.

### 4. 🌐 Platform Publik & Karir Pelamar (`/`, `/blog`, `/upload`)
* **Responsive Modern UI:** Desain antarmuka profesional berbasis Tailwind CSS, tipografi modern (*Plus Jakarta Sans* & *Inter*), serta dukungan SEO menyeluruh (meta tags, Google AdSense, dan Google Analytics).
* **Dynamic Content Management (SiteAsset Engine):** Mengubah banner dan aset visual landing page secara langsung via panel admin tanpa perlu modifikasi kode template Blade.
* **Digital Application Form:** Formulir lamaran kerja online dan upload resume terenkripsi bagi calon peserta magang/kandidat tenaga kerja.

---

## 🔐 Arsitektur dan Role-Based Access Control (RBAC)

Aplikasi menerapkan pemisahan hak akses berbasis peran yang ketat (*Strict RBAC*) melalui Laravel Middleware:

| Role | Endpoint Prefix | Akses & Wewenang |
|---|---|---|
| **`superadmin`** | `/gik-superadmin/*` | Kontrol penuh sistem HR, *talent matching*, audit trail, dan kepatuhan kontrak. |
| **`admin`** | `/admin/*` | Pengelolaan konten website (Berita, Lowongan Kerja, Fasilitas, Banner `SiteAsset`, Legalitas). |
| **`finance`** | `/finance/*` | Manajemen slip gaji, batch parsing PDF, konfigurasi Multi-SMTP mailer, dan laporan payroll. |
| **`company`** | `/company/*` | Dashboard mitra industri, monitoring peserta magang, dan pelaporan status. |
| **`user` / Publik** | `/`, `/upload`, `/blog` | Portal publik informasi LPK, lowongan kerja, dan submit formulir pendaftaran. |

---

## 🛠 Teknologi yang Digunakan

### Backend & Core
* **Framework:** [Laravel 10.x](https://laravel.com/) (PHP 8.2+)
* **Database Engine:** MySQL / MariaDB (Normalized Relational Schema, Eloquent ORM, Foreign Keys, Indexing)
* **Architecture:** MVC (Model-View-Controller) dengan Service & Repository Pattern
* **Document Processing:** Smalot PDF Parser & Python Data Extractor
* **Integration Services:** Multi-SMTP Mailer, Baileys Node.js WhatsApp Engine, Google Drive API

### Frontend & UI/UX
* **Engine:** Laravel Blade Templating Engine
* **CSS Framework:** Tailwind CSS (Custom Color System & Token Identitas Gyokai `#af101a`)
* **Interactivity:** Alpine.js & Modern Vanilla JavaScript
* **Iconography & Typography:** Google Material Symbols Outlined, Google Fonts (Plus Jakarta Sans & Inter)

### Quality Assurance & Testing
* **Testing Framework:** PHPUnit (Feature & Unit Tests)
* **Coverage Focus:** Role-based access authorization, BPJS calculation integrity, dan session handling.

---

## 🛡 Keamanan & Best Practices

1. **Proteksi Kredensial:** Seluruh kredensial sensitif diisolasi via environment variables (`.env`) dan tidak dilacak pada *version control*.
2. **Database Encryption:** Penggunaan fitur *Encrypted Eloquent Casts* pada kolom sensitif (seperti kredensial SMTP dan data identitas penting).
3. **Content Security Policy (CSP):** Middleware kustom [SecurityHeaders.php](file:///Applications/XAMPP/xamppfiles/htdocs/Gyokai02/app/Http/Middleware/SecurityHeaders.php) untuk mencegah serangan XSS, Clickjacking (`SAMEORIGIN`), dan MIME sniffing.
4. **Rate Limiting & Anti-Brute Force:** Proteksi throttle pada endpoint otentikasi dan login portal internal.
5. **CSRF & Session Hijacking Prevention:** Regenerasi ID sesi otomatis setelah login dan token validation pada setiap mutasi data (POST/PUT/DELETE).

---

## 💻 Cara Instalasi & Menjalankan Proyek

### Prasyarat
* PHP >= 8.2 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`
* Composer 2.x
* MySQL / MariaDB Server (misal via XAMPP)
* Node.js & NPM (opsional untuk asset bundling / microservice WhatsApp)

### Langkah Instalasi

1. **Clone Repository:**
   ```bash
   git clone https://github.com/USERNAME/Gyokai02.git
   cd Gyokai02
   ```

2. **Install Dependensi Composer:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Konfigurasi Environment:**
   Salin file template `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan koneksi database serta konfigurasi server lokal Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gyokai02
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi & Database Seeder:**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat Symlink Storage Publik:**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban di: `http://localhost:8000`

---

## 📂 Struktur Direktori Utama

```text
├── app/
│   ├── Http/Controllers/     # Controller logika bisnis (HR, Admin, Finance, Company, Publik)
│   ├── Http/Middleware/      # Pengamanan RBAC (EnsureAdmin, EnsureFinance, EnsureCompany, CSP)
│   └── Models/               # Model Eloquent (User, HrTalent, Member, SmtpMailer, SiteAsset, dll)
├── config/                   # Berkas konfigurasi aplikasi & integrasi pihak ketiga
├── database/
│   ├── migrations/           # Skema tabel database relasional
│   └── seeders/              # Seeder data awal & akun dummy per role
├── public/                   # Aset web publik (gambar, logo, CSS, JavaScript)
├── resources/
│   └── views/                # Template Blade (layouts frontend, portal gik-hrd, finance, admin)
├── routes/
│   ├── web.php               # Rute web & pemisahan route group middleware
│   └── console.php           # Perintah command artisan terjadwal
├── storage/                  # Log, cache sistem, dan berkas privat
└── tests/                    # Pengujian otomatis Feature & Unit Test (PHPUnit)
```

---

<p align="center">
  Dikelola & Dikembangkan dengan standar profesional untuk <strong>LPK Gyokai Indonesia Kompeten</strong>.<br>
  <em>Menghubungkan talenta kompeten Indonesia dengan peluang karir internasional di Jepang.</em>
</p>
