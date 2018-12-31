(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mapLink = {
    attach: function (context, settings) {
      $(context).find('.map-link--smooth-scroll').once('mapLinkInit').click(function(e) {
        var adminHeight = parseInt($('body').css('paddingTop')) || 0;
        var anchorDestination = '#' + $('.geolocation-map-wrapper').attr('id');
        e.preventDefault();
        $('html, body').animate({
          //scrollTop: $($(this).attr("href")).offset().top
          scrollTop: $(anchorDestination).offset().top - 85 - adminHeight
        }, 1000);
        if (history.pushState) {
          history.pushState(null, null, anchorDestination);
        } else {
          location.hash = anchorDestination;
        }
      });

    }
  };
})(jQuery, Drupal, drupalSettings);
