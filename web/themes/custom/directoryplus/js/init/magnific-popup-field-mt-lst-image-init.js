(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mtMagnificPopupFieldLstImage = {
    attach: function (context, settings) {
      $(context).find(".field--name-field-mt-lst-image a.image-popup").once('mtMagnificPopupFieldLstImageInit').magnificPopup({
        type:"image",
        removalDelay: 300,
        mainClass: "mfp-fade",
        gallery: {
          enabled: true, // set to true to enable gallery
        },
        image: {
          titleSrc: function(item) {
            return item.el.closest('.overlay-container').children()[1].title || '';
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
