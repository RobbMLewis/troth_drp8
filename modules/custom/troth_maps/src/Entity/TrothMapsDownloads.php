<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the troth_maps_downloads entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_downloads",
 *   label = @Translation("Country Download Dates"),
 *   base_table = "troth_maps_downloads",
 *   entity_keys = {
 *     "id" = "id",
 *     "country" = "country",
 *     "last_update" = "last_update",
 *   },
 *   fieldable = FALSE,
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "storage_schema" = "Drupal\troth_maps\TrothMapsEntityStorageSchema",
 *   },
 *   admin_permission = "administer site configuration",
 * )
 */
class TrothMapsDownloads extends ContentEntityBase implements TrothMapsDownloadsEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getCountry() {
    return $this->get('country');
  }

  /**
   * {@inheritdoc}
   */
  public function setCountry($country) {
    $this->set('country', $country);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastUpdate() {
    return $this->get('last_update');
  }

  /**
   * {@inheritdoc}
   */
  public function setLastUpdate($last_update) {
    $this->set('last_update', $last_update);
    return $this;
  }

  /**
   * Determines the schema for the base_table property defined above.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name of country'));

    $fields['last_update'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Timestamp last updated'));

    return $fields;
  }

}
