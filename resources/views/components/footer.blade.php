<!-- Footer Start -->
<footer class="footer-section">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="map-container wow fadeInLeft" data-wow-delay="0.3s">
                        <div class="map-frame">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5489.423650483964!2d106.44066201183526!3d-6.261330461276567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e42065128d869a1%3A0xac3788bc22eaba78!2sLPK%20Gyokai%20Indonesia!5e1!3m2!1sid!2sid!4v1737734656213!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info-box wow fadeInRight" data-wow-delay="0.3s">
                        <h3>Hubungi Kami</h3>
                        <div class="contact-info-list">
                            <div class="info-item">
                                <div class="icon">
                                    <i class="fa fa-map-marker-alt"></i>
                                </div>
                                <div class="text">
                                    <p>{{ config('gyokai.address') }}</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="text">
                                    <a href="mailto:{{ config('gyokai.contact_email') }}">{{ config('gyokai.contact_email') }}</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="text">
                                    <a href="tel:{{ config('gyokai.phone') }}">{{ config('gyokai.phone') }}</a><br>
                                    <a href="tel:{{ config('gyokai.phone2') }}">{{ config('gyokai.phone2') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="social-links">
                            <a href="{{ config('gyokai.facebook_url') }}" class="social-btn facebook" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="{{ config('gyokai.twitter_url') }}" class="social-btn twitter" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="{{ config('gyokai.instagram_url') }}" class="social-btn instagram" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="{{ config('gyokai.linkedin_url') }}" class="social-btn linkedin" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer-section {
        background: linear-gradient(135deg, #2b1215 0%, #e63946 200%);
        position: relative;
        overflow: hidden;
        color: #fff;
    }

    .footer-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('{{ asset('img/pattern.png') }}') repeat;
        opacity: 0.05;
        pointer-events: none;
    }

    .footer-top {
        padding: 80px 0 40px;
        position: relative;
        z-index: 1;
    }

    .map-container {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(230, 57, 70, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 3px solid rgba(255, 255, 255, 0.1);
    }

    .map-frame {
        position: relative;
        padding-bottom: 75%;
        height: 0;
        overflow: hidden;
    }

    .map-frame iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        transition: all 0.3s ease;
    }

    .map-container:hover .map-frame iframe {
        transform: scale(1.02);
    }

    .contact-info-box {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        height: 100%;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .contact-info-box h3 {
        font-size: 2rem;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
        color: #fff;
    }

    .contact-info-box h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: #e63946;
        border-radius: 3px;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
        padding: 15px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-3px);
    }

    .info-item .icon {
        min-width: 45px;
        height: 45px;
        background: rgba(230, 57, 70, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        transition: all 0.3s ease;
    }

    .info-item:hover .icon {
        background: #e63946;
        transform: rotate(360deg);
    }

    .info-item .icon i {
        font-size: 1.2rem;
        color: #e63946;
        transition: all 0.3s ease;
    }

    .info-item:hover .icon i {
        color: #fff;
    }

    .info-item .text {
        flex: 1;
    }

    .info-item .text p,
    .info-item .text a {
        color: #fff;
        margin: 0;
        font-size: 1rem;
        line-height: 1.6;
        transition: all 0.3s ease;
    }

    .info-item:hover .text a {
        color: #e63946;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        justify-content: center;
    }

    .social-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        color: #fff;
    }

    .social-btn:hover {
        transform: translateY(-5px);
        color: #fff;
    }

    .social-btn.facebook:hover { background: #1877f2; }
    .social-btn.twitter:hover { background: #1da1f2; }
    .social-btn.instagram:hover { background: #e4405f; }
    .social-btn.linkedin:hover { background: #0077b5; }

    .footer-bottom {
        background: rgba(0, 0, 0, 0.2);
        padding: 20px 0;
        position: relative;
        z-index: 1;
    }

    .copyright {
        text-align: center;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .footer-top {
            padding: 50px 0 30px;
        }

        .contact-info-box {
            padding: 25px;
            margin-top: 30px;
        }

        .contact-info-box h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 12px;
            margin-bottom: 15px;
        }

        .info-item .icon {
            min-width: 35px;
            height: 35px;
        }

        .info-item .text p,
        .info-item .text a {
            font-size: 0.9rem;
        }
    }
</style>
<!-- Footer End -->












