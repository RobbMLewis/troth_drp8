<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\geofield\GeoPHP\GeoPHPWrapper;

/**
 * Defines the troth_maps_zipcodes entity.
 *
 * @ContentEntityType(
 *   id = "troth_maps_zipcodes",
 *   label = @Translation("Zipcodes"),
 *   base_table = "troth_maps_zipcodes",
 *   entity_keys = {
 *     "id" = "id",
 *     "zipcode" = "zipcode",
 *     "city" = "city",
 *     "state" = "state",
 *     "state_code" = "state_code",
 *     "county" = "county",
 *     "county_code" = "county_code",
 *     "community" = "community",
 *     "community_code" = "community_code",
 *     "country" = "country",
 *     "latlong" = "latlong",
 *     "regid" = "regid",
 *     "locregid" = "locregid",
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
class TrothMapsZipcodes extends ContentEntityBase implements TrothMapsZipcodesEntityInterface {

  /**
   * {@inheritdoc}
   */
  public function getRegion() {
    $regid = $this->get('regid');
    $locregid = $this->get('locregid');
    if ($locregid > 0) {
      return $locregid;
    }
    else {
      return $regid;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getZipcode() {
    return $this->get('zipcode');
  }

  /**
   * {@inheritdoc}
   */
  public function setZipcode($zipcode) {
    $this->set('zipcode', $zipcode);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCity() {
    return $this->get('city');
  }

  /**
   * {@inheritdoc}
   */
  public function setCity($city) {
    $this->set('city', $city);
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
  public function getStateCode() {
    return $this->get('state_code');
  }

  /**
   * {@inheritdoc}
   */
  public function setStateCode($state_code) {
    $this->set('state_code', $state_code);
    return $this;
  }

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
  public function getCountyCode() {
    return $this->get('county_code');
  }

  /**
   * {@inheritdoc}
   */
  public function setCountyCode($county_code) {
    $this->set('county_code', $county_code);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCommunity() {
    return $this->get('community');
  }

  /**
   * {@inheritdoc}
   */
  public function setCommunity($community) {
    $this->set('community', $community);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCommunityCode() {
    return $this->get('community_code');
  }

  /**
   * {@inheritdoc}
   */
  public function setCommunityCode($community_code) {
    $this->set('community_code', $community_code);
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
  public function getLatitude() {
    return $this->get('latlong')->lat;
  }

  /**
   * {@inheritdoc}
   */
  public function getLongitude() {
    return $this->get('latlong')->lon;
  }

  /**
   * {@inheritdoc}
   */
  public function setLatLong($latitude, $longitude) {
    $value = \Drupal::service('geofield.wkt_generator')->WktBuildPoint([
      $latitude,
      $longitude,
    ]);
    $this->set('latlong', $value);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegid() {
    return $this->get('regid');
  }

  /**
   * {@inheritdoc}
   */
  public function setRegid($regid) {
    $this->set('regid', $regid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocRegid() {
    return $this->get('locregid');
  }

  /**
   * {@inheritdoc}
   */
  public function setLocRegid($locregid) {
    $this->set('locregid', $locregid);
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

    $fields['zipcode'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Zipcode'));

    $fields['city'] = BaseFieldDefinition::create('string')
      ->setLabel(t('City'));

    $fields['state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('State'));

    $fields['state_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('State Code'));

    $fields['county'] = BaseFieldDefinition::create('string')
      ->setLabel(t('County'));

    $fields['county_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('County Code'));

    $fields['community'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Community'));

    $fields['community_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Community Code'));

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Country'));

    $fields['latlong'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('latlong'));

    $fields['regid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Region ID'));

    $fields['locregid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Local Region ID'));

    $fields['geom'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Geometry'));

    return $fields;
  }

}
