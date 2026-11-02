@php
    $currentRoute = request()->route()->getName() ?? '';
    $isHome = $currentRoute === 'index';
@endphp
<!-- Navbar & Hero Start -->
<div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg navbar-light {{ $isHome ? 'fixed-top' : 'bg-transparent' }} px-4 px-lg-5 py-3 py-lg-0">
        <a href="{{ route('index') }}" class="navbar-brand p-0">
            <img src="{{ asset('img/jelas.png') }}" alt="Logo" style="width: 150px; height: 100px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <a href="{{ route('index') }}" class="nav-item nav-link {{ $currentRoute === 'index' ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-item nav-link {{ $currentRoute === 'about' ? 'active' : '' }}">About</a>
                <a href="{{ route('our-services') }}" class="nav-item nav-link {{ in_array($currentRoute, ['our-services', 'benefit-pemagangan', 'benefit-labor-supply', 'benefit-training', 'benefit-outsourcing']) ? 'active' : '' }}">Our Services</a>
                <a href="{{ route('service') }}" class="nav-item nav-link {{ $currentRoute === 'service' ? 'active' : '' }}">Clients</a>
                <a href="{{ route('blog') }}" class="nav-item nav-link {{ $currentRoute === 'blog' ? 'active' : '' }}">Career</a>
                <a href="{{ route('news') }}" class="nav-item nav-link {{ in_array($currentRoute, ['news', 'news.detail']) ? 'active' : '' }}">News</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ in_array($currentRoute, ['team', 'fasilitas', 'legalitas', 'upload']) ? 'active' : '' }}" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu m-0">
                        <a href="{{ route('team') }}" class="dropdown-item {{ $currentRoute === 'team' ? 'active' : '' }}">Team</a>
                        <a href="{{ route('fasilitas') }}" class="dropdown-item {{ $currentRoute === 'fasilitas' ? 'active' : '' }}">Fasilitas & Galeri</a>
                        <a href="{{ route('legalitas') }}" class="dropdown-item {{ $currentRoute === 'legalitas' ? 'active' : '' }}">Legalitas</a>
                        <a href="{{ route('upload') }}" class="dropdown-item {{ $currentRoute === 'upload' ? 'active' : '' }}">Upload</a>
                    </div>
                </div>
                <a href="{{ route('contact') }}" class="nav-item nav-link {{ $currentRoute === 'contact' ? 'active' : '' }}">Contact Us</a>
            </div>
        </div>
    </nav>
</div>
<!-- Navbar & Hero End -->







