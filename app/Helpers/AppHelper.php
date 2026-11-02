<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class AppHelper
{
    public static function getPageTitle($page = '')
    {
        $titles = [
            'index' => 'Home',
            'about' => 'About Us',
            'service' => 'Our Clients',
            'blog' => 'Job Vacancy',
            'contact' => 'Contact Us',
            'team' => 'Our Team',
            'fasilitas' => 'Fasilitas & Galeri',
            'legalitas' => 'Legalitas',
            'upload' => 'Upload File',
            'sipeduli' => 'Sipeduli',
            'our-services' => 'Our Services',
            'benefit-pemagangan' => 'Benefit Program Pemagangan',
            'benefit-labor-supply' => 'Benefit Program Labor Supply',
            'benefit-training' => 'Benefit Program Training Program Development',
            'benefit-outsourcing' => 'Benefit Program Outsourcing'
        ];
        
        $title = isset($titles[$page]) ? $titles[$page] : config('app.name');
        return $title . ' - ' . config('app.name');
    }

    public static function isActivePage($page)
    {
        $currentRoute = request()->route()->getName() ?? '';
        return $currentRoute === $page || str_contains($currentRoute, $page);
    }

    public static function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * Get storage URL with fallback
     * This function handles both Storage::url() and route() methods
     * to ensure images are accessible even if storage link is not created
     */
    public static function storageUrl($path)
    {
        if (empty($path)) {
            return asset('img/blog-1.png'); // Default image
        }

        // Try Storage::url() first (works if storage link exists)
        try {
            $url = Storage::url($path);
            // Check if file exists in public/storage (storage link exists)
            $publicPath = public_path('storage/' . $path);
            if (file_exists($publicPath) || is_link($publicPath)) {
                return $url;
            }
        } catch (\Exception $e) {
            // Fall through to route method
        }

        // Fallback: Use route to serve file directly from storage
        // This works even if storage link is not created
        return route('storage.file', ['path' => $path]);
    }
}










