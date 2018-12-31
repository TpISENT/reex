<?php

namespace Drupal\geolocation_google_maps\Plugin\geolocation\MapProvider;

use Drupal\geolocation_google_maps\GoogleMapsProviderBase;
use Drupal\Core\Render\BubbleableMetadata;

/**
 * Provides Google Maps.
 *
 * @MapProvider(
 *   id = "google_maps",
 *   name = @Translation("Google Maps"),
 *   description = @Translation("You do require an API key for this plugin to work."),
 * )
 */
class GoogleMaps extends GoogleMapsProviderBase {

  /**
   * Google map max zoom level.
   *
   * @var int
   */
  public static $MAXZOOMLEVEL = 18;

  /**
   * Google map min zoom level.
   *
   * @var int
   */
  public static $MINZOOMLEVEL = 0;

  /**
   * {@inheritdoc}
   */
  public static $GOOGLEMAPSAPIURLPATH = '/maps/api/js';

  /**
   * {@inheritdoc}
   */
  public function getGoogleMapsApiParameters(array $additional_parameters = []) {
    $parameters = parent::getGoogleMapsApiParameters($additional_parameters);
    $parameters['callback'] = 'Drupal.geolocation.google.load';

    if (!empty($parameters['client'])) {
      unset($parameters['key']);
    }

    return $parameters;
  }

  /**
   * {@inheritdoc}
   */
  public static function getDefaultSettings() {
    return array_replace_recursive(
      parent::getDefaultSettings(),
      [
        'minZoom' => static::$MINZOOMLEVEL,
        'maxZoom' => static::$MAXZOOMLEVEL,
        'rotateControl' => FALSE,
        'scrollwheel' => TRUE,
        'disableDoubleClickZoom' => FALSE,
        'preferScrollingToZooming' => FALSE,
        'gestureHandling' => 'auto',
        'map_features' => [
          'marker_infowindow' => [
            'enabled' => TRUE,
          ],
          'control_locate' => [
            'enabled' => TRUE,
          ],
          'control_zoom' => [
            'enabled' => TRUE,
          ],
          'control_maptype' => [
            'enabled' => TRUE,
          ],
        ],
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function getControlPositions() {
    return [
      'LEFT_TOP' => t('Left top'),
      'LEFT_CENTER' => t('Left center'),
      'LEFT_BOTTOM' => t('Left bottom'),
      'TOP_LEFT' => t('Top left'),
      'TOP_CENTER' => t('Top center'),
      'TOP_RIGHT' => t('Top right'),
      'RIGHT_TOP' => t('Right top'),
      'RIGHT_CENTER' => t('Right center'),
      'RIGHT_BOTTOM' => t('Right bottom'),
      'BOTTOM_LEFT' => t('Bottom left'),
      'BOTTOM_CENTER' => t('Bottom center'),
      'BOTTOM_RIGHT' => t('Bottom right'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettings(array $settings) {
    $settings = parent::getSettings($settings);

    $settings['rotateControl'] = (bool) $settings['rotateControl'];
    $settings['scrollwheel'] = (bool) $settings['scrollwheel'];
    $settings['disableDoubleClickZoom'] = (bool) $settings['disableDoubleClickZoom'];
    $settings['preferScrollingToZooming'] = (bool) $settings['preferScrollingToZooming'];

    $settings['minZoom'] = (int) $settings['minZoom'];
    $settings['maxZoom'] = (int) $settings['maxZoom'];

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $settings, array $parents = []) {
    $settings = $this->getSettings($settings);
    $parents_string = '';
    if ($parents) {
      $parents_string = implode('][', $parents) . '][';
    }

    $form = parent::getSettingsForm($settings, $parents);

    $form['zoom']['#min'] = static::$MINZOOMLEVEL;
    $form['zoom']['#max'] = static::$MAXZOOMLEVEL;
    $form['maxZoom'] = [
      '#group' => $parents_string . 'general_settings',
      '#type' => 'number',
      '#min' => static::$MINZOOMLEVEL,
      '#max' => static::$MAXZOOMLEVEL,
      '#title' => $this->t('Max Zoom level'),
      '#description' => $this->t('The maximum zoom level of the map. If omitted, or set to null, the default maximum zoom from the current map type is used instead.'),
      '#default_value' => $settings['maxZoom'],
      '#process' => [
        ['\Drupal\Core\Render\Element\RenderElement', 'processGroup'],
      ],
      '#pre_render' => [
        ['\Drupal\Core\Render\Element\Number', 'preRenderNumber'],
        ['\Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'],
      ],
    ];
    $form['minZoom'] = [
      '#group' => $parents_string . 'general_settings',
      '#type' => 'number',
      '#min' => static::$MINZOOMLEVEL,
      '#max' => static::$MAXZOOMLEVEL,
      '#title' => $this->t('Min Zoom level'),
      '#description' => $this->t('The minimum zoom level of the map. If omitted, or set to null, the default minimum zoom from the current map type is used instead.'),
      '#default_value' => $settings['minZoom'],
      '#process' => [
        ['\Drupal\Core\Render\Element\RenderElement', 'processGroup'],
      ],
      '#pre_render' => [
        ['\Drupal\Core\Render\Element\Number', 'preRenderNumber'],
        ['\Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'],
      ],
    ];

    $form['control_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Controls'),
    ];
    $form['rotateControl'] = [
      '#group' => $parents_string . 'control_settings',
      '#type' => 'checkbox',
      '#title' => $this->t('Rotate control'),
      '#description' => $this->t('Show rotate control.'),
      '#default_value' => $settings['rotateControl'],
    ];

    $form['behavior_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Behavior'),
    ];
    $form['scrollwheel'] = [
      '#group' => $parents_string . 'behavior_settings',
      '#type' => 'checkbox',
      '#title' => $this->t('Scrollwheel'),
      '#description' => $this->t('Allow the user to zoom the map using the scrollwheel.'),
      '#default_value' => $settings['scrollwheel'],
    ];
    $form['gestureHandling'] = [
      '#group' => $parents_string . 'behavior_settings',
      '#type' => 'select',
      '#title' => $this->t('Gesture Handling'),
      '#default_value' => $settings['gestureHandling'],
      '#description' => $this->t('Define how to handle interactions with map on mobile. Read the <a href=":introduction">introduction</a> for handling or the <a href=":details">details</a>, <i>available as of v3.27 / Nov. 2016</i>.', [
        ':introduction' => 'https://googlegeodevelopers.blogspot.de/2016/11/smart-scrolling-comes-to-mobile-web-maps.html',
        ':details' => 'https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions',
      ]),
      '#options' => [
        'auto' => $this->t('auto (default)'),
        'cooperative' => $this->t('cooperative'),
        'greedy' => $this->t('greedy'),
        'none' => $this->t('none'),
      ],
      '#process' => [
        ['\Drupal\Core\Render\Element\RenderElement', 'processGroup'],
        ['\Drupal\Core\Render\Element\Select', 'processSelect'],
      ],
      '#pre_render' => [
        ['\Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'],
      ],
    ];
    $form['preferScrollingToZooming'] = [
      '#group' => $parents_string . 'behavior_settings',
      '#type' => 'checkbox',
      '#title' => $this->t('Require the user to click the map once to zoom, to ease scrolling behavior.'),
      '#description' => $this->t('Note: this is only relevant, when the Scrollwheel option is enabled.'),
      '#default_value' => $settings['preferScrollingToZooming'],
    ];
    $form['disableDoubleClickZoom'] = [
      '#group' => $parents_string . 'behavior_settings',
      '#type' => 'checkbox',
      '#title' => $this->t('Disable double click zoom'),
      '#description' => $this->t('Disables the double click zoom functionality.'),
      '#default_value' => $settings['disableDoubleClickZoom'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRenderArray(array $render_array, array $map_settings, array $context = []) {

    $map_settings = $this->getSettings($map_settings);

    $render_array['#attached'] = BubbleableMetadata::mergeAttachments(
      empty($render_array['#attached']) ? [] : $render_array['#attached'],
      [
        'library' => [
          'geolocation_google_maps/googlemapsapi',
        ],
        'drupalSettings' => [
          'geolocation' => [
            'google_map_url' => $this->getGoogleMapsApiUrl(),
            'maps' => [
              $render_array['#id'] => [
                'settings' => [
                  'google_map_settings' => $map_settings,
                ],
              ],
            ],
          ],
        ],
      ]
    );

    $render_array = parent::alterRenderArray($render_array, $map_settings, $context);

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function alterCommonMap(array $render_array, array $map_settings, array $context) {
    $render_array['#attached'] = BubbleableMetadata::mergeAttachments(
      empty($render_array['#attached']) ? [] : $render_array['#attached'],
      [
        'library' => [
          'geolocation_google_maps/commonmap.google',
        ],
      ]
    );

    return $render_array;
  }

}