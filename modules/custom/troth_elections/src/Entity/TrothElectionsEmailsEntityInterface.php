<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsEmailsEntityInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the TrothElectionsEmails entity email.
   *
   * @return string
   *   email of the TrothElectionsEmails entity.
   */
  public function getEmail();

  /**
   * Sets the TrothElectionsEmails entity email.
   *
   * @param string $email
   *   The TrothElectionsEmails entity email.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsEmailsEntityInterface
   *   The called TrothElectionsEmails entity entity.
   */
  public function setEmail($email);

  /**
   * Gets the TrothElectionsEmails entity date sent.
   *
   * @return string
   *   subscribed of the TrothElectionsEmails entity.
   */
  public function getDateSent();

  /**
   * Sets the TrothElectionsEmails entity date sent.
   *
   * @param string $date_sent
   *   The TrothElectionsEmails entity date sent.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsEmailsEntityInterface
   *   The called TrothElectionsEmails entity entity.
   */
  public function setDateSent($date_sent);

  /**
   * Gets the TrothElectionsEmails entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsEmails entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsEmails entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsEmails entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsEmailsEntityInterface
   *   The called TrothElectionsEmails entity entity.
   */
  public function setCreatedTime($timestamp);

}
