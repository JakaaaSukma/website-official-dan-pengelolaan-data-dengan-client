@php 
    $seoPage = 'news';
    $canonical = \App\Helpers\CanonicalHelper::forArticle($newsItem->slug ?? '');
    $readMinutes = max(1, ceil(str_word_count(strip_tags($newsItem->content ?? '')) / 200));
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Top Breadcrumb & Article Header Section -->
    <section class="w-full bg-surface-container-lowest border-b border-surface-container">
        <div class="max-w-7xl mx-auto px-gutter py-space-md">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="Breadcrumb" class="flex items-center flex-wrap gap-space-xs font-label-md text-label-md text-secondary mb-space-md">
                <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('index') }}">
                    <span class="material-symbols-outlined text-[16px]">home</span> Beranda
                </a>
                <span class="text-surface-dim">/</span>
                <a class="hover:text-primary transition-colors" href="{{ route('news') }}">Berita</a>
                <span class="text-surface-dim">/</span>
                <span class="text-primary truncate max-w-xs md:max-w-md font-semibold">{{ $newsItem->title }}</span>
            </nav>

            <!-- Category Badge & Headline -->
            <div class="max-w-4xl flex flex-col gap-space-sm mb-space-md">
                <div class="inline-flex items-center gap-space-xs px-space-sm py-1 rounded-lg bg-primary-container text-on-primary-container w-fit font-label-sm text-label-sm uppercase tracking-wider font-bold">
                    <span class="material-symbols-outlined text-[15px]">campaign</span>
                    {{ $newsItem->is_headline ? 'Berita Utama' : 'Media & Publikasi Resmi' }}
                </div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-tight font-extrabold">
                    {{ $newsItem->title }}
                </h1>
            </div>

            <!-- Metadata Strip -->
            <div class="flex flex-wrap items-center gap-space-md py-space-sm bg-surface-container-low rounded-xl px-space-md font-body-sm text-body-sm text-secondary border border-surface-container-high/60">
                <div class="flex items-center gap-1 text-on-surface">
                    <span class="material-symbols-outlined text-primary text-[18px]">calendar_today</span>
                    <span>{{ $newsItem->created_at ? $newsItem->created_at->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <span class="hidden sm:inline text-surface-dim">•</span>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-primary text-[18px]">person</span>
                    <span>Admin LPK Gyokai</span>
                </div>
                <span class="hidden sm:inline text-surface-dim">•</span>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-primary text-[18px]">visibility</span>
                    <span>{{ number_format($newsItem->views ?? 0) }} Views</span>
                </div>
                <span class="hidden sm:inline text-surface-dim">•</span>
                <div class="flex items-center gap-1 text-tertiary font-semibold">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                    <span>{{ $readMinutes }} Menit Baca</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Article Body & Sidebar Layout (70/30) -->
    <section class="w-full bg-background py-space-xl">
        <div class="max-w-7xl mx-auto px-gutter grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
            
            <!-- Primary Article Content Column (Lg: col-span-8) -->
            <article class="lg:col-span-8 flex flex-col gap-space-lg bg-surface-container-lowest p-space-md md:p-space-xl rounded-2xl shadow-sm border border-surface-container-high/60">
                
                <!-- Featured Hero Image -->
                <figure class="flex flex-col gap-space-xs -mx-space-md md:-mx-space-xl -mt-space-md md:-mt-space-xl mb-space-sm overflow-hidden rounded-t-2xl">
                    <div class="relative aspect-video w-full overflow-hidden bg-surface-container-high">
                        @if($newsItem->thumbnail)
                            <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" 
                                 src="{{ App\Helpers\AppHelper::storageUrl($newsItem->thumbnail) }}" 
                                 alt="{{ $newsItem->title }}"
                                 onerror="this.src='{{ asset('img/blog-1.png') }}'"/>
                        @else
                            <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" 
                                 src="{{ asset('img/blog-1.png') }}" 
                                 alt="{{ $newsItem->title }}"/>
                        @endif
                        @if($newsItem->is_headline)
                            <div class="absolute top-space-md right-space-md px-space-sm py-1 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm flex items-center gap-1 font-bold shadow">
                                <span class="material-symbols-outlined text-[14px]">star</span> Berita Utama
                            </div>
                        @endif
                    </div>
                </figure>

                <!-- Article Body Content -->
                <div class="prose max-w-none text-on-surface leading-relaxed text-body-lg font-body-lg">
                    {!! nl2br(e($newsItem->content)) !!}
                </div>

                <!-- Google AdSense In-Article Ad -->
                <div class="my-space-md py-space-sm border-y border-surface-container/60">
                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6612669082404375"
                         crossorigin="anonymous"></script>
                    <ins class="adsbygoogle"
                         style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"
                         data-ad-client="ca-pub-6612669082404375"
                         data-ad-slot="9841943333"></ins>
                    <script>
                         (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>

                <!-- Foto Tambahan / Galeri Dokumentasi Tambahan (Jika Ada) -->
                @if($newsItem->images && count($newsItem->images) > 0)
                    <div class="mt-space-md pt-space-md border-t border-surface-container">
                        <div class="flex items-center gap-2 mb-space-md">
                            <span class="material-symbols-outlined text-primary text-[22px]">photo_library</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Foto Dokumentasi Tambahan</h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-space-sm">
                            @foreach($newsItem->images as $index => $image)
                                @php
                                    $imageUrl = App\Helpers\AppHelper::storageUrl($image);
                                @endphp
                                <div class="group relative aspect-4/3 rounded-xl overflow-hidden bg-surface-container cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                                     onclick="openImageDetailModal('{{ $imageUrl }}', 'Foto Dokumentasi {{ $index + 1 }}')">
                                    <img src="{{ $imageUrl }}" 
                                         alt="Foto {{ $index + 1 }} - {{ $newsItem->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='{{ asset('img/blog-1.png') }}'"/>
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="w-9 h-9 rounded-full bg-white/90 text-primary flex items-center justify-center shadow">
                                            <span class="material-symbols-outlined text-[18px]">zoom_in</span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Social Share Strip -->
                <div class="pt-space-md border-t border-surface-container-high/60 flex flex-col sm:flex-row items-center justify-between gap-space-md bg-surface-container-low/50 p-space-md rounded-xl mt-space-sm">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[20px]">share</span>
                        <span class="font-label-md text-label-md text-on-surface font-bold">Bagikan Berita Ini:</span>
                    </div>
                    <div class="flex items-center gap-space-xs flex-wrap">
                        <button type="button" 
                                class="flex items-center gap-1.5 px-space-md py-space-xs rounded-lg bg-surface-container-lowest text-on-surface hover:bg-primary hover:text-on-primary font-label-sm text-label-sm shadow-sm transition-all border border-surface-container-high/40" 
                                onclick="window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(window.location.href))" 
                                title="Bagikan ke WhatsApp">
                            <span class="material-symbols-outlined text-[16px]">chat</span> WhatsApp
                        </button>
                        <button type="button" 
                                class="flex items-center gap-1.5 px-space-md py-space-xs rounded-lg bg-surface-container-lowest text-on-surface hover:bg-primary hover:text-on-primary font-label-sm text-label-sm shadow-sm transition-all border border-surface-container-high/40" 
                                onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href))" 
                                title="Bagikan ke Facebook">
                            <span class="material-symbols-outlined text-[16px]">public</span> Facebook
                        </button>
                        <button type="button" 
                                class="flex items-center gap-1.5 px-space-md py-space-xs rounded-lg bg-surface-container-lowest text-on-surface hover:bg-primary hover:text-on-primary font-label-sm text-label-sm shadow-sm transition-all border border-surface-container-high/40" 
                                onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.location.href))" 
                                title="Bagikan ke LinkedIn">
                            <span class="material-symbols-outlined text-[16px]">business</span> LinkedIn
                        </button>
                        <button type="button" 
                                class="flex items-center gap-1.5 px-space-md py-space-xs rounded-lg bg-surface-container-lowest text-on-surface hover:bg-primary hover:text-on-primary font-label-sm text-label-sm shadow-sm transition-all border border-surface-container-high/40" 
                                id="copy-share-btn" 
                                onclick="navigator.clipboard.writeText(window.location.href); this.innerText='Tersalin!';" 
                                title="Salin Tautan">
                            <span class="material-symbols-outlined text-[16px]">link</span> Salin Tautan
                        </button>
                    </div>
                </div>

                <!-- Back to News Button -->
                <div class="pt-space-md flex items-center justify-between">
                    <a href="{{ route('news') }}" class="inline-flex items-center gap-2 font-label-md text-label-md text-primary font-bold hover:underline">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        <span>Kembali ke Semua Berita</span>
                    </a>
                </div>

            </article>

            <!-- Sticky Sidebar Column (Lg: col-span-4) -->
            <aside class="lg:col-span-4 flex flex-col gap-space-lg lg:sticky lg:top-24">
                
                <!-- Widget 1: Berita Terkait / Lainnya -->
                <div class="bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-surface-container-high/60 flex flex-col gap-space-md">
                    <div class="flex items-center justify-between pb-space-xs border-b border-surface-container">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2 font-bold">
                            <span class="w-1.5 h-4 bg-primary rounded-full"></span>
                            Berita Lainnya
                        </h3>
                        <a class="font-label-sm text-label-sm text-primary hover:underline font-semibold" href="{{ route('news') }}">Lihat Semua</a>
                    </div>

                    <div class="flex flex-col gap-space-md">
                        @if($related && $related->isNotEmpty())
                            @foreach($related as $item)
                                <a class="flex gap-space-sm group items-start p-2 rounded-xl hover:bg-surface-container-low transition-colors" 
                                   href="{{ !empty($item->slug) ? route('news.detail', $item->slug) : '#' }}">
                                    <div class="w-20 h-20 shrink-0 rounded-lg overflow-hidden bg-surface-container-high border border-surface-container-high/60">
                                        @if($item->thumbnail)
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                                                 src="{{ App\Helpers\AppHelper::storageUrl($item->thumbnail) }}" 
                                                 alt="{{ $item->title }}"
                                                 onerror="this.src='{{ asset('img/blog-1.png') }}'"/>
                                        @else
                                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                                                 src="{{ asset('img/blog-1.png') }}" 
                                                 alt="{{ $item->title }}"/>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="font-label-sm text-label-sm text-secondary text-xs">
                                            {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '' }}
                                        </span>
                                        <h4 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors line-clamp-2 font-bold leading-snug">
                                            {{ $item->title }}
                                        </h4>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="text-xs text-secondary py-2">Belum ada berita terkait lainnya.</p>
                        @endif
                    </div>
                </div>

                <!-- Widget: Info Karir / Lowongan Cepat -->
                <div class="bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-surface-container-high/60 flex flex-col gap-space-md">
                    <div class="flex items-start gap-space-sm">
                        <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-[26px]">work</span>
                        </div>
                        <div class="flex flex-col">
                            <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold text-base">Lowongan Kerja Terbuka</h4>
                            <p class="font-body-sm text-body-sm text-secondary text-xs">Lihat daftar posisi manufaktur, pengelasan, dan kejuruan terbaru.</p>
                        </div>
                    </div>
                    <a href="{{ route('blog') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md rounded-xl transition-colors font-bold text-sm">
                        <span>Cek Lowongan Tersedia</span>
                        <span class="material-symbols-outlined text-[16px] text-primary">chevron_right</span>
                    </a>
                </div>

            </aside>
        </div>
    </section>
</div>

<!-- Lightbox Modal for Detail Images -->
<div id="imageDetailModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="relative max-w-4xl w-full bg-surface-container-lowest rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-surface-container">
            <h4 id="imageDetailTitle" class="font-headline-sm text-on-surface font-bold text-base truncate pr-4">Preview Foto</h4>
            <button onclick="closeImageDetailModal()" class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="max-h-[75vh] w-full bg-black/95 flex items-center justify-center overflow-hidden p-2">
            <img id="imageDetailSrc" src="" alt="Detail" class="max-h-[70vh] max-w-full object-contain rounded-lg"/>
        </div>
    </div>
</div>

<script>
    function openImageDetailModal(src, title) {
        if (!src) return;
        const modal = document.getElementById('imageDetailModal');
        const img = document.getElementById('imageDetailSrc');
        const titleEl = document.getElementById('imageDetailTitle');
        img.src = src;
        titleEl.innerText = title || 'Dokumentasi Foto';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeImageDetailModal() {
        const modal = document.getElementById('imageDetailModal');
        const img = document.getElementById('imageDetailSrc');
        img.src = '';
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.getElementById('imageDetailModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageDetailModal();
        }
    });
</script>
@endsection
