<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\LoginLog;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Rate limiting untuk login attempts (5 attempts per minute per IP)
        $key = 'login.attempts.' . $request->ip();
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= 5) {
            Log::warning('Too many login attempts', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi setelah 1 menit.')
                ->with('login_type', 'admin')
                ->withInput($request->only('email'));
        }

        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Check if user exists (always check password hash to prevent timing attacks)
        try {
            $user = User::where('email', $credentials['email'])->first();
        } catch (QueryException $e) {
            Log::error('Database error during admin login user lookup', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terjadi masalah koneksi database. Silakan coba lagi nanti.')
                ->with('login_type', 'admin')
                ->withInput($request->only('email'));
        }
        
        // Always hash password check to prevent timing attacks
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Cache::put($key, $attempts + 1, now()->addMinutes(1));
            
            Log::warning('Failed login attempt', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Email atau password salah.')
                ->with('login_type', 'admin')
                ->withInput($request->only('email'));
        }

        // For HR Portal roles, send OTP instead of logging in directly
        if (in_array($user->role, ['hrd_company', 'super_admin_recruitment'])) {
            Cache::forget($key);
            $otpCode = $user->generateOtp();
            try {
                Mail::to($user->email)->send(new OtpMail($otpCode, 'Verifikasi Login'));
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', ['email' => $user->email, 'error' => $e->getMessage()]);
                return redirect()->route('admin.login')
                    ->with('error', 'Gagal mengirim kode OTP ke email Anda. Silakan coba lagi.')
                    ->with('login_type', 'admin')
                    ->withInput($request->only('email'));
            }

            // Store email + password in session temporarily for OTP verification
            $request->session()->put('otp_email', $user->email);
            $request->session()->put('otp_password', $credentials['password']);
            $request->session()->put('otp_remember', $request->filled('remember'));

            return redirect()->route('otp.verify.form')
                ->with('success', 'Kode OTP telah dikirim ke email ' . $user->email);
        }

        // For non-HR roles, login directly as before
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Cache::forget($key);
            
            $this->recordLoginLog($user, $request, 'admin');
            
            if ($user->role === 'company') {
                return redirect()->intended('/company/members');
            }
            if ($user->role === 'finance') {
                return redirect()->intended('/finance/dashboard');
            }
            
            return redirect()->intended('/admin/dashboard');
        }

        Cache::put($key, $attempts + 1, now()->addMinutes(1));
        
        return redirect()->route('admin.login')
            ->with('error', 'Email atau password salah.')
            ->with('login_type', 'admin')
            ->withInput($request->only('email'));
    }

    /**
     * Show OTP verification form
     */
    public function showOtpVerifyForm(Request $request)
    {
        $email = $request->session()->get('otp_email');
        if (!$email) {
            return redirect()->route('admin.login')->with('error', 'Sesi verifikasi telah berakhir. Silakan login kembali.');
        }
        return view('admin.otp-verify', ['email' => $email]);
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp_code' => 'required|string|size:6']);

        $email = $request->session()->get('otp_email');
        $password = $request->session()->get('otp_password');
        $remember = $request->session()->get('otp_remember', false);

        if (!$email || !$password) {
            return redirect()->route('admin.login')->with('error', 'Sesi verifikasi telah berakhir. Silakan login kembali.');
        }

        $user = User::where('email', $email)->first();
        if (!$user || !$user->verifyOtp($request->otp_code)) {
            return redirect()->route('otp.verify.form')
                ->with('error', 'Kode OTP tidak valid atau sudah kadaluarsa.');
        }

        $user->clearOtp();

        // Now actually perform Auth::attempt
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            // Clear temporary session data
            $request->session()->forget(['otp_email', 'otp_password', 'otp_remember']);
            $request->session()->regenerate();

            $this->recordLoginLog($user, $request, $user->role);

            return redirect()->intended('/hr');
        }

        return redirect()->route('admin.login')->with('error', 'Gagal login setelah verifikasi OTP.');
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('otp_email');
        if (!$email) {
            return redirect()->route('admin.login')->with('error', 'Sesi verifikasi telah berakhir.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Akun tidak ditemukan.');
        }

        $otpCode = $user->generateOtp();
        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, 'Verifikasi Login'));
        } catch (\Exception $e) {
            return redirect()->route('otp.verify.form')->with('error', 'Gagal mengirim ulang OTP. Coba lagi.');
        }

        return redirect()->route('otp.verify.form')->with('success', 'Kode OTP baru telah dikirim ke ' . $email);
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('admin.forgot-password');
    }

    /**
     * Send OTP for forgot password
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar dalam sistem.')->withInput();
        }

        $otpCode = $user->generateOtp();
        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, 'Reset Password'));
        } catch (\Exception $e) {
            Log::error('Failed to send forgot password OTP', ['email' => $user->email, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengirim kode OTP. Coba lagi.')->withInput();
        }

        $request->session()->put('reset_email', $user->email);
        return redirect()->route('password.reset.form')
            ->with('success', 'Kode OTP telah dikirim ke email ' . $user->email);
    }

    /**
     * Show reset password form (OTP + new password)
     */
    public function showResetPasswordForm(Request $request)
    {
        $email = $request->session()->get('reset_email');
        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Silakan masukkan email Anda terlebih dahulu.');
        }
        return view('admin.reset-password', ['email' => $email]);
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = $request->session()->get('reset_email');
        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $user = User::where('email', $email)->first();
        if (!$user || !$user->verifyOtp($request->otp_code)) {
            return redirect()->route('password.reset.form')
                ->with('error', 'Kode OTP tidak valid atau sudah kadaluarsa.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        $user->clearOtp();
        $request->session()->forget('reset_email');

        Log::channel('activity')->info('Password reset via OTP', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.login')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }

    /**
     * Record login log with IP geolocation
     */
    private function recordLoginLog($user, Request $request, string $role): void
    {
        $location = 'Unknown';
        $ip = $request->ip();

        if ($ip == '127.0.0.1' || $ip == '::1') {
            $location = 'Localhost';
        } else {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'success') {
                        $location = $data['city'] . ', ' . $data['country'];
                    }
                }
            } catch (\Exception $e) {
                // Ignore API errors
            }
        }

        LoginLog::create([
            'user_id' => $user->id,
            'role' => $role,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'location' => $location,
        ]);

        Log::channel('activity')->info('Successful login (' . $role . ')', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role ?? 'unknown',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public function companyLogin(Request $request)
    {
        // Rate limiting untuk login attempts
        $key = 'login.attempts.' . $request->ip();
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= 5) {
            Log::warning('Too many login attempts (company)', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi setelah 1 menit.')
                ->with('login_type', 'company')
                ->withInput($request->only('email'));
        }

        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Check if user exists and has company role BEFORE attempting login
        try {
            $user = User::where('email', $credentials['email'])->first();
        } catch (QueryException $e) {
            Log::error('Database error during company login user lookup', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terjadi masalah koneksi database. Silakan coba lagi nanti.')
                ->with('login_type', 'company')
                ->withInput($request->only('email'));
        }
        
        if (!$user) {
            Cache::put($key, $attempts + 1, now()->addMinutes(1));
            
            Log::warning('Failed login attempt (company) - User not found', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Email atau password salah.')
                ->with('login_type', 'company')
                ->withInput($request->only('email'));
        }

        // Check if role is company or hrd_company
        $userRole = $user->role ?? null;
        if (!in_array($userRole, ['company', 'hrd_company'])) {
            Cache::put($key, $attempts + 1, now()->addMinutes(1));
            
            Log::warning('Failed login attempt (company) - Invalid role', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_role' => $userRole,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Akun ini bukan akun perusahaan. Silakan hubungi administrator.')
                ->with('login_type', 'company')
                ->withInput($request->only('email'));
        }

        // Attempt login using Laravel's Auth::attempt
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Cache::forget($key);
            
            // Get location from IP
            $location = 'Unknown';
            $ip = $request->ip();
            
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $location = 'Localhost';
            } else {
                try {
                    $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if ($data['status'] === 'success') {
                            $location = $data['city'] . ', ' . $data['country'];
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore API errors
                }
            }

            LoginLog::create([
                'user_id' => $user->id,
                'role' => 'company',
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'location' => $location,
            ]);
            
            Log::channel('activity')->info('Successful login (company)', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            if ($user->role === 'hrd_company') {
                return redirect()->intended('/hr');
            }
            return redirect()->intended('/company/members');
        }

        // Login failed - password incorrect
        Cache::put($key, $attempts + 1, now()->addMinutes(1));
        
        Log::warning('Failed login attempt (company) - Password incorrect', [
            'ip' => $request->ip(),
            'email' => $credentials['email'],
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        return redirect()->route('admin.login')
            ->with('error', 'Email atau password salah.')
            ->with('login_type', 'company')
            ->withInput($request->only('email'));
    }

    public function financeLogin(Request $request)
    {
        // Rate limiting untuk login attempts
        $key = 'login.attempts.' . $request->ip();
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= 5) {
            Log::warning('Too many login attempts (finance)', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi setelah 1 menit.')
                ->with('login_type', 'finance')
                ->withInput($request->only('email'));
        }

        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Check if user exists and has finance role BEFORE attempting login
        try {
            $user = User::where('email', $credentials['email'])->first();
        } catch (QueryException $e) {
            Log::error('Database error during finance login user lookup', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Terjadi masalah koneksi database. Silakan coba lagi nanti.')
                ->with('login_type', 'finance')
                ->withInput($request->only('email'));
        }
        
        if (!$user) {
            Cache::put($key, $attempts + 1, now()->addMinutes(1));
            
            Log::warning('Failed login attempt (finance) - User not found', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Email atau password salah.')
                ->with('login_type', 'finance')
                ->withInput($request->only('email'));
        }

        // Check if role is finance
        $userRole = $user->role ?? null;
        if ($userRole !== 'finance') {
            Cache::put($key, $attempts + 1, now()->addMinutes(1));
            
            Log::warning('Failed login attempt (finance) - Invalid role', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_role' => $userRole,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Akun ini bukan akun finance. Silakan hubungi administrator.')
                ->with('login_type', 'finance')
                ->withInput($request->only('email'));
        }

        // Attempt login using Laravel's Auth::attempt
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Cache::forget($key);
            
            // Get location from IP
            $location = 'Unknown';
            $ip = $request->ip();
            
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $location = 'Localhost';
            } else {
                try {
                    $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if ($data['status'] === 'success') {
                            $location = $data['city'] . ', ' . $data['country'];
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore API errors
                }
            }

            LoginLog::create([
                'user_id' => $user->id,
                'role' => 'finance',
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'location' => $location,
            ]);
            
            Log::channel('activity')->info('Successful login (finance)', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->intended('/finance/dashboard');
        }

        // Login failed - password incorrect
        Cache::put($key, $attempts + 1, now()->addMinutes(1));
        
        Log::warning('Failed login attempt (finance) - Password incorrect', [
            'ip' => $request->ip(),
            'email' => $credentials['email'],
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        return redirect()->route('admin.login')
            ->with('error', 'Email atau password salah.')
            ->with('login_type', 'finance')
            ->withInput($request->only('email'));
    }

    public function financeLogout(Request $request)
    {
        $user = Auth::user();
        
        Log::channel('activity')->info('Logout (finance)', [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function companyLogout(Request $request)
    {
        $user = Auth::user();
        
        Log::channel('activity')->info('Logout (company)', [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Log::channel('activity')->info('Logout (admin)', [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
