<?php

namespace App\Helpers;

class CanonicalHelper
{
    private static string $base = 'https://lpkgyokai.com';

    /**
     * Generate canonical URL yang selalu benar:
     * HTTPS + non-www + tanpa trailing slash
     */
    public static function generate(?string $customPath = null): string
    {
        if ($customPath !== null) {
            // Path manual: bersihkan dan kembalikan
            $path = '/' . ltrim($customPath, '/');
            $path = rtrim($path, '/');
            // Homepage exception
            return self::$base . ($path === '/' ? '' : $path);
        }

        // Otomatis dari request URL saat ini
        $path = request()->getPathInfo();
        $path = rtrim($path, '/');

        return self::$base . $path;
    }

    /**
     * Khusus untuk artikel/berita — canonical ke URL artikel
     */
    public static function forArticle(string $slug): string
    {
        return self::$base . '/news/' . ltrim($slug, '/');
    }

    /**
     * Khusus untuk job posting / loker
     */
    public static function forJob(string $slug): string
    {
        return self::$base . '/blog/' . ltrim($slug, '/');
    }
}
