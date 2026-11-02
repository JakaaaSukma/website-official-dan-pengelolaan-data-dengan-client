<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Models\Client;
use App\Models\JobVacancy;
use App\Models\Team;
use App\Models\Facility;
use App\Models\Legalitas;
use App\Models\News;
use App\Models\Video;
use App\Models\CompanyProfileDownload;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        try {
            $clients = Client::where('is_hidden', 0)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $clients = collect([]);
        }

        // Ambil data berita untuk ditampilkan di beranda
        try {
            $headline = News::where('is_published', true)
                            ->where('is_headline', true)
                            ->latest('created_at')
                            ->first();
            $latest = News::where('is_published', true)
                          ->latest('created_at')
                          ->get();
        } catch (\Exception $e) {
            $headline = null;
            $latest = collect([]);
        }

        $videos = Video::where('is_active', true)
                       ->latest('created_at')
                       ->get();

        return view('pages.index', [
            'pageTitle' => AppHelper::getPageTitle('index'),
            'pageDescription' => 'LPK Gyokai Indonesia Kompeten - Lembaga Pelatihan Kerja terakreditasi yang bergerak dibidang Pendidikan Industri dan Sertifikasi Profesi sesuai kebutuhan industri dan pasar kerja',
            'keywords' => 'LPK, Pelatihan Kerja, Sertifikasi Profesi, Pendidikan Industri, Gyokai Indonesia, Pemagangan',
            'clients' => $clients,
            'headline' => $headline,
            'latest' => $latest,
            'videos' => $videos,
            'seoPage' => 'home',
        ]);
    }

    public function about()
    {
        try {
            $legalitas = Legalitas::where('is_active', true)->orderBy('order')->get();
        } catch (\Exception $e) {
            $legalitas = collect([]);
        }

        return view('pages.about', [
            'pageTitle' => AppHelper::getPageTitle('about'),
            'pageDescription' => 'About Us - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'About, Tentang, LPK Gyokai',
            'legalitas' => $legalitas,
            'seoPage' => 'about'
        ]);
    }

    public function service()
    {
        try {
            $openClients = Client::where('is_hidden', 0)->where('is_active', true)->orderBy('created_at', 'desc')->get();
            $closedClients = Client::where('is_hidden', 0)->where('is_active', false)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $openClients = collect([]);
            $closedClients = collect([]);
        }
        
        return view('pages.service', [
            'pageTitle' => AppHelper::getPageTitle('service'),
            'pageDescription' => 'Our Clients - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Clients, Klien, Partner, LPK Gyokai',
            'openClients' => $openClients,
            'closedClients' => $closedClients,
            'seoPage' => 'service'
        ]);
    }

    public function ourServices()
    {
        return view('pages.our-services', [
            'pageTitle' => AppHelper::getPageTitle('our-services'),
            'pageDescription' => 'Our Services - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Services, Layanan, Pemagangan, Labor Supply, Training, Outsourcing, LPK Gyokai',
            'seoPage' => 'our-services'
        ]);
    }

    public function benefitPemagangan()
    {
        return view('pages.benefit-pemagangan', [
            'pageTitle' => AppHelper::getPageTitle('benefit-pemagangan'),
            'pageDescription' => 'Benefit Program Pemagangan - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Benefit, Pemagangan, Magang, LPK Gyokai',
            'seoPage' => 'benefit-pemagangan'
        ]);
    }

    public function benefitLaborSupply()
    {
        return view('pages.benefit-labor-supply', [
            'pageTitle' => AppHelper::getPageTitle('benefit-labor-supply'),
            'pageDescription' => 'Benefit Program Labor Supply - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Benefit, Labor Supply, Supply Lepas, LPK Gyokai',
            'seoPage' => 'benefit-labor-supply'
        ]);
    }

    public function benefitTraining()
    {
        return view('pages.benefit-training', [
            'pageTitle' => AppHelper::getPageTitle('benefit-training'),
            'pageDescription' => 'Benefit Program Training Program Development - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Benefit, Training, Program Development, LPK Gyokai',
            'seoPage' => 'benefit-training'
        ]);
    }

    public function benefitOutsourcing()
    {
        return view('pages.benefit-outsourcing', [
            'pageTitle' => AppHelper::getPageTitle('benefit-outsourcing'),
            'pageDescription' => 'Benefit Program Outsourcing - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Benefit, Outsourcing, Cleaning Service, Security Service, Produksi, LPK Gyokai',
            'seoPage' => 'benefit-outsourcing'
        ]);
    }

    public function blog()
    {
        try {
            $jobVacancies = JobVacancy::where('is_active', true)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $jobVacancies = collect([]);
        }
        
        return view('pages.blog', [
            'pageTitle' => AppHelper::getPageTitle('blog'),
            'pageDescription' => 'Job Vacancy - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Career, Lowongan Kerja, Job Vacancy, LPK Gyokai',
            'jobVacancies' => $jobVacancies,
            'seoPage' => 'blog'
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'pageTitle' => AppHelper::getPageTitle('contact'),
            'pageDescription' => 'Contact Us - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Contact, Hubungi, Kontak, LPK Gyokai',
            'seoPage' => 'contact'
        ]);
    }

    public function team()
    {
        try {
            $teams = Team::where('is_active', true)->orderBy('order')->get();
        } catch (\Exception $e) {
            $teams = collect([]);
        }
        
        return view('pages.team', [
            'pageTitle' => AppHelper::getPageTitle('team'),
            'pageDescription' => 'Our Team - Tim profesional LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Team, Tim, Staff, LPK Gyokai, Direktur',
            'teams' => $teams,
            'seoPage' => 'team'
        ]);
    }

    public function fasilitas()
    {
        try {
            $allFacilities = Facility::where('is_active', true)->get();
            $facilities = $allFacilities->where('category', 'fasilitas')->sortBy('order');
            $galeri = $allFacilities->where('category', 'galeri');
        } catch (\Exception $e) {
            $facilities = collect([]);
            $galeri = collect([]);
        }
        
        return view('pages.fasilitas', [
            'pageTitle' => AppHelper::getPageTitle('fasilitas'),
            'pageDescription' => 'Fasilitas & Galeri - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Fasilitas, Galeri, Gallery, LPK Gyokai',
            'facilities' => $facilities,
            'galeri' => $galeri,
            'seoPage' => 'fasilitas'
        ]);
    }

    public function legalitas()
    {
        try {
            $legalitas = Legalitas::where('is_active', true)->orderBy('order')->get();
        } catch (\Exception $e) {
            $legalitas = collect([]);
        }
        
        return view('pages.legalitas', [
            'pageTitle' => AppHelper::getPageTitle('legalitas'),
            'pageDescription' => 'Legalitas LPK Gyokai Indonesia Kompeten - Dokumen resmi dan sertifikat legalitas perusahaan',
            'keywords' => 'Legalitas, Sertifikat, Dokumen Resmi, LPK Gyokai',
            'legalitas' => $legalitas,
            'seoPage' => 'legalitas'
        ]);
    }

    public function upload()
    {
        return view('pages.upload', [
            'pageTitle' => AppHelper::getPageTitle('upload'),
            'pageDescription' => 'Upload File - LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Upload, File, Dokumen, LPK Gyokai'
        ]);
    }

    public function sipeduli()
    {
        return view('pages.sipeduli', [
            'pageTitle' => AppHelper::getPageTitle('sipeduli'),
            'pageDescription' => 'Sipeduli - Sistem Informasi Pengaduan Masyarakat LPK Gyokai Indonesia Kompeten',
            'keywords' => 'Sipeduli, Pengaduan, Sistem Informasi, LPK Gyokai'
        ]);
    }

    public function news()
    {
        $headline = News::where('is_published', true)
                        ->where('is_headline', true)
                        ->latest('created_at')
                        ->first();

        $trending = News::where('is_published', true)
                        ->orderByDesc('views')
                        ->latest()
                        ->take(6)
                        ->get();

        $latest = News::where('is_published', true)
                      ->latest('created_at')
                      ->take(9)
                      ->get();

        $videos = Video::where('is_active', true)
                       ->latest('created_at')
                       ->get();

        return view('pages.news', [
            'pageTitle' => 'Berita',
            'pageDescription' => 'Berita dan update terbaru Gyokai',
            'keywords' => 'Berita, News, Gyokai',
            'headline' => $headline,
            'trending' => $trending,
            'latest' => $latest,
            'videos' => $videos,
            'seoPage' => 'news'
        ]);
    }

    public function newsDetail($slug)
    {
        // URL decode the slug in case it's encoded
        $slug = urldecode($slug);
        
        // Try to find by exact slug match first
        $newsItem = News::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        // If not found, try to find by matching the beginning of slug (for old invalid slugs)
        // This handles cases where slug in DB contains the URL slug as a substring
        if (!$newsItem) {
            // Try to find by matching first part of slug (before any /)
            $slugPart = explode('/', $slug)[0];
            $newsItem = News::where('is_published', true)
                ->where(function($query) use ($slug, $slugPart) {
                    $query->where('slug', 'like', $slugPart . '%')
                          ->orWhere('slug', 'like', '%' . $slugPart . '%')
                          ->orWhereRaw('SUBSTRING_INDEX(slug, "/", 1) = ?', [$slugPart]);
                })
                ->first();
        }

        // If still not found, try to find by matching content (for cases where slug was set to content)
        if (!$newsItem) {
            $slugWords = explode(' ', $slug);
            if (count($slugWords) > 3) {
                // If slug looks like content, try to find by matching title or content
                $searchTerm = implode(' ', array_slice($slugWords, 0, 5)); // First 5 words
                $newsItem = News::where('is_published', true)
                    ->where(function($query) use ($searchTerm) {
                        $query->where('title', 'like', '%' . $searchTerm . '%')
                              ->orWhere('content', 'like', '%' . $searchTerm . '%');
                    })
                    ->first();
            }
        }

        // If still not found, try to find by ID as fallback (for backward compatibility)
        if (!$newsItem && is_numeric($slug)) {
            $newsItem = News::where('id', $slug)
                ->where('is_published', true)
                ->first();
        }

        if (!$newsItem) {
            abort(404, 'Berita tidak ditemukan.');
        }

        // Fix invalid slug if needed (contains invalid characters or too long)
        $needsSlugFix = false;
        if (empty($newsItem->slug) || 
            strlen($newsItem->slug) > 100 || 
            strpos($newsItem->slug, '/') !== false ||
            strpos($newsItem->slug, ' ') !== false ||
            strpos($newsItem->slug, '%') !== false) {
            $needsSlugFix = true;
        }

        if ($needsSlugFix) {
            $newSlug = Str::slug($newsItem->title);
            $baseSlug = $newSlug;
            $counter = 1;
            while (News::where('slug', $newSlug)->where('id', '!=', $newsItem->id)->exists()) {
                $newSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $newsItem->slug = $newSlug;
            $newsItem->save();
            if ($slug !== $newSlug) {
                return redirect()->route('news.detail', ['slug' => $newSlug], 302);
            }
        }

        $newsItem->increment('views');

        $related = News::where('is_published', true)
                       ->where('id', '!=', $newsItem->id)
                       ->latest('created_at')
                       ->take(4)
                       ->get();

        return view('pages.news-detail', [
            'pageTitle' => $newsItem->title,
            'pageDescription' => strip_tags(Str::limit($newsItem->content, 150)),
            'keywords' => 'Berita, News, Gyokai',
            'newsItem' => $newsItem,
            'related' => $related,
        ]);
    }

    public function downloadCompanyProfile(Request $request)
    {
        // Validate form data with strict rules
        $validated = $request->validate([
            'company_name' => 'required|string|min:2|max:255',
            'full_name' => 'required|string|min:2|max:255',
            'position' => 'required|string|min:2|max:255',
            'contact' => 'required|string|max:255',
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_name.min' => 'Nama perusahaan minimal 2 karakter.',
            'company_name.max' => 'Nama perusahaan maksimal 255 karakter.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 2 karakter.',
            'full_name.max' => 'Nama lengkap maksimal 255 karakter.',
            'position.required' => 'Jabatan wajib diisi.',
            'position.min' => 'Jabatan minimal 2 karakter.',
            'position.max' => 'Jabatan maksimal 255 karakter.',
            'contact.required' => 'Nomor HP atau Email wajib diisi.',
            'contact.max' => 'Nomor HP atau Email maksimal 255 karakter.',
        ]);

        // Trim all inputs
        $validated['company_name'] = trim($validated['company_name']);
        $validated['full_name'] = trim($validated['full_name']);
        $validated['position'] = trim($validated['position']);
        $validated['contact'] = trim($validated['contact']);

        // Re-validate after trim (check if empty after trim)
        if (empty($validated['company_name']) || strlen($validated['company_name']) < 2) {
            return back()->withErrors(['company_name' => 'Nama perusahaan minimal 2 karakter.'])->withInput();
        }

        if (empty($validated['full_name']) || strlen($validated['full_name']) < 2) {
            return back()->withErrors(['full_name' => 'Nama lengkap minimal 2 karakter.'])->withInput();
        }

        if (empty($validated['position']) || strlen($validated['position']) < 2) {
            return back()->withErrors(['position' => 'Jabatan minimal 2 karakter.'])->withInput();
        }

        // Additional validation for contact (email or phone)
        $contact = $validated['contact'];
        $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        // Indonesian phone pattern: +62, 62, or 0 followed by 9-13 digits
        $phonePattern = '/^(\+62|62|0)[0-9]{9,13}$/';
        // Simple phone pattern for international numbers
        $phonePatternSimple = '/^[0-9+\-\s()]{10,20}$/';
        
        // Clean phone number for validation (remove spaces, dashes, parentheses)
        $cleanPhone = preg_replace('/[\s\-()]/', '', $contact);
        
        $isValidEmail = preg_match($emailPattern, $contact);
        $isValidPhone = preg_match($phonePattern, $cleanPhone) || preg_match($phonePatternSimple, $contact);
        
        if (!$isValidEmail && !$isValidPhone) {
            return back()->withErrors(['contact' => 'Nomor HP atau Email tidak valid. Masukkan nomor HP (contoh: 081234567890) atau email (contoh: email@example.com) yang valid.'])->withInput();
        }

        // Save the data to database for tracking
        try {
            CompanyProfileDownload::create([
                'company_name' => $validated['company_name'],
                'full_name' => $validated['full_name'],
                'position' => $validated['position'],
                'contact' => $validated['contact'],
            ]);
        } catch (\Exception $e) {
            // Log error but don't prevent download
            \Log::error('Error saving company profile download: ' . $e->getMessage());
        }
        
        // Look for company profile PDF file
        $pdfPaths = [
            public_path('company-profile.pdf'),
            public_path('files/company-profile.pdf'),
            public_path('documents/company-profile.pdf'),
            storage_path('app/public/company-profile.pdf'),
            base_path('../public_html/company-profile.pdf'),
            base_path('public_html/company-profile.pdf'),
            base_path('../company-profile.pdf'),
        ];

        $pdfPath = null;
        foreach ($pdfPaths as $path) {
            if (file_exists($path)) {
                $pdfPath = $path;
                break;
            }
        }

        // If PDF file exists, return it for download
        if ($pdfPath) {
            return response()->download($pdfPath, 'Company-Profile-LPK-Gyokai.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // If PDF file doesn't exist, return error message
        return back()->withErrors(['pdf' => 'File Company Profile tidak ditemukan. Silakan hubungi administrator.'])->withInput();
    }

    /**
     * Tampilkan formulir pelamaran kerja (Non-Member)
     */
    public function lamaran()
    {
        $dbPositions = [];
        try {
            $dbPositions = JobVacancy::where('is_active', true)->pluck('position')->toArray();
        } catch (\Exception $e) {
            $dbPositions = [];
        }

        $defaultPositions = [
            'Pengelasan (Welding / Las SMAW, GMAW, GTAW)',
            'Operator Manufaktur / Pabrik / Assembly',
            'Operator Mesin CNC / Bubut / Milling',
            'Pengecatan & Body Repair Otomotif',
            'Perakitan Elektronik & Listrik Industri',
            'Konstruksi & Scaffolding (Perancah)',
            'Perawatan Lansia (Careworker / Kaigo)',
            'Pertanian & Pengolahan Hasil Tani (Agriculture)',
            'Peternakan (Livestock Farming)',
            'Pengolahan Makanan & Minuman (Food Processing)',
            'Perhotelan, Tata Boga & Hospitality',
            'Operator Forklift & Gudang / Pergudangan (Warehouse)',
            'Pembersihan Gedung & Building Cleaning',
            'Staff Administrasi & Operasional Kantor',
            'Staff Akuntansi / Keuangan (Finance & Accounting)',
            'Staff Human Resources (HRD) & Personalia',
            'Quality Control / Quality Assurance (QC/QA)',
            'Teknisi Mekanik & Pemeliharaan Mesin (Maintenance)',
            'IT Programmer / Web Developer / IT Support',
            'Marketing / Sales Executive'
        ];

        // Combine DB positions + default positions, unique and sorted
        $allPositions = array_values(array_unique(array_filter(array_merge($dbPositions, $defaultPositions))));
        sort($allPositions);

        return view('pages.lamaran', [
            'pageTitle' => 'Formulir Pelamaran Kerja - LPK Gyokai Indonesia Kompeten',
            'pageDescription' => 'Formulir pendaftaran dan pelamaran kerja langsung di LPK Gyokai Indonesia Kompeten.',
            'keywords' => 'Lamaran Kerja, Karir, Formulir Pelamar, LPK Gyokai',
            'positions' => $allPositions,
            'seoPage' => 'lamaran'
        ]);
    }

    /**
     * Proses submit formulir pelamaran kerja (Non-Member)
     */
    public function storeLamaran(Request $request, \App\Services\GoogleDriveService $driveService)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'domisili' => 'required|string|max:255',
            'pendidikan_terakhir' => 'required|string|in:SD,SMP,SMA,SMK,D1,D2,D3,D4,S1,S2',
            'jurusan_prodi' => 'nullable|string|max:255',
            'ipk_nilai' => 'nullable|string|max:50',
            'nama_institusi' => 'required|string|max:255',
            'tipe_pengalaman' => 'required|in:pengalaman,non_pengalaman',
            'deskripsi_pengalaman' => 'nullable|string|max:3000',
            'posisi_dilamar' => 'required|string|max:255',
            'ekspektasi_gaji' => 'required|string|max:100',
            'kapan_bisa_bekerja' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:30',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'pernyataan_benar' => 'required|accepted',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'domisili.required' => 'Domisili saat ini wajib diisi.',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
            'nama_institusi.required' => 'Nama Universitas / Sekolah wajib diisi.',
            'posisi_dilamar.required' => 'Posisi yang dilamar wajib diisi.',
            'ekspektasi_gaji.required' => 'Ekspektasi gaji wajib diisi.',
            'kapan_bisa_bekerja.required' => 'Waktu kesiapan bekerja wajib dipilih.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'cv_file.required' => 'File CV wajib diunggah.',
            'cv_file.mimes' => 'Format CV harus berupa PDF, DOC, atau DOCX.',
            'cv_file.max' => 'Ukuran file CV maksimal 10 MB.',
            'pernyataan_benar.accepted' => 'Anda wajib menyetujui pernyataan kebenaran data.',
        ]);

        if ($validated['tipe_pengalaman'] === 'pengalaman' && empty(trim($request->input('deskripsi_pengalaman', '')))) {
            return back()->withErrors(['deskripsi_pengalaman' => 'Harap isi detail pengalaman kerja Anda.'])->withInput();
        }

        // Upload and format file name: CV_{Nama}_{Posisi}.{ext}
        $file = $request->file('cv_file');
        $ext = $file->getClientOriginalExtension();
        $safeNama = Str::slug($validated['nama_lengkap'], '_');
        $safePosisi = Str::slug($validated['posisi_dilamar'], '_');
        $formattedFileName = "CV_{$safeNama}_{$safePosisi}_" . time() . ".{$ext}";
        $gdriveFileName = "CV_{$safeNama}_{$safePosisi}.{$ext}";

        // Save locally temporarily for uploading to Google Drive
        $localPath = $file->storeAs('cv_applicants', $formattedFileName, 'public');
        $fullLocalPath = storage_path('app/public/' . $localPath);

        // Upload to Google Drive folder 'pelamar non member'
        $gdriveResult = $driveService->uploadFile(
            $fullLocalPath,
            $gdriveFileName,
            $file->getClientMimeType() ?: 'application/pdf'
        );

        // Jika berhasil diunggah ke Google Drive, hapus file fisik lokal agar tidak membebani disk server
        if (!empty($gdriveResult['success']) && !empty($gdriveResult['file_id'])) {
            if (file_exists($fullLocalPath)) {
                @unlink($fullLocalPath);
            }
            $savedLocalPath = null;
        } else {
            // Jika Google Drive gagal/belum terhubung, file lokal tetap dipertahankan sebagai fallback
            $savedLocalPath = $localPath;
        }

        // Create record in database
        \App\Models\NonMemberApplicant::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'domisili' => $validated['domisili'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
            'jurusan_prodi' => $validated['jurusan_prodi'] ?? null,
            'ipk_nilai' => $validated['ipk_nilai'] ?? null,
            'nama_institusi' => $validated['nama_institusi'],
            'tipe_pengalaman' => $validated['tipe_pengalaman'],
            'deskripsi_pengalaman' => $validated['tipe_pengalaman'] === 'pengalaman' ? $request->input('deskripsi_pengalaman') : null,
            'posisi_dilamar' => $validated['posisi_dilamar'],
            'ekspektasi_gaji' => $validated['ekspektasi_gaji'],
            'kapan_bisa_bekerja' => $validated['kapan_bisa_bekerja'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'cv_file_path' => $savedLocalPath,
            'cv_original_name' => $formattedFileName,
            'gdrive_file_id' => $gdriveResult['file_id'] ?? null,
            'gdrive_web_view_link' => $gdriveResult['web_view_link'] ?? null,
            'pernyataan_benar' => true,
            'status' => 'menunggu_review',
        ]);

        return redirect()->route('lamaran')->with('success_lamaran', 'Terima kasih, berkas lamaran Anda berhasil dikirim! Tim seleksi LPK Gyokai akan segera meninjau kualifikasi Anda.');
    }
}

