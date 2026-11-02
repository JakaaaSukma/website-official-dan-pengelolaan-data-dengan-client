@props([
    'page' => 'home',
    'title' => null,
    'description' => null,
    'jobs' => null,
    'news' => null,
    'robots' => 'index, follow',
    'canonical' => null,
])

@php
$seoConfig = config('seo');
$business = $seoConfig['business'];
$pageData = $seoConfig['pages'][$page] ?? $seoConfig['pages']['home'];

$finalTitle = $title ?? $pageData['title'];
$finalDescription = $description ?? $pageData['meta_description'];
$finalKeywords = $pageData['focus_keyword'];
$canonicalUrl = $canonical ?? $pageData['canonical'] ?? \App\Helpers\CanonicalHelper::generate();
$ogImage = $business['url'] . $pageData['og_image'];
@endphp

<!-- Basic Meta Tags -->
<meta name="robots" content="{{ $robots }}">
<meta name="google-site-verification" content="ikqZ7VWQ346hObkoQVQkIFjhTXKlOPxXBJUnEJZG5PI">
<title>{{ $finalTitle }}</title>
<meta name="description" content="{{ $finalDescription }}">
<meta name="keywords" content="{{ $finalKeywords }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $business['name'] }}">
<meta property="og:locale" content="id_ID">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $finalTitle }}">
<meta name="twitter:description" content="{{ $finalDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<!-- JSON-LD Schemas -->
@php
$schemas = [];

// BreadcrumbList for all pages
$breadcrumbs = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $business['url'],
        ],
    ],
];

$pageNames = [
    'home' => 'Home',
    'about' => 'Tentang Kami',
    'our-services' => 'Layanan Kami',
    'benefit-pemagangan' => 'Manfaat Pemagangan',
    'benefit-labor-supply' => 'Manfaat Labor Supply',
    'benefit-training' => 'Manfaat Training',
    'benefit-outsourcing' => 'Manfaat Outsourcing',
    'blog' => 'Lowongan Kerja',
    'news' => 'Berita',
    'team' => 'Tim Kami',
    'fasilitas' => 'Fasilitas',
    'legalitas' => 'Legalitas',
    'contact' => 'Hubungi Kami',
    'service' => 'Klien Kami',
];

if ($page !== 'home' && isset($pageNames[$page])) {
    $breadcrumbs['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => $pageNames[$page],
        'item' => $canonical,
    ];
}
$schemas[] = $breadcrumbs;

// Page-specific schemas
switch ($page) {
    case 'home':
        // LocalBusiness
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business['name'],
            'image' => $business['url'] . $business['logo'],
            '@id' => $business['url'],
            'url' => $business['url'],
            'telephone' => $business['phone'][0],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business['address'],
                'addressLocality' => 'Kabupaten Tangerang',
                'addressRegion' => 'Banten',
                'postalCode' => '15730',
                'addressCountry' => 'ID',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $business['lat'],
                'longitude' => $business['lng'],
            ],
            'priceRange' => '$$',
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '08:00',
                'closes' => '17:00',
            ],
            'sameAs' => [$business['instagram']],
        ];
        // WebSite
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $business['name'],
            'url' => $business['url'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $business['url'] . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
        // FAQPage
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Apa itu LPK Gyokai Indonesia?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'LPK Gyokai Indonesia adalah lembaga pelatihan kerja terpercaya di Tangerang yang menyediakan program pemagangan, labor supply, training SDM, dan outsourcing sejak 2017.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Bagaimana cara mendaftar program pemagangan?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Anda dapat mendaftar melalui website kami atau menghubungi kontak yang tersedia untuk informasi lebih lanjut.',
                    ],
                ],
            ],
        ];
        break;

    case 'about':
        // Organization
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $business['name'],
            'url' => $business['url'],
            'logo' => $business['url'] . $business['logo'],
            'foundingDate' => $business['founded'],
            'sameAs' => [$business['instagram']],
        ];
        // EducationalOrganization
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $business['name'],
            'description' => $business['description'],
            'url' => $business['url'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business['address'],
                'addressLocality' => 'Kabupaten Tangerang',
                'addressRegion' => 'Banten',
                'postalCode' => '15730',
                'addressCountry' => 'ID',
            ],
        ];
        break;

    case 'our-services':
        // Service with OfferCatalog
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'provider' => [
                '@type' => 'Organization',
                'name' => $business['name'],
            ],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Layanan LPK Gyokai Indonesia',
                'itemListElement' => [
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Program Pemagangan',
                            'description' => 'Program magang berbayar untuk pengembangan karir',
                        ],
                    ],
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Labor Supply',
                            'description' => 'Penyediaan tenaga kerja profesional',
                        ],
                    ],
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Training SDM',
                            'description' => 'Pelatihan pengembangan sumber daya manusia',
                        ],
                    ],
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => 'Outsourcing',
                            'description' => 'Layanan outsourcing untuk kebutuhan perusahaan',
                        ],
                    ],
                ],
            ],
        ];
        break;

    case 'benefit-pemagangan':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => 'Program Pemagangan (Paid Internship)',
            'description' => 'Program magang berbayar dengan berbagai manfaat untuk pengembangan karir',
            'provider' => [
                '@type' => 'Organization',
                'name' => $business['name'],
            ],
        ];
        break;

    case 'benefit-labor-supply':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => 'Labor Supply',
            'description' => 'Layanan penyediaan tenaga kerja profesional untuk perusahaan',
            'provider' => [
                '@type' => 'Organization',
                'name' => $business['name'],
            ],
        ];
        break;

    case 'benefit-training':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => 'Training Program Development',
            'description' => 'Program pelatihan untuk pengembangan sumber daya manusia',
            'provider' => [
                '@type' => 'Organization',
                'name' => $business['name'],
            ],
        ];
        break;

    case 'benefit-outsourcing':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => 'Outsourcing',
            'description' => 'Layanan outsourcing untuk berbagai kebutuhan perusahaan',
            'provider' => [
                '@type' => 'Organization',
                'name' => $business['name'],
            ],
        ];
        break;

    case 'blog':
        // ItemList for JobPostings
        $jobListItems = [];
        if ($jobs && $jobs->isNotEmpty()) {
            foreach ($jobs as $index => $job) {
                $jobListItems[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => [
                        '@type' => 'JobPosting',
                        'title' => $job->title ?? 'Lowongan Kerja',
                        'description' => $job->description ?? '',
                        'datePosted' => $job->created_at ? $job->created_at->toIso8601String() : now()->toIso8601String(),
                        'hiringOrganization' => [
                            '@type' => 'Organization',
                            'name' => $business['name'],
                        ],
                        'jobLocation' => [
                            '@type' => 'Place',
                            'address' => [
                                '@type' => 'PostalAddress',
                                'addressLocality' => 'Tangerang',
                                'addressRegion' => 'Banten',
                                'addressCountry' => 'ID',
                            ],
                        ],
                        'employmentType' => 'Full-time',
                    ],
                ];
            }
        }
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Lowongan Kerja',
            'itemListElement' => $jobListItems,
        ];
        break;

    case 'news':
        // Blog
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => 'Berita Gyokai Indonesia',
            'description' => 'Berita dan update terbaru dari LPK Gyokai Indonesia',
            'blogPost' => [],
        ];
        // NewsArticle for latest news
        if ($news && $news->isNotEmpty()) {
            foreach ($news as $article) {
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'NewsArticle',
                    'headline' => $article->title ?? '',
                    'description' => strip_tags(Str::limit($article->content ?? '', 150)),
                    'datePublished' => $article->created_at ? $article->created_at->toIso8601String() : now()->toIso8601String(),
                    'author' => [
                        '@type' => 'Organization',
                        'name' => $business['name'],
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $business['name'],
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $business['url'] . $business['logo'],
                        ],
                    ],
                ];
            }
        }
        break;

    case 'legalitas':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $business['name'],
            'description' => 'Legalitas dan dokumen resmi LPK Gyokai Indonesia',
            'url' => $canonical,
            'sameAs' => [$business['instagram']],
        ];
        break;

    case 'contact':
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $business['name'],
            'image' => $business['url'] . $business['logo'],
            'telephone' => $business['phone'][0],
            'email' => $business['email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business['address'],
                'addressLocality' => 'Kabupaten Tangerang',
                'addressRegion' => 'Banten',
                'postalCode' => '15730',
                'addressCountry' => 'ID',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $business['lat'],
                'longitude' => $business['lng'],
            ],
            'url' => $business['url'],
        ];
        break;
}
@endphp

@foreach($schemas as $schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endforeach
