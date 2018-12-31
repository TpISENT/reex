(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mtOwlCarouselArticles = {
    attach: function (context, settings) {
      $(context).find('.mt-carousel-articles').once('mtOwlCarouselArticlesInit').each(function() {
        $(this).owlCarousel({
          items: 2,
          responsive:{
            0:{
              items:1,
            },
            480:{
              items:1,
            },
            768:{
              items:2,
            },
            992:{
              items:2,
            },
            1200:{
              items:3,
            },
            1680:{
              items:3,
            }
          },
          autoplay: drupalSettings.directoryplus.owlCarouselArticlesInit.owlArticlesAutoPlay,
          autoplayTimeout: drupalSettings.directoryplus.owlCarouselArticlesInit.owlArticlesEffectTime,
          nav: true,
          dots: false,
          loop: true,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
