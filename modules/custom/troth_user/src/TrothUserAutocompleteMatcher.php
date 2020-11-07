<?php

namespace Drupal\troth_user;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\EntityAutocompleteMatcher;

/**
 * Custom AutocompleteMatcher.
 */
class TrothUserAutocompleteMatcher extends EntityAutocompleteMatcher {

  /**
   * Gets matched labels based on a given search string.
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {

    $matches = [];
    $options = $selection_settings + [
      'target_type' => $target_type,
      'handler' => $selection_handler,
    ];
    $handler = $this->selectionManager->getInstance($options);

    if (isset($string)) {
      // Get an array of matching entities.
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $match_limit = isset($selection_settings['match_limit']) ? (int) $selection_settings['match_limit'] : 10;
      $entity_labels = $handler->getReferenceableEntities($string, $match_operator, $match_limit);
      foreach ($entity_labels as $values) {
        if ($target_type == 'user') {
          foreach ($values as $entity_id => $label) {
            $entity = \Drupal::entityTypeManager()->getStorage($target_type)->load($entity_id);
            $name = $entity->name->value;
            $fname = $entity->field_profile_first_name->value;
            $lname = $entity->field_profile_last_name->value;
            $tname = $entity->field_profile_troth_name->value;
            $key = "$label ($entity_id)";
            $label = "$name ($entity_id): $fname $lname ($tname)";
            // Strip things like starting/trailing white spaces, line breaks and
            // tags.
            $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(strip_tags($key))));
            // Names containing commas or quotes must be wrapped in quotes.
            $key = Tags::encode($key);

            $matches[] = ['value' => $key, 'label' => $label];
          }
        }
        else {
          foreach ($values as $entity_id => $label) {
            $key = "$label ($entity_id)";
            $label = $key;
            // Strip things like starting/trailing white spaces, line breaks and
            // tags.
            $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(strip_tags($key))));
            // Names containing commas or quotes must be wrapped in quotes.
            $key = Tags::encode($key);

            $matches[] = ['value' => $key, 'label' => $label];
          }
        }
      }
    }

    return $matches;
  }

}
