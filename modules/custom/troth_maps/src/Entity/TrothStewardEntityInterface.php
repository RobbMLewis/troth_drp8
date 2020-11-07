<?php

namespace Drupal\troth_maps\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface for defining Troth Officer entity entities.
 *
 * @ingroup troth_maps
 */
interface TrothStewardEntityInterface extends EntityChangedInterface {

  /**
   * Returns the entity officer's user entity.
   *
   * @return \Drupal\user\UserInterface
   *   The officer user entity.
   */
  public function getOfficer();

  /**
   * Sets the entity officer's user entity.
   *
   * @param \Drupal\user\UserInterface $account
   *   The officer user entity.
   *
   * @return $this
   */
  public function setOfficer(UserInterface $account);

  /**
   * Returns the entity officer's user ID.
   *
   * @return int|null
   *   The officer user ID, or NULL in case the user ID field has not been set
   *   on the entity.
   */
  public function getOfficerId();

  /**
   * Sets the entity officer's user ID.
   *
   * @param int $uid
   *   The officer user id.
   *
   * @return $this
   */
  public function setOfficerId($uid);

  /**
   * Returns the entity Region ID.
   *
   * @return int
   *   The Region ID on the entity.
   */
  public function getRegionId();

  /**
   * Sets the entity Region ID.
   *
   * @param int $region_id
   *   The region id.
   *
   * @return $this
   */
  public function setRegionId($region_id);

  /**
   * Gets the TrothSteward entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothSteward entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothSteward entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothSteward entity creation timestamp.
   *
   * @return \Drupal\troth_maps\Entity\TrothStewardEntityInterface
   *   The called TrothSteward entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the TrothSteward entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothSteward entity.
   */
  public function getStartDate();

  /**
   * Sets the TrothSteward entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothSteward entity creation timestamp.
   *
   * @return \Drupal\troth_maps\Entity\TrothStewardEntityInterface
   *   The called TrothSteward entity entity.
   */
  public function setStartDate($timestamp);

  /**
   * Gets the TrothSteward entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothSteward entity.
   */
  public function getEndDate();

  /**
   * Sets the TrothSteward entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothSteward entity creation timestamp.
   *
   * @return \Drupal\troth_maps\Entity\TrothStewardEntityInterface
   *   The called TrothSteward entity entity.
   */
  public function setEndDate($timestamp);

}
