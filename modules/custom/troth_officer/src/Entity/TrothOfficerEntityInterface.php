<?php

namespace Drupal\troth_officer\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface for defining Troth Officer entity entities.
 *
 * @ingroup troth_officer
 */
interface TrothOfficerEntityInterface extends EntityChangedInterface {

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
   * Gets the TrothOfficer entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothOfficer entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothOfficer entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothOfficer entity creation timestamp.
   *
   * @return \Drupal\troth_officer\Entity\TrothOfficerEntityInterface
   *   The called TrothOfficer entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the TrothOfficer entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothOfficer entity.
   */
  public function getStartDate();

  /**
   * Sets the TrothOfficer entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothOfficer entity creation timestamp.
   *
   * @return \Drupal\troth_officer\Entity\TrothOfficerEntityInterface
   *   The called TrothOfficer entity entity.
   */
  public function setStartDate($timestamp);

  /**
   * Gets the TrothOfficer entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothOfficer entity.
   */
  public function getEndDate();

  /**
   * Sets the TrothOfficer entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothOfficer entity creation timestamp.
   *
   * @return \Drupal\troth_officer\Entity\TrothOfficerEntityInterface
   *   The called TrothOfficer entity entity.
   */
  public function setEndDate($timestamp);

}
