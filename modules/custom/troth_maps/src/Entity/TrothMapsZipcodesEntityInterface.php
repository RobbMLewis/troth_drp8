<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps Zipcodes entity entities.
 *
 * @ingroup troth_maps_zipcodes
 */
interface TrothMapsZipcodesEntityInterface {

  /**
   * Gets the TrothMapsZipcodes entity email.
   *
   * @return int
   *   Region ID of the TrothMapsZipcodes entity.
   */
  public function getRegion();

  /**
   * Gets the TrothMapsZipcodes entity zipcode.
   *
   * @return string
   *   Zipcode of the TrothMapsZipcodes entity.
   */
  public function getZipcode();

  /**
   * Sets the TrothMapsZipcodes entity zipcode.
   *
   * @param string $zipcode
   *   The TrothMapsZipcodes entity zipcode.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setZipcode($zipcode);

  /**
   * Gets the TrothMapsZipcodes entity city.
   *
   * @return string
   *   City of the TrothMapsZipcodes entity.
   */
  public function getCity();

  /**
   * Sets the TrothMapsZipcodes entity city.
   *
   * @param string $city
   *   The TrothMapsZipcodes entity city.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCity($city);

  /**
   * Gets the TrothMapsZipcodes entity state.
   *
   * @return string
   *   State of the TrothMapsZipcodes entity.
   */
  public function getState();

  /**
   * Sets the TrothMapsZipcodes entity state.
   *
   * @param string $state
   *   The TrothMapsZipcodes entity state.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setState($state);

  /**
   * Gets the TrothMapsZipcodes entity state_code.
   *
   * @return string
   *   StateCode of the TrothMapsZipcodes entity.
   */
  public function getStateCode();

  /**
   * Sets the TrothMapsZipcodes entity state_code.
   *
   * @param string $state_code
   *   The TrothMapsZipcodes entity state_code.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setStateCode($state_code);

  /**
   * Gets the TrothMapsZipcodes entity county.
   *
   * @return string
   *   County of the TrothMapsZipcodes entity.
   */
  public function getCounty();

  /**
   * Sets the TrothMapsZipcodes entity county.
   *
   * @param string $county
   *   The TrothMapsZipcodes entity county.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCounty($county);

  /**
   * Gets the TrothMapsZipcodes entity county_code.
   *
   * @return string
   *   CountyCode of the TrothMapsZipcodes entity.
   */
  public function getCountyCode();

  /**
   * Sets the TrothMapsZipcodes entity county_code.
   *
   * @param string $county_code
   *   The TrothMapsZipcodes entity county_code.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCountyCode($county_code);

  /**
   * Gets the TrothMapsZipcodes entity community.
   *
   * @return string
   *   Community of the TrothMapsZipcodes entity.
   */
  public function getCommunity();

  /**
   * Sets the TrothMapsZipcodes entity community.
   *
   * @param string $community
   *   The TrothMapsZipcodes entity community.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCommunity($community);

  /**
   * Gets the TrothMapsZipcodes entity community_code.
   *
   * @return string
   *   CommunityCode of the TrothMapsZipcodes entity.
   */
  public function getCommunityCode();

  /**
   * Sets the TrothMapsZipcodes entity community_code.
   *
   * @param string $community_code
   *   The TrothMapsZipcodes entity community_code.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCommunityCode($community_code);

  /**
   * Gets the TrothMapsZipcodes entity country.
   *
   * @return string
   *   Country of the TrothMapsZipcodes entity.
   */
  public function getCountry();

  /**
   * Sets the TrothMapsZipcodes entity country.
   *
   * @param string $country
   *   The TrothMapsZipcodes entity country.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setCountry($country);

  /**
   * Gets the TrothMapsZipcodes entity latitude.
   *
   * @return string
   *   Latitude of the TrothMapsZipcodes entity.
   */
  public function getLatitude();

  /**
   * Gets the TrothMapsZipcodes entity longitude.
   *
   * @return string
   *   Longitude of the TrothMapsZipcodes entity.
   */
  public function getLongitude();

  /**
   * Sets the TrothMapsZipcodes entity longitude.
   *
   * @param string $latitude
   *   The TrothMapsZipcodes entity latitude.
   * @param string $longitude
   *   The TrothMapsZipcodes entity longitude.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setLatLong($latitude, $longitude);

  /**
   * Gets the TrothMapsZipcodes entity regid.
   *
   * @return string
   *   Regid of the TrothMapsZipcodes entity.
   */
  public function getRegid();

  /**
   * Sets the TrothMapsZipcodes entity regid.
   *
   * @param string $regid
   *   The TrothMapsZipcodes entity regid.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setRegid($regid);

  /**
   * Gets the TrothMapsZipcodes entity locregid.
   *
   * @return string
   *   LocRegid of the TrothMapsZipcodes entity.
   */
  public function getLocRegid();

  /**
   * Sets the TrothMapsZipcodes entity locregid.
   *
   * @param string $locregid
   *   The TrothMapsZipcodes entity locregid.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setLocRegid($locregid);

  /**
   * Gets the TrothMapsZipcodes entity geom.
   *
   * @return string
   *   Geom of the TrothMapsZipcodes entity.
   */
  public function getGeom();

  /**
   * Sets the TrothMapsZipcodes entity geom.
   *
   * @param string $format
   *   The TrothMapsZipcodes entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function getGeomFormat($format);

  /**
   * Sets the TrothMapsZipcodes entity geom.
   *
   * @param string $geom
   *   The TrothMapsZipcodes entity geom.
   * @param string $format
   *   The TrothMapsZipcodes entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsZipcodesEntityInterface
   *   The called TrothMapsZipcodes entity entity.
   */
  public function setGeom($geom, $format);

}
