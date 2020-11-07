<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps Country entity entities.
 *
 * @ingroup troth_maps_country
 */
interface TrothMapsCountryEntityInterface {

  /**
   * Gets the TrothMapsCountry entity country.
   *
   * @return string
   *   country of the TrothMapsCountry entity.
   */
  public function getCountry();

  /**
   * Sets the TrothMapsCountry entity country.
   *
   * @param string $country
   *   The TrothMapsCountry entity country.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountryEntityInterface
   *   The called TrothMapsCountry entity entity.
   */
  public function setCountry($country);

  /**
   * Gets the TrothMapsCountry entity country_abr.
   *
   * @return string
   *   country_abr of the TrothMapsCountry entity.
   */
  public function getCountryAbr();

  /**
   * Sets the TrothMapsCountry entity country_abr.
   *
   * @param string $country_abr
   *   The TrothMapsCountry entity country_abr.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountryEntityInterface
   *   The called TrothMapsCountry entity entity.
   */
  public function setCountryAbr($country_abr);

  /**
   * Gets the TrothMapsCountry entity geom.
   *
   * @return string
   *   Geom of the TrothMapsCountry entity.
   */
  public function getGeom();

  /**
   * Sets the TrothMapsCountry entity geom.
   *
   * @param string $format
   *   The TrothMapsCountry entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountryEntityInterface
   *   The called TrothMapsCountry entity entity.
   */
  public function getGeomFormat($format);

  /**
   * Sets the TrothMapsCountry entity geom.
   *
   * @param string $geom
   *   The TrothMapsCountry entity geom.
   * @param string $format
   *   The TrothMapsCountry entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsCountryEntityInterface
   *   The called TrothMapsCountry entity entity.
   */
  public function setGeom($geom, $format);

}
