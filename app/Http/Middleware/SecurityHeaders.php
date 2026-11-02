<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only set headers if response is an instance of Response
        if ($response instanceof Response) {
            try {
                // Get headers - RedirectResponse and Response both have headers property
                if (property_exists($response, 'headers') && $response->headers !== null) {
                    // Security Headers
                    $response->headers->set('X-Content-Type-Options', 'nosniff');
                    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                    $response->headers->set('X-XSS-Protection', '1; mode=block');
                    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                    $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
                    
                    // Content Security Policy (CSP) - Modern XSS defense with full AdSense & Maps support
                    $csp = implode('; ', [
                        "default-src 'self' data: blob:",
                        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://ajax.googleapis.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://www.googletagmanager.com https://*.googlesyndication.com https://pagead2.googlesyndication.com https://*.google-analytics.com https://*.doubleclick.net https://*.google.com https://*.googleadservices.com",
                        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://use.fontawesome.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://*.googlesyndication.com",
                        "font-src 'self' https://fonts.gstatic.com https://use.fontawesome.com https://cdn.jsdelivr.net data:",
                        "img-src 'self' data: blob: https://*.google.com https://*.googleapis.com https://*.gstatic.com https://www.googletagmanager.com https://*.google-analytics.com https://*.googlesyndication.com https://pagead2.googlesyndication.com https://*.doubleclick.net https://*.googleadservices.com https://img.youtube.com https://i.ytimg.com",
                        "connect-src 'self' https://*.google.com https://*.googleapis.com https://*.google-analytics.com https://*.googletagmanager.com https://*.googlesyndication.com https://pagead2.googlesyndication.com https://*.doubleclick.net https://*.googleadservices.com",
                        "frame-src 'self' data: blob: https://*.google.com https://www.google.com https://maps.google.com https://*.googlesyndication.com https://pagead2.googlesyndication.com https://*.doubleclick.net https://*.googleadservices.com https://*.youtube.com https://youtube.com",
                        "object-src 'none'",
                        "base-uri 'self'",
                        "form-action 'self'",
                    ]);
                    $response->headers->set('Content-Security-Policy', $csp);
                    
                    // Force HTTPS in production
                    if (app()->environment('production')) {
                        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                    }
                    
                    // Remove server information
                    if ($response->headers->has('Server')) {
                        $response->headers->remove('Server');
                    }
                    if ($response->headers->has('X-Powered-By')) {
                        $response->headers->remove('X-Powered-By');
                    }
                }
            } catch (\Exception $e) {
                // If setting headers fails, just return the response without modification
                // Log error if needed
                Log::warning('SecurityHeaders middleware: Failed to set headers', [
                    'error' => $e->getMessage(),
                    'response_type' => get_class($response)
                ]);
            }
        }

        return $response;
    }
}

