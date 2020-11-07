<?php

namespace Drupal\troth_elections\Entity;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Troth Elections Email entity entities.
 *
 * @ingroup troth_elections_emails
 */
interface TrothElectionsNominationVoterEntityInterface extends EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the TrothElectionsNominationVoter entity Office ID.
   *
   * @return string
   *   email of the TrothElectionsNominationVoter entity.
   */
  public function getSignature();

  /**
   * Sets the TrothElectionsNominationVoter entity Office ID.
   *
   * @param string $signature
   *   The TrothElectionsNominationVoter entity Office ID.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoterEntityInterface
   *   The called TrothElectionsNominationVoter entity entity.
   */
  public function setSignature($signature);

  /**
   * Gets the TrothElectionsNominationVoter entity Office ID.
   *
   * @return string
   *   email of the TrothElectionsNominationVoter entity.
   */
  public function getProxy();

  /**
   * Sets the TrothElectionsNominationVoter entity Office ID.
   *
   * @param string $proxy
   *   The TrothElectionsNominationVoter entity Office ID.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoterEntityInterface
   *   The called TrothElectionsNominationVoter entity entity.
   */
  public function setProxy($proxy);

  /**
   * Gets the TrothElectionsNominationVoter entity Office ID.
   *
   * @return string
   *   email of the TrothElectionsNominationVoter entity.
   */
  public function getIp();

  /**
   * Sets the TrothElectionsNominationVoter entity Office ID.
   *
   * @param string $ip
   *   The TrothElectionsNominationVoter entity Office ID.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoterEntityInterface
   *   The called TrothElectionsNominationVoter entity entity.
   */
  public function setIp($ip);

  /**
   * Gets the TrothElectionsNominationVoter entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the TrothElectionsNominationVoter entity.
   */
  public function getCreatedTime();

  /**
   * Sets the TrothElectionsNominationVoter entity creation timestamp.
   *
   * @param int $timestamp
   *   The TrothElectionsNominationVoter entity creation timestamp.
   *
   * @return \Drupal\troth_elections\Entity\TrothElectionsNominationVoterEntityInterface
   *   The called TrothElectionsNominationVoter entity entity.
   */
  public function setCreatedTime($timestamp);

}
