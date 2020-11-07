<?php

namespace Drupal\troth_maps\Entity;

/**
 * Provides an interface for defining Troth Maps Downloads entity entities.
 *
 * @ingroup troth_maps_downloads
 */
interface TrothMapsDownloadsEntityInterface {

  /**
   * Gets the TrothMapsDownloads entity country.
   *
   * @return string
   *   country of the TrothMapsDownloads entity.
   */
  public function getCountry();

  /**
   * Sets the TrothMapsDownloads entity country.
   *
   * @param string $country
   *   The TrothMapsDownloads entity country.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsDownloadsEntityInterface
   *   The called TrothMapsDownloads entity entity.
   */
  public function setCountry($country);

  /**
   * Gets the TrothMapsDownloads entity last_update.
   *
   * @return int
   *   last_update of the TrothMapsDownloads entity.
   */
  public function getLastUpdate();

  /**
   * Sets the TrothMapsDownloads entity last_update.
   *
   * @param int $last_update
   *   The TrothMapsDownloads entity last_update.
   *
   * @return \Drupal\troth_maps\Entity\TrothMapsDownloadsEntityInterface
   *   The called TrothMapsDownloads entity entity.
   */
  public function setLastUpdate($last_update);

}
