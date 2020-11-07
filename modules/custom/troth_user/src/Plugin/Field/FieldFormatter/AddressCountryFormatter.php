<?php

namespace Drupal\troth_user\Plugin\Field\FieldFormatter;

use Drupal\address\Plugin\Field\FieldFormatter\AddressDefaultFormatter;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'address_country_only_default' formatter.
 *
 * @FieldFormatter(
 *   id = "address_country_only_default",
 *   label = @Translation("Country Only"),
 *   field_types = {
 *     "address",
 *   },
 * )
 */
class AddressCountryFormatter extends AddressDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function postRender($content, array $element) {
    /** @var \CommerceGuys\Addressing\AddressFormat\AddressFormat $address_format */
    /* See https://docs.drupalcommerce.org/commerce2/developer-guide/customers/addresses/address-display */
    $address_format = $element['#address_format'];
    $locale = $element['#locale'];
    $format_string = '%country';
    // $format_string = $address_format->getFormat();
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
