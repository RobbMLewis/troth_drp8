<?php

namespace Drupal\troth_maps\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * Enables product variation selection by title or SKU.
 *
 * @EntityReferenceSelection(
 *   id = "default:troth_maps_regions",
 *   label = @Translation("Troth Maps Regions selection"),
 *   entity_types = {"troth_maps_regions"},
 *   group = "default",
 *   weight = 1
 * )
 */
class RegionsSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $configuration = $this->getConfiguration();

    $query = $this->entityTypeManager->getStorage('troth_maps_regions')->getQuery();

    if (isset($match)) {
      $query->condition('region_name', $match, $match_operator);
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $query = $this->buildEntityQuery($match, $match_operator);
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    $options = [];
    $entities = $this->entityTypeManager->getStorage('troth_maps_regions')->loadMultiple($result);
    /** @var \Drupal\troth_maps\Entity\TrothMapsRegions $entity */
    foreach ($entities as $entity_id => $entity) {
      $options[$entity_id][$entity_id] = Html::escape($entity->getRegionName());
    }

    return $options;
  }

}
