@php
    $seoPage = 'blog';
    $phone = preg_replace('/[^0-9]/', '', config('gyokai.phone', '082128496168'));
    if (substr($phone, 0, 2) !== '62' && substr($phone, 0, 1) !== '0') {
        $phone = '62' . ltrim($phone, '0');
    } elseif (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
@endphp
@extends('layouts.frontend')

@section('content')
<div class="flex flex-col w-full">
    <!-- Sub-Header / Breadcrumb Banner -->
    <section class="relative w-full overflow-hidden bg-on-secondary-fixed text-on-secondary py-space-xl">
        <div class="absolute inset-0 bg-cover bg-center opacity-25 mix-blend-luminosity" 
             style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBKYOifWoyAPaEQAZnN-4SEPCH-vWjDJ-yOQa3Hi-YBa_cYAvy91lUox5e40b2-U7s_Pmyf2QVccdCMaWsH2LagrLZnbith1dWhXa7mUnU2ZREXIMwm0OScKME59BDvOXjchRoFol4-FXvzruZFp4TUkQuFjtR8vbDV7ZAsHRIFh5wu0mnxQvHPTyCvCI_vKwscu9R9WYulMk0sIaYveD72YEMHDAB98I_3Ngfpl6g')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-on-secondary-fixed via-on-secondary-fixed/90 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-gutter flex flex-col md:flex-row md:items-end md:justify-between gap-space-md">
            <div class="flex flex-col gap-space-xs">
                <div class="inline-flex items-center gap-space-xs">
                    <a href="{{ route('index') }}" class="font-label-sm text-label-sm text-secondary-fixed hover:text-on-secondary transition-colors uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">home</span> Beranda
                    </a>
                    <span class="text-secondary text-body-sm">/</span>
                    <span class="font-label-sm text-label-sm text-primary-fixed uppercase tracking-wider font-bold">Lowongan Kerja</span>
                </div>
                <h1 class="font-headline-xl text-headline-xl tracking-tight text-on-secondary flex items-center gap-space-sm font-bold">
                    Lowongan Kerja
                    <span class="inline-flex items-center px-space-sm py-space-xs rounded-full bg-primary-container text-on-primary-container font-label-sm text-label-sm uppercase tracking-wide">
                        Loker Terpercaya
                    </span>
                </h1>
                <p class="font-body-md text-body-md text-secondary-fixed max-w-2xl leading-relaxed">
                    Akses resmi rekrutmen tenaga kerja industri manufaktur terkemuka kawasan industri Tangerang, Karawang, Jakarta &amp; Program Magang Jepang terafiliasi LPK GYOKAI.
                </p>
            </div>
            <!-- Key Metric Pill Badge -->
            <div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-xl p-space-md border border-white/10 flex items-center gap-space-lg self-start md:self-auto">
                <div class="flex flex-col">
                    <span class="font-headline-md text-headline-md text-on-secondary font-bold">{{ isset($jobVacancies) ? $jobVacancies->count() : '0' }}+</span>
                    <span class="font-body-sm text-body-sm text-secondary-fixed">Posisi Tersedia</span>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="flex flex-col">
                    <span class="font-headline-md text-headline-md text-primary-fixed font-bold">100%</span>
                    <span class="font-body-sm text-body-sm text-secondary-fixed">Penyaluran Resmi</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Filter Quick Bar -->
    <section class="max-w-7xl mx-auto px-gutter w-full -mt-6 z-10">
        <div class="bg-surface-container-lowest rounded-xl shadow-md p-space-md md:p-space-lg border border-surface-container">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-space-md items-center">
                <!-- Search Keyword Input -->
                <div class="md:col-span-8 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                    <input id="jobSearchInput" 
                           class="w-full pl-10 pr-4 py-2.5 bg-surface rounded-lg font-body-md text-body-md text-on-surface placeholder:text-secondary focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all border border-surface-container" 
                           placeholder="Ketik nama posisi atau kualifikasi..." 
                           type="text"
                           onkeyup="filterJobs()"/>
                </div>
                <!-- Action Button -->
                <div class="md:col-span-4 flex items-center gap-3">
                    <a href="https://wa.me/{{ $phone }}?text={{ urlencode('Halo LPK Gyokai, saya ingin menanyakan lowongan kerja yang tersedia.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-full flex items-center justify-center gap-2 py-2.5 px-space-md rounded-lg bg-primary hover:bg-tertiary text-on-primary font-label-md text-label-md shadow-sm transition-all font-bold">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        Konsultasi via WA
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Body Content: Job Listings -->
    <section class="max-w-7xl mx-auto px-gutter w-full py-space-xl">
        <div class="flex items-center justify-between pb-6">
            <h2 class="font-headline-md text-headline-md text-on-surface font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[28px]">work</span>
                Daftar Lowongan Kerja Aktif
            </h2>
            <span class="font-label-sm text-label-sm text-secondary bg-surface-container px-3 py-1.5 rounded-full font-semibold" id="jobCountBadge">
                {{ isset($jobVacancies) ? $jobVacancies->count() : '0' }} Lowongan Tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="jobGridContainer">
            @forelse($jobVacancies as $job)
                @php
                    $waMsg = 'Halo LPK Gyokai, saya tertarik melamar untuk posisi: ' . $job->position;
                    $waJobUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($waMsg);
                    $jobImg = $job->photo ? \App\Helpers\AppHelper::storageUrl($job->photo) : asset('img/blog-1.png');
                @endphp
                <article class="job-item bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden border border-surface-container group">
                    <div>
                        <!-- Image / Thumbnail Cover -->
                        <div class="relative bg-surface-container-low h-48 overflow-hidden flex items-center justify-center p-3 cursor-pointer"
                             onclick="showJobDetailModal('{{ addslashes($job->position) }}', '{{ addslashes($jobImg) }}', '{{ addslashes(e($job->responsibilities)) }}', '{{ addslashes(e($job->qualifications)) }}', '{{ $waJobUrl }}')">
                            <img src="{{ $jobImg }}" 
                                 alt="Loker {{ $job->position }}" 
                                 class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy"/>
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded bg-primary text-on-primary font-label-sm text-[10px] uppercase tracking-wider shadow-sm font-bold">
                                DIBUKA
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex flex-col gap-3">
                            <h3 class="job-title font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors font-bold line-clamp-2">
                                {{ $job->position }}
                            </h3>

                            <!-- Preview Text snippets -->
                            @if($job->qualifications)
                                <div class="bg-surface-container-low rounded-lg p-3 text-body-sm text-secondary">
                                    <span class="font-bold text-on-surface block mb-1 text-[11px] uppercase tracking-wider text-primary">Kualifikasi Utama:</span>
                                    <p class="line-clamp-2 leading-relaxed">
                                        {{ Str::limit(strip_tags($job->qualifications), 120) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Card Buttons -->
                    <div class="p-5 pt-0 flex items-center gap-2">
                        <button type="button" 
                                onclick="showJobDetailModal('{{ addslashes($job->position) }}', '{{ addslashes($jobImg) }}', '{{ addslashes(e($job->responsibilities)) }}', '{{ addslashes(e($job->qualifications)) }}', '{{ $waJobUrl }}')"
                                class="flex-1 py-2.5 px-3 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-all text-center font-semibold flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            Detail
                        </button>
                        <a href="{{ $waJobUrl }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="flex-1 py-2.5 px-3 rounded-lg bg-primary hover:bg-tertiary text-on-primary font-label-md text-label-md transition-all text-center font-bold flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">send</span>
                            Lamar
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16 text-center bg-surface-container-lowest rounded-2xl border border-surface-container">
                    <span class="material-symbols-outlined text-[48px] text-secondary mb-2">work_off</span>
                    <h3 class="font-headline-md text-on-surface font-bold">Belum Ada Lowongan Kerja Aktif</h3>
                    <p class="font-body-md text-secondary mt-1">Silakan hubungi tim kami untuk mendaftarkan resume Anda ke talent pool.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Bottom CTA / Talent Pool Banner -->
    <section class="max-w-7xl mx-auto px-gutter w-full pb-space-xl">
        <div class="relative overflow-hidden bg-primary rounded-2xl p-space-lg md:p-space-xl text-on-primary shadow-xl flex flex-col md:flex-row items-center justify-between gap-space-lg">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="flex items-start gap-space-md z-10">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-[28px]">contact_mail</span>
                </div>
                <div class="flex flex-col gap-1">
                    <h3 class="font-headline-md text-headline-md font-bold text-white">Belum menemukan lowongan yang sesuai?</h3>
                    <p class="font-body-md text-body-md text-on-primary/90 max-w-2xl leading-relaxed">
                        Daftarkan diri Anda ke bank data talent pool LPK GYOKAI. Kami akan menghubungi Anda secara prioritas saat posisi sesuai keahlian teknis Anda dibuka di perusahaan mitra.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-space-sm z-10 w-full md:w-auto shrink-0">
                <a href="{{ route('contact') }}" 
                   class="w-full md:w-auto px-space-xl py-3.5 rounded-lg bg-surface-container-lowest text-primary font-headline-sm text-headline-sm hover:bg-white transition-all shadow-md active:scale-95 text-center font-bold">
                    Kirim Resume / CV
                </a>
            </div>
        </div>
    </section>
</div>

<!-- Job Detail Modal -->
<div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4" id="jobDetailModal" onclick="closeJobDetailModal()">
    <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full p-6 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-on-surface hover:bg-surface-container-high transition-colors" onclick="closeJobDetailModal()" type="button">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-3 border-b border-surface-container pb-4">
                <div class="w-12 h-12 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">business_center</span>
                </div>
                <div>
                    <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-wider">Lowongan Kerja Resmi</span>
                    <h3 class="font-headline-md text-headline-md text-on-surface font-bold" id="modalJobTitle">-</h3>
                </div>
            </div>

            <!-- Job Poster Image Preview -->
            <div id="modalJobImgWrapper" class="hidden rounded-xl overflow-hidden bg-surface-container-low max-h-60 flex items-center justify-center p-2 border border-surface-container">
                <img id="modalJobImg" src="" alt="Poster Lowongan" class="max-h-56 w-auto object-contain rounded" />
            </div>

            <!-- Responsibilities -->
            <div id="modalRespWrapper" class="bg-surface-container-low rounded-xl p-4 border border-surface-container">
                <h4 class="font-headline-sm text-[15px] text-on-surface font-bold flex items-center gap-1.5 mb-2 text-primary">
                    <span class="material-symbols-outlined text-[18px]">checklist</span>
                    Tanggung Jawab Pekerjaan
                </h4>
                <div class="font-body-sm text-body-sm text-secondary whitespace-pre-line leading-relaxed" id="modalJobResp">-</div>
            </div>

            <!-- Qualifications -->
            <div id="modalQualWrapper" class="bg-surface-container-low rounded-xl p-4 border border-surface-container">
                <h4 class="font-headline-sm text-[15px] text-on-surface font-bold flex items-center gap-1.5 mb-2 text-primary">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    Kualifikasi &amp; Persyaratan
                </h4>
                <div class="font-body-sm text-body-sm text-secondary whitespace-pre-line leading-relaxed" id="modalJobQual">-</div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <a id="modalApplyBtn" href="#" target="_blank" rel="noopener noreferrer" class="flex-1 py-3 px-4 rounded-lg bg-primary hover:bg-tertiary text-on-primary font-label-md text-label-md transition-all text-center flex items-center justify-center gap-2 font-bold shadow-md">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Lamar Sekarang via WhatsApp
                </a>
                <button type="button" class="py-3 px-5 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-all font-semibold" onclick="closeJobDetailModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showJobDetailModal(title, imgSrc, resp, qual, applyUrl) {
        document.getElementById('modalJobTitle').innerText = title;

        const imgWrapper = document.getElementById('modalJobImgWrapper');
        const img = document.getElementById('modalJobImg');
        if (imgSrc && !imgSrc.includes('blog-1.png')) {
            img.src = imgSrc;
            imgWrapper.classList.remove('hidden');
        } else {
            imgWrapper.classList.add('hidden');
            img.src = '';
        }

        const respBox = document.getElementById('modalJobResp');
        respBox.innerText = resp || 'Tidak ada keterangan khusus.';

        const qualBox = document.getElementById('modalJobQual');
        qualBox.innerText = qual || 'Tidak ada kualifikasi khusus.';

        document.getElementById('modalApplyBtn').href = applyUrl;
        document.getElementById('jobDetailModal').classList.remove('hidden');
    }

    function closeJobDetailModal() {
        document.getElementById('jobDetailModal').classList.add('hidden');
    }

    function filterJobs() {
        const input = document.getElementById('jobSearchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.job-item');
        let count = 0;

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            if (text.includes(input)) {
                card.style.display = '';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        const badge = document.getElementById('jobCountBadge');
        if (badge) {
            badge.innerText = count + ' Lowongan Tersedia';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeJobDetailModal();
        }
    });
</script>
@endpush
@endsection
