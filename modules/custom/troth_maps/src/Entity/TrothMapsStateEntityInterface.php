<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps State entity entities.
 *
 * @ingroup troth_maps_state
 */
interface TrothMapsStateEntityInterface {

  /**
   * Gets the TrothMapsState entity state.
   *
   * @return string
   *   state of the TrothMapsState entity.
   */
  public function getState();

  /**
   * Sets the TrothMapsState entity state.
   *
   * @param string $state
   *   The TrothMapsState entity state.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function setState($state);

  /**
   * Gets the TrothMapsState entity state_abr.
   *
   * @return string
   *   state_abr of the TrothMapsState entity.
   */
  public function getStateAbr();

  /**
   * Sets the TrothMapsState entity state_abr.
   *
   * @param string $state_abr
   *   The TrothMapsState entity state_abr.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function setStateAbr($state_abr);

  /**
   * Gets the TrothMapsState entity state_abr.
   *
   * @return string
   *   state_no of the TrothMapsState entity.
   */
  public function getStateNo();

  /**
   * Sets the TrothMapsState entity state_no.
   *
   * @param string $state_no
   *   The TrothMapsState entity state_no.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function setStateNo($state_no);

  /**
   * Gets the TrothMapsState entity country.
   *
   * @return string
   *   country of the TrothMapsState entity.
   */
  public function getCountry();

  /**
   * Sets the TrothMapsState entity country.
   *
   * @param string $country
   *   The TrothMapsState entity country.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function setCountry($country);

  /**
   * Gets the TrothMapsState entity geom.
   *
   * @return string
   *   Geom of the TrothMapsState entity.
   */
  public function getGeom();

  /**
   * Sets the TrothMapsState entity geom.
   *
   * @param string $format
   *   The TrothMapsState entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function getGeomFormat($format);

  /**
   * Sets the TrothMapsState entity geom.
   *
   * @param string $geom
   *   The TrothMapsState entity geom.
   * @param string $format
   *   The TrothMapsState entity geom format.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsStateEntityInterface
   *   The called TrothMapsState entity entity.
   */
  public function setGeom($geom, $format);

}
