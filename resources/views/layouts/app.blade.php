<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="{{ asset('logo/Gyokai.png') }}" type="image/png">

    <!-- Preload Hero Image -->
    @if(file_exists(public_path('img/jelas.webp')))
        <link 
            rel="preload" 
            as="image" 
            href="{{ asset('img/jelas.webp') }}" 
            type="image/webp" 
            fetchpriority="high"
        >
    @endif
    <link 
        rel="preload" 
        as="image" 
        href="{{ asset('img/jelas.png') }}" 
        fetchpriority="high"
    >

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- SEO Component -->
    @include('components.seo-head', [
        'page' => $seoPage ?? 'home',
        'title' => $pageTitle ?? null,
        'description' => $pageDescription ?? null,
        'jobs' => $jobVacancies ?? null,
        'news' => $latest ?? null,
        'canonical' => $canonical ?? null,
    ])

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=500;600;700&family=Rubik:wght=400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="preload" href="{{ asset('lib/animate/animate.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet"></noscript>
    <link rel="preload" href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet"></noscript>
    <link rel="preload" href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet"></noscript>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('gyokai.ga_tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('gyokai.ga_tracking_id') }}');
    </script>

    <!-- Google Ads -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('gyokai.google_ads_client') }}" crossorigin="anonymous"></script>

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.navbar')

    @yield('content')

    @include('partials.internal-links', ['page' => $page ?? $seoPage ?? 'home'])

    @include('components.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}" defer></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}" defer></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}" defer></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}" defer></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}" defer></script>
    <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}" defer></script>

    <!-- Template Javascript -->
    <script src="{{ asset('js/main.js') }}" defer></script>
</body>
</html>












