@php
    $links = [];
    
    switch ($page ?? 'home') {
        case 'our-services':
        case 'benefit-pemagangan':
        case 'benefit-labor-supply':
        case 'benefit-training':
        case 'benefit-outsourcing':
            $links = [
                ['url' => route('blog'), 'text' => 'Loker Terpercaya Tangerang', 'icon' => 'fas fa-briefcase'],
                ['url' => route('our-services'), 'text' => 'Layanan LPK Gyokai', 'icon' => 'fas fa-concierge-bell'],
                ['url' => route('benefit-pemagangan'), 'text' => 'Program Pemagangan Tangerang', 'icon' => 'fas fa-graduation-cap'],
                ['url' => route('contact'), 'text' => 'Hubungi LPK Gyokai', 'icon' => 'fas fa-phone-alt'],
            ];
            break;
            
        case 'blog':
            $links = [
                ['url' => route('our-services'), 'text' => 'Layanan LPK Gyokai', 'icon' => 'fas fa-concierge-bell'],
                ['url' => route('benefit-pemagangan'), 'text' => 'Program Pemagangan Tangerang', 'icon' => 'fas fa-graduation-cap'],
                ['url' => route('contact'), 'text' => 'Hubungi LPK Gyokai', 'icon' => 'fas fa-phone-alt'],
            ];
            break;
            
        case 'about':
            $links = [
                ['url' => route('our-services'), 'text' => 'Layanan LPK Gyokai', 'icon' => 'fas fa-concierge-bell'],
                ['url' => route('legalitas'), 'text' => 'Legalitas LPK Gyokai', 'icon' => 'fas fa-certificate'],
                ['url' => route('contact'), 'text' => 'Hubungi LPK Gyokai', 'icon' => 'fas fa-phone-alt'],
            ];
            break;
            
        case 'home':
        default:
            $links = [
                ['url' => route('our-services'), 'text' => 'Layanan LPK Gyokai', 'icon' => 'fas fa-concierge-bell'],
                ['url' => route('blog'), 'text' => 'Loker Terpercaya Tangerang', 'icon' => 'fas fa-briefcase'],
                ['url' => route('contact'), 'text' => 'Hubungi LPK Gyokai', 'icon' => 'fas fa-phone-alt'],
            ];
            break;
    }
@endphp

<!-- Internal Links Section -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #f0f4f8 100%);">
    <div class="container">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <h2 class="display-4 mb-3" style="color: #1d3557;">Halaman Terkait</h2>
            <p style="color: #457b9d; font-size: 1.1rem;">Jelajahi halaman penting lainnya dari LPK Gyokai Indonesia</p>
        </div>
        
        <div class="row justify-content-center g-4 wow fadeInUp" data-wow-delay="0.2s">
            @foreach($links as $link)
                <div class="col-md-3 col-sm-6">
                    <a href="{{ $link['url'] }}" class="d-block p-4 bg-white rounded-xl text-center" style="box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-decoration: none; transition: all 0.3s ease;">
                        <div class="mb-3">
                            <i class="{{ $link['icon'] }}" style="font-size: 2.5rem; color: #e63946;"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1d3557; margin: 0;">{{ $link['text'] }}</h5>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
