<?php

namespace Drupal\troth_user\Plugin\EntityReferenceSelection;

use Drupal\Core\Database\Query\SelectInterface;
use Drupal\user\Plugin\EntityReferenceSelection\UserSelection;

/**
 * Enables product variation selection by title or SKU.
 *
 * @EntityReferenceSelection(
 *   id = "default:troth_user",
 *   label = @Translation("Troth User selection"),
 *   entity_types = {"user"},
 *   group = "default",
 *   weight = 10
 * )
 */
class TrothUserSelection extends UserSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);
    $matches = explode(' ', $match);
    $or = $query->orConditionGroup();
    $or->condition('name', $match, $match_operator);
    if (count($matches) > 1) {
      $or->condition('field_profile_first_name', $matches[0], $match_operator);
      $or->condition('field_profile_last_name', $matches[1], $match_operator);
      $or->condition('uid',$matches[0],$match_operator);
    }
    else {
      $or->condition('field_profile_first_name', $match, $match_operator);
      $or->condition('field_profile_last_name', $match, $match_operator);
      $or->condition('uid',$matches[0],$match_operator);
    }
    $or->condition('field_profile_troth_name', $match, $match_operator);
    if ($this->getConfiguration()['include_anonymous']) {
      $or->condition('uid', 0, '=');
    }

    $query->condition($or);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function entityQueryAlter(SelectInterface $query) {
    // The parent entityQueryAlter brings in Anonymous
    // We dont't want that user, so we will not bring in the original
    // alter query.
    // parent::entityQueryAlter($query);
    $conditions = &$query->conditions();
    // We need to delete the regular name condition.
    foreach ($conditions as $key => $condition) {
      if ($key !== '#conjunction' && is_string($condition['field']) && $condition['field'] === 'users_field_data.name') {
        // Remove the condition.
        unset($conditions[$key]);
      }
    }
  }

}
