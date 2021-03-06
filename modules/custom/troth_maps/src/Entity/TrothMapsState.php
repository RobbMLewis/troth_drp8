<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * Defines the troth_maps_state entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_state",
 *   label = @Translation("State Definitions"),
 *   base_table = "troth_maps_state",
 *   entity_keys = {
 *     "id" = "id",
 *     "state" = "state",
 *     "state_abr" = "state_abr",
 *     "state_no" = "state_no",
 *     "country" = "country",
 *     "geom" = "geom",
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
class TrothMapsState extends ContentEntityBase implements TrothMapsStateEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->get('state')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setState($state) {
    $this->set('state', $state);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStateAbr() {
    return $this->get('state_abr')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStateAbr($state_abr) {
    $this->set('state_abr', $state_abr);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStateNo() {
    return $this->get('state_no')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStateNo($state_no) {
    $this->set('state_no', $state_no);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry() {
    return $this->get('country')->value;
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
  public function getGeom() {
    return $this->get('geom')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getGeomFormat($format = 'kml') {
    $geom = $this->get('geom')->value;
    $geophp_wrapper = new GeoPHPWrapper();
    $geometry = $geophp_wrapper->load($geom);
    return $geometry->out($format);
  }

  /**
   * {@inheritdoc}
   */
  public function setGeom($geom, $format = 'wkt') {
    if ($format != 'wkt') {
      $geophp_wrapper = new GeoPHPWrapper();
      $geometry = $geophp_wrapper->load($geom, $format);
      $this->set('geom', $geometry->out('wkt'));
    }
    else {
      $this->set('geom', $geom);
    }
    return $this;
  }

  /**
   * Determines the schema for the base_table property defined above.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name of state'));

    $fields['state_abr'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Abbreviation for State'));

    $fields['state_no'] = BaseFieldDefinition::create('string')
      ->setLabel(t('State Number internal to shape files.'));

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Country that state is located in'));

    $fields['geom'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Geometry'));

    return $fields;
  }

}
