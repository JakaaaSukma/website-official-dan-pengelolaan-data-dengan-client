<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log important activities for authenticated users
        if (Auth::check()) {
            try {
                $user = Auth::user();
                $method = $request->method();
                $path = $request->path();
                
                // Log only important actions (POST, PUT, DELETE)
                if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                    // Skip logging for certain paths (like session refresh, etc)
                    $skipPaths = ['logout', 'session', 'csrf-token'];
                    $shouldSkip = false;
                    
                    foreach ($skipPaths as $skipPath) {
                        if (str_contains($path, $skipPath)) {
                            $shouldSkip = true;
                            break;
                        }
                    }

                    if (!$shouldSkip) {
                        Log::channel('activity')->info('User Activity', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'user_role' => $user->role ?? 'unknown',
                            'method' => $method,
                            'path' => $path,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'timestamp' => now()->toDateTimeString(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Fail silently if logging fails
                Log::error('Failed to log activity: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
