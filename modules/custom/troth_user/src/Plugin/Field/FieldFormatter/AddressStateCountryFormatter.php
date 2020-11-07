<?php

namespace Drupal\troth_user\Plugin\Field\FieldFormatter;

use Drupal\address\Plugin\Field\FieldFormatter\AddressDefaultFormatter;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'address_state_country_default' formatter.
 *
 * @FieldFormatter(
 *   id = "address_state_country_default",
 *   label = @Translation("State and Country"),
 *   field_types = {
 *     "address",
 *   },
 * )
 */
class AddressStateCountryFormatter extends AddressDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function postRender($content, array $element) {
    /** @var \CommerceGuys\Addressing\AddressFormat\AddressFormat $address_format */
    /* See https://docs.drupalcommerce.org/commerce2/developer-guide/customers/addresses/address-display */
    $address_format = $element['#address_format'];
    $locale = $element['#locale'];
    $format_string = '%administrativeArea %country';

    $replacements = [];
    foreach (Element::getVisibleChildren($element) as $key) {
      $child = $element[$key];
      if (isset($child['#placeholder'])) {
        $replacements[$child['#placeholder']] = $child['#value'] ? $child['#markup'] : '';
      }
    }

    $content = self::replacePlaceholders($format_string, $replacements);
    $content = nl2br($content, FALSE);

    return $content;
  }

}
