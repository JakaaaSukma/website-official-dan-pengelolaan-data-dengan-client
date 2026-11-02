(function ($) {
    "use strict";

    // Redirect to YouTube when facade is clicked
    window.loadYT = function(el) {
        const videoId = el.dataset.videoid;
        if (videoId) {
            window.open(`https://www.youtube.com/watch?v=${videoId}`, '_blank');
        }
    };

    // Add event listeners to all yt-facade elements on page load
    $(document).ready(function() {
        $(document).on('click', '.yt-facade', function() {
            window.loadYT(this);
        });

        // Clone client marquee tracks for infinite scroll
        const marqueeTop = $('#marquee-top');
        const marqueeBottom = $('#marquee-bottom');
        
        if (marqueeTop.length) {
            const topContent = marqueeTop.html();
            marqueeTop.html(topContent + topContent + topContent);
        }
        
        if (marqueeBottom.length) {
            const bottomContent = marqueeBottom.html();
            marqueeBottom.html(bottomContent + bottomContent + bottomContent);
        }
    });

    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.navbar').addClass('sticky-top shadow-sm');
        } else {
            $('.navbar').removeClass('sticky-top shadow-sm');
        }
    });


    // Hero Header carousel
    $(".header-carousel").owlCarousel({
        animateOut: 'slideOutDown',
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
    });


    // International carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        items: 1,
        smartSpeed: 1500,
        dots: true,
        dotsData: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ]
    });


    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $('.btn-play').click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $('#videoModal').on('shown.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
        })

        $('#videoModal').on('hide.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc);
        })
    });


    // testimonial carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        dots: true,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
    });

    $(".news-slider").each(function(){
        var $el = $(this);
        var itemCount = $el.children().length;
        var desktopItems = itemCount >= 4 ? 4 : itemCount; // tampilkan 4 item di desktop jika tersedia
        $el.owlCarousel({
            items: desktopItems,
            margin: 16,
            nav: itemCount > 1,
            navText: [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ],
            loop: itemCount > desktopItems,
            rewind: true,
            smartSpeed: 220,
            autoplay: itemCount > 1,
            autoplayTimeout: 2200,
            autoplayHoverPause: true,
            dots: false,
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                768: { items: 3 },
                992: { items: desktopItems }
            }
        });
        // Pastikan klik ikon panah memicu geser
        $el.on('click', '.owl-nav .owl-prev', function(e){
            e.preventDefault();
            $el.trigger('prev.owl.carousel');
        });
        $el.on('click', '.owl-nav .owl-next', function(e){
            e.preventDefault();
            $el.trigger('next.owl.carousel');
        });
    });

    $(".video-slider").each(function(){
        var $el = $(this);
        var itemCount = $el.children().length;
        var desktopItems = itemCount >= 4 ? 4 : itemCount;
        $el.owlCarousel({
            items: desktopItems,
            margin: 20,
            nav: itemCount > 1,
            navText: [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ],
            loop: itemCount > desktopItems,
            rewind: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            dots: true,
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                768: { items: 3 },
                992: { items: desktopItems }
            }
        });

        // Event listeners untuk tombol navigasi custom
        $el.on('click', '.owl-nav .owl-prev', function(e){
            e.preventDefault();
            $el.trigger('prev.owl.carousel');
        });
        $el.on('click', '.owl-nav .owl-next', function(e){
            e.preventDefault();
            $el.trigger('next.owl.carousel');
        });
    });

    
    
   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    function myMove() {
        let id = null;
        const elem = document.getElementById("animate");   
        let pos = 0;
        clearInterval(id);
        id = setInterval(frame, 5);
        function frame() {
          if (pos == 350) {
            clearInterval(id);
          } else {
            pos++; 
            elem.style.top = pos + "px"; 
            elem.style.left = pos + "px"; 
          }
        }
      }


   

    // Lazy Load Video (Dibatalkan)
    // $(document).on('click', '.video-container', function(e) {
    //     e.preventDefault();
    //     var $container = $(this);
    //     var videoUrl = $container.attr('data-video-url');
        
    //     if (videoUrl && $container.find('iframe').length === 0) {
    //         var separator = videoUrl.indexOf('?') !== -1 ? '&' : '?';
    //         var finalUrl = videoUrl + separator + 'autoplay=1&rel=0';
            
    //         var iframe = $('<iframe>')
    //             .attr('src', finalUrl)
    //             .attr('title', 'YouTube video player')
    //             .attr('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture')
    //             .attr('allowfullscreen', true)
    //             .css({
    //                 'border': '0',
    //                 'width': '100%',
    //                 'height': '100%',
    //                 'position': 'absolute',
    //                 'top': '0',
    //                 'left': '0'
    //             });
                
    //         $container.html(iframe);
    //     }
    // });

})(jQuery);

