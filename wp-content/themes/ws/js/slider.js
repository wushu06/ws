jQuery(document).ready(function($){

    var href = path.templateUrl;
    var left = href+'/img/icons/left-arrow.png';
    var right = href+'/img/icons/right-arrow.png';



    var arrowLeft = "<div class='arrow left'><img src='"+left+"'></div>";
    var arrowRight = "<div class='arrow right'><img src='"+right+"'></div>";


    $slickGreen = false;
    function greenSlider(){
        var $heroslide = $('.hero-slide');
        if($(window).width() > 768){
            if(!$slickGreen){
                $heroslide.on('init', function (slick) {
                    $('.hero-slide').show();
                }).slick({
                    cssEase: 'linear',
                    prevArrow: arrowLeft,
                    nextArrow: arrowRight,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    fade: true,
                    pauseOnHover:false

                });
                $('.slick-mobile').hide();

                $slickGreen = true;
            }else {
                // $('.slick-mobile').slick('unslick');

            }
        } else if($(window).width() < 768){
            if(!$slickGreen){
                // $heroslide.slick('unslick');
                $slickGreen = false;
                $('.hero-slide').hide();
                $('.slick-mobile').on('init', function (slick) {
                    $('.slick-mobile').show();
                }).slick({
                    cssEase: 'linear',
                    prevArrow: arrowLeft,
                    nextArrow: arrowRight,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    fade: true,
                    pauseOnHover:false
                });

            }


        }
    };

    $(document).ready(function(){

        greenSlider();
    });




    var $designsslide = $('.designs-slide')
        .on('init', function (slick) {
            $('.designs-slide').show();

        }).slick({
            arrows: true,
            slidesToShow: 5,
            slidesToScroll: 3,
            cssEase: 'linear',
            prevArrow: arrowLeft,
            nextArrow: arrowRight,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1140,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 3,
                    }
                },
                {
                    breakpoint: 960,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                    }
                },
                {
                    breakpoint: 720,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                    }
                },
                {
                    breakpoint: 560,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ]
        });

    $('.designs-slide input.radio-designs').change(function () {
        reinitSlick();
    });

    var reinitSlick = function() {
        $designsslide.slick('slickSetOption', {
            'autoplay': false
        }, false);
    }

    var $blockssliderul = $('.blocks-3-slider ul')
        .on('init', function (slick) {
            $('.blocks-3-slider ul').show();

        }).slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            cssEase: 'linear',
            arrows: true,
            prevArrow: arrowLeft,
            nextArrow: arrowRight,
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });
    $('.block-product-grid .slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        cssEase: 'linear',
        prevArrow: arrowLeft,
        nextArrow: arrowRight,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    // arrows: false
                }
            }
        ]
    });
});