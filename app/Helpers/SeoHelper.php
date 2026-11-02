<?php

namespace App\Helpers;

class SeoHelper
{
    public static function getSeoData(string $page): array
    {
        $seoConfig = config('seo');
        return $seoConfig['pages'][$page] ?? $seoConfig['pages']['home'];
    }

    public static function generateTitle(string $page): string
    {
        $data = self::getSeoData($page);
        return $data['title'];
    }

    public static function generateMeta(string $page): string
    {
        $seoConfig = config('seo');
        $data = self::getSeoData($page);
        $business = $seoConfig['business'];

        $meta = '';

        // Basic meta tags
        $meta .= '<title>' . htmlspecialchars($data['title']) . '</title>' . PHP_EOL;
        $meta .= '<meta name="description" content="' . htmlspecialchars($data['meta_description']) . '">' . PHP_EOL;
        $meta .= '<meta name="keywords" content="' . htmlspecialchars($data['focus_keyword']) . '">' . PHP_EOL;
        $meta .= '<link rel="canonical" href="' . htmlspecialchars($data['canonical']) . '">' . PHP_EOL;

        // Open Graph
        $meta .= '<meta property="og:title" content="' . htmlspecialchars($data['title']) . '">' . PHP_EOL;
        $meta .= '<meta property="og:description" content="' . htmlspecialchars($data['meta_description']) . '">' . PHP_EOL;
        $meta .= '<meta property="og:image" content="' . htmlspecialchars($business['url'] . $data['og_image']) . '">' . PHP_EOL;
        $meta .= '<meta property="og:url" content="' . htmlspecialchars($data['canonical']) . '">' . PHP_EOL;
        $meta .= '<meta property="og:type" content="website">' . PHP_EOL;
        $meta .= '<meta property="og:site_name" content="' . htmlspecialchars($business['name']) . '">' . PHP_EOL;

        // Twitter Card
        $meta .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
        $meta .= '<meta name="twitter:title" content="' . htmlspecialchars($data['title']) . '">' . PHP_EOL;
        $meta .= '<meta name="twitter:description" content="' . htmlspecialchars($data['meta_description']) . '">' . PHP_EOL;
        $meta .= '<meta name="twitter:image" content="' . htmlspecialchars($business['url'] . $data['og_image']) . '">' . PHP_EOL;

        return $meta;
    }

    public static function generateSchema(string $page): string
    {
        $seoConfig = config('seo');
        $business = $seoConfig['business'];
        $data = self::getSeoData($page);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $data['schema_type'],
            'name' => $business['name'],
            'url' => $business['url'],
            'logo' => $business['url'] . $business['logo'],
            'description' => $business['description'],
            'telephone' => $business['phone'][0],
            'email' => $business['email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business['address'],
                'addressLocality' => 'Kab. Tangerang',
                'addressRegion' => 'Banten',
                'postalCode' => '15730',
                'addressCountry' => 'ID'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $business['lat'],
                'longitude' => $business['lng']
            ],
            'sameAs' => [
                $business['instagram']
            ]
        ];

        if ($data['schema_type'] === 'Organization') {
            $schema['foundingDate'] = $business['founded'];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
