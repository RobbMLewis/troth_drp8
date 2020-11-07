<?php

namespace Drupal\troth_google\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Troth Google Group entity entities.
 *
 * @ingroup troth_google
 */
interface TrothGoogleGroupEntityInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the TrothGoogleGroup entity email.
   *
   * @return string
   *   email of the TrothGoogleGroup entity.
   */
  public function getEmail();

  /**
   * Sets the TrothGoogleGroup entity email.
   *
   * @param string $email
   *   The TrothGoogleGroup entity email.
   *
   * @return \Drupal\troth_google\Entity\TrothGoogleGroupEntityInterface
   *   The called TrothGoogleGroup entity entity.
   */
  public function setEmail($email);

  /**
   * Gets the TrothGoogleGroup entity subscribed.
   *
   * @return int
   *   subscribed of the TrothGoogleGroup entity.
   */
  public function getSubscribed();

  /**
   * Sets the TrothGoogleGroup entity subscribed.
   *
   * @param int $subscribed
   *   The TrothGoogleGroup entity subscribed.
   *
   * @return \Drupal\troth_google\Entity\TrothGoogleGroupEntityInterface
   *   The called TrothGoogleGroup entity entity.
   */
  public function setSubscribed($subscribed);

  /**
   * Gets the TrothGoogleGroup entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothGoogleGroup entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothGoogleGroup entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothGoogleGroup entity creation timestamp.
   *
   * @return \Drupal\troth_google\Entity\TrothGoogleGroupEntityInterface
   *   The called TrothGoogleGroup entity entity.
   */
  public function setCreatedTime($timestamp);

}
