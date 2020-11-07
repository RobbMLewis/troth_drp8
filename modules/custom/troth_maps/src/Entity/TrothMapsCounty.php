<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * Defines the troth_maps_county entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_county",
 *   label = @Translation("County"),
 *   base_table = "troth_maps_county",
 *   entity_keys = {
 *     "id" = "id",
 *     "county" = "county",
 *     "county_full" = "county_full",
 *     "state" = "state",
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
class TrothMapsCounty extends ContentEntityBase implements TrothMapsCountyEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getCounty() {
    return $this->get('county');
  }

  /**
   * {@inheritdoc}
   */
  public function setCounty($county) {
    $this->set('county', $county);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountyFull() {
    return $this->get('county_full');
  }

  /**
   * {@inheritdoc}
   */
  public function setCountyFull($county_full) {
    $this->set('county_full', $county_full);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->get('state');
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

    $fields['county'] = BaseFieldDefinition::create('string')
      ->setLabel(t('County'));

    $fields['county_full'] = BaseFieldDefinition::create('string')
      ->setLabel(t('County Full'));

    $fields['state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('State'));

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Country'));

    $fields['geom'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Geometry'));

    return $fields;
  }

}
