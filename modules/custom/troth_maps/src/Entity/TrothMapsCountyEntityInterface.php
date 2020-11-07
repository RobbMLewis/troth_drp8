<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps County entity entities.
 *
 * @ingroup troth_maps_county
 */
interface TrothMapsCountyEntityInterface {

  /**
   * Gets the TrothMapsCounty entity county.
   *
   * @return string
   *   County of the TrothMapsCounty entity.
   */
  public function getCounty();

  /**
   * Sets the TrothMapsCounty entity county.
   *
   * @param string $county
   *   The TrothMapsCounty entity county.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function setCounty($county);

  /**
   * Gets the TrothMapsCounty entity county_full.
   *
   * @return string
   *   CountyCode of the TrothMapsCounty entity.
   */
  public function getCountyFull();

  /**
   * Sets the TrothMapsCounty entity county_full.
   *
   * @param string $county_full
   *   The TrothMapsCounty entity county_full.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function setCountyFull($county_full);

  /**
   * Gets the TrothMapsCounty entity state.
   *
   * @return string
   *   State of the TrothMapsCounty entity.
   */
  public function getState();

  /**
   * Sets the TrothMapsCounty entity state.
   *
   * @param string $state
   *   The TrothMapsCounty entity state.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function setState($state);

  /**
   * Gets the TrothMapsCounty entity country.
   *
   * @return string
   *   Country of the TrothMapsCounty entity.
   */
  public function getCountry();

  /**
   * Sets the TrothMapsCounty entity country.
   *
   * @param string $country
   *   The TrothMapsCounty entity country.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function setCountry($country);

  /**
   * Gets the TrothMapsCounty entity geom.
   *
   * @return string
   *   Geom of the TrothMapsCounty entity.
   */
  public function getGeom();

  /**
   * Sets the TrothMapsCounty entity geom.
   *
   * @param string $format
   *   The TrothMapsCounty entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function getGeomFormat($format);

  /**
   * Sets the TrothMapsCounty entity geom.
   *
   * @param string $geom
   *   The TrothMapsCounty entity geom.
   * @param string $format
   *   The TrothMapsCounty entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountyEntityInterface
   *   The called TrothMapsCounty entity entity.
   */
  public function setGeom($geom, $format);

}
