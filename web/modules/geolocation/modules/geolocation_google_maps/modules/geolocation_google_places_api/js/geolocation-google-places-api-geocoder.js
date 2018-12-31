/**
 * @file
 *   Javascript for the Google Places API geocoder.
 */

/**
 * @property {Object} drupalSettings.geolocation.geocoder.googlePlacesAPI.componentRestrictions
 */

(function ($, Drupal) {
  'use strict';

  /* global google */

  if (typeof Drupal.geolocation.geocoder === 'undefined') {
    return false;
  }

  Drupal.geolocation.geocoder.googlePlacesAPI = {};
  drupalSettings.geolocation.geocoder.googlePlacesAPI = drupalSettings.geolocation.geocoder.googlePlacesAPI || {};

  /**
   * @param {HTMLElement} context Context
   */
  Drupal.geolocation.geocoder.googlePlacesAPI.attach = function (context) {
    var autocomplete = $('input.geolocation-geocoder-google-places-api', context);
    if (!autocomplete.length) {
      return;
    }

    autocomplete.once().autocomplete({
      autoFocus: true,
      source: function (request, response) {
        var autocompleteResults = [];
        var componentRestrictions = {};
        if (typeof drupalSettings.geolocation.geocoder.googlePlacesAPI.componentRestrictions !== 'undefined') {
          componentRestrictions = drupalSettings.geolocation.geocoder.googlePlacesAPI.componentRestrictions;
        }

        Drupal.geolocation.geocoder.googlePlacesAPI.autocompleteService.getPlacePredictions(
          {
            input: request.term,
            componentRestrictions: componentRestrictions
          },

          function (results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
              $.each(results, function (index, result) {
                autocompleteResults.push({
                  value: result.description,
                  place_id: result.place_id,
                  classes: result.types.reverse()
                });
              });
            }
            response(autocompleteResults);
          }
        );
      },

      /**
       * Option form autocomplete selected.
       *
       * @param {Object} event - See jquery doc
       * @param {Object} ui - See jquery doc
       * @param {Object} ui.item - See jquery doc
       */
      select: function (event, ui) {
        Drupal.geolocation.geocoder.googlePlacesAPI.service.getDetails(
          {
            placeId: ui.item.place_id
          },

          function (place, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
              if (typeof place.geometry.location === 'undefined') {
                return;
              }
              Drupal.geolocation.geocoder.resultCallback(place, $(event.target).data('source-identifier').toString());
              $('.geolocation-geocoder-google-places-api-state[data-source-identifier="' + $(event.target).data('source-identifier') + '"]').val(1);
            }
          }
        );
      }
    })
    .autocomplete('instance')
    ._renderItem = function (ul, item) {
      return $('<li></li>')
        .attr('data-value', item.value)
        .append('<div><div class="geolocation-geocoder-item ' + item.classes.join(' ') + '">' + item.label + '</div></div>')
        .appendTo(ul);
    };

    autocomplete.on('input', function () {
      $('.geolocation-geocoder-google-places-api-state[data-source-identifier="' + $(this).data('source-identifier') + '"]').val(0);
      Drupal.geolocation.geocoder.clearCallback($(this).data('source-identifier').toString());
    });
  };

  /**
   * Attach geocoder input for Google places API
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches views geocoder input for Google places API to relevant elements.
   */
  Drupal.behaviors.geolocationGeocoderGooglePlacesApi = {
    attach: function (context) {
      var attribution_block = $('#geolocation-google-places-api-attribution');
      if (attribution_block.length === 0) {
        console.error("Geolocation Google Places API attribution block missing."); // eslint-disable-line no-console
        return;
      }

      Drupal.geolocation.google.addLoadedCallback(function() {
        if (typeof Drupal.geolocation.geocoder.googlePlacesAPI.service === 'undefined') {
          Drupal.geolocation.geocoder.googlePlacesAPI.service = new google.maps.places.PlacesService(attribution_block[0]);
          Drupal.geolocation.geocoder.googlePlacesAPI.autocompleteService = new google.maps.places.AutocompleteService();

          Drupal.geolocation.geocoder.googlePlacesAPI.attach(context);
        }
      });

      // Load Google Maps API and execute all callbacks.
      Drupal.geolocation.google.load();
    }
  };

})(jQuery, Drupal);