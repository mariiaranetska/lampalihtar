$(window).on("load", function(){

    /* scroll */

    $("a[href^='#']").click(function(){
        var _href = $(this).attr("href");
        $("html, body").animate({scrollTop: $(_href).offset().top+"px"});
        return false;
    });




    $('.owl-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout: 4000,
        autoplaySpeed: 2000,
        loop: true,
        nav: true,
        dots: true,
        margin: 4,
        center: true,
        navText : ["",""],
        responsive:{
            0:{
                items:1
            },
            1100:{
                items:3
            }
        }

    });

});

