        <?php

        use Illuminate\Support\Facades\Route;
        use Illuminate\Support\Facades\Storage;
        use App\Http\Controllers\PageController;
        use App\Http\Controllers\AuthController;
        use App\Http\Controllers\AdminController;
        use App\Http\Controllers\CompanyController;
        use App\Http\Controllers\FinanceController;
        use App\Http\Controllers\AdminVideoController;
        use App\Services\GoogleSheetService;
        use App\Http\Controllers\HrBpjsController;
        use App\Http\Controllers\GoogleDriveAuthController;

        /*
        |--------------------------------------------------------------------------
        | Web Routes
        |--------------------------------------------------------------------------
        |
        | Here is where you can register web routes for your application. These
        | routes are loaded by the RouteServiceProvider and all of them will
        | be assigned to the "web" middleware group. Make something great!
        |
        */

        // Sitemap Route
        Route::get('/sitemap.xml', function () {
            // URL with priorities and lastmod
            $urls = [
                ['loc' => route('index'), 'priority' => '1.0', 'lastmod' => date('Y-m-d')],
                ['loc' => route('our-services'), 'priority' => '0.9', 'lastmod' => date('Y-m-d')],
                ['loc' => route('benefit-pemagangan'), 'priority' => '0.9', 'lastmod' => date('Y-m-d')],
                ['loc' => route('blog'), 'priority' => '0.8', 'lastmod' => date('Y-m-d')],
                ['loc' => route('about'), 'priority' => '0.8', 'lastmod' => date('Y-m-d')],
                ['loc' => route('news'), 'priority' => '0.7', 'lastmod' => date('Y-m-d')],
                ['loc' => route('contact'), 'priority' => '0.7', 'lastmod' => date('Y-m-d')],
                ['loc' => route('legalitas'), 'priority' => '0.7', 'lastmod' => date('Y-m-d')],
                ['loc' => route('team'), 'priority' => '0.6', 'lastmod' => date('Y-m-d')],
                ['loc' => route('fasilitas'), 'priority' => '0.6', 'lastmod' => date('Y-m-d')],
                ['loc' => route('service'), 'priority' => '0.6', 'lastmod' => date('Y-m-d')],
            ];

            // Add individual news articles
            $news = \App\Models\News::all();
            foreach ($news as $article) {
                $urls[] = [
                    'loc' => route('news.detail', $article->slug),
                    'priority' => '0.5',
                    'lastmod' => $article->updated_at ? $article->updated_at->format('Y-m-d') : date('Y-m-d')
                ];
            }

            // Generate XML
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($urls as $url) {
                $xml .= '<url>';
                $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
                $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
                $xml .= '<priority>' . $url['priority'] . '</priority>';
                $xml .= '</url>';
            }
            $xml .= '</urlset>';

            return response($xml)->header('Content-Type', 'application/xml');
        })->name('sitemap');

        // Storage file route (fallback if storage link not created)
        // 🔒 SECURITY: Path traversal protection — block ../ sequences
        Route::get('/storage/{path}', function ($path) {
            try {
                // Block path traversal attempts
                if (str_contains($path, '..') || str_contains($path, "\0")) {
                    abort(403);
                }

                $basePath = storage_path('app/public');
                $filePath = realpath($basePath . '/' . $path);

                // Ensure resolved path is still within storage/app/public
                if (!$filePath || !str_starts_with($filePath, realpath($basePath))) {
                    abort(404);
                }

                if (!is_file($filePath)) {
                    abort(404);
                }
                
                $type = mime_content_type($filePath);
                
                return response()->file($filePath, ['Content-Type' => $type]);
            } catch (\Exception $e) {
                abort(404);
            }
        })->where('path', '.*')->name('storage.file');

        // Google Drive OAuth2 Routes
        Route::get('/gdrive/connect', [GoogleDriveAuthController::class, 'index'])->name('gdrive.connect');
        Route::get('/gdrive/redirect', [GoogleDriveAuthController::class, 'redirect'])->name('gdrive.redirect');
        Route::get('/gdrive/callback', [GoogleDriveAuthController::class, 'callback'])->name('gdrive.callback');
        Route::post('/gdrive/manual-auth', [GoogleDriveAuthController::class, 'manualAuth'])->name('gdrive.manual-auth');
        Route::get('/gdrive/test', [GoogleDriveAuthController::class, 'testUpload'])->name('gdrive.test');
        Route::post('/gdrive/disconnect', [GoogleDriveAuthController::class, 'disconnect'])->name('gdrive.disconnect');

        // Public Routes
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/about', [PageController::class, 'about'])->name('about');
        Route::get('/service', [PageController::class, 'service'])->name('service');
        Route::get('/our-services', [PageController::class, 'ourServices'])->name('our-services');
        Route::get('/benefit-pemagangan', [PageController::class, 'benefitPemagangan'])->name('benefit-pemagangan');
        Route::get('/benefit-labor-supply', [PageController::class, 'benefitLaborSupply'])->name('benefit-labor-supply');
        Route::get('/benefit-training', [PageController::class, 'benefitTraining'])->name('benefit-training');
        Route::get('/benefit-outsourcing', [PageController::class, 'benefitOutsourcing'])->name('benefit-outsourcing');
        Route::get('/blog', [PageController::class, 'blog'])->name('blog');
        Route::get('/contact', [PageController::class, 'contact'])->name('contact');
        Route::get('/team', [PageController::class, 'team'])->name('team');
        Route::get('/fasilitas', [PageController::class, 'fasilitas'])->name('fasilitas');
        Route::get('/legalitas', [PageController::class, 'legalitas'])->name('legalitas');
        Route::get('/upload', [PageController::class, 'upload'])->name('upload');
        Route::get('/lamaran', [PageController::class, 'lamaran'])->name('lamaran');
        Route::post('/lamaran', [PageController::class, 'storeLamaran'])->name('lamaran.store');
        Route::get('/sipeduli', [PageController::class, 'sipeduli'])->name('sipeduli');
        Route::get('/news', [PageController::class, 'news'])->name('news');
        Route::get('/news/{slug}', [PageController::class, 'newsDetail'])->name('news.detail')->where('slug', '.*');

        // Company Profile Download
        Route::post('/company-profile/download', [PageController::class, 'downloadCompanyProfile'])->name('company-profile.download');

        // Alias /login -> admin login for compatibility with default Authenticate middleware
        Route::get('/login', function () {
            return redirect()->route('admin.login');
        })->name('login');

        // Mobile App → WebView bridge: validates Sanctum token and creates web session
        Route::get('/mobile-bridge', function (\Illuminate\Http\Request $request) {
            $token = $request->query('token');
            if (!$token) {
                return redirect()->route('admin.login')->with('error', 'Token tidak valid.');
            }

            // Find the personal access token
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return redirect()->route('admin.login')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
            }

            $user = $accessToken->tokenable;
            if (!$user) {
                return redirect()->route('admin.login')->with('error', 'Pengguna tidak ditemukan.');
            }

            // Create web session for this user
            \Illuminate\Support\Facades\Auth::login($user);
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'super_admin_recruitment') {
                return redirect('/hr-superadmin/dashboard');
            } elseif ($user->role === 'hrd_company') {
                return redirect('/hr-portal/dashboard');
            }

            return redirect('/hr');
        })->name('mobile.bridge');

        // Admin Authentication Routes
        Route::middleware(['throttle:login'])->group(function () {
            Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
            Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
        });
        Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

        // OTP Verification Routes (rate limited: 5 attempts/min)
        Route::middleware(['throttle:otp-verify'])->group(function () {
            Route::get('/otp/verify', [AuthController::class, 'showOtpVerifyForm'])->name('otp.verify.form');
            Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
        });
        Route::middleware(['throttle:otp-send'])->group(function () {
            Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
        });

        // Forgot & Reset Password Routes (rate limited: 3 attempts/min)
        Route::middleware(['throttle:password-reset'])->group(function () {
            Route::get('/password/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
            Route::post('/password/forgot', [AuthController::class, 'sendForgotPasswordOtp'])->name('password.forgot.send');
            Route::get('/password/reset', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
            Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
        });

        // Company Authentication Routes
        // GET redirects to admin/login (which has both forms), POST handles company login
        Route::get('/company/login', function () {
            return redirect()->route('admin.login');
        })->name('company.login');
        Route::middleware(['throttle:login'])->group(function () {
            Route::post('/company/login', [AuthController::class, 'companyLogin'])->name('company.login.post');
        });
        Route::post('/company/logout', [AuthController::class, 'companyLogout'])->name('company.logout');

        // Finance Authentication Routes
        Route::middleware(['throttle:login'])->group(function () {
            Route::post('/finance/login', [AuthController::class, 'financeLogin'])->name('finance.login.post');
        });
        Route::post('/finance/logout', [AuthController::class, 'financeLogout'])->name('finance.logout');

        // Admin Protected Routes
        Route::middleware(['auth', 'admin', 'prevent-back-history'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            
            // Clients Routes
            Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
            Route::post('/clients/bulk-update', [AdminController::class, 'bulkUpdateClients'])->name('clients.bulk-update');
            Route::get('/clients/create', [AdminController::class, 'createClient'])->name('clients.create');
            Route::post('/clients', [AdminController::class, 'storeClient'])->name('clients.store');
            Route::get('/clients/{id}/edit', [AdminController::class, 'editClient'])->name('clients.edit');
            Route::put('/clients/{id}', [AdminController::class, 'updateClient'])->name('clients.update');
            Route::delete('/clients/{id}', [AdminController::class, 'deleteClient'])->name('clients.delete');
            
            // Job Vacancies Routes
            Route::get('/job-vacancies', [AdminController::class, 'jobVacancies'])->name('job-vacancies');
            Route::get('/job-vacancies/create', [AdminController::class, 'createJobVacancy'])->name('job-vacancies.create');
            Route::post('/job-vacancies', [AdminController::class, 'storeJobVacancy'])->name('job-vacancies.store');
            Route::get('/job-vacancies/{id}/edit', [AdminController::class, 'editJobVacancy'])->name('job-vacancies.edit');
            Route::put('/job-vacancies/{id}', [AdminController::class, 'updateJobVacancy'])->name('job-vacancies.update');
            Route::delete('/job-vacancies/{id}', [AdminController::class, 'deleteJobVacancy'])->name('job-vacancies.delete');
            
            // Teams Routes
            Route::get('/teams', [AdminController::class, 'teams'])->name('teams');
            Route::get('/teams/create', [AdminController::class, 'createTeam'])->name('teams.create');
            Route::post('/teams', [AdminController::class, 'storeTeam'])->name('teams.store');
            Route::get('/teams/{id}/edit', [AdminController::class, 'editTeam'])->name('teams.edit');
            Route::put('/teams/{id}', [AdminController::class, 'updateTeam'])->name('teams.update');
            Route::delete('/teams/{id}', [AdminController::class, 'deleteTeam'])->name('teams.delete');
            
            // Facilities Routes
            Route::get('/facilities', [AdminController::class, 'facilities'])->name('facilities');
            Route::get('/facilities/create', [AdminController::class, 'createFacility'])->name('facilities.create');
            Route::post('/facilities', [AdminController::class, 'storeFacility'])->name('facilities.store');
            Route::get('/facilities/{id}/edit', [AdminController::class, 'editFacility'])->name('facilities.edit');
            Route::put('/facilities/{id}', [AdminController::class, 'updateFacility'])->name('facilities.update');
            Route::delete('/facilities/{id}', [AdminController::class, 'deleteFacility'])->name('facilities.delete');
            
            // Legalitas Routes
            Route::get('/legalitas', [AdminController::class, 'legalitas'])->name('legalitas');
            Route::get('/legalitas/create', [AdminController::class, 'createLegalitas'])->name('legalitas.create');
            Route::post('/legalitas', [AdminController::class, 'storeLegalitas'])->name('legalitas.store');
            Route::get('/legalitas/{id}/edit', [AdminController::class, 'editLegalitas'])->name('legalitas.edit');
            Route::put('/legalitas/{id}', [AdminController::class, 'updateLegalitas'])->name('legalitas.update');
            Route::delete('/legalitas/{id}', [AdminController::class, 'deleteLegalitas'])->name('legalitas.delete');

            // News Routes
            Route::get('/news', [AdminController::class, 'news'])->name('news');
            Route::get('/news/create', [AdminController::class, 'createNews'])->name('news.create');
            Route::post('/news', [AdminController::class, 'storeNews'])->name('news.store');
            Route::get('/news/{id}/edit', [AdminController::class, 'editNews'])->name('news.edit');
            Route::put('/news/{id}', [AdminController::class, 'updateNews'])->name('news.update');
            Route::delete('/news/{id}', [AdminController::class, 'deleteNews'])->name('news.delete');

            // Videos Routes
            Route::get('/videos', [AdminVideoController::class, 'index'])->name('videos');
            Route::get('/videos/create', [AdminVideoController::class, 'create'])->name('videos.create');
            Route::post('/videos', [AdminVideoController::class, 'store'])->name('videos.store');
            Route::get('/videos/{id}/edit', [AdminVideoController::class, 'edit'])->name('videos.edit');
            Route::put('/videos/{id}', [AdminVideoController::class, 'update'])->name('videos.update');
            Route::delete('/videos/{id}', [AdminVideoController::class, 'destroy'])->name('videos.delete');
            Route::post('/videos/{id}/toggle-status', [AdminVideoController::class, 'toggleStatus'])->name('videos.toggle-status');

            // Site Assets (Kelola Gambar & Banner Website)
            Route::get('/site-assets', [AdminController::class, 'siteAssets'])->name('site-assets');
            Route::get('/site-assets/create', [AdminController::class, 'createSiteAsset'])->name('site-assets.create');
            Route::post('/site-assets', [AdminController::class, 'storeSiteAsset'])->name('site-assets.store');
            Route::get('/site-assets/{id}/edit', [AdminController::class, 'editSiteAsset'])->name('site-assets.edit');
            Route::put('/site-assets/{id}', [AdminController::class, 'updateSiteAsset'])->name('site-assets.update');
            Route::delete('/site-assets/{id}', [AdminController::class, 'deleteSiteAsset'])->name('site-assets.delete');

            // Change Password Routes
            Route::get('/change-password', [AdminController::class, 'showChangePasswordForm'])->name('change-password');
            Route::post('/change-password', [AdminController::class, 'updatePassword'])->name('change-password.update');
            Route::post('/change-email', [AdminController::class, 'updateEmail'])->name('change-email.update');

            // Login Logs Route
            Route::get('/login-logs', [AdminController::class, 'loginLogs'])->name('login-logs');

            // Company Profile Downloads Routes
            Route::get('/company-profile-downloads', [AdminController::class, 'companyProfileDownloads'])->name('company-profile-downloads');
            Route::get('/company-profile-downloads/{id}/edit', [AdminController::class, 'editCompanyProfileDownload'])->name('company-profile-downloads.edit');
            Route::put('/company-profile-downloads/{id}', [AdminController::class, 'updateCompanyProfileDownload'])->name('company-profile-downloads.update');
            Route::delete('/company-profile-downloads/{id}', [AdminController::class, 'deleteCompanyProfileDownload'])->name('company-profile-downloads.delete');
            Route::post('/company-profile-downloads/{id}/toggle-contacted', [AdminController::class, 'toggleContactedStatus'])->name('company-profile-downloads.toggle-contacted');
            Route::get('/company-profile-downloads/export/excel', [AdminController::class, 'exportCompanyProfileDownloads'])->name('company-profile-downloads.export');
        });

        // Company Protected Routes
        Route::middleware(['auth', 'company.user', 'prevent-back-history'])->prefix('company')->name('company.')->group(function () {

            // Participants Routes
            // IMPORTANT: Routes with specific paths must come BEFORE routes with parameters
            Route::get('/participants', [CompanyController::class, 'index'])->name('participants');
            Route::get('/participants/create', [CompanyController::class, 'create'])->name('participants.create');
            Route::post('/participants', [CompanyController::class, 'store'])->name('participants.store');
            Route::get('/participants/import', [CompanyController::class, 'showImportForm'])->name('participants.import');
            Route::post('/participants/import', [CompanyController::class, 'import'])->name('participants.import.post');
            Route::get('/participants/template/download', [CompanyController::class, 'downloadTemplate'])->name('participants.download-template');
            Route::get('/participants/export', [CompanyController::class, 'export'])->name('participants.export');
            Route::get('/participants/bulk-edit', [CompanyController::class, 'bulkEdit'])->name('participants.bulk-edit');
            Route::put('/participants/bulk-update', [CompanyController::class, 'bulkUpdate'])->name('participants.bulk-update');
            Route::post('/participants/bulk-followup', [CompanyController::class, 'bulkFollowup'])->name('participants.bulk-followup');
            Route::post('/participants/bulk-unfollowup', [CompanyController::class, 'bulkUnfollowup'])->name('participants.bulk-unfollowup');
            Route::delete('/participants/bulk-delete', [CompanyController::class, 'bulkDelete'])->name('participants.bulk-delete');
            // Routes with parameters come last (must be after bulk routes)
            Route::get('/participants/{id}/edit', [CompanyController::class, 'edit'])->name('participants.edit');
            Route::put('/participants/{id}', [CompanyController::class, 'update'])->name('participants.update');
            Route::post('/participants/{id}/toggle-followup', [CompanyController::class, 'toggleFollowup'])->name('participants.toggle-followup');
            Route::delete('/participants/{id}', [CompanyController::class, 'destroy'])->name('participants.destroy');
            
            // Members Routes
            Route::get('/members', [CompanyController::class, 'members'])->name('members');
            Route::get('/members/search', [CompanyController::class, 'searchMembers'])->name('members.search');
            Route::get('/members/create', [CompanyController::class, 'createMember'])->name('members.create');
            Route::post('/members', [CompanyController::class, 'storeMember'])->name('members.store');
            Route::get('/members/{id}/edit', [CompanyController::class, 'editMember'])->name('members.edit');
            Route::put('/members/{id}', [CompanyController::class, 'updateMember'])->name('members.update');
            Route::delete('/members/{id}', [CompanyController::class, 'destroyMember'])->name('members.destroy');
            Route::get('/members/export', [CompanyController::class, 'exportMembers'])->name('members.export');
            // Single inline status update
            Route::put('/members/{id}/update-status', [CompanyController::class, 'updateSingleMemberStatus'])->name('members.update-status');
            Route::post('/members/update-atm-status', [CompanyController::class, 'updateMemberAtmStatus'])->name('members.update-atm-status');
            Route::post('/members/update-reference', [CompanyController::class, 'updateMemberReference'])->name('members.update-reference');
            // Bulk status update by IDs
            Route::post('/members/bulk-update-status', [CompanyController::class, 'bulkUpdateMemberStatus'])->name('members.bulk-update-status');
            
            // Pelamar Non Member Routes
            Route::get('/pelamar-non-member', [CompanyController::class, 'nonMemberApplicants'])->name('pelamar-non-member');
            Route::put('/pelamar-non-member/{id}/status', [CompanyController::class, 'updateNonMemberApplicantStatus'])->name('pelamar-non-member.update-status');
            Route::delete('/pelamar-non-member/{id}', [CompanyController::class, 'destroyNonMemberApplicant'])->name('pelamar-non-member.destroy');
            

            
            // Change Password Routes
            Route::get('/change-password', [CompanyController::class, 'showChangePasswordForm'])->name('change-password');
            Route::post('/change-password', [CompanyController::class, 'updatePassword'])->name('change-password.update');
            Route::post('/change-email', [CompanyController::class, 'updateEmail'])->name('change-email.update');

            // Login Logs Route
            Route::get('/login-logs', [CompanyController::class, 'loginLogs'])->name('login-logs');
        });

        // Finance Protected Routes
        Route::middleware(['auth', 'finance', 'prevent-back-history'])->prefix('finance')->name('finance.')->group(function () {
            Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
            
            // Payslip Routes
            Route::get('/payslips', [FinanceController::class, 'payslips'])->name('payslips');
            Route::post('/payslips/upload', [FinanceController::class, 'uploadPayslips'])->name('payslips.upload');
            Route::post('/payslips/send/start', [FinanceController::class, 'startPayslipSending'])->name('payslips.send.start');
            Route::get('/payslips/sending', [FinanceController::class, 'showPayslipSending'])->name('payslips.sending');
            Route::post('/payslips/send', [FinanceController::class, 'sendPayslips'])->name('payslips.send');
            Route::post('/payslips/send/cancel', [FinanceController::class, 'cancelPayslipSending'])->name('payslips.send.cancel');
            Route::get('/payslips/reports/{sendSession}', [FinanceController::class, 'showPayslipReport'])->name('payslips.reports.show');

            // Change Password Routes
            Route::get('/change-password', [FinanceController::class, 'showChangePasswordForm'])->name('change-password');
            Route::post('/change-password', [FinanceController::class, 'updatePassword'])->name('change-password.update');
            Route::post('/change-email', [FinanceController::class, 'updateEmail'])->name('change-email.update');
        });

        // 🔒 SECURITY: Route /setup-finance-user dan /test-sync telah DIHAPUS.
        // Route ini sebelumnya terbuka tanpa autentikasi dan mengekspos data sensitif.
        // Gunakan `php artisan tinker` atau seeder untuk setup user di production.

        // Google OAuth Routes
        Route::middleware(['auth'])->group(function () {
            Route::get('/google/auth', function (GoogleSheetService $googleSheetService) {
                $authUrl = $googleSheetService->getAuthorizationUrl();
                if ($authUrl) {
                    return redirect($authUrl);
                }
                return redirect()->route('company.members')->with('error', 'Google OAuth not configured');
            })->name('google.auth');

            Route::get('/google/callback', function (Illuminate\Http\Request $request, GoogleSheetService $googleSheetService) {
                $code = $request->query('code');
                if ($googleSheetService->handleCallback($code)) {
                    return redirect()->route('company.members')->with('success', 'Google Sheets connected successfully!');
                }
                return redirect()->route('company.members')->with('error', 'Failed to connect Google Sheets');
            })->name('google.callback');

            Route::post('/google/disconnect', function () {
                $tokenPath = storage_path('app/google/token.json');
                if (file_exists($tokenPath)) {
                    unlink($tokenPath);
                }
                return redirect()->route('company.members')->with('success', 'Google Sheets disconnected');
            })->name('google.disconnect');
        });

        // ===== HR PORTAL MULTI-TENANT =====

        // HRD Company Portal Routes
        Route::middleware(['auth', 'hrd.company', 'prevent-back-history'])->prefix('hr-portal')->name('gik-hrd.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\HrDashboardController::class, 'index'])->name('dashboard');
            Route::get('/operations', [\App\Http\Controllers\HrDashboardController::class, 'operations'])->name('operations');
            Route::get('/messages', [\App\Http\Controllers\HrDashboardController::class, 'messages'])->name('messages');
            Route::post('/messages/send', [\App\Http\Controllers\HrDashboardController::class, 'sendMessage'])->name('messages.send');
            Route::get('/messages/{message}', [\App\Http\Controllers\HrDashboardController::class, 'showMessage'])->name('messages.show');
            Route::post('/messages/{message}/reply', [\App\Http\Controllers\HrDashboardController::class, 'replyMessage'])->name('messages.reply');

            // ===== Active Participants / Peserta Aktif (Employees & Interns) =====
            // 🔒 🔴 KEBIJAKAN: HANYA SUPER ADMIN REKRUTMEN YANG BOLEH MENAMBAH, MENGIMPORT, MENGHAPUS DATA PESERTA AKTIF!
            // Routes CREATE, STORE, DESTROY, IMPORT, TEMPLATE telah DIPINDAH 100% ke group gik-superadmin (prefix hr-superadmin).
            // HR Company (group gik-hrd) hanya dapat: VIEW DAFTAR peserta milik perusahaannya, EDIT / UPDATE (edit data), REVIEW EXPIRING (ajukan perpanjangan).
            // Route yang DISABLED di HRD dibawah ini:
            // Route::get('/employees/create', ...) → PINDAH ke gik-superadmin.employees.create
            // Route::post('/employees', ...) → PINDAH ke gik-superadmin.employees.store
            // Route::delete('/employees/{hrContract}', ...) → PINDAH ke gik-superadmin.employees.destroy
            // Route::get/post('/employees/import', ...) → PINDAH ke gik-superadmin.employees.import
            // Route::get('/employees/template/download', ...) → PINDAH ke gik-superadmin.employees.template
            Route::get('/employees/{hrContract}/edit', [\App\Http\Controllers\HrEmployeeController::class, 'edit'])->name('employees.edit');
            Route::put('/employees/{hrContract}', [\App\Http\Controllers\HrEmployeeController::class, 'update'])->name('employees.update');
            Route::get('/employees/{hrContract}', [\App\Http\Controllers\HrEmployeeController::class, 'show'])->name('employees.show');
            Route::get('/employees/{hrContract}/review-expiring', [\App\Http\Controllers\HrEmployeeController::class, 'reviewExpiring'])->name('employees.review-expiring');

            Route::get('/candidates', [\App\Http\Controllers\HrCandidateController::class, 'index'])->name('candidates');
            Route::get('/candidates/create', [\App\Http\Controllers\HrCandidateController::class, 'create'])->name('candidates.create');
            Route::post('/candidates', [\App\Http\Controllers\HrCandidateController::class, 'store'])->name('candidates.store');
            Route::get('/candidates/{candidate}', [\App\Http\Controllers\HrCandidateController::class, 'show'])->name('candidates.show');
            Route::delete('/candidates/{candidate}', [\App\Http\Controllers\HrCandidateController::class, 'destroy'])->name('candidates.destroy');
            Route::get('/candidates/import/form', [\App\Http\Controllers\HrCandidateController::class, 'showImportForm'])->name('candidates.import');
            Route::post('/candidates/import', [\App\Http\Controllers\HrCandidateController::class, 'import'])->name('candidates.import.post');
            Route::get('/candidates/template/download', [\App\Http\Controllers\HrCandidateController::class, 'downloadTemplate'])->name('candidates.download-template');

            Route::get('/request-hub', [\App\Http\Controllers\HrRequestHubController::class, 'index'])->name('request-hub');
            Route::get('/requests/create/{type}', [\App\Http\Controllers\HrRequestHubController::class, 'create'])->name('requests.create');
            Route::get('/requests/{hrRequest}/edit', [\App\Http\Controllers\HrRequestHubController::class, 'edit'])->name('requests.edit');
            Route::post('/requests/{hrRequest}/save-step', [\App\Http\Controllers\HrRequestHubController::class, 'saveStep'])->name('requests.save-step');
            Route::post('/requests/{hrRequest}/save-draft', [\App\Http\Controllers\HrRequestHubController::class, 'saveDraft'])->name('requests.save-draft');
            Route::post('/requests/{hrRequest}/submit', [\App\Http\Controllers\HrRequestHubController::class, 'submit'])->name('requests.submit');
            Route::delete('/requests/{hrRequest}', [\App\Http\Controllers\HrRequestHubController::class, 'destroy'])->name('requests.destroy');
            Route::post('/requests/{hrRequest}/review', [\App\Http\Controllers\HrRequestHubController::class, 'review'])->name('requests.review');
            Route::get('/requests/{hrRequest}/detail-json', [\App\Http\Controllers\HrContractFlowController::class, 'getRequestDetailJson'])->name('requests.detail-json');

            // ===== Add New Contract (HR Company side - review & seleksi kandidat) =====
            Route::get('/add-contracts', [\App\Http\Controllers\HrContractFlowController::class, 'listAddContractForHrd'])->name('add-contract.list');
            Route::get('/add-contracts/{batch}', [\App\Http\Controllers\HrContractFlowController::class, 'showAddContract'])->name('add-contract.show');
            Route::post('/add-contracts/{batch}/submit-selection', [\App\Http\Controllers\HrContractFlowController::class, 'hrdSubmitSelection'])->name('add-contract.submit-selection');

            // ===== Batch Extend Contract / Off-boarding (HR Company side) =====
            Route::get('/batch/{type}', [\App\Http\Controllers\HrContractFlowController::class, 'createBatchForm'])->name('batch.select');
            Route::post('/batch/{type}/submit', [\App\Http\Controllers\HrContractFlowController::class, 'storeBatchRequest'])->name('batch.submit');

            // ===== QUICK Action Extend / Off-boarding LANGSUNG dari Dashboard (single + batch via ?ids=) =====
            Route::get('/quick-action/{type}', [\App\Http\Controllers\HrContractFlowController::class, 'quickAction'])->name('quick.action');
            Route::post('/quick-action/{type}', [\App\Http\Controllers\HrContractFlowController::class, 'quickActionStore'])->name('quick.submit');

            // ===== Talent Database & Candidate Call Requests (HR Company) =====
            Route::get('/talents', [\App\Http\Controllers\HrTalentController::class, 'indexTalentsHrd'])->name('talents');
            Route::get('/talents/{talent}', [\App\Http\Controllers\HrTalentController::class, 'showTalentHrd'])->name('talents.show');
            Route::post('/talents/{talent}/call-request', [\App\Http\Controllers\HrTalentController::class, 'submitTalentCallRequestHrd'])->name('talents.call-request');

            // ===== Pengajuan Permintaan Tenaga Ahli (HR Company) =====
            Route::get('/client-needs/create', [\App\Http\Controllers\HrTalentController::class, 'createClientNeedHrd'])->name('client-needs.create');
            Route::post('/client-needs', [\App\Http\Controllers\HrTalentController::class, 'storeClientNeedHrd'])->name('client-needs.store');

            // ===== Pelaporan Pelanggaran Peserta (HR Company) =====
            Route::get('/violations', [\App\Http\Controllers\HrViolationController::class, 'indexHrd'])->name('violations.index');
            Route::get('/violations/create/{contract}', [\App\Http\Controllers\HrViolationController::class, 'createHrd'])->name('violations.create');
            Route::post('/violations/{contract}', [\App\Http\Controllers\HrViolationController::class, 'storeHrd'])->name('violations.store');
            Route::get('/violations/{report}', [\App\Http\Controllers\HrViolationController::class, 'showHrd'])->name('violations.show');

            // ===== Pengajuan Pemberhentian Sementara (HR Company) =====
            Route::get('/suspensions', [\App\Http\Controllers\HrSuspensionController::class, 'indexHrd'])->name('suspensions.index');
            Route::get('/suspensions/create/{contract}', [\App\Http\Controllers\HrSuspensionController::class, 'createHrd'])->name('suspensions.create');
            Route::post('/suspensions/{contract}', [\App\Http\Controllers\HrSuspensionController::class, 'storeHrd'])->name('suspensions.store');
            Route::get('/suspensions/{report}', [\App\Http\Controllers\HrSuspensionController::class, 'showHrd'])->name('suspensions.show');

            // ===== BPJS Ketenagakerjaan (HR Company) =====
            Route::get('/bpjs', [HrBpjsController::class, 'indexHrd'])->name('bpjs.index');
            Route::get('/bpjs/rate-settings', [HrBpjsController::class, 'viewRateSettings'])->name('bpjs.rate-settings');
            Route::get('/bpjs/accidents', [HrBpjsController::class, 'accidentReportsHrd'])->name('bpjs.accidents');
            Route::get('/bpjs/accidents/dashboard', [HrBpjsController::class, 'accidentDashboardHrd'])->name('bpjs.accidents.dashboard');
            Route::get('/bpjs/accidents/{report}', [HrBpjsController::class, 'showAccidentReport'])->name('bpjs.accidents.show');
            Route::get('/bpjs/f2a', [HrBpjsController::class, 'f2aReportHrd'])->name('bpjs.f2a');
            Route::get('/bpjs/f2a/export-excel', [HrBpjsController::class, 'exportF2aExcel'])->name('bpjs.f2a.excel');
            Route::get('/bpjs/f2a/export-word', [HrBpjsController::class, 'exportF2aWord'])->name('bpjs.f2a.word');
            Route::post('/bpjs/pocket-money', [HrBpjsController::class, 'storePocketMoney'])->name('bpjs.pocket-money.store');
            Route::post('/bpjs/pocket-money/import', [HrBpjsController::class, 'importPocketMoneyExcel'])->name('bpjs.pocket-money.import');

            // ===== HR Company Self-Service: Change Password & Activity Logs =====
            Route::get('/profile/change-password', [\App\Http\Controllers\HrDashboardController::class, 'showChangePassword'])->name('profile.change-password');
            Route::post('/profile/change-password', [\App\Http\Controllers\HrDashboardController::class, 'updatePassword'])->name('profile.update-password');
            Route::get('/activity-logs', [\App\Http\Controllers\HrDashboardController::class, 'activityLogs'])->name('activity-logs');

            // ===== Panduan Penggunaan =====
            Route::get('/panduan', [\App\Http\Controllers\HrDashboardController::class, 'panduan'])->name('panduan');
            Route::get('/panduan/download', [\App\Http\Controllers\HrDashboardController::class, 'downloadPanduan'])->name('panduan.download');
        });

        // Super Admin Recruitment Portal Routes
        Route::middleware(['auth', 'super.admin.recruitment', 'prevent-back-history'])->prefix('hr-superadmin')->name('gik-superadmin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\HrSuperAdminController::class, 'dashboard'])->name('dashboard');

            Route::get('/clients', [\App\Http\Controllers\HrSuperAdminController::class, 'clients'])->name('clients');
            Route::get('/clients/create', [\App\Http\Controllers\HrSuperAdminController::class, 'createClient'])->name('clients.create');
            Route::get('/clients/{client}/edit', [\App\Http\Controllers\HrSuperAdminController::class, 'editClient'])->name('clients.edit');
            Route::post('/clients', [\App\Http\Controllers\HrSuperAdminController::class, 'storeClient'])->name('clients.store');
            Route::match(['put', 'patch'], '/clients/{client}', [\App\Http\Controllers\HrSuperAdminController::class, 'updateClient'])->name('clients.update');
            Route::delete('/clients/{client}', [\App\Http\Controllers\HrSuperAdminController::class, 'destroyClient'])->name('clients.destroy');
            Route::delete('/clients/{client}/force-delete', [\App\Http\Controllers\HrSuperAdminController::class, 'forceDeleteClient'])->name('clients.force-delete');

            // ===== Manajemen Pelanggaran (Super Admin Rekrutmen) =====
            Route::get('/violations', [\App\Http\Controllers\HrViolationController::class, 'index'])->name('violations.index');
            Route::get('/violations/{report}', [\App\Http\Controllers\HrViolationController::class, 'show'])->name('violations.show');
            Route::post('/violations/{report}/update-status', [\App\Http\Controllers\HrViolationController::class, 'updateStatus'])->name('violations.update-status');

            // ===== Manajemen Pemberhentian Sementara (Super Admin Rekrutmen) =====
            Route::get('/suspensions', [\App\Http\Controllers\HrSuspensionController::class, 'index'])->name('suspensions.index');
            Route::get('/suspensions/{report}', [\App\Http\Controllers\HrSuspensionController::class, 'show'])->name('suspensions.show');
            Route::post('/suspensions/{report}/update-status', [\App\Http\Controllers\HrSuspensionController::class, 'updateStatus'])->name('suspensions.update-status');

            Route::get('/profile/change-password', [\App\Http\Controllers\HrSuperAdminController::class, 'showChangePassword'])->name('profile.change-password');
            Route::post('/profile/change-password', [\App\Http\Controllers\HrSuperAdminController::class, 'updatePassword'])->name('profile.update-password');
            Route::post('/profile/change-email', [\App\Http\Controllers\HrSuperAdminController::class, 'updateEmail'])->name('profile.update-email');

            Route::get('/activity-logs', [\App\Http\Controllers\HrSuperAdminController::class, 'activityLogs'])->name('activity-logs');

            Route::get('/inbox', [\App\Http\Controllers\HrSuperAdminController::class, 'inbox'])->name('inbox');
            Route::get('/inbox/{message}', [\App\Http\Controllers\HrSuperAdminController::class, 'showMessage'])->name('inbox.show');
            Route::post('/inbox/{message}/reply', [\App\Http\Controllers\HrSuperAdminController::class, 'replyMessage'])->name('inbox.reply');
            Route::post('/inbox/{message}/resolve', [\App\Http\Controllers\HrSuperAdminController::class, 'markResolved'])->name('inbox.resolve');
            Route::post('/inbox/{message}/assign', [\App\Http\Controllers\HrSuperAdminController::class, 'assignMessage'])->name('inbox.assign');

            Route::get('/talents', [\App\Http\Controllers\HrTalentController::class, 'index'])->name('talents');
            Route::get('/talents/create', [\App\Http\Controllers\HrTalentController::class, 'createTalent'])->name('talents.create');
            Route::post('/talents', [\App\Http\Controllers\HrTalentController::class, 'storeTalent'])->name('talents.store');
            Route::delete('/talents/{talent}', [\App\Http\Controllers\HrTalentController::class, 'destroyTalent'])->name('talents.destroy');
            Route::get('/talents/import', [\App\Http\Controllers\HrTalentController::class, 'showImportForm'])->name('talents.import');
            Route::post('/talents/import', [\App\Http\Controllers\HrTalentController::class, 'importTalents'])->name('talents.import.process');
            Route::get('/talents/template/download', [\App\Http\Controllers\HrTalentController::class, 'downloadTalentTemplate'])->name('talents.template');

            Route::post('/client-needs', [\App\Http\Controllers\HrTalentController::class, 'storeClientNeed'])->name('client-needs.store');
            Route::post('/client-needs/{need}/match', [\App\Http\Controllers\HrTalentController::class, 'runMatchEngine'])->name('client-needs.match');

            Route::get('/employees', [\App\Http\Controllers\HrEmployeeController::class, 'index'])->name('employees');
            Route::get('/employees/create', [\App\Http\Controllers\HrEmployeeController::class, 'create'])->name('employees.create');
            Route::post('/employees', [\App\Http\Controllers\HrEmployeeController::class, 'store'])->name('employees.store');
            Route::get('/employees/import', [\App\Http\Controllers\HrEmployeeController::class, 'showImportForm'])->name('employees.import');
            Route::post('/employees/import', [\App\Http\Controllers\HrEmployeeController::class, 'import'])->name('employees.import.process');
            Route::get('/employees/template/download', [\App\Http\Controllers\HrEmployeeController::class, 'downloadEmployeeTemplate'])->name('employees.template');
            Route::get('/employees/{hrContract}/edit', [\App\Http\Controllers\HrEmployeeController::class, 'edit'])->name('employees.edit');
            Route::put('/employees/{hrContract}', [\App\Http\Controllers\HrEmployeeController::class, 'update'])->name('employees.update');
            Route::get('/employees/{hrContract}', [\App\Http\Controllers\HrEmployeeController::class, 'show'])->name('employees.show');
            Route::delete('/employees/{hrContract}', [\App\Http\Controllers\HrEmployeeController::class, 'destroy'])->name('employees.destroy');

            Route::get('/candidates', [\App\Http\Controllers\HrCandidateController::class, 'index'])->name('candidates');
            Route::get('/candidates/create', [\App\Http\Controllers\HrCandidateController::class, 'create'])->name('candidates.create');
            Route::post('/candidates', [\App\Http\Controllers\HrCandidateController::class, 'store'])->name('candidates.store');
            Route::get('/candidates/{candidate}', [\App\Http\Controllers\HrCandidateController::class, 'show'])->name('candidates.show');
            Route::delete('/candidates/{candidate}', [\App\Http\Controllers\HrCandidateController::class, 'destroy'])->name('candidates.destroy');
            Route::get('/candidates/import', [\App\Http\Controllers\HrCandidateController::class, 'showImportForm'])->name('candidates.import');
            Route::post('/candidates/import', [\App\Http\Controllers\HrCandidateController::class, 'import'])->name('candidates.import.process');
            Route::get('/candidates/template/download', [\App\Http\Controllers\HrCandidateController::class, 'downloadTemplate'])->name('candidates.template');

            // ===== BPJS Ketenagakerjaan (Super Admin) =====
            Route::get('/bpjs', [HrBpjsController::class, 'indexSuperAdmin'])->name('bpjs.index');
            Route::put('/bpjs/{id}/update-status', [HrBpjsController::class, 'updateBpjsStatusSuperAdmin'])->name('bpjs.update-status');
            Route::get('/bpjs/rate-settings', [HrBpjsController::class, 'rateSettingsIndex'])->name('bpjs.rate-settings');
            Route::put('/bpjs/rate-settings/{companyId}', [HrBpjsController::class, 'updateRateSettings'])->name('bpjs.rate-settings.update');
            Route::get('/bpjs/rate-settings/{companyId}/history', [HrBpjsController::class, 'rateChangeHistory'])->name('bpjs.rate-history');
            Route::get('/bpjs/f2a', [HrBpjsController::class, 'f2aReportSuperAdmin'])->name('bpjs.f2a');
            Route::get('/bpjs/f2a/export-excel', [HrBpjsController::class, 'exportF2aExcelSuperAdmin'])->name('bpjs.f2a.excel');
            Route::get('/bpjs/f2a/export-word', [HrBpjsController::class, 'exportF2aWordSuperAdmin'])->name('bpjs.f2a.word');
            Route::get('/bpjs/accidents', [HrBpjsController::class, 'accidentReportsSuperAdmin'])->name('bpjs.accidents');
            Route::get('/bpjs/accidents/create', [HrBpjsController::class, 'createAccidentReportSuperAdmin'])->name('bpjs.accidents.create');
            Route::post('/bpjs/accidents', [HrBpjsController::class, 'storeAccidentReportSuperAdmin'])->name('bpjs.accidents.store');
            Route::get('/bpjs/accidents/dashboard', [HrBpjsController::class, 'accidentDashboard'])->name('bpjs.accidents.dashboard');
            Route::post('/bpjs/accidents/{report}/update-status', [HrBpjsController::class, 'updateAccidentStatus'])->name('bpjs.accidents.update-status');
            Route::get('/bpjs/accidents/export', [HrBpjsController::class, 'exportAccidentRecap'])->name('bpjs.accidents.export');

            Route::get('/operations', [\App\Http\Controllers\HrDashboardController::class, 'operations'])->name('operations');
            Route::get('/requests/create/{type}', [\App\Http\Controllers\HrRequestHubController::class, 'create'])->name('requests.create');
            Route::get('/requests/{hrRequest}/edit', [\App\Http\Controllers\HrRequestHubController::class, 'edit'])->name('requests.edit');
            Route::post('/requests/{hrRequest}/save-step', [\App\Http\Controllers\HrRequestHubController::class, 'saveStep'])->name('requests.save-step');
            Route::post('/requests/{hrRequest}/save-draft', [\App\Http\Controllers\HrRequestHubController::class, 'saveDraft'])->name('requests.save-draft');
            Route::post('/requests/{hrRequest}/submit', [\App\Http\Controllers\HrRequestHubController::class, 'submit'])->name('requests.submit');
            Route::delete('/requests/{hrRequest}', [\App\Http\Controllers\HrRequestHubController::class, 'destroy'])->name('requests.destroy');
            Route::post('/requests/{hrRequest}/review', [\App\Http\Controllers\HrRequestHubController::class, 'review'])->name('requests.review');
            Route::get('/requests/{hrRequest}/detail-json', [\App\Http\Controllers\HrContractFlowController::class, 'getRequestDetailJson'])->name('requests.detail-json');

            // ===== Panduan Penggunaan =====
            Route::get('/panduan', [\App\Http\Controllers\HrSuperAdminController::class, 'panduan'])->name('panduan');
            Route::get('/panduan/download', [\App\Http\Controllers\HrSuperAdminController::class, 'downloadPanduan'])->name('panduan.download');
        });

        // ===== Super Admin Contract Flows =====
        Route::middleware(['auth', 'super.admin.recruitment', 'prevent-back-history'])->prefix('hr-superadmin')->name('gik-superadmin.')->group(function () {
            // Flow 2: Add New Contract (Super Admin buat pengajuan ke HR Company)
            Route::get('/add-contract/create', [\App\Http\Controllers\HrContractFlowController::class, 'createAddContractForm'])->name('add-contract.create');
            Route::get('/add-contract/template', [\App\Http\Controllers\HrContractFlowController::class, 'downloadAddContractTemplate'])->name('add-contract.template');
            Route::post('/add-contract/import', [\App\Http\Controllers\HrContractFlowController::class, 'importAddContractExcel'])->name('add-contract.import');
            Route::post('/add-contract', [\App\Http\Controllers\HrContractFlowController::class, 'storeAddContract'])->name('add-contract.store');
            Route::get('/add-contracts', [\App\Http\Controllers\HrContractFlowController::class, 'listAddContractForSuperAdmin'])->name('add-contract.list');
            Route::get('/add-contract/{batch}', [\App\Http\Controllers\HrContractFlowController::class, 'showAddContract'])->name('add-contract.show');

            // Flow 3 & 4: Review Batch Extend / Off-boarding dari HR Company
            Route::post('/requests/{hrRequest}/review-batch', [\App\Http\Controllers\HrContractFlowController::class, 'reviewBatchRequest'])->name('requests.review-batch');
            Route::get('/requests/{hrRequest}/detail-json', [\App\Http\Controllers\HrContractFlowController::class, 'getRequestDetailJson'])->name('requests.detail-json');

            // Flow 4: Manajemen Off-boarding (Super Admin Rekrutmen)
            Route::get('/offboarding', [\App\Http\Controllers\HrContractFlowController::class, 'listOffboardingForSuperAdmin'])->name('offboarding.index');
            Route::get('/offboarding/create', [\App\Http\Controllers\HrContractFlowController::class, 'createDirectOffboardingForm'])->name('offboarding.create');
            Route::post('/offboarding', [\App\Http\Controllers\HrContractFlowController::class, 'storeDirectOffboarding'])->name('offboarding.store');
            Route::post('/offboarding/{hrContract}/single', [\App\Http\Controllers\HrContractFlowController::class, 'singleOffboarding'])->name('offboarding.single');
        });

        // Helper redirect route for HR Portal (auto redirect based on role)
        Route::middleware(['auth'])->get('/hr', function () {
            $user = auth()->user();
            if ($user->isSuperAdminRecruitment()) {
                return redirect()->route('gik-superadmin.dashboard');
            }
            if ($user->isHrdCompany() || $user->role === 'company') {
                return redirect()->route('gik-hrd.dashboard');
            }
            abort(403, 'Anda tidak memiliki akses ke HR Portal.');
        })->name('gik-hr.home');
