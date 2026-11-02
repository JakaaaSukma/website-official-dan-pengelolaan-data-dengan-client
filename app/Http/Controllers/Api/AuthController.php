<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    /**
     * Handle mobile app login and issue Sanctum token
     */
    public function login(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email atau password salah.'
                ], 401);
            }

            // Check if user has access (HR Company or Super Admin)
            if (!in_array($user->role, ['hrd_company', 'super_admin_recruitment', 'admin'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Aplikasi mobile hanya untuk HR Company dan Super Admin Rekrutmen.'
                ], 403);
            }

            // Check account status for HR Company
            if ($user->role === 'hrd_company') {
                if ($user->account_status !== 'active') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akun Anda tidak aktif atau sedang direview. Hubungi Super Admin.'
                    ], 403);
                }
                if (!$user->company_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akun HRD Anda belum terhubung dengan perusahaan manapun.'
                    ], 403);
                }
            }

            // Generate OTP and send email
            $otpCode = $user->generateOtp();
            try {
                Mail::to($user->email)->send(new OtpMail($otpCode, 'Verifikasi Login'));
            } catch (\Exception $e) {
                // If mail fails, we might want to log it and return error
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengirim kode OTP ke email. Pastikan server email berjalan.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'requires_otp' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server.'
            ], 500);
        }
    }

    /**
     * Resend OTP code to user's email (for login verification)
     */
    public function resendOtp(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }

            // Generate new OTP and send
            $otpCode = $user->generateOtp();
            try {
                Mail::to($user->email)->send(new OtpMail($otpCode, 'Verifikasi Login'));
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengirim kode OTP. Silakan coba lagi.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP baru telah dikirim ke email Anda.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server.'
            ], 500);
        }
    }

    /**
     * Verify OTP and issue Sanctum token
     */
    public function verifyLogin(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
                'otp_code' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengguna tidak ditemukan.'
                ], 404);
            }

            if (!$user->verifyOtp($request->otp_code)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'
                ], 401);
            }

            // Clear OTP
            $user->clearOtp();

            // Issue new token
            $tokenName = 'mobile-app-' . now()->timestamp;
            $token = $user->createToken($tokenName)->plainTextToken;

            // Load company relationship
            $user->load('company');

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'company_id' => $user->company_id,
                        'company_name' => $user->company?->company_name ?? 'Gyokai Connect',
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle mobile app logout and revoke token
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Get current authenticated user profile
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('company');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Request OTP for forgot password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar.'], 404);
            }

            $otpCode = $user->generateOtp();
            Mail::to($user->email)->send(new OtpMail($otpCode, 'Reset Password'));

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP telah dikirim ke email Anda.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
                'otp_code' => 'required|string|size:6',
                'new_password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->verifyOtp($request->otp_code)) {
                return response()->json(['status' => 'error', 'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'], 401);
            }

            $user->update(['password' => Hash::make($request->new_password)]);
            $user->clearOtp();

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah. Silakan login dengan password baru.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * Request OTP for changing password (authenticated)
     */
    public function requestChangePasswordOtp(Request $request)
    {
        try {
            $user = $request->user();
            $otpCode = $user->generateOtp();
            Mail::to($user->email)->send(new OtpMail($otpCode, 'Ganti Password'));

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP telah dikirim ke email Anda.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * Verify OTP and change password (authenticated)
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'otp_code' => 'required|string|size:6',
                'new_password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
            }

            $user = $request->user();

            if (!$user->verifyOtp($request->otp_code)) {
                return response()->json(['status' => 'error', 'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'], 401);
            }

            $user->update(['password' => Hash::make($request->new_password)]);
            $user->clearOtp();

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }
}
