{{-- GYOKAI CONNECT COMPREHENSIVE FLOWCHART COMPONENT --}}
{{-- Swimlane & Flow Visual antara Tim Rekrutmen LPK Gyokai (Super Admin) & HR Company Mitra --}}

<div id="flowchart" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8 scroll-mt-6">
    <!-- Header Banner -->
    <div class="px-6 py-6 bg-gradient-to-r from-[#7f1d1d] via-[#991B1B] to-[#b91c1c] text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm text-xs font-extrabold uppercase tracking-widest text-red-100 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    Master Flowchart & Workflow Architecture
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold leading-tight">Diagram Alur Operasional Terintegrasi</h2>
                <p class="text-red-100 text-xs md:text-sm mt-1 max-w-2xl">
                    Peta interaksi lengkap antara <strong>Tim Rekrutmen LPK Gyokai (Super Admin Rekrutmen)</strong> dan <strong>Perusahaan Mitra Industri Jepang & Lokal (HR Company)</strong> dalam ekosistem Gyokai Connect.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl bg-white/20 text-xs font-bold backdrop-blur-sm">v2.4 Production</span>
            </div>
        </div>

        <!-- Role Pill Legend -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-6 pt-5 border-t border-white/20 text-xs">
            <div class="flex items-center gap-3 bg-black/20 rounded-xl px-4 py-2.5 backdrop-blur-sm">
                <div class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">🏛️</div>
                <div>
                    <div class="font-extrabold text-white text-[13px]">Tim Rekrutmen LPK Gyokai</div>
                    <div class="text-red-200 text-[11px]">Super Admin Rekrutmen • Cross-Tenant Control Center</div>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-black/20 rounded-xl px-4 py-2.5 backdrop-blur-sm">
                <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">🏢</div>
                <div>
                    <div class="font-extrabold text-white text-[13px]">Perusahaan Mitra Industri</div>
                    <div class="text-blue-200 text-[11px]">HR Company Klien Jepang & Lokal • Multi-Tenant Client</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills / Tab Filter Alur -->
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2 overflow-x-auto text-xs font-bold">
        <button type="button" onclick="switchFlowTab('all')" id="tab-btn-all" class="flow-tab-btn px-4 py-2 rounded-xl bg-gik-primary text-white shadow-sm transition whitespace-nowrap">
            🌟 Semua Alur (Overview Lengkap)
        </button>
        <button type="button" onclick="switchFlowTab('flow1')" id="tab-btn-flow1" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            1. Onboarding Klien
        </button>
        <button type="button" onclick="switchFlowTab('flow2')" id="tab-btn-flow2" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            2. Add New Contract
        </button>
        <button type="button" onclick="switchFlowTab('flow3')" id="tab-btn-flow3" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            3. Extend Contract
        </button>
        <button type="button" onclick="switchFlowTab('flow4')" id="tab-btn-flow4" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            4. Manajemen Off-boarding
        </button>
        <button type="button" onclick="switchFlowTab('flow5')" id="tab-btn-flow5" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            5. Compliance & BPJS
        </button>
        <button type="button" onclick="switchFlowTab('flow6')" id="tab-btn-flow6" class="flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap">
            6. Talent Matching
        </button>
    </div>

    <!-- Content Area -->
    <div class="p-6 md:p-8 space-y-10">

        <!-- ========================================================================= -->
        <!-- FLOW 1: ONBOARDING & CLIENT ACCOUNT MANAGEMENT -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow1" class="flow-section space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-red-100 text-gik-primary font-extrabold flex items-center justify-center text-sm">1</span>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Alur 1: Pendaftaran Klien & Onboarding Akun HR</h3>
                    <p class="text-xs text-gray-500">Mendaftarkan mitra industri baru hingga akun HR siap digunakan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Step 1.1 Super Admin -->
                <div class="bg-red-50/60 border-2 border-red-200 rounded-2xl p-5 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-red-600 text-white">Langkah 1.1 • Super Admin</span>
                        <span class="text-xs font-mono text-red-700">/hr-superadmin/clients/create</span>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1.5">Input Data Perusahaan Mitra</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Super Admin membuka menu <strong>Client Account Mgmt</strong> $\rightarrow$ klik <strong>+ Tambah Perusahaan</strong>. Mengisi Nama Perusahaan, PIC, Email HR, Telepon, dan kualifikasi badan hukum.
                    </p>
                    <div class="mt-3 pt-3 border-t border-red-200/60 flex items-center justify-between text-[11px] font-bold text-red-800">
                        <span>Aksi: Simpan Akun Klien</span>
                        <span>Output: Client ID Generated</span>
                    </div>
                </div>

                <!-- Step 1.2 System & HR Mitra -->
                <div class="bg-blue-50/60 border-2 border-blue-200 rounded-2xl p-5 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-blue-600 text-white">Langkah 1.2 • HR Company</span>
                        <span class="text-xs font-mono text-blue-700">/admin/login</span>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1.5">Aktivasi & Login Pertama</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Sistem otomatis membuatkan akun user ber-role <code>hrd_company</code> terikat ke <code>company_id</code>. PIC menerima credential resmi, lalu login melalui tab <strong>Company / HR Login</strong> untuk masuk ke Dashboard.
                    </p>
                    <div class="mt-3 pt-3 border-t border-blue-200/60 flex items-center justify-between text-[11px] font-bold text-blue-800">
                        <span>Aksi: Login Akun HRD</span>
                        <span>Status: Tenant Aktif & Terisolasi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FLOW 2: ADD NEW CONTRACT / PENGAJUAN KANDIDAT BARU -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow2" class="flow-section space-y-4 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-green-100 text-green-700 font-extrabold flex items-center justify-center text-sm">2</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Alur 2: Add New Contract (Pengajuan & Seleksi Kandidat Baru)</h3>
                        <p class="text-xs text-gray-500">Pemberian rekomendasi kandidat dari Gyokai hingga peserta aktif di perusahaan mitra.</p>
                    </div>
                </div>
                <span class="hidden md:inline-flex text-[11px] font-bold text-green-800 bg-green-100 px-3 py-1 rounded-full">Core Recruitment Pipeline</span>
            </div>

            <!-- Visual Flow Steps -->
            <div class="space-y-3">
                <!-- Step 2.1: Super Admin Upload Batch -->
                <div class="p-5 bg-white rounded-2xl border-2 border-red-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center font-extrabold shrink-0 mt-0.5">2.1</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold bg-red-600 text-white px-2 py-0.5 rounded">SUPER ADMIN</span>
                                <span class="font-bold text-gray-800 text-sm">Buat Pengajuan Paket Kandidat (Add Contract Batch)</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Super Admin membuka <strong>Add New Contract</strong> (`/hr-superadmin/add-contract/create`) $\rightarrow$ pilih perusahaan tujuan $\rightarrow$ input manual atau import Excel daftar kandidat (Nama, NIK, Tgl Lahir, Jabatan, Gaji, dll) $\rightarrow$ klik <strong>Submit Pengajuan ke HR Klien</strong>.
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-800 text-xs font-bold">Status: Submitted</span>
                    </div>
                </div>

                <!-- Down Arrow Indicator -->
                <div class="flex justify-center my-1 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </div>

                <!-- Step 2.2: HRD Company Review & Selection -->
                <div class="p-5 bg-white rounded-2xl border-2 border-blue-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-extrabold shrink-0 mt-0.5">2.2</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold bg-blue-600 text-white px-2 py-0.5 rounded">HR COMPANY</span>
                                <span class="font-bold text-gray-800 text-sm">Review Kandidat Baru (Inbox Seleksi)</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                HR Perusahaan membuka menu <strong>Review Kandidat Baru</strong> (`/hr-portal/add-contracts`) $\rightarrow$ klik Detail Batch $\rightarrow$ memeriksa biodata dan CV kandidat $\rightarrow$ mencentang radio button <strong>"Lolos Seleksi"</strong> atau <strong>"Tidak Lolos"</strong> per kandidat $\rightarrow$ klik <strong>Kirim Hasil Seleksi</strong>.
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 text-xs font-bold">Aksi: Seleksi & Approval</span>
                    </div>
                </div>

                <!-- Down Arrow Indicator -->
                <div class="flex justify-center my-1 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </div>

                <!-- Step 2.3: Automatic Creation of Active Participants -->
                <div class="p-5 bg-gradient-to-r from-emerald-50 via-teal-50 to-green-50 rounded-2xl border-2 border-emerald-300 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold shrink-0 mt-0.5">2.3</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold bg-emerald-700 text-white px-2 py-0.5 rounded">SISTEM OTOMATIS</span>
                                <span class="font-bold text-emerald-950 text-sm">Peserta Aktif Tercipta Otomatis (Real-Time Synchronized)</span>
                            </div>
                            <p class="text-xs text-emerald-800 mt-1">
                                Sistem secara otomatis membuat entri di tabel <code>hr_contracts</code> dengan status <strong>"active"</strong> untuk setiap kandidat yang lolos. Peserta langsung muncul di menu <strong>Monitoring Peserta Aktif</strong> (HRD Klien) dan <strong>All Active Participants</strong> (Super Admin).
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-xs font-bold">✓ Status: ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FLOW 3: BATCH EXTEND CONTRACT -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow3" class="flow-section space-y-4 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-sm">3</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Alur 3: Batch Extend Contract (Perpanjangan Masa Kontrak)</h3>
                        <p class="text-xs text-gray-500">Perpanjangan kontrak efisien untuk banyak peserta sekaligus.</p>
                    </div>
                </div>
                <span class="hidden md:inline-flex text-[11px] font-bold text-blue-800 bg-blue-100 px-3 py-1 rounded-full">Multi-Select Workflow</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 3.1 HR Company Initiates -->
                <div class="bg-blue-50/70 border-2 border-blue-200 rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-blue-600 text-white">Langkah 3.1</span>
                            <span class="text-xs font-mono text-blue-800">HR Company</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Pilih Peserta & Tgl Baru</h4>
                        <p class="text-xs text-gray-600">
                            HRD membuka <strong>Extend Contract</strong> $\rightarrow$ centang checklist peserta $\rightarrow$ isi tanggal akhir kontrak baru & catatan per row $\rightarrow$ klik <strong>Ajukan Perpanjangan Kontrak</strong>.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-blue-200 text-[11px] font-bold text-blue-700">
                        Request ID Terbit (EXT-XXXX)
                    </div>
                </div>

                <!-- 3.2 Super Admin Reviews -->
                <div class="bg-purple-50/70 border-2 border-purple-200 rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-600 text-white">Langkah 3.2</span>
                            <span class="text-xs font-mono text-purple-800">Super Admin</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Review & Verifikasi</h4>
                        <p class="text-xs text-gray-600">
                            Super Admin membuka <strong>Monitoring Request & Operasi</strong> $\rightarrow$ klik <strong>Review</strong> untuk membuka modal verifikasi seluruh item peserta $\rightarrow$ pilih <strong>Approve</strong> atau <strong>Reject</strong>.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-purple-200 text-[11px] font-bold text-purple-700">
                        Opsi: Quick Approve / Review Modal
                    </div>
                </div>

                <!-- 3.3 System Execution -->
                <div class="bg-emerald-50/70 border-2 border-emerald-200 rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-600 text-white">Langkah 3.3</span>
                            <span class="text-xs font-mono text-emerald-800">Sistem Otomatis</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Kontrak Baru Terbit</h4>
                        <p class="text-xs text-gray-600">
                            Kontrak lama diarsipkan menjadi <code>extended</code>, kontrak perpanjangan baru diterbitkan dengan ID <code>-EXT</code>, masa aktif peserta diperbarui otomatis tanpa mengganggu data lama.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-200 text-[11px] font-bold text-emerald-700">
                        Status: COMPLETED & EXTENDED
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FLOW 4: MANAJEMEN OFF-BOARDING (DUAL PATH) -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow4" class="flow-section space-y-4 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 font-extrabold flex items-center justify-center text-sm">4</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Alur 4: Manajemen Off-boarding (Dual-Path Workflow)</h3>
                        <p class="text-xs text-gray-500">Pelepasan masa tugas, pensiun, atau penyelesaian kontrak peserta dengan dua opsi jalur fleksibel.</p>
                    </div>
                </div>
                <span class="hidden md:inline-flex text-[11px] font-bold text-orange-800 bg-orange-100 px-3 py-1 rounded-full">Dual-Path: Approval vs Direct</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- JALUR A: PENGAJUAN OLEH HRD MITRA -->
                <div class="p-6 rounded-2xl border-2 border-orange-200 bg-orange-50/30 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full bg-orange-600 text-white text-[11px] font-bold">JALUR A • Pengajuan dari Klien</span>
                            <span class="text-xs text-orange-700 font-semibold">Standard HR Flow</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-base mb-2">Inisiasi oleh HR Company $\rightarrow$ Approval Super Admin</h4>
                        <ol class="list-decimal list-inside text-xs text-gray-600 space-y-2 leading-relaxed">
                            <li><strong>HR Company:</strong> Masuk ke <em>Pusat Pengajuan $\rightarrow$ Off-boarding Batch</em> (`/hr-portal/batch/select/off_boarding`).</li>
                            <li><strong>Pilih Peserta:</strong> Centang peserta yang selesai kontrak/resign, tentukan Tanggal Efektif & Alasan.</li>
                            <li><strong>Kirim Permohonan:</strong> Request <code>OFF-XXXX</code> terkirim ke Super Admin.</li>
                            <li><strong>Review Super Admin:</strong> Super Admin membuka <em>Manajemen Off-boarding (Tab Pengajuan Masuk)</em> $\rightarrow$ klik Setujui (Approve).</li>
                            <li><strong>Arsip:</strong> Status peserta otomatis berubah menjadi <code>offboarded</code> dan tercatat di tab Arsip.</li>
                        </ol>
                    </div>
                    <div class="mt-4 pt-3 border-t border-orange-200 flex items-center justify-between text-xs font-bold text-orange-800">
                        <span>Pemeriksaan 2 Pihak</span>
                        <span>Notifikasi Real-time</span>
                    </div>
                </div>

                <!-- JALUR B: DIRECT OFF-BOARDING OLEH SUPER ADMIN -->
                <div class="p-6 rounded-2xl border-2 border-red-200 bg-red-50/30 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-3 py-1 rounded-full bg-red-700 text-white text-[11px] font-bold">JALUR B • Direct Eksekusi Super Admin</span>
                            <span class="text-xs text-red-700 font-semibold">Otoritas LPK Gyokai</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-base mb-2">Off-boarding Langsung Lintas Tenant (Tanpa Antri)</h4>
                        <ol class="list-decimal list-inside text-xs text-gray-600 space-y-2 leading-relaxed">
                            <li><strong>Super Admin:</strong> Masuk ke menu <em>Manajemen Off-boarding</em> $\rightarrow$ klik tombol <strong>+ Proses Off-boarding Langsung</strong> (`/hr-superadmin/offboarding/create`).</li>
                            <li><strong>Atau Tombol Cepat:</strong> Klik tombol merah <strong>Off-board</strong> langsung dari baris tabel <em>All Active Participants</em>.</li>
                            <li><strong>Pilih Peserta:</strong> Pilih perusahaan, centang peserta, isi tgl efektif & pilih preset alasan (Habis Kontrak, Pelanggaran, Deportasi, Sakit).</li>
                            <li><strong>Eksekusi Aman:</strong> Sistem menerapkan status <code>offboarded</code>, mencatat audit log level <code>critical</code>, dan <strong>otomatis mengirim pesan konfirmasi ke inbox HR Klien</strong>.</li>
                        </ol>
                    </div>
                    <div class="mt-4 pt-3 border-t border-red-200 flex items-center justify-between text-xs font-bold text-red-800">
                        <span>Eksekusi Cepat & Audit Trail</span>
                        <span>Auto-Notify ke Mitra</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FLOW 5: COMPLIANCE, VIOLATIONS & BPJS -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow5" class="flow-section space-y-4 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 font-extrabold flex items-center justify-center text-sm">5</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Alur 5: Kepatuhan, Pelanggaran (SP), Suspend & BPJS Ketenagakerjaan</h3>
                        <p class="text-xs text-gray-500">Penanganan kedisiplinan dan jaminan perlindungan ketenagakerjaan peserta.</p>
                    </div>
                </div>
                <span class="hidden md:inline-flex text-[11px] font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full">Compliance & Safety</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-5 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold mb-3">⚠️</div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1.5">Laporan Pelanggaran & SP</h4>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        HR Klien melaporkan pelanggaran tata tertib/mangkir $\rightarrow$ Super Admin verifikasi bukti di <strong>Manajemen Pelanggaran</strong> $\rightarrow$ Terbitkan sanksi tertulis (SP1, SP2, atau SP3).
                    </p>
                    <span class="font-mono text-[10px] text-gray-400">Tabel: hr_violation_reports</span>
                </div>

                <div class="p-5 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold mb-3">⏸️</div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1.5">Pemberhentian Sementara (Suspend)</h4>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        HR Klien mengajukan permohonan suspend sementara bagi peserta berkasus $\rightarrow$ Super Admin meninjau $\rightarrow$ Status peserta menjadi <code>suspended</code> untuk rentang waktu tertentu.
                    </p>
                    <span class="font-mono text-[10px] text-gray-400">Tabel: hr_suspension_requests</span>
                </div>

                <div class="p-5 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-3">🛡️</div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1.5">BPJS F2A & Kecelakaan Kerja</h4>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        Pengelolaan jaminan perlindungan kerja: pelaporan iuran F2A bulanan $\rightarrow$ jika ada insiden, HR Klien lapor kecelakaan kerja $\rightarrow$ Super Admin proses klaim & rekapitulasi ke dinas.
                    </p>
                    <span class="font-mono text-[10px] text-gray-400">Modul: bpjs-accidents & bpjs-f2a</span>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FLOW 6: TALENT MATCHING & NEED REQUEST -->
        <!-- ========================================================================= -->
        <div id="flow-section-flow6" class="flow-section space-y-4 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 font-extrabold flex items-center justify-center text-sm">6</span>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Alur 6: Talent Matching & Pengajuan Tenaga Ahli (SSW/Tokutei Ginou)</h3>
                        <p class="text-xs text-gray-500">Pencarian tenaga kerja berpengalaman berdasarkan kualifikasi spesifik klien.</p>
                    </div>
                </div>
                <span class="hidden md:inline-flex text-[11px] font-bold text-rose-800 bg-rose-100 px-3 py-1 rounded-full">Talent Match Engine</span>
            </div>

            <div class="p-6 bg-gradient-to-r from-gray-50 via-rose-50/30 to-red-50/30 rounded-2xl border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-xs text-gray-600 flex-1">
                    <div class="font-bold text-gray-800 text-sm">Alur Kerja Matching Talent:</div>
                    <p>1. <strong>HR Company:</strong> Mengirimkan kebutuhan tenaga ahli via form <em>Pengajuan Tenaga Ahli</em> (`/hr-portal/client-needs/create`) atau browsing <em>Candidate Database</em> lalu klik <strong>Ajukan Pemanggilan (Call Request)</strong>.</p>
                    <p>2. <strong>Gyokai Matching Engine:</strong> Super Admin membuka <em>Talent DB & Matching</em> (`/hr-superadmin/talents`) $\rightarrow$ algoritma mencocokkan bidang industri, kemampuan bahasa (JLPT/JFT), dan pengalaman kerja.</p>
                    <p>3. <strong>Shortlist Kandidat:</strong> Super Admin merekomendasikan profil terbaik kepada perusahaan untuk jadwal wawancara.</p>
                </div>
                <div class="shrink-0 bg-white p-4 rounded-xl border border-gray-200 shadow-sm text-center min-w-[200px]">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Kualifikasi</div>
                    <div class="text-xl font-extrabold text-gik-primary">98% Match Rate</div>
                    <div class="text-[11px] text-green-700 font-semibold mt-1">✓ Siap Interview</div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MERMAID GRAPH VISUAL DIAGRAM -->
        <!-- ========================================================================= -->
        <div class="pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-base">📊</span>
                    <h3 class="text-base font-bold text-gray-800">Diagram Arsitektur Logika Sistem (Node-Graph)</h3>
                </div>
                <button type="button" onclick="toggleMermaidCode()" class="text-xs font-bold text-gik-primary hover:underline">
                    Lihat / Sembunyikan Kode Mermaid
                </button>
            </div>

            <!-- Mermaid Container -->
            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-200 overflow-x-auto text-center" id="mermaidContainer">
                <pre class="mermaid text-xs">
flowchart TD
    %% Roles definition
    subgraph SUPER_ADMIN["🏛️ Tim Rekrutmen LPK Gyokai (Super Admin Rekrutmen)"]
        SA_Start["Mulai / Dashboard Control Center"]
        SA_Client["1. Daftarkan Klien Mitra (Client Account Mgmt)"]
        SA_AddContract["2. Upload & Ajukan Paket Kandidat (Add New Contract)"]
        SA_OpReview["3. Review Permohonan Batch (Extend / Offboarding)"]
        SA_DirectOff["4. Proses Off-boarding Langsung Lintas Tenant"]
        SA_Vio["5. Tindak Lanjut Pelanggaran & Terbitkan SP1/SP2/SP3"]
        SA_Match["6. Matching Engine Talent SSW / Tokutei Ginou"]
    end

    subgraph ENGINE["⚡ Gyokai Connect Engine (Sistem Cloud Terintegrasi)"]
        SYS_Auth["Auto-Generate Kredensial & Enkripsi Hash"]
        SYS_Batch["Kirim Notifikasi Paket Batch ke Tenant"]
        SYS_Contract["Auto-Create Kontrak Aktif (hr_contracts)"]
        SYS_Extend["Perbarui Akhir Kontrak & Terbitkan ID-EXT"]
        SYS_Offboard["Ubah Status Peserta -> offboarded & Audit Log"]
        SYS_Alert["Notifikasi Otomatis ke Inbox HR Mitra"]
    end

    subgraph HR_COMPANY["🏢 Perusahaan Mitra Industri (HR Company Jepang & Lokal)"]
        HR_Login["Login Akun HR Company di Portal"]
        HR_Review["Review Kandidat Baru (Centang Lolos / Tidak Lolos)"]
        HR_Active["Monitoring Peserta Aktif di Perusahaan"]
        HR_Extend["Ajukan Batch Extend Kontrak (Multi-Select)"]
        HR_Offboard["Ajukan Batch Off-boarding Peserta"]
        HR_Report["Lapor Pelanggaran / Pengajuan Suspend / Klaim BPJS"]
        HR_Need["Kirim Kebutuhan Tenaga Ahli (Client Needs)"]
        HR_Inbox["Notifikasi Otomatis Masuk ke Inbox HR"]
    end

    %% Relations
    SA_Client --> SYS_Auth --> HR_Login
    SA_AddContract --> SYS_Batch --> HR_Review
    HR_Review -->|Submit Hasil Seleksi| SYS_Contract
    SYS_Contract --> HR_Active

    HR_Active -->|Masa Kontrak Mau Habis| HR_Extend
    HR_Extend --> SA_OpReview
    SA_OpReview -->|Approve Extend| SYS_Extend --> HR_Active

    HR_Active -->|Peserta Resign / Habis Kontrak| HR_Offboard
    HR_Offboard -->|Permohonan Masuk| SA_OpReview
    SA_OpReview -->|Approve Off-board| SYS_Offboard

    SA_DirectOff -->|Eksekusi Langsung| SYS_Offboard
    SYS_Offboard --> SYS_Alert --> HR_Inbox

    HR_Active -->|Ada Mangkir / Insiden| HR_Report
    HR_Report --> SA_Vio

    HR_Need --> SA_Match
                </pre>
            </div>

            <!-- Mermaid Raw Code Box (Collapsible) -->
            <div id="mermaidCodeBlock" class="hidden mt-3 p-4 bg-gray-900 text-gray-200 rounded-2xl text-xs font-mono overflow-x-auto">
                <div class="text-gray-400 text-[10px] uppercase font-bold mb-2">// Mermaid.js Raw Syntax (Bisa dicopy untuk dokumentasi / presentasi markdown)</div>
<code>flowchart TD
    subgraph SUPER_ADMIN["🏛️ Tim Rekrutmen LPK Gyokai (Super Admin Rekrutmen)"]
        SA_Start["Mulai / Dashboard Control Center"]
        SA_Client["1. Daftarkan Klien Mitra (Client Account Mgmt)"]
        SA_AddContract["2. Upload & Ajukan Paket Kandidat (Add New Contract)"]
        SA_OpReview["3. Review Permohonan Batch (Extend / Offboarding)"]
        SA_DirectOff["4. Proses Off-boarding Langsung Lintas Tenant"]
        SA_Vio["5. Tindak Lanjut Pelanggaran & Terbitkan SP1/SP2/SP3"]
        SA_Match["6. Matching Engine Talent SSW / Tokutei Ginou"]
    end

    subgraph ENGINE["⚡ Gyokai Connect Engine (Sistem Cloud Terintegrasi)"]
        SYS_Auth["Auto-Generate Kredensial & Enkripsi Hash"]
        SYS_Batch["Kirim Notifikasi Paket Batch ke Tenant"]
        SYS_Contract["Auto-Create Kontrak Aktif (hr_contracts)"]
        SYS_Extend["Perbarui Akhir Kontrak & Terbitkan ID-EXT"]
        SYS_Offboard["Ubah Status Peserta -> offboarded & Audit Log"]
        SYS_Alert["Notifikasi Otomatis ke Inbox HR Mitra"]
    end

    subgraph HR_COMPANY["🏢 Perusahaan Mitra Industri (HR Company Jepang & Lokal)"]
        HR_Login["Login Akun HR Company di Portal"]
        HR_Review["Review Kandidat Baru (Centang Lolos / Tidak Lolos)"]
        HR_Active["Monitoring Peserta Aktif di Perusahaan"]
        HR_Extend["Ajukan Batch Extend Kontrak (Multi-Select)"]
        HR_Offboard["Ajukan Batch Off-boarding Peserta"]
        HR_Report["Lapor Pelanggaran / Pengajuan Suspend / Klaim BPJS"]
        HR_Need["Kirim Kebutuhan Tenaga Ahli (Client Needs)"]
        HR_Inbox["Notifikasi Otomatis Masuk ke Inbox HR"]
    end

    SA_Client --> SYS_Auth --> HR_Login
    SA_AddContract --> SYS_Batch --> HR_Review
    HR_Review --> SYS_Contract --> HR_Active
    HR_Active --> HR_Extend --> SA_OpReview --> SYS_Extend --> HR_Active
    HR_Active --> HR_Offboard --> SA_OpReview --> SYS_Offboard
    SA_DirectOff --> SYS_Offboard --> SYS_Alert --> HR_Inbox
    HR_Active --> HR_Report --> SA_Vio
    HR_Need --> SA_Match</code>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof mermaid !== 'undefined') {
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            themeVariables: {
                primaryColor: '#FEE2E2',
                primaryTextColor: '#991B1B',
                primaryBorderColor: '#DC2626',
                lineColor: '#6B7280',
                secondaryColor: '#DBEAFE',
                tertiaryColor: '#FEF3C7'
            }
        });
    }
});

function switchFlowTab(tabKey) {
    // Reset all buttons
    document.querySelectorAll('.flow-tab-btn').forEach(btn => {
        btn.className = 'flow-tab-btn px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition whitespace-nowrap';
    });

    const activeBtn = document.getElementById('tab-btn-' + tabKey);
    if (activeBtn) {
        activeBtn.className = 'flow-tab-btn px-4 py-2 rounded-xl bg-gik-primary text-white shadow-sm transition whitespace-nowrap';
    }

    const sections = document.querySelectorAll('.flow-section');
    if (tabKey === 'all') {
        sections.forEach(sec => sec.classList.remove('hidden'));
    } else {
        sections.forEach(sec => sec.classList.add('hidden'));
        const target = document.getElementById('flow-section-' + tabKey);
        if (target) {
            target.classList.remove('hidden');
        }
    }
}

function toggleMermaidCode() {
    const codeBlock = document.getElementById('mermaidCodeBlock');
    if (codeBlock) {
        codeBlock.classList.toggle('hidden');
    }
}
</script>
