<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-sync members from Google Sheet every 5 minutes
        $schedule->command('members:sync')->everyFiveMinutes()->withoutOverlapping();

        // ── Security & Cleanup ──────────────────────────────────────────

        // Clean expired OTP codes every 15 minutes (security: prevent stale OTPs)
        $schedule->call(function () {
            \App\Models\User::whereNotNull('otp_code')
                ->where('otp_expires_at', '<', now())
                ->update(['otp_code' => null, 'otp_expires_at' => null]);
        })->everyFifteenMinutes()->name('cleanup-expired-otps')->withoutOverlapping();

        // Prune expired sessions from database (daily at 2 AM)
        $schedule->call(function () {
            $lifetime = config('session.lifetime', 120); // minutes
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '<', now()->subMinutes($lifetime)->getTimestamp())
                ->delete();
        })->dailyAt('02:00')->name('cleanup-expired-sessions')->withoutOverlapping()
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::warning('Session cleanup failed');
            });

        // Retry failed queue jobs, then prune old ones (daily at 3 AM)
        $schedule->command('queue:retry all')
            ->dailyAt('03:00')
            ->withoutOverlapping();
        $schedule->command('queue:prune-failed --hours=168') // Keep failed jobs for 7 days
            ->dailyAt('03:15')
            ->withoutOverlapping();

        // Clean up temporary files older than 7 days (daily at 4 AM)
        $schedule->call(function () {
            $tempPaths = [
                storage_path('app/temp'),
                storage_path('app/public/temp'),
            ];
            foreach ($tempPaths as $path) {
                if (!is_dir($path)) continue;
                $files = glob($path . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < strtotime('-7 days')) {
                        @unlink($file);
                    }
                }
            }
        })->dailyAt('04:00')->name('cleanup-temp-files')->withoutOverlapping();

        // Clear old log entries — activity logs older than 90 days are auto-rotated
        // by config/logging.php, but we also purge cache/compiled views weekly
        $schedule->command('view:clear')->weekly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
