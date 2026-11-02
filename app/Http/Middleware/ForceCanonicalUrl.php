<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalUrl
{
    /**
     * Canonical URL yang ditetapkan:
     * - HTTPS
     * - Non-www (lpkgyokai.com)
     * - Tanpa trailing slash (kecuali root /)
     */
    private string $canonicalHost = 'lpkgyokai.com';
    private string $canonicalScheme = 'https';

    public function handle(Request $request, Closure $next): Response
    {
        // Skip di local/testing environment
        if (app()->isLocal() || app()->runningUnitTests()) {
            return $next($request);
        }

        $host = $request->getHost();
        $scheme = $request->getScheme();

        // Deteksi juga via header LiteSpeed/proxy
        $forwardedProto = $request->header('X-Forwarded-Proto');
        if ($forwardedProto) {
            $scheme = $forwardedProto;
        }

        $needsRedirect = false;
        $redirectUrl = null;

        // Cek apakah perlu redirect
        if ($scheme !== $this->canonicalScheme || $host !== $this->canonicalHost) {
            $needsRedirect = true;
            $path = $request->getRequestUri();
            $redirectUrl = $this->canonicalScheme . '://' . $this->canonicalHost . $path;
        }

        if ($needsRedirect && $redirectUrl) {
            return redirect($redirectUrl, 301)
                ->header('Cache-Control', 'max-age=86400'); // cache redirect 1 hari
        }

        return $next($request);
    }
}
