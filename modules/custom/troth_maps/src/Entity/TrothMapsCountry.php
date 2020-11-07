<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * Defines the troth_maps_country entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_country",
 *   label = @Translation("Country Definitions"),
 *   base_table = "troth_maps_country",
 *   entity_keys = {
 *     "id" = "id",
 *     "country" = "country",
 *     "country_abr" = "country_abr",
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
class TrothMapsCountry extends ContentEntityBase implements TrothMapsCountryEntityInterface {

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
  public function getCountryAbr() {
    return $this->get('country_abr');
  }

  /**
   * {@inheritdoc}
   */
  public function setCountryAbr($country_abr) {
    $this->set('country_abr', $country_abr);
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

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name of country'));

    $fields['country_abr'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Abbreviation for Country'));

    $fields['geom'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Geometry'));

    return $fields;
  }

}
