(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mtowlCarouselRelatedNodes = {
    attach: function (context, settings) {
      $(context).find('.mt-carousel-related-nodes').once('mtowlCarouselRelatedNodesInit').each(function() {
        $(this).owlCarousel({
          items: 2,
          responsive:{
            0:{
              items:2,
            },
            480:{
              items:2,
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
          autoplay: drupalSettings.tpresume.owlCarouselRelatedNodesInit.owlRelatedNodesAutoPlay,
          autoplayTimeout: drupalSettings.tpresume.owlCarouselRelatedNodesInit.owlRelatedNodesEffectTime,
          nav: true,
          dots: false,
          loop: true,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
