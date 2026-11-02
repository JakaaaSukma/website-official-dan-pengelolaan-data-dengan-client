<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip()); // 5 attempts per minute per IP
        });

        // OTP verification: max 5 attempts per minute per IP (anti brute-force)
        RateLimiter::for('otp-verify', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return back()->with('error', 'Terlalu banyak percobaan verifikasi. Silakan tunggu 1 menit.');
            });
        });

        // OTP send/resend: max 3 per minute per IP (anti spam email)
        RateLimiter::for('otp-send', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function () {
                return back()->with('error', 'Terlalu banyak permintaan kirim kode. Silakan tunggu 1 menit.');
            });
        });

        // Password reset: max 3 per minute per IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(function () {
                return back()->with('error', 'Terlalu banyak permintaan reset password. Silakan tunggu 1 menit.');
            });
        });
    }
}
